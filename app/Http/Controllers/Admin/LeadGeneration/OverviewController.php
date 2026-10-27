<?php
namespace App\Http\Controllers\Admin\LeadGeneration;

use App\Http\Controllers\Controller;
use App\Models\Lead\{LeadAuthAttempt, LeadCall, LeadGuardian, LeadOwnerAlert, LeadQueryLog};
use App\Services\LeadAgent\{LeadSettings, TwilioClient};

class OverviewController extends Controller {
    public function index(TwilioClient $twilio) {
        $stats = [
            'guardians'   => LeadGuardian::where('is_active', true)->count(),
            'queries'     => LeadQueryLog::whereDate('created_at', today())->count(),
            'errors'      => LeadQueryLog::whereDate('created_at', today())->where('status', 'error')->count(),
            'blocked'     => LeadAuthAttempt::where('blocked_until', '>', now())->count(),
            'alerts'      => LeadOwnerAlert::where('status', 'new')->count(),
            'calls'       => LeadCall::whereDate('created_at', today())->count(),
        ];
        $byStatus = LeadQueryLog::selectRaw('status, count(*) as c')->whereDate('created_at', '>=', now()->subDays(7))->groupBy('status')->pluck('c', 'status');
        $recent   = LeadQueryLog::with('student.user')->latest()->limit(8)->get();
        $alerts   = LeadOwnerAlert::where('status', 'new')->latest()->limit(5)->get();

        $integrations = [
            'ai'      => LeadSettings::aiActive(),
            'twilio'  => $twilio->configured(),
            'calls'   => $twilio->canCall(),
            'owner'   => (bool) LeadSettings::get('owner_phone'),
        ];
        $webhooks = [
            'WhatsApp (incoming message)' => url('/webhooks/lead/whatsapp'),
            'Voice (incoming call)'       => url('/webhooks/lead/voice/incoming'),
        ];
        return view('lead-generation.overview', compact('stats', 'byStatus', 'recent', 'alerts', 'integrations', 'webhooks'));
    }
}
