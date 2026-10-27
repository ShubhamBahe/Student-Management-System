<?php
namespace App\Models\Lead;

use Illuminate\Database\Eloquent\Model;

class LeadSession extends Model {
    protected $fillable = ['phone', 'pending', 'language'];
    protected $casts = ['pending' => 'array'];
}
