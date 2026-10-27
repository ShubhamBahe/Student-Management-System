<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class AcademicYear extends Model {
    protected $fillable = ['name', 'start_date', 'end_date', 'is_current'];

    public function classes()   { return $this->hasMany(SchoolClass::class, 'academic_year_id'); }
    public function students()  { return $this->hasMany(Student::class); }
    public function exams()     { return $this->hasMany(Exam::class); }
}