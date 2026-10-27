<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadScript extends Model
{
    protected $fillable = ['name', 'channel', 'content', 'is_default'];

    protected $casts = ['is_default' => 'boolean'];

    /** Fill {name}, {student_name}, {class}, {school} placeholders for a lead. */
    public function render(Lead $lead): string
    {
        return strtr($this->content, [
            '{name}'         => $lead->name,
            '{student_name}' => $lead->student_name ?: 'your child',
            '{class}'        => $lead->class_interest ?: 'the class you enquired about',
            '{school}'       => setting('school_name', 'our school'),
        ]);
    }
}
