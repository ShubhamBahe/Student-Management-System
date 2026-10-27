<?php
namespace App\Services\LeadAgent;

use App\Models\Lead\LeadOwnerAlert;
use App\Models\SystemNotification;
use Illuminate\Support\Facades\Log;

class OwnerNotifier {
    public function __construct(private TwilioClient $twilio) {}

    public function alert(string $type, string $title, string $details = '', ?string $phone = null, ?string $senderName = null): ?LeadOwnerAlert {
        try {
            // do not flood the owner with repeated system errors
            if ($type === 'system_error' && LeadOwnerAlert::where('type', 'system_error')->where('created_at', '>=', now()->subMinutes(15))->exists()) {
                return null;
            }

            $alert = LeadOwnerAlert::create([
                'type' => $type, 'title' => $title, 'details' => $details,
                'phone' => $phone, 'sender_name' => $senderName, 'status' => 'new',
            ]);

            try {
                SystemNotification::notifyAdmins($title, mb_substr($details ?: $title, 0, 240),
                    route('dashboard.lead.logs', ['tab' => 'alerts']), 'fas fa-headset', '#dc2626');
            } catch (\Throwable $e) { Log::warning('Lead alert panel notification failed: ' . $e->getMessage()); }

            $ownerPhone = LeadSettings::get('owner_phone');
            if ($ownerPhone && $this->twilio->configured()) {
                try {
                    $msg = "[" . strtoupper(str_replace('_', ' ', $type)) . "] " . $title
                        . ($phone ? "\nFrom: " . $phone : '') . ($senderName ? " (" . $senderName . ")" : '')
                        . ($details ? "\n" . mb_substr($details, 0, 900) : '');
                    $alert->update(['forwarded' => $this->twilio->send($ownerPhone, $msg)]);
                } catch (\Throwable $e) { Log::warning('Lead alert forward failed: ' . $e->getMessage()); }
            }
            return $alert;
        } catch (\Throwable $e) {
            Log::error('Lead alert could not be stored: ' . $e->getMessage());
            return null;
        }
    }
}
