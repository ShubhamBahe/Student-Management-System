<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\AcademicYear;
use Illuminate\Http\Request;

class ExamController extends Controller {
    public function index() {
        $exams = Exam::with('academicYear')->latest()->paginate(15);
        return view('exams.index', compact('exams'));
    }

    public function create() {
        $academicYears = AcademicYear::all();
        return view('exams.create', compact('academicYears'));
    }
    
    public function store(Request $request) {
        $request->validate([
            'name'             => 'required|string|max:255',
            'academic_year_id' => 'required|exists:academic_years,id',
            'start_date'       => 'required|date',
            'end_date'         => 'required|date|after_or_equal:start_date',
        ]);
        Exam::create($request->all());
        return redirect()->route('dashboard.exams.index')->with('success', 'Exam created successfully!');
    }

    public function show(Exam $exam) {
        $exam->load('academicYear', 'marks');
        return view('exams.show', compact('exam'));
    }

    public function edit(Exam $exam) {
        $academicYears = AcademicYear::all();
        return view('exams.edit', compact('exam', 'academicYears'));
    }

    public function update(Request $request, Exam $exam) {
        $request->validate([
            'name'       => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'status'     => 'required|in:upcoming,ongoing,completed',
        ]);

        $exam->update($request->all());
        return redirect()->route('dashboard.exams.index')->with('success', 'Exam updated successfully!');
    }

    public function destroy(Exam $exam) {
        $exam->delete();
        if(request()->ajax()) {
            return response()->json(['success' => true]);
        }
        return redirect()->route('dashboard.exams.index')->with('success', 'Exam deleted successfully!');
    }
}