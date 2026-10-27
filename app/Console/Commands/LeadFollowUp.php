<?php
namespace App\Console\Commands;

use App\Jobs\SendLeadWhatsApp;
use App\Models\Lead;
use Illuminate\Console\Command;

class LeadFollowUp extends Command
{
    protected $signature   = 'leadgen:follow-up';
    protected $description = 'Send WhatsApp follow-ups to leads whose follow-up date has arrived';

    public function handle(): int
    {
        if (setting('leadgen_auto_followup', '0') !== '1') {
            return self::SUCCESS;
        }

        $school = setting('school_name', 'our school');
        $count  = 0;

        Lead::where('source', '!=', 'parent')
            ->whereNotIn('status', ['enrolled', 'not_interested'])
            ->where('consent', true)->where('opted_out', false)->where('needs_human', false)
            ->whereNotNull('next_follow_up_at')
            ->where('next_follow_up_at', '<=', now())
            ->get()
            ->each(function (Lead $lead) use ($school, &$count) {
                SendLeadWhatsApp::dispatch(
                    $lead->id,
                    "Hello {$lead->name}, this is a quick follow-up from {$school} regarding your admission enquiry. "
                    . 'Would you like to schedule a school visit or have any questions answered?'
                )->delay(now()->addSeconds($count * 5));
                $lead->update(['next_follow_up_at' => null]);
                $count++;
            });

        $this->info("Queued {$count} follow-up message(s).");
        return self::SUCCESS;
    }
}
