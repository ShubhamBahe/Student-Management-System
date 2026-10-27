<?php
namespace App\Models\Lead;

use Illuminate\Database\Eloquent\Model;

class LeadCallScript extends Model {
    protected $fillable = ['name', 'purpose', 'language', 'body', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];

    public const PLACEHOLDERS = [
        '{parent_name}', '{student_name}', '{class}', '{school_name}', '{today}',
        '{attendance_percent}', '{last30_percent}', '{absent_streak}',
        '{total_fee}', '{paid_fee}', '{due_amount}', '{due_date}',
        '{weakest_subject}', '{strongest_subject}', '{owner_name}',
    ];
}
