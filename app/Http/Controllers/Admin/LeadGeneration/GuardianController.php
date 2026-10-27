<?php
namespace App\Http\Controllers\Admin\LeadGeneration;

use App\Http\Controllers\Controller;
use App\Models\Lead\LeadGuardian;
use App\Models\Student;
use App\Services\LeadAgent\PhoneNormalizer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class GuardianController extends Controller {
    public function index(Request $request) {
        $q = LeadGuardian::with('student.user', 'student.class', 'student.section');
        if ($s = trim((string) $request->search)) {
            $q->where(fn ($w) => $w->where('name', 'like', "%$s%")->orWhere('phone', 'like', '%' . preg_replace('/\D+/', '', $s) . '%')
                ->orWhereHas('student.user', fn ($u) => $u->where('name', 'like', "%$s%")));
        }
        $guardians = $q->latest()->paginate(15)->withQueryString();
        $students  = Student::with('user', 'class', 'section')->where('status', 'active')->get()->sortBy(fn ($s) => $s->user->name ?? '');
        $edit = $request->edit ? LeadGuardian::find($request->edit) : null;

        // phones shared by several children (the agent will ask "which child?")
        $multi = LeadGuardian::selectRaw('phone, count(*) c')->where('is_active', true)->groupBy('phone')->having('c', '>', 1)->pluck('c', 'phone');
        return view('lead-generation.guardians', compact('guardians', 'students', 'edit', 'multi'));
    }

    public function store(Request $request) {
        $data = $this->validated($request);
        $exists = LeadGuardian::where('student_id', $data['student_id'])->where('phone', PhoneNormalizer::normalize($data['phone']))->exists();
        if ($exists) return back()->withInput()->with('error', 'This phone number is already saved for that student.');
        LeadGuardian::create($data + ['is_active' => true]);
        return redirect()->route('dashboard.lead.guardians.index')->with('success', 'Parent contact saved.');
    }

    public function update(Request $request, LeadGuardian $guardian) {
        $guardian->update($this->validated($request) + ['is_active' => $request->boolean('is_active')]);
        return redirect()->route('dashboard.lead.guardians.index')->with('success', 'Parent contact updated.');
    }

    public function destroy(LeadGuardian $guardian) {
        $guardian->delete();
        return back()->with('success', 'Parent contact removed.');
    }

    /** CSV: admission_number,parent_name,relation,phone,language */
    public function import(Request $request) {
        $request->validate(['file' => 'required|file|mimes:csv,txt|max:2048']);
        $h = fopen($request->file('file')->getRealPath(), 'r');
        $added = 0; $skipped = [];
        $first = true;
        while (($row = fgetcsv($h)) !== false) {
            if ($first) { $first = false; if (!preg_match('/\d{6,}/', implode('', $row))) continue; }
            [$adm, $name, $rel, $phone, $lang] = array_pad(array_map('trim', $row), 5, null);
            $student = $adm ? Student::where('admission_number', $adm)->orWhere('roll_number', $adm)->first() : null;
            $norm = PhoneNormalizer::normalize($phone);
            if (!$student || !$norm || !$name) { $skipped[] = $adm ?: '(blank)'; continue; }
            $lang = in_array($lang, ['hi', 'hinglish', 'en'], true) ? $lang : 'hinglish';
            LeadGuardian::updateOrCreate(['student_id' => $student->id, 'phone' => $norm],
                ['name' => $name, 'relation' => $rel ?: 'Parent', 'preferred_language' => $lang, 'is_active' => true]);
            $added++;
        }
        fclose($h);
        $msg = "Imported {$added} contact(s)." . ($skipped ? ' Skipped ' . count($skipped) . ' row(s): ' . implode(', ', array_slice($skipped, 0, 8)) : '');
        return back()->with($added ? 'success' : 'error', $msg);
    }

    private function validated(Request $request): array {
        $data = $request->validate([
            'student_id' => 'required|exists:students,id',
            'name'       => 'required|string|max:120',
            'relation'   => 'nullable|string|max:40',
            'phone'      => ['required', 'string', 'max:25', function ($a, $v, $fail) {
                if (!PhoneNormalizer::normalize($v)) $fail('Enter a valid phone number.');
            }],
            'preferred_language' => ['required', Rule::in(['hi', 'hinglish', 'en'])],
        ]);
        $data['relation'] = $data['relation'] ?: 'Parent';
        return $data;
    }
}
