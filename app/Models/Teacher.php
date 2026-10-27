<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model {
    protected $fillable = [
        'user_id', 'employee_id', 'joining_date',
        'qualification', 'specialization', 'salary', 'status',
    ];

    public function user()      { return $this->belongsTo(User::class); }
    public function classes()   { return $this->hasMany(SchoolClass::class); }
    public function subjects()  { return $this->belongsToMany(Subject::class, 'class_subjects'); }
    public function salaries()  { return $this->hasMany(Salary::class); }
    public function leaves()    { return $this->hasMany(LeaveRequest::class); }
    public function attendances() { return $this->hasMany(TeacherAttendance::class); }
}