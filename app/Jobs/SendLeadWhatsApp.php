<?php
namespace App\Jobs;

use App\Models\Lead;
use App\Models\LeadScript;
use App\Services\LeadGen\WhatsAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendLeadWhatsApp implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;

    public function __construct(
        public int $leadId,
        public ?string $text = null,
        public ?int $scriptId = null,
    ) {}

    public function handle(WhatsAppService $wa): void
    {
        $lead = Lead::find($this->leadId);
        if (!$lead) {
            return;
        }

        $text = $this->text;
        if ($text === null) {
            $script = $this->scriptId
                ? LeadScript::find($this->scriptId)
                : LeadScript::where('channel', 'whatsapp')->where('is_default', true)->first();

            $text = $script
                ? $script->render($lead)
                : "Hello {$lead->name}, thank you for your interest in " . setting('school_name', 'our school')
                  . '. How can we help you today?';
        }

        $wa->sendSmart($lead, $text);
    }
}
