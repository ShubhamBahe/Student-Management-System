<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model {
    protected $fillable = ['name', 'code', 'description', 'full_marks', 'pass_marks'];

    public function classes()  { return $this->belongsToMany(SchoolClass::class, 'class_subjects', 'subject_id', 'class_id'); }
    public function marks()    { return $this->hasMany(Mark::class); }
}