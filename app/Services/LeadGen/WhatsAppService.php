<?php
namespace App\Services\LeadGen;

use App\Models\Lead;
use App\Models\LeadMessage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/** WhatsApp Business Cloud API (Meta). */
class WhatsAppService
{
    public function configured(): bool
    {
        return config('leadgen.whatsapp.token') && config('leadgen.whatsapp.phone_number_id');
    }

    /** True if the lead wrote to us in the last 24h (free-form replies allowed). */
    public function inServiceWindow(Lead $lead): bool
    {
        return $lead->messages()
            ->where('channel', 'whatsapp')
            ->where('direction', 'in')
            ->where('created_at', '>=', now()->subHours(24))
            ->exists();
    }

    /**
     * Send free text inside the 24h window, otherwise the approved template.
     * Always records the attempt (success or failure) on the lead.
     */
    public function sendSmart(Lead $lead, string $text, bool $byAi = false): LeadMessage
    {
        if ($this->inServiceWindow($lead)) {
            return $this->sendText($lead, $text, $byAi);
        }
        if (config('leadgen.whatsapp.template_name')) {
            return $this->sendTemplate($lead, $byAi);
        }
        return $this->record($lead, $text, $byAi, null, 'failed',
            'Outside the 24-hour window and no WHATSAPP_TEMPLATE_NAME is configured.');
    }

    public function sendText(Lead $lead, string $text, bool $byAi = false): LeadMessage
    {
        if (!$lead->canBeContacted()) {
            return $this->record($lead, $text, $byAi, null, 'failed', 'Lead has opted out or has no consent.');
        }

        $result = $this->post([
            'messaging_product' => 'whatsapp',
            'to'                => $lead->phone,
            'type'              => 'text',
            'text'              => ['preview_url' => false, 'body' => mb_substr($text, 0, 4000)],
        ]);

        return $this->record($lead, $text, $byAi, $result['id'], $result['ok'] ? 'sent' : 'failed', $result['error']);
    }

    public function sendTemplate(Lead $lead, bool $byAi = false): LeadMessage
    {
        $name   = config('leadgen.whatsapp.template_name');
        $school = setting('school_name', 'our school');
        $label  = "[Template: {$name}] {$lead->name}, {$school}";

        if (!$lead->canBeContacted()) {
            return $this->record($lead, $label, $byAi, null, 'failed', 'Lead has opted out or has no consent.');
        }

        $result = $this->post([
            'messaging_product' => 'whatsapp',
            'to'                => $lead->phone,
            'type'              => 'template',
            'template'          => [
                'name'     => $name,
                'language' => ['code' => config('leadgen.whatsapp.template_lang', 'en')],
                'components' => [[
                    'type'       => 'body',
                    'parameters' => [
                        ['type' => 'text', 'text' => $lead->name],
                        ['type' => 'text', 'text' => $school],
                    ],
                ]],
            ],
        ]);

        return $this->record($lead, $label, $byAi, $result['id'], $result['ok'] ? 'sent' : 'failed', $result['error']);
    }

    /** @return array{ok:bool, id:?string, error:?string} */
    protected function post(array $payload): array
    {
        if (!$this->configured()) {
            return ['ok' => false, 'id' => null, 'error' => 'WhatsApp is not configured (.env WHATSAPP_TOKEN / WHATSAPP_PHONE_NUMBER_ID).'];
        }

        $url = sprintf('https://graph.facebook.com/%s/%s/messages',
            config('leadgen.whatsapp.api_version'), config('leadgen.whatsapp.phone_number_id'));

        try {
            $response = Http::withToken(config('leadgen.whatsapp.token'))->timeout(20)->post($url, $payload);
        } catch (\Throwable $e) {
            Log::error('WhatsApp send exception', ['error' => $e->getMessage()]);
            return ['ok' => false, 'id' => null, 'error' => 'Network error: ' . $e->getMessage()];
        }

        if (!$response->successful()) {
            $error = $response->json('error.message') ?: $response->body();
            Log::warning('WhatsApp send failed', ['status' => $response->status(), 'body' => $response->body()]);
            return ['ok' => false, 'id' => null, 'error' => $error];
        }

        return ['ok' => true, 'id' => $response->json('messages.0.id'), 'error' => null];
    }

    protected function record(Lead $lead, string $body, bool $byAi, ?string $providerId, string $status, ?string $error): LeadMessage
    {
        $message = $lead->messages()->create([
            'channel'     => 'whatsapp',
            'direction'   => 'out',
            'body'        => $body,
            'status'      => $status,
            'provider_id' => $providerId,
            'by_ai'       => $byAi,
            'meta'        => $error ? ['error' => $error] : null,
        ]);

        if ($status === 'sent') {
            $lead->forceFill(['last_contacted_at' => now()])->save();
            if ($lead->status === 'new') {
                $lead->update(['status' => 'contacted']);
            }
        }

        return $message;
    }
}
