<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Fee extends Model {
    protected $fillable = [
        'student_id', 'fee_type_id', 'amount', 'discount', 'fine',
        'paid_amount', 'status', 'due_date', 'paid_date', 'month', 'receipt_number',
    ];

    public function student() { return $this->belongsTo(Student::class); }
    public function feeType() { return $this->belongsTo(FeeType::class); }
    public function payments(){ return $this->hasMany(FeePayment::class); }
}