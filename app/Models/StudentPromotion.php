<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class StudentPromotion extends Model {
    protected $fillable = ['student_id', 'from_class_id', 'to_class_id', 'from_section_id', 'to_section_id', 'academic_year_id', 'promoted_by', 'promoted_at'];
    public function student()   { return $this->belongsTo(Student::class); }
    public function fromClass() { return $this->belongsTo(SchoolClass::class, 'from_class_id'); }
    public function toClass()   { return $this->belongsTo(SchoolClass::class, 'to_class_id'); }
}