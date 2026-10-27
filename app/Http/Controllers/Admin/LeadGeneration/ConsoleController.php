<?php
namespace App\Http\Controllers\Admin\LeadGeneration;

use App\Http\Controllers\Controller;
use App\Models\Lead\{LeadCallScript, LeadGuardian};
use App\Models\Student;
use App\Services\LeadAgent\{AgentService, LanguageDetector, LeadSettings, ScriptRenderer};
use Illuminate\Http\Request;

class ConsoleController extends Controller {
    public function index() {
        $guardians = LeadGuardian::with('student.user', 'student.class', 'student.section')->where('is_active', true)->orderBy('phone')->get();
        $scripts   = LeadCallScript::where('is_active', true)->orderBy('name')->get();
        $students  = Student::with('user')->where('status', 'active')->get()->sortBy(fn ($s) => $s->user->name ?? '');
        return view('lead-generation.console', [
            'guardians' => $guardians, 'scripts' => $scripts, 'students' => $students,
            'aiActive' => LeadSettings::aiActive(),
        ]);
    }

    /** Admin test chat: behaves exactly like a parent messaging from the chosen phone number. */
    public function send(Request $request, AgentService $agent) {
        $data = $request->validate([
            'phone'   => 'required|string|max:30',
            'message' => 'nullable|string|max:1000',
            'media'   => 'nullable|in:voice,image',
            'channel' => 'nullable|in:web,voice',
        ]);
        $res = $agent->handle([
            'phone' => $data['phone'], 'text' => $data['message'] ?? '',
            'media' => $data['media'] ?? null, 'channel' => $data['channel'] ?? 'web',
        ]);
        return response()->json([
            'reply' => $res['reply'], 'status' => $res['status'],
            'language' => LanguageDetector::label($res['language'] ?? 'en'),
            'topics' => $res['topics'] ?? [],
        ]);
    }

    /** Render a call script for one student (for the "Play script" button). */
    public function preview(Request $request, ScriptRenderer $renderer) {
        $data = $request->validate(['script_id' => 'required|exists:lead_call_scripts,id', 'student_id' => 'required|exists:students,id']);
        $script  = LeadCallScript::findOrFail($data['script_id']);
        $student = Student::with(['user', 'class', 'section'])->findOrFail($data['student_id']);
        $guardian = LeadGuardian::where('student_id', $student->id)->where('is_active', true)->first();
        return response()->json(['text' => $renderer->render($script, $student, $guardian), 'language' => $script->language]);
    }
}
