<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assignment extends Model {
    protected $fillable = [
        'title',
        'description',
        'class_id',
        'subject_id',
        'teacher_id',
        'due_date',
        'total_marks',
        'status',
    ];

    public function class()       { return $this->belongsTo(SchoolClass::class, 'class_id'); }
    public function subject()     { return $this->belongsTo(Subject::class); }
    public function submissions() { return $this->hasMany(AssignmentSubmission::class); }
}