<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Book extends Model {
    protected $fillable = [
        'category_id', 'title', 'author', 'isbn', 'publisher',
        'publish_year', 'total_copies', 'available_copies', 'price', 'rack_number', 'description',
    ];

    public function category() { return $this->belongsTo(BookCategory::class); }
    public function issues()   { return $this->hasMany(BookIssue::class); }
}