<?php
namespace App\Jobs;

use App\Models\Lead;
use App\Models\SystemNotification;
use App\Services\LeadGen\AiAgent;
use App\Services\LeadGen\Phone;
use App\Services\LeadGen\StudentLookup;
use App\Services\LeadGen\WhatsAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class ProcessIncomingWhatsApp implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;
    public int $timeout = 120;

    public function __construct(
        public string $from,
        public ?string $profileName,
        public string $type,
        public ?string $text,
        public string $waMessageId,
    ) {}

    public function handle(WhatsAppService $wa, AiAgent $ai): void
    {
        $phone = Phone::normalize($this->from) ?: preg_replace('/\D+/', '', $this->from);

        $lead = Lead::where('phone', $phone)->first()
            ?? Lead::where('phone', 'like', '%' . Phone::last10($phone))->first();

        if (!$lead) {
            $isFamily = StudentLookup::forPhone($phone)->isNotEmpty();
            $lead = Lead::create([
                'name'    => $this->profileName ?: ('+' . $phone),
                'phone'   => $phone,
                'source'  => $isFamily ? 'parent' : 'whatsapp',
                'status'  => 'new',
                'consent' => true,
            ]);
            if (!$isFamily) {
                SystemNotification::notifyAdmins(
                    'New WhatsApp lead',
                    $lead->name . ' messaged the school on WhatsApp',
                    route('dashboard.leadgen.leads.show', $lead),
                    'fab fa-whatsapp',
                    '#16a34a'
                );
            }
        }

        $body = $this->type === 'text'
            ? (string) $this->text
            : "[Contact sent a {$this->type} message — the assistant cannot read it]";

        try {
            $lead->messages()->create([
                'channel'     => 'whatsapp',
                'direction'   => 'in',
                'body'        => $body,
                'status'      => 'received',
                'provider_id' => $this->waMessageId,
            ]);
        } catch (QueryException $e) {
            if ((string) $e->getCode() === '23000') {
                return; // duplicate webhook delivery
            }
            throw $e;
        }

        // ---- opt-out / opt-in keywords
        $word = Str::lower(trim($body));
        if (in_array($word, ['stop', 'unsubscribe', 'opt out', 'optout', 'stop messages'], true)) {
            $wa->sendText($lead, 'You have been unsubscribed and will not receive further messages. Reply START to subscribe again.');
            $lead->update(['opted_out' => true]);
            return;
        }
        if (in_array($word, ['start', 'subscribe'], true) && $lead->opted_out) {
            $lead->update(['opted_out' => false]);
            $wa->sendText($lead, 'Thank you, you are subscribed again. How can we help you?');
            return;
        }
        if ($lead->opted_out) {
            return;
        }

        // ---- human has taken over this chat
        if (!$lead->ai_enabled) {
            return;
        }

        // ---- AI master switch is off
        if (setting('leadgen_enabled', '1') !== '1') {
            $this->flagHuman($lead, 'AI replies are switched off — new message waiting');
            return;
        }

        $reply = $ai->reply($lead, $ai->historyFor($lead));

        if ($reply === null) {
            $this->flagHuman($lead, 'The AI could not answer this message');
            $reply = 'Thank you for your message. A member of our team will get back to you shortly.';
        }

        $wa->sendText($lead, $reply, true);
    }

    protected function flagHuman(Lead $lead, string $reason): void
    {
        if ($lead->needs_human) {
            return;
        }
        $lead->update(['needs_human' => true]);
        SystemNotification::notifyAdmins(
            'Lead needs a human',
            $lead->name . ': ' . $reason,
            route('dashboard.leadgen.leads.show', $lead),
            'fas fa-headset',
            '#dc2626'
        );
    }
}
