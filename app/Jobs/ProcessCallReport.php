<?php
namespace App\Jobs;

use App\Models\LeadMessage;
use App\Models\SystemNotification;
use App\Services\LeadGen\AiAgent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class ProcessCallReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;
    public int $timeout = 90;

    public function __construct(public int $messageId) {}

    public function handle(AiAgent $ai): void
    {
        $message = LeadMessage::with('lead')->find($this->messageId);
        if (!$message || !$message->lead || empty($message->body)) {
            return;
        }
        $lead = $message->lead;

        $result = $ai->classifyCall($lead, $message->body);

        $meta = $message->meta ?? [];
        if (!empty($result['summary'])) {
            $meta['summary'] = $result['summary'];
            $message->update(['meta' => $meta]);
        }

        $update = [];
        if (!empty($result['status']) && !in_array($lead->status, ['enrolled'], true)) {
            $update['status'] = $result['status'];
        }
        if (!empty($result['follow_up_date'])) {
            try {
                $update['next_follow_up_at'] = Carbon::parse($result['follow_up_date'])->setTime(10, 0);
            } catch (\Throwable $e) {
            }
        }
        if (!empty($result['summary'])) {
            $update['notes'] = trim(($lead->notes ? $lead->notes . "\n" : '') . '[Call ' . now()->format('d M H:i') . '] ' . $result['summary']);
        }
        if ($update) {
            $lead->update($update);
        }

        if (($update['status'] ?? null) === 'interested') {
            SystemNotification::notifyAdmins(
                'Interested lead after AI call',
                $lead->name . ' — ' . ($result['summary'] ?? 'see call transcript'),
                route('dashboard.leadgen.leads.show', $lead),
                'fas fa-phone-volume',
                '#16a34a'
            );
        }
    }
}
