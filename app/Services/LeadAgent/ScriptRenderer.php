<?php
namespace App\Services\LeadAgent;

use App\Models\Lead\LeadCallScript;
use App\Models\Lead\LeadGuardian;
use App\Models\Student;

class ScriptRenderer {
    public function __construct(private StudentDataService $data) {}

    public function render(LeadCallScript $script, Student $student, ?LeadGuardian $guardian = null): string {
        $f = $this->data->all($student);
        $a = $f['attendance']; $m = $f['marks']; $fee = $f['fees'];
        $c = $fee['currency'];

        $map = [
            '{parent_name}'        => $guardian->name ?? 'Parent',
            '{student_name}'       => $f['student']['name'],
            '{class}'              => $f['student']['class'],
            '{school_name}'        => setting('school_name', 'the school'),
            '{today}'              => now()->format('d M Y'),
            '{attendance_percent}' => ReplyBuilder::pct($a['overall_percent']),
            '{last30_percent}'     => ReplyBuilder::pct($a['last30_percent']),
            '{absent_streak}'      => (string) $a['absent_streak'],
            '{total_fee}'          => $c . ReplyBuilder::money($fee['total']),
            '{paid_fee}'           => $c . ReplyBuilder::money($fee['paid']),
            '{due_amount}'         => $c . ReplyBuilder::money($fee['due']),
            '{due_date}'           => $fee['due_date'] ?? '-',
            '{weakest_subject}'    => $m['weakest']['subject'] ?? '-',
            '{strongest_subject}'  => $m['strongest']['subject'] ?? '-',
            '{owner_name}'         => LeadSettings::get('owner_name', 'School'),
        ];
        return trim(strtr($script->body, $map));
    }
}
