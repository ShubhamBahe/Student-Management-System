<?php
namespace App\Models\Lead;

use Illuminate\Database\Eloquent\Model;

class LeadOwnerAlert extends Model {
    protected $fillable = ['type', 'title', 'details', 'phone', 'sender_name', 'forwarded', 'status'];
    protected $casts = ['forwarded' => 'boolean'];
}
