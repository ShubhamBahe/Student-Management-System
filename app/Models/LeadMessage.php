<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadMessage extends Model
{
    protected $fillable = [
        'lead_id', 'channel', 'direction', 'body', 'status', 'provider_id', 'by_ai', 'meta',
    ];

    protected $casts = [
        'by_ai' => 'boolean',
        'meta'  => 'array',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }
}
