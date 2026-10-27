<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mark;
use App\Models\Exam;
use App\Models\Student;
use App\Models\Subject;
use App\Models\SchoolClass;
use Illuminate\Http\Request;

class MarkController extends Controller {
    public function entryForm(Request $request) {
        $exams    = Exam::all();
        $classes  = SchoolClass::all();
        $subjects = Subject::all();
        $students = collect();
        $selectedExam    = null;
        $selectedSubject = null;
        if ($request->filled('exam_id') && $request->filled('class_id') && $request->filled('subject_id')) {
            $selectedExam    = Exam::find($request->exam_id);
            $selectedSubject = Subject::find($request->subject_id);
            $students = Student::with(['user', 'marks' => function($q) use ($request) {
                $q->where('exam_id', $request->exam_id)
                  ->where('subject_id', $request->subject_id);
            }])
            ->where('class_id', $request->class_id)
            ->where('status', 'active')
            ->get();
        }
        return view('exams.marks-entry', compact(
            'exams', 'classes', 'subjects', 'students', 'selectedExam', 'selectedSubject'
        ));
    }

    public function store(Request $request) {
        $request->validate([
            'exam_id'    => 'required|exists:exams,id',
            'subject_id' => 'required|exists:subjects,id',
            'marks'      => 'required|array',
        ]);
        $subject = Subject::find($request->subject_id);
        foreach ($request->marks as $studentId => $marksObtained) {
            if ($marksObtained === null || $marksObtained === '') continue;
            $grade = $this->calculateGrade($marksObtained, $subject->full_marks);
            Mark::updateOrCreate(
                [
                    'student_id' => $studentId,
                    'exam_id'    => $request->exam_id,
                    'subject_id' => $request->subject_id,
                ],
                [
                    'marks_obtained' => $marksObtained,
                    'full_marks'     => $subject->full_marks,
                    'grade'          => $grade,
                    'remarks'        => $request->remarks[$studentId] ?? null,
                ]
            );
        }
        return redirect()->back()->with('success', 'Marks saved successfully!');
    }

    private function calculateGrade($marks, $fullMarks) {
        $percentage = ($marks / $fullMarks) * 100;
        if ($percentage >= 90) return 'A+';
        if ($percentage >= 80) return 'A';
        if ($percentage >= 70) return 'B';
        if ($percentage >= 60) return 'C';
        if ($percentage >= 50) return 'D';
        if ($percentage >= 33) return 'E';
        return 'F';
    }

    public function reportCard(Student $student, Exam $exam) {
        $student->load('user', 'class', 'section');
        $marks = Mark::with('subject')
            ->where('student_id', $student->id)
            ->where('exam_id', $exam->id)
            ->get();
        $totalObtained = $marks->sum('marks_obtained');
        $totalFull     = $marks->sum('full_marks');
        $percentage    = $totalFull > 0 ? round(($totalObtained / $totalFull) * 100, 2) : 0;
        $grade         = $this->calculateGrade($totalObtained, $totalFull ?: 1);
        return view('exams.report-card', compact('student', 'exam', 'marks', 'totalObtained', 'totalFull', 'percentage', 'grade'));
    }
}