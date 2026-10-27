<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\SchoolClass;
use App\Models\ParentModel;
use App\Models\Fee;
use App\Models\Book;
use App\Models\Attendance;
use App\Models\Exam;
use Carbon\Carbon;

class DashboardController extends Controller {
    public function index() {
        $today = Carbon::today();
        $stats = [
            'total_students'  => Student::where('status', 'active')->count(),
            'total_teachers'  => Teacher::where('status', 'active')->count(),
            'total_classes'   => SchoolClass::count(),
            'total_parents'   => \App\Models\ParentModel::count(),
            'present_today'   => Attendance::where('date', $today)->where('status', 'present')->count(),
            'absent_today'    => Attendance::where('date', $today)->where('status', 'absent')->count(),
            'fees_collected'  => Fee::where('status', 'paid')->sum('paid_amount'),
            'fees_pending'    => Fee::where('status', 'pending')->sum('amount'),
            'total_books'     => \App\Models\Book::count(),
            'upcoming_exams'  => \App\Models\Exam::where('status', 'upcoming')->count(),
            'overdue_fees'    => Fee::where('status', 'overdue')->count(), // ← ADD THIS
        ];
        // Monthly attendance chart data
        $attendanceData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $attendanceData[] = [
                'month'   => $month->format('M'),
                'present' => Attendance::whereMonth('date', $month->month)
                                       ->whereYear('date', $month->year)
                                       ->where('status', 'present')->count(),
                'absent'  => Attendance::whereMonth('date', $month->month)
                                       ->whereYear('date', $month->year)
                                       ->where('status', 'absent')->count(),
            ];
        }
        // Monthly fees data
        $feesData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $feesData[] = [
                'month'     => $month->format('M'),
                'collected' => Fee::whereMonth('paid_date', $month->month)
                                   ->whereYear('paid_date', $month->year)
                                   ->where('status', 'paid')->sum('paid_amount'),
            ];
        }
        $recentStudents = Student::with('user', 'class', 'section')
                                  ->latest()->take(5)->get();
        $upcomingExams = Exam::where('status', 'upcoming')
                              ->orderBy('start_date')->take(5)->get();
        // Students by class chart data
        $classStats = [
            'labels' => SchoolClass::pluck('name')->toArray(),
            'data'   => SchoolClass::withCount('students')->get()->pluck('students_count')->toArray(),
        ];
        return view('dashboard.index', compact(
            'stats', 'attendanceData', 'feesData', 'recentStudents', 'upcomingExams', 'classStats'
        ));
    }
}