<?php
namespace App\Http\Controllers\Admin\LeadGeneration;

use App\Http\Controllers\Controller;
use App\Models\Lead\LeadCallScript;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ScriptController extends Controller {
    public function index(Request $request) {
        $scripts = LeadCallScript::latest()->get();
        $edit = $request->edit ? LeadCallScript::find($request->edit) : null;
        return view('lead-generation.scripts', ['scripts' => $scripts, 'edit' => $edit, 'placeholders' => LeadCallScript::PLACEHOLDERS]);
    }

    public function store(Request $request) {
        LeadCallScript::create($this->validated($request) + ['is_active' => true]);
        return redirect()->route('dashboard.lead.scripts.index')->with('success', 'Call script saved.');
    }

    public function update(Request $request, LeadCallScript $script) {
        $script->update($this->validated($request) + ['is_active' => $request->boolean('is_active')]);
        return redirect()->route('dashboard.lead.scripts.index')->with('success', 'Call script updated.');
    }

    public function destroy(LeadCallScript $script) {
        $script->delete();
        return back()->with('success', 'Call script deleted.');
    }

    public function samples() {
        $samples = [
            ['Fee reminder (Hinglish)', 'fee_reminder', 'hinglish',
             "Namaste {parent_name}, main {school_name} se bol raha hoon. {student_name} ki fees mein {due_amount} baaki hai, jiski antim tareekh {due_date} hai. Kripya time par jama kar dein. Dhanyavaad."],
            ['Absence follow-up (Hinglish)', 'attendance', 'hinglish',
             "Namaste {parent_name}, {school_name} se call hai. {student_name} lagataar {absent_streak} din se school nahi aaya hai aur uski attendance {attendance_percent} hai. Kya sab theek hai? Kripya jaldi school bhejiye."],
            ['Parent meeting (English)', 'ptm', 'en',
             "Hello {parent_name}, this is {school_name}. We would like to invite you to meet {student_name}'s class teacher this week to discuss progress. {student_name} is doing best in {strongest_subject} and could use some support in {weakest_subject}. Thank you."],
            ['Fee reminder (Hindi)', 'fee_reminder', 'hi',
             "नमस्ते {parent_name}, मैं {school_name} से बोल रहा हूँ। {student_name} की फीस में {due_amount} बाकी है, जिसकी अंतिम तारीख {due_date} है। कृपया समय पर जमा कर दें। धन्यवाद।"],
        ];
        $n = 0;
        foreach ($samples as [$name, $purpose, $lang, $body]) {
            if (!LeadCallScript::where('name', $name)->exists()) {
                LeadCallScript::create(['name' => $name, 'purpose' => $purpose, 'language' => $lang, 'body' => $body, 'is_active' => true]);
                $n++;
            }
        }
        return back()->with('success', $n ? "{$n} sample script(s) added." : 'Sample scripts already exist.');
    }

    private function validated(Request $request): array {
        return $request->validate([
            'name'     => 'required|string|max:120',
            'purpose'  => ['required', Rule::in(['general', 'fee_reminder', 'attendance', 'ptm', 'marks'])],
            'language' => ['required', Rule::in(['hi', 'hinglish', 'en'])],
            'body'     => 'required|string|max:3000',
        ]);
    }
}
