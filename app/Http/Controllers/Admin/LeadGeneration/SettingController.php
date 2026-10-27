<?php
namespace App\Http\Controllers\Admin\LeadGeneration;

use App\Http\Controllers\Controller;
use App\Services\LeadAgent\{LeadSettings, TwilioClient};
use Illuminate\Http\Request;

class SettingController extends Controller {
    public function index(TwilioClient $twilio) {
        $values = [];
        foreach (LeadSettings::KEYS as $k) $values[$k] = LeadSettings::get($k);
        return view('lead-generation.settings', [
            'v' => $values, 'aiKey' => (bool) config('leadagent.ai.key'), 'model' => config('leadagent.ai.model'),
            'twilio' => $twilio->configured(), 'canCall' => $twilio->canCall(),
            'tz' => config('app.timezone'),
        ]);
    }

    public function update(Request $request) {
        $data = $request->validate([
            'owner_name' => 'nullable|string|max:80',
            'owner_phone' => 'nullable|string|max:25',
            'max_failed_attempts' => 'required|integer|min:1|max:10',
            'block_minutes' => 'required|integer|min:1|max:1440',
            'ai_enabled' => 'nullable|in:0,1',
            'call_start' => ['required', 'regex:/^([01]\d|2[0-3]):[0-5]\d$/'],
            'call_end' => ['required', 'regex:/^([01]\d|2[0-3]):[0-5]\d$/'],
        ]);
        $data['ai_enabled'] = $request->boolean('ai_enabled') ? '1' : '0';
        LeadSettings::save($data);
        return back()->with('success', 'Lead Generation settings saved.');
    }
}
