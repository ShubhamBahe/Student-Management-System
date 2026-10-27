<?php
namespace App\Services\LeadAgent;

use App\Models\Attendance;
use App\Models\Fee;
use App\Models\Lead\LeadGuardian;
use App\Models\Mark;
use App\Models\ParentModel;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

/**
 * All numbers the agent speaks come from here (database), never from the AI.
 */
class StudentDataService {
    /** Active students linked to this (normalised) phone number. */
    public function studentsForPhone(string $phone): Collection {
        $ids = LeadGuardian::where('phone', $phone)->where('is_active', true)->pluck('student_id');

        // Also honour phones saved on the regular parents table, if the parent_student pivot exists
        if (Schema::hasTable('parent_student')) {
            $parents = ParentModel::query()->whereNotNull('father_phone')->orWhereNotNull('mother_phone')->get();
            foreach ($parents as $p) {
                if (PhoneNormalizer::normalize($p->father_phone) === $phone || PhoneNormalizer::normalize($p->mother_phone) === $phone) {
                    $ids = $ids->merge($p->students()->pluck('students.id'));
                }
            }
        }

        return Student::with(['user', 'class', 'section'])
            ->whereIn('id', $ids->unique())
            ->where('status', 'active')
            ->get();
    }

    public function findByIdentifier(string $id): ?Student {
        $id = trim($id);
        return Student::with(['user', 'class', 'section'])
            ->where('admission_number', $id)->orWhere('roll_number', $id)
            ->first();
    }

    public function guardianName(string $phone, int $studentId): ?string {
        return LeadGuardian::where('phone', $phone)->where('student_id', $studentId)->value('name');
    }

    // ------------------------------------------------------------------
    public function profile(Student $s): array {
        return [
            'id'     => $s->id,
            'name'   => $s->user->name ?? ('Student #' . $s->id),
            'class'  => trim(($s->class->name ?? '') . ' ' . ($s->section->name ?? '')),
            'roll'   => $s->roll_number,
        ];
    }

    public function attendance(Student $s): array {
        $rows = Attendance::where('student_id', $s->id)->orderBy('date', 'desc')->orderBy('id', 'desc')->get();
        $score = fn (Collection $c) => $c->sum(fn ($r) => match ($r->status) { 'present', 'late' => 1, 'half_day' => 0.5, default => 0 });
        $pct = fn (Collection $c) => $c->count() ? round($score($c) / $c->count() * 100, 1) : null;

        $since = now()->subDays(30)->startOfDay();
        $last30 = $rows->filter(fn ($r) => Carbon::parse($r->date)->gte($since));

        $streak = 0;
        foreach ($rows as $r) {
            if ($r->status === 'absent') $streak++; else break;
        }

        $absentDates = $rows->where('status', 'absent')->take(5)
            ->map(fn ($r) => Carbon::parse($r->date)->format('d M'))->values()->all();

        return [
            'days_recorded'    => $rows->count(),
            'overall_percent'  => $pct($rows),
            'last30_days'      => $last30->count(),
            'last30_percent'   => $pct($last30),
            'absent_total'     => $rows->where('status', 'absent')->count(),
            'recent_absent'    => $absentDates,
            'absent_streak'    => $streak,
            'last_marked'      => $rows->first() ? Carbon::parse($rows->first()->date)->format('d M Y') : null,
        ];
    }

    public function marks(Student $s): array {
        $marks = Mark::with(['exam', 'subject'])->where('student_id', $s->id)->get();
        $subjects = [];

        foreach ($marks->groupBy('subject_id') as $rows) {
            $rows = $rows->sortBy(fn ($m) => ($m->exam->start_date ?? '0000') . '|' . str_pad((string) $m->id, 10, '0', STR_PAD_LEFT))->values();
            $pcts = $rows->map(fn ($m) => $m->full_marks > 0 ? round($m->marks_obtained / $m->full_marks * 100, 1) : 0)->all();
            $last = end($pcts);
            $prev = count($pcts) >= 2 ? $pcts[count($pcts) - 2] : null;
            $change = $prev === null ? null : round($last - $prev, 1);
            $trend = $change === null ? 'not_enough_data' : ($change >= 2 ? 'improving' : ($change <= -2 ? 'declining' : 'stable'));
            $lastRow = $rows->last();

            $subjects[] = [
                'subject'      => $rows->first()->subject->name ?? 'Subject',
                'tests'        => count($pcts),
                'average'      => round(array_sum($pcts) / count($pcts), 1),
                'last_score'   => $lastRow->marks_obtained . '/' . $lastRow->full_marks,
                'last_percent' => $last,
                'last_exam'    => $lastRow->exam->name ?? null,
                'previous_percent' => $prev,
                'change'       => $change,
                'trend'        => $trend,
            ];
        }

        usort($subjects, fn ($a, $b) => $a['average'] <=> $b['average']);
        $weakest   = $subjects[0] ?? null;
        $strongest = $subjects ? $subjects[count($subjects) - 1] : null;

        return [
            'subjects'  => array_values($subjects),
            'weakest'   => $weakest,
            'strongest' => $strongest,
            'only_one_subject' => count($subjects) === 1,
            'overall_average'  => $subjects ? round(array_sum(array_column($subjects, 'average')) / count($subjects), 1) : null,
        ];
    }

    public function fees(Student $s): array {
        $fees = Fee::where('student_id', $s->id)->get();
        $total = $fees->sum(fn ($f) => (float) $f->amount - (float) $f->discount + (float) $f->fine);
        $paid  = (float) $fees->sum('paid_amount');
        $open  = $fees->filter(fn ($f) => ((float) $f->amount - (float) $f->discount + (float) $f->fine - (float) $f->paid_amount) > 0.009);
        $due   = max(0, round($total - $paid, 2));
        $next  = $open->sortBy('due_date')->first();

        return [
            'total'     => round($total, 2),
            'paid'      => round($paid, 2),
            'due'       => $due,
            'due_date'  => $next ? Carbon::parse($next->due_date)->format('d M Y') : null,
            'overdue'   => $next ? Carbon::parse($next->due_date)->lt(today()) : false,
            'has_fee_records' => $fees->count() > 0,
            'currency'  => self::currencySymbol(),
        ];
    }

    /** Last 5 teacher remarks, newest first (exam remarks + attendance remarks). */
    public function remarks(Student $s): array {
        $items = collect();

        Mark::with(['exam', 'subject'])->where('student_id', $s->id)
            ->whereNotNull('remarks')->where('remarks', '!=', '')->get()
            ->each(fn ($m) => $items->push([
                'when' => $m->updated_at, 'text' => trim($m->remarks),
                'context' => ($m->subject->name ?? 'Subject') . ($m->exam ? ' - ' . $m->exam->name : ''),
            ]));

        Attendance::with('marker')->where('student_id', $s->id)
            ->whereNotNull('remarks')->where('remarks', '!=', '')->get()
            ->each(fn ($a) => $items->push([
                'when' => Carbon::parse($a->date), 'text' => trim($a->remarks),
                'context' => 'Attendance' . ($a->marker ? ' - ' . $a->marker->name : ''),
            ]));

        return $items->sortByDesc('when')->take(5)->values()
            ->map(fn ($i) => ['date' => Carbon::parse($i['when'])->format('d M Y'), 'text' => $i['text'], 'context' => $i['context']])
            ->all();
    }

    public function all(Student $s): array {
        return [
            'student'    => $this->profile($s),
            'attendance' => $this->attendance($s),
            'marks'      => $this->marks($s),
            'fees'       => $this->fees($s),
            'remarks'    => $this->remarks($s),
        ];
    }

    public static function currencySymbol(): string {
        $c = strtoupper((string) setting('currency', 'INR'));
        return ['INR' => 'Rs ', 'PKR' => 'Rs ', 'USD' => '$', 'EUR' => 'EUR '][$c] ?? ($c . ' ');
    }
}
