<?php
namespace App\Services\LeadGen;

use App\Models\Lead;
use App\Models\LeadMessage;
use App\Models\LeadScript;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Outbound AI phone calls through Vapi (managed voice agent: speech-to-text,
 * LLM, text-to-speech and the phone line are handled by Vapi).
 *
 * One-time setup in the Vapi dashboard: create an assistant whose system prompt
 * contains the variables {{script}}, {{knowledge}}, {{lead_name}}, {{student_name}},
 * {{class_interest}}, {{school_name}} and {{agent_name}} — they are filled per call.
 */
class VoiceService
{
    public function configured(): bool
    {
        $c = config('leadgen.voice');
        return !empty($c['api_key']) && !empty($c['assistant_id']) && !empty($c['phone_number_id']);
    }

    public function call(Lead $lead, ?LeadScript $script = null): LeadMessage
    {
        if (!$lead->canBeContacted()) {
            return $this->record($lead, 'failed', 'Lead has opted out or has no consent.');
        }
        if (!$this->configured()) {
            return $this->record($lead, 'failed', 'Voice is not configured (.env VAPI_API_KEY / VAPI_ASSISTANT_ID / VAPI_PHONE_NUMBER_ID).');
        }

        $script   = $script ?: LeadScript::where('channel', 'call')->where('is_default', true)->first();
        $agent    = setting('leadgen_agent_name', 'School Assistant');
        $school   = setting('school_name', 'our school');
        $scriptTx = $script ? $script->render($lead) : "Introduce yourself, ask if {$lead->name} is still looking for admission, "
                  . 'answer questions about the school, and offer a visit or a callback from the admission team.';

        $first = strtr(
            (string) setting('leadgen_call_first_message', 'Hello, may I speak with {name}? This is {agent} calling from {school}.'),
            ['{name}' => $lead->name, '{agent}' => $agent, '{school}' => $school]
        );

        $payload = [
            'assistantId'        => config('leadgen.voice.assistant_id'),
            'phoneNumberId'      => config('leadgen.voice.phone_number_id'),
            'customer'           => ['number' => '+' . $lead->phone, 'name' => $lead->name],
            'assistantOverrides' => [
                'firstMessage'   => $first,
                'variableValues' => [
                    'script'         => $scriptTx,
                    'knowledge'      => app(AiAgent::class)->schoolKnowledge(),
                    'lead_name'      => $lead->name,
                    'student_name'   => $lead->student_name ?: 'your child',
                    'class_interest' => $lead->class_interest ?: 'not specified',
                    'school_name'    => $school,
                    'agent_name'     => $agent,
                ],
            ],
        ];

        try {
            $response = Http::withToken(config('leadgen.voice.api_key'))
                ->timeout(25)
                ->post(rtrim(config('leadgen.voice.base_url'), '/') . '/call', $payload);
        } catch (\Throwable $e) {
            Log::error('Voice call exception', ['error' => $e->getMessage()]);
            return $this->record($lead, 'failed', 'Network error: ' . $e->getMessage());
        }

        if (!$response->successful()) {
            Log::warning('Voice call failed', ['status' => $response->status(), 'body' => $response->body()]);
            return $this->record($lead, 'failed', $response->json('message') ?: $response->body());
        }

        return $this->record($lead, 'calling', null, $response->json('id'));
    }

    protected function record(Lead $lead, string $status, ?string $error, ?string $callId = null): LeadMessage
    {
        $message = $lead->messages()->create([
            'channel'     => 'call',
            'direction'   => 'out',
            'body'        => $error ? null : 'Call started…',
            'status'      => $status,
            'provider_id' => $callId,
            'by_ai'       => true,
            'meta'        => $error ? ['error' => $error] : null,
        ]);

        if ($status === 'calling') {
            $lead->forceFill(['last_contacted_at' => now()])->save();
        }

        return $message;
    }
}
