<?php
namespace App\Http\Controllers\Admin\LeadGeneration;

use App\Http\Controllers\Controller;
use App\Models\Fee;
use App\Models\Lead\{LeadCall, LeadCallScript, LeadGuardian};
use App\Models\Student;
use App\Services\LeadAgent\{LeadSettings, ScriptRenderer, TwilioClient};
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class CallController extends Controller {
    public function index() {
        $calls   = LeadCall::with('student.user', 'script', 'guardian')->latest()->paginate(15);
        $scripts = LeadCallScript::where('is_active', true)->orderBy('name')->get();
        $students = Student::with('user', 'class', 'section')->where('status', 'active')->get()->sortBy(fn ($s) => $s->user->name ?? '');
        $twilio = app(TwilioClient::class);
        return view('lead-generation.calls', [
            'calls' => $calls, 'scripts' => $scripts, 'students' => $students,
            'canCall' => $twilio->canCall(),
            'window' => [LeadSettings::get('call_start'), LeadSettings::get('call_end')],
        ]);
    }

    public function store(Request $request, ScriptRenderer $renderer, TwilioClient $twilio) {
        $data = $request->validate([
            'script_id'   => 'required|exists:lead_call_scripts,id',
            'audience'    => 'required|in:selected,fee_due,absent_streak',
            'student_ids' => 'required_if:audience,selected|array',
            'student_ids.*' => 'exists:students,id',
        ]);

        if ($twilio->canCall() && !$this->insideCallWindow()) {
            return back()->withInput()->with('error', 'Calls can only be placed between ' . LeadSettings::get('call_start') . ' and ' . LeadSettings::get('call_end') . '.');
        }

        $script = LeadCallScript::findOrFail($data['script_id']);
        $students = $this->audience($data['audience'], $data['student_ids'] ?? [])->take(50);
        if ($students->isEmpty()) return back()->withInput()->with('error', 'No students match this audience.');

        $placed = 0; $queued = 0; $skipped = 0;
        URL::forceRootUrl(rtrim(config('app.url'), '/'));
        try {
            foreach ($students as $student) {
                $guardian = LeadGuardian::where('student_id', $student->id)->where('is_active', true)->orderBy('id')->first();
                if (!$guardian) {
                    LeadCall::create(['script_id' => $script->id, 'student_id' => $student->id, 'phone' => '-', 'status' => 'skipped',
                        'note' => 'No parent phone saved', 'created_by' => auth()->id()]);
                    $skipped++; continue;
                }
                // avoid double-calling within 10 minutes
                if (LeadCall::where('student_id', $student->id)->where('script_id', $script->id)->where('created_at', '>=', now()->subMinutes(10))
                    ->whereIn('status', ['queued', 'calling'])->exists()) { $skipped++; continue; }

                $call = LeadCall::create([
                    'script_id' => $script->id, 'student_id' => $student->id, 'guardian_id' => $guardian->id,
                    'phone' => $guardian->phone, 'status' => 'queued', 'created_by' => auth()->id(),
                    'script_text' => $renderer->render($script, $student, $guardian),
                ]);

                if (!$twilio->canCall()) {
                    $call->update(['note' => 'Phone provider not configured - use Console > Play script to rehearse.']);
                    $queued++; continue;
                }
                try {
                    $sid = $twilio->call($guardian->phone,
                        URL::signedRoute('lead.webhook.outbound', ['call' => $call->id]),
                        route('lead.webhook.voice-status', ['call' => $call->id]));
                    $call->update(['status' => 'calling', 'provider_sid' => $sid]);
                    $placed++;
                } catch (\Throwable $e) {
                    $call->update(['status' => 'failed', 'note' => mb_substr($e->getMessage(), 0, 250)]);
                }
            }
        } finally {
            URL::forceRootUrl(null);
        }

        $msg = "Calls placed: {$placed}" . ($queued ? ", saved without dialing: {$queued}" : '') . ($skipped ? ", skipped: {$skipped}" : '') . '.';
        return redirect()->route('dashboard.lead.calls.index')->with($placed || $queued ? 'success' : 'error', $msg);
    }

    private function audience(string $type, array $ids) {
        $q = Student::with(['user', 'class', 'section'])->where('status', 'active');
        if ($type === 'selected') return $q->whereIn('id', $ids)->get();
        if ($type === 'fee_due') {
            $ids = Fee::whereRaw('(amount - discount + fine - paid_amount) > 0.009')->pluck('student_id')->unique();
            return $q->whereIn('id', $ids)->get();
        }
        // absent_streak: latest recorded day is absent
        $svc = app(\App\Services\LeadAgent\StudentDataService::class);
        return $q->get()->filter(fn ($s) => $svc->attendance($s)['absent_streak'] >= 2)->values();
    }

    private function insideCallWindow(): bool {
        $now = now()->format('H:i');
        return $now >= LeadSettings::get('call_start', '09:00') && $now <= LeadSettings::get('call_end', '19:00');
    }
}
