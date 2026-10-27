<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Salary extends Model {
    protected $fillable = ['teacher_id', 'amount', 'month', 'year', 'paid_date', 'status', 'remarks'];
    public function teacher() { return $this->belongsTo(Teacher::class); }
}