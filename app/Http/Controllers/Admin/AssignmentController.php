<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\Student;
use Illuminate\Http\Request;

class AssignmentController extends Controller {
    public function index() {
        $assignments = Assignment::with('class', 'subject')->latest()->paginate(15);
        return view('assignments.index', compact('assignments'));
    }

    public function create() {
        $classes  = SchoolClass::all();
        $subjects = Subject::all();
        return view('assignments.create', compact('classes', 'subjects'));
    }

    public function store(Request $request) {
        $request->validate([
            'title'      => 'required|string|max:255',
            'class_id'   => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'due_date'   => 'required|date',
        ]);
        Assignment::create([
            'title'       => $request->title,
            'description' => $request->description,
            'class_id'    => $request->class_id,
            'subject_id'  => $request->subject_id,
            'teacher_id'  => auth()->id(),
            'due_date'    => $request->due_date,
            'total_marks' => $request->total_marks ?? 100,
            'status'      => 'active',
        ]);
        return redirect()->route('dashboard.assignments.index')
            ->with('success', 'Assignment created successfully!');
    }

    public function show(Assignment $assignment) {
        $assignment->load('class', 'subject', 'submissions');
        return view('assignments.show', compact('assignment'));
    }

    public function edit(Assignment $assignment) {
        $classes  = SchoolClass::all();
        $subjects = Subject::all();
        return view('assignments.edit', compact('assignment', 'classes', 'subjects'));
    }

    public function update(Request $request, Assignment $assignment) {
        $assignment->update($request->all());
        return redirect()->route('dashboard.assignments.index')
            ->with('success', 'Assignment updated successfully!');
    }

    public function destroy(Assignment $assignment) {
        $assignment->delete();
        if(request()->ajax()) {
            return response()->json(['success' => true]);
        }
        return redirect()->route('dashboard.assignments.index')
            ->with('success', 'Assignment deleted successfully!');
    }

    public function submit(Request $request, Assignment $assignment) {
        $request->validate(['remarks' => 'nullable|string']);
        AssignmentSubmission::updateOrCreate(
            ['assignment_id' => $assignment->id, 'student_id' => auth()->user()->student->id ?? 0],
            ['remarks' => $request->remarks, 'submitted_at' => now(), 'status' => 'submitted']
        );
        return redirect()->back()->with('success', 'Assignment submitted!');
    }
}