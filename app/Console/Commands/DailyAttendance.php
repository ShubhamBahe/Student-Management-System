<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Student;
use App\Models\Attendance;
use Carbon\Carbon;

class DailyAttendance extends Command {
    protected $signature   = 'attendance:daily';
    protected $description = 'Auto generate demo attendance for today';

    public function handle() {
        $date     = today()->toDateString();
        $students = Student::where('status', 'active')->take(80)->get();
        $count    = 0;
        foreach ($students as $student) {
            $rand   = rand(1, 10);
            $status = $rand <= 7 ? 'present' : ($rand <= 9 ? 'absent' : 'late');
            $created = Attendance::firstOrCreate(
                ['student_id' => $student->id, 'date' => $date],
                [
                    'class_id'   => $student->class_id,
                    'section_id' => $student->section_id,
                    'status'     => $status,
                    'marked_by'  => 2,
                ]
            );
            if ($created->wasRecentlyCreated) $count++;
        }
        $this->info("Done! Date: {$date} | Added: {$count} records");
    }
}