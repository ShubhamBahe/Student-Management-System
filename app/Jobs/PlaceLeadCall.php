<?php
namespace App\Jobs;

use App\Models\Lead;
use App\Models\LeadScript;
use App\Services\LeadGen\VoiceService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PlaceLeadCall implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;

    public function __construct(public int $leadId, public ?int $scriptId = null) {}

    public function handle(VoiceService $voice): void
    {
        $lead = Lead::find($this->leadId);
        if (!$lead) {
            return;
        }
        $script = $this->scriptId ? LeadScript::find($this->scriptId) : null;
        $voice->call($lead, $script);
    }
}
