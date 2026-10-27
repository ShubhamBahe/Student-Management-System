<?php
namespace App\Models\Lead;

use App\Models\Student;
use Illuminate\Database\Eloquent\Model;

class LeadCall extends Model {
    protected $fillable = ['script_id', 'student_id', 'guardian_id', 'phone', 'status', 'provider_sid',
        'duration', 'script_text', 'transcript', 'note', 'created_by'];

    public function script()   { return $this->belongsTo(LeadCallScript::class, 'script_id'); }
    public function student()  { return $this->belongsTo(Student::class); }
    public function guardian() { return $this->belongsTo(LeadGuardian::class, 'guardian_id'); }
}
