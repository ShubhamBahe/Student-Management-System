<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class SchoolClass extends Model {
    protected $table = 'classes';
    protected $fillable = ['academic_year_id', 'name', 'numeric_name', 'teacher_id'];

    public function academicYear() { return $this->belongsTo(AcademicYear::class); }
    public function teacher()      { return $this->belongsTo(Teacher::class); }
    public function sections()     { return $this->hasMany(Section::class, 'class_id'); }
    public function students()     { return $this->hasMany(Student::class, 'class_id'); }
    public function subjects()     { return $this->belongsToMany(Subject::class, 'class_subjects', 'class_id', 'subject_id'); }
    public function timetables()   { return $this->hasMany(Timetable::class, 'class_id'); }
}