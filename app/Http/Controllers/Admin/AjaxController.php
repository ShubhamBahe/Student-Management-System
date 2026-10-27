<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Section;
use App\Models\Student;
use App\Models\Attendance;
use App\Models\Mark;
use App\Models\Exam;
use App\Models\Subject;
use Illuminate\Http\Request;

class AjaxController extends Controller {
    // Get sections by class
    public function getSections($classId) {
        $sections = Section::where('class_id', $classId)
            ->select('id', 'name')
            ->get();

        return response()->json($sections);
    }

    // Get students for attendance marking
    public function getStudentsForAttendance(Request $request) {
        $students = Student::with('user')
            ->where('class_id', $request->class_id)
            ->where('section_id', $request->section_id)
            ->where('status', 'active')
            ->get();

        // Get existing attendance for this date
        $existing = Attendance::where('date', $request->date)
            ->where('class_id', $request->class_id)
            ->where('section_id', $request->section_id)
            ->pluck('status', 'student_id');

        $data = $students->map(function($student) use ($existing) {
            return [
                'id'                => $student->id,
                'name'              => $student->user->name,
                'roll_number'       => $student->roll_number,
                'photo'             => $student->user->photo_url,
                'attendance_status' => $existing->get($student->id, 'present'),
            ];
        });

        return response()->json(['students' => $data]);
    }

    // Get students for marks entry
    public function getStudentsForMarks(Request $request) {
        $students = Student::with(['user', 'marks' => function($q) use ($request) {
            $q->where('exam_id', $request->exam_id)
              ->where('subject_id', $request->subject_id);
        }])
        ->where('class_id', $request->class_id)
        ->where('status', 'active')
        ->get();

        $exam    = Exam::find($request->exam_id);
        $subject = Subject::find($request->subject_id);

        $data = $students->map(function($student) {
            $mark = $student->marks->first();
            return [
                'id'               => $student->id,
                'name'             => $student->user->name,
                'roll_number'      => $student->roll_number,
                'photo'            => $student->user->photo_url,
                'existing_marks'   => $mark ? $mark->marks_obtained : null,
                'existing_grade'   => $mark ? $mark->grade : null,
                'existing_remarks' => $mark ? $mark->remarks : null,
            ];
        });

        return response()->json([
            'students'   => $data,
            'exam'       => $exam->name ?? '',
            'subject'    => $subject->name ?? '',
            'full_marks' => $subject->full_marks ?? 100,
            'pass_marks' => $subject->pass_marks ?? 33,
        ]);
    }

    // Live search students
    public function searchStudents(Request $request) {
        $query = $request->get('q', '');

        $students = Student::with('user', 'class', 'section')
            ->when($query, function($q) use ($query) {
                $q->whereHas('user', function($uq) use ($query) {
                    $uq->where('name', 'like', "%$query%")
                       ->orWhere('email', 'like', "%$query%");
                })->orWhere('roll_number', 'like', "%$query%")
                  ->orWhere('admission_number', 'like', "%$query%");
            })
            ->latest()
            ->take(50)
            ->get()
            ->map(function($student) {
                return [
                    'id'               => $student->id,
                    'name'             => $student->user->name,
                    'email'            => $student->user->email,
                    'photo'            => $student->user->photo_url,
                    'admission_number' => $student->admission_number,
                    'roll_number'      => $student->roll_number,
                    'class'            => $student->class->name ?? '-',
                    'section'          => $student->section->name ?? '-',
                    'gender'           => ucfirst($student->user->gender ?? '-'),
                    'status'           => $student->status,
                ];
            });

        return response()->json($students);
    }
}