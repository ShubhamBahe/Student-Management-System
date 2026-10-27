<?php
namespace App\Services\LeadGen;

use App\Models\ParentModel;
use App\Models\Student;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

/**
 * Identity check for the AI agent: a phone number is only trusted with private
 * student data if it is registered in the school system (student's own number,
 * or a parent's father/mother number linked to the student).
 */
class StudentLookup
{
    /** @return Collection<int, Student> */
    public static function forPhone(?string $phone): Collection
    {
        $last10 = Phone::last10($phone);
        if (strlen($last10) < 10) {
            return collect();
        }

        $students = Student::with(['user', 'class', 'section'])
            ->where('status', 'active')
            ->whereHas('user', function ($q) use ($last10) {
                $q->whereRaw('SUBSTR(' . Phone::cleanColumn('phone') . ', -10) = ?', [$last10]);
            })->get();

        $parents = ParentModel::where(function ($q) use ($last10) {
            $q->whereRaw('SUBSTR(' . Phone::cleanColumn('father_phone') . ', -10) = ?', [$last10])
              ->orWhereRaw('SUBSTR(' . Phone::cleanColumn('mother_phone') . ', -10) = ?', [$last10]);
        })->get();

        if ($parents->isNotEmpty() && Schema::hasTable('parent_student')) {
            foreach ($parents as $parent) {
                $kids = $parent->students()->with(['user', 'class', 'section'])->where('status', 'active')->get();
                $students = $students->merge($kids);
            }
        }

        return $students->unique('id')->values();
    }
}
