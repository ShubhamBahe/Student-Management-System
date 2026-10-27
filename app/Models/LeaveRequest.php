<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class LeaveRequest extends Model {
    protected $fillable = ['teacher_id', 'from_date', 'to_date', 'reason', 'status', 'approved_by'];
    public function teacher()  { return $this->belongsTo(Teacher::class); }
    public function approver() { return $this->belongsTo(User::class, 'approved_by'); }
}