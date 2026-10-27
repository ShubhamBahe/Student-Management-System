<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class News extends Model {
    protected $fillable = ['title', 'slug', 'content', 'image', 'author_id', 'status', 'published_at'];
    public function author() { return $this->belongsTo(User::class, 'author_id'); }
}