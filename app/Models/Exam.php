<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model {
    protected $fillable = ['name', 'academic_year_id', 'start_date', 'end_date', 'status', 'description'];

    public function academicYear() { return $this->belongsTo(AcademicYear::class); }
    public function schedules()    { return $this->hasMany(ExamSchedule::class); }
    public function marks()        { return $this->hasMany(Mark::class); }
}