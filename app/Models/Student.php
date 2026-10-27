<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Student extends Model {
    protected $fillable = [
        'user_id', 'roll_number', 'admission_number', 'admission_date',
        'class_id', 'section_id', 'academic_year_id', 'previous_school',
        'medical_conditions', 'status',
    ];

    public function user()         { return $this->belongsTo(User::class); }
    public function class()        { return $this->belongsTo(SchoolClass::class, 'class_id'); }
    public function section()      { return $this->belongsTo(Section::class); }
    public function academicYear() { return $this->belongsTo(AcademicYear::class); }
    public function attendances()  { return $this->hasMany(Attendance::class); }
    public function marks()        { return $this->hasMany(Mark::class); }
    public function fees()         { return $this->hasMany(Fee::class); }
    public function parents()      { return $this->belongsToMany(ParentModel::class, 'parent_student', 'student_id', 'parent_id'); }
    public function transport()    { return $this->hasOne(StudentTransport::class); }
    public function promotions()   { return $this->hasMany(StudentPromotion::class); }
}