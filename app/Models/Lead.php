<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    protected $fillable = [
        'name', 'phone', 'email', 'student_name', 'class_interest', 'source', 'status',
        'notes', 'consent', 'opted_out', 'ai_enabled', 'needs_human',
        'last_contacted_at', 'next_follow_up_at', 'message_id',
    ];

    protected $casts = [
        'consent'           => 'boolean',
        'opted_out'         => 'boolean',
        'ai_enabled'        => 'boolean',
        'needs_human'       => 'boolean',
        'last_contacted_at' => 'datetime',
        'next_follow_up_at' => 'datetime',
    ];

    public const STATUSES = [
        'new'            => ['New',            '#dbeafe', '#1d4ed8'],
        'contacted'      => ['Contacted',      '#e0e7ff', '#4338ca'],
        'interested'     => ['Interested',     '#dcfce7', '#166534'],
        'follow_up'      => ['Follow-up',      '#fef9c3', '#854d0e'],
        'enrolled'       => ['Enrolled',       '#bbf7d0', '#14532d'],
        'not_interested' => ['Not interested', '#fee2e2', '#991b1b'],
    ];

    public function messages()
    {
        return $this->hasMany(LeadMessage::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status][0] ?? ucfirst($this->status);
    }

    public function canBeContacted(): bool
    {
        return $this->consent && !$this->opted_out;
    }
}
