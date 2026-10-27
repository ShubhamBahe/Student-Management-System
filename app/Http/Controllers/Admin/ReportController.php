<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Attendance;
use App\Models\Fee;
use App\Models\Exam;
use App\Models\Mark;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller {
    public function students(Request $request) {
        $query = Student::with('user', 'class', 'section');
        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('gender')) {
            $query->whereHas('user', fn($q) => $q->where('gender', $request->gender));
        }
        $students = $query->get();
        $classes  = SchoolClass::all();
        $summary = [
            'total'    => $students->count(),
            'active'   => $students->where('status', 'active')->count(),
            'inactive' => $students->where('status', 'inactive')->count(),
            'male'     => $students->filter(fn($s) => $s->user->gender === 'male')->count(),
            'female'   => $students->filter(fn($s) => $s->user->gender === 'female')->count(),
        ];
        return view('reports.students', compact('students', 'classes', 'summary'));
    }

    public function attendance(Request $request) {
        $month   = $request->get('month', date('m'));
        $year    = $request->get('year', date('Y'));
        $classId = $request->get('class_id');
        $query = Student::with(['user', 'attendances' => function($q) use ($month, $year) {
            $q->whereMonth('date', $month)->whereYear('date', $year);
        }])->where('status', 'active');
        if ($classId) $query->where('class_id', $classId);
        $students = $query->get();
        $classes  = SchoolClass::all();
        return view('reports.attendance', compact('students', 'classes', 'month', 'year'));
    }

    public function fees(Request $request) {
        $query = Fee::with('student.user', 'feeType');
        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('month'))  $query->where('month', $request->month);
        $fees = $query->get();
        $summary = [
            'total_amount'    => $fees->sum('amount'),
            'total_collected' => $fees->sum('paid_amount'),
            'total_pending'   => $fees->where('status', 'pending')->sum('amount'),
            'total_paid'      => $fees->where('status', 'paid')->count(),
            'total_pending_count' => $fees->where('status', 'pending')->count(),
        ];
        return view('reports.fees', compact('fees', 'summary'));
    }

    public function exams(Request $request) {
        $exams = Exam::with('academicYear')->get();
        return view('reports.exams', compact('exams'));
    }

    public function export(Request $request, $type, $format) {
        if ($type === 'students') {
            $data = Student::with('user', 'class', 'section')->get();
            if ($format === 'csv') {
                $headers = ['Content-Type' => 'text/csv', 'Content-Disposition' => 'attachment; filename=students.csv'];
                $callback = function() use ($data) {
                    $file = fopen('php://output', 'w');
                    fputcsv($file, ['#', 'Name', 'Email', 'Admission No', 'Roll No', 'Class', 'Section', 'Gender', 'Status']);
                    foreach ($data as $i => $student) {
                        fputcsv($file, [
                            $i + 1,
                            $student->user->name,
                            $student->user->email,
                            $student->admission_number,
                            $student->roll_number,
                            $student->class->name ?? '-',
                            $student->section->name ?? '-',
                            ucfirst($student->user->gender ?? '-'),
                            ucfirst($student->status),
                        ]);
                    }
                    fclose($file);
                };
                return response()->stream($callback, 200, $headers);
            }
        }
        if ($type === 'fees') {
            $data = Fee::with('student.user', 'feeType')->get();
            if ($format === 'csv') {
                $headers = ['Content-Type' => 'text/csv', 'Content-Disposition' => 'attachment; filename=fees.csv'];
                $callback = function() use ($data) {
                    $file = fopen('php://output', 'w');
                    fputcsv($file, ['#', 'Student', 'Fee Type', 'Month', 'Amount', 'Paid', 'Status', 'Due Date']);
                    foreach ($data as $i => $fee) {
                        fputcsv($file, [
                            $i + 1,
                            $fee->student->user->name ?? '-',
                            $fee->feeType->name ?? '-',
                            $fee->month ?? '-',
                            $fee->amount,
                            $fee->paid_amount,
                            ucfirst($fee->status),
                            $fee->due_date,
                        ]);
                    }
                    fclose($file);
                };
                return response()->stream($callback, 200, $headers);
            }
        }
        return redirect()->back()->with('error', 'Export format not supported.');
    }
}