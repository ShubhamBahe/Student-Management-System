<?php
namespace App\Models\Lead;

use App\Models\Student;
use App\Services\LeadAgent\PhoneNormalizer;
use Illuminate\Database\Eloquent\Model;

class LeadGuardian extends Model {
    protected $fillable = ['student_id', 'name', 'relation', 'phone', 'preferred_language', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];

    public function student() { return $this->belongsTo(Student::class); }

    public function setPhoneAttribute($value): void {
        $this->attributes['phone'] = PhoneNormalizer::normalize($value) ?? preg_replace('/\D+/', '', (string) $value);
    }
}
