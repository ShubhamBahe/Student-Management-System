<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ParentModel extends Model {
    protected $table = 'parents';
    protected $fillable = [
        'user_id', 'father_name', 'mother_name',
        'father_phone', 'mother_phone',
        'father_occupation', 'mother_occupation', 'cnic',
    ];

    public function user()     { return $this->belongsTo(User::class); }
    public function students() { return $this->belongsToMany(Student::class, 'parent_student', 'parent_id', 'student_id'); }
}