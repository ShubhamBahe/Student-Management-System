<?php
namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessCallReport;
use App\Jobs\ProcessIncomingWhatsApp;
use App\Models\Lead;
use App\Models\LeadMessage;
use App\Services\LeadGen\Phone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LeadGenWebhookController extends Controller
{
    // ------------------------------------------------------------ WhatsApp

    /** Meta calls this once when you save the webhook URL in the developer console. */
    public function whatsappVerify(Request $request)
    {
        $mode      = $request->query('hub_mode', $request->query('hub.mode'));
        $token     = $request->query('hub_verify_token', $request->query('hub.verify_token'));
        $challenge = $request->query('hub_challenge', $request->query('hub.challenge'));

        $expected = config('leadgen.whatsapp.verify_token');

        if ($mode === 'subscribe' && $expected && hash_equals((string) $expected, (string) $token)) {
            return response((string) $challenge, 200)->header('Content-Type', 'text/plain');
        }
        return response('Forbidden', 403);
    }

    public function whatsappReceive(Request $request)
    {
        $secret = config('leadgen.whatsapp.app_secret');
        if (!$secret) {
            Log::error('LeadGen: WHATSAPP_APP_SECRET is not set; rejecting webhook.');
            return response('App secret not configured', 403);
        }

        $signature = (string) $request->header('X-Hub-Signature-256', '');
        $expected  = 'sha256=' . hash_hmac('sha256', $request->getContent(), $secret);
        if (!hash_equals($expected, $signature)) {
            return response('Invalid signature', 403);
        }

        foreach ((array) $request->input('entry', []) as $entry) {
            foreach ((array) ($entry['changes'] ?? []) as $change) {
                $value = $change['value'] ?? [];

                // Incoming messages
                $names = [];
                foreach ((array) ($value['contacts'] ?? []) as $contact) {
                    $names[$contact['wa_id'] ?? ''] = $contact['profile']['name'] ?? null;
                }
                foreach ((array) ($value['messages'] ?? []) as $msg) {
                    $id = $msg['id'] ?? null;
                    if (!$id || LeadMessage::where('provider_id', $id)->exists()) {
                        continue;
                    }
                    $type = $msg['type'] ?? 'text';
                    ProcessIncomingWhatsApp::dispatch(
                        (string) ($msg['from'] ?? ''),
                        $names[$msg['from'] ?? ''] ?? null,
                        $type,
                        $type === 'text' ? ($msg['text']['body'] ?? '') : null,
                        $id
                    );
                }

                // Delivery receipts for messages we sent
                foreach ((array) ($value['statuses'] ?? []) as $status) {
                    $row = LeadMessage::where('provider_id', $status['id'] ?? '')->first();
                    if (!$row) {
                        continue;
                    }
                    $new = $status['status'] ?? null; // sent | delivered | read | failed
                    if ($new) {
                        $meta = $row->meta ?? [];
                        if ($new === 'failed') {
                            $meta['error'] = $status['errors'][0]['title'] ?? 'Delivery failed';
                        }
                        $row->update(['status' => $new, 'meta' => $meta ?: null]);
                    }
                }
            }
        }

        return response('ok', 200);
    }

    // ------------------------------------------------------------ Voice (Vapi)

    public function voice(Request $request)
    {
        $secret = config('leadgen.voice.webhook_secret');
        if (!$secret) {
            Log::error('LeadGen: VAPI_WEBHOOK_SECRET is not set; rejecting webhook.');
            return response('Secret not configured', 403);
        }
        if (!hash_equals((string) $secret, (string) $request->header('x-vapi-secret', ''))) {
            return response('Forbidden', 403);
        }

        $message = (array) $request->input('message', []);
        if (($message['type'] ?? '') !== 'end-of-call-report') {
            return response()->json(['ok' => true]);
        }

        $callId     = data_get($message, 'call.id');
        $transcript = data_get($message, 'artifact.transcript') ?? data_get($message, 'transcript');
        $ended      = data_get($message, 'endedReason');
        $recording  = data_get($message, 'artifact.recordingUrl') ?? data_get($message, 'recordingUrl');

        $row = $callId ? LeadMessage::where('provider_id', $callId)->first() : null;

        if (!$row) {
            // Fall back to matching by the dialled number
            $number = Phone::normalize(data_get($message, 'call.customer.number'));
            $lead   = $number ? Lead::where('phone', $number)->first() : null;
            if (!$lead) {
                return response()->json(['ok' => true]);
            }
            $row = $lead->messages()->create([
                'channel' => 'call', 'direction' => 'out', 'by_ai' => true, 'provider_id' => $callId,
            ]);
        }

        $answered = !empty(trim((string) $transcript));
        $row->update([
            'body'   => $answered ? (string) $transcript : null,
            'status' => $answered ? 'completed' : ($ended ?: 'no-answer'),
            'meta'   => array_filter([
                'ended_reason' => $ended,
                'recording'    => $recording,
                'summary'      => data_get($message, 'analysis.summary') ?? data_get($message, 'summary'),
            ]),
        ]);

        if ($answered) {
            ProcessCallReport::dispatch($row->id);
        }

        return response()->json(['ok' => true]);
    }
}
