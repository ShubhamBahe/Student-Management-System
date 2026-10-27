<?php
namespace App\Models\Lead;

use App\Models\Student;
use Illuminate\Database\Eloquent\Model;

class LeadQueryLog extends Model {
    protected $fillable = ['phone', 'student_id', 'channel', 'language', 'intent', 'status', 'question', 'reply', 'error'];

    public function student() { return $this->belongsTo(Student::class); }
}
