<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Notice extends Model {
    protected $fillable = ['title', 'content', 'audience', 'published_by', 'publish_date', 'status'];
    public function publisher() { return $this->belongsTo(User::class, 'published_by'); }
}