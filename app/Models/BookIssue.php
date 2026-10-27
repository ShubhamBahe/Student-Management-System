<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class BookIssue extends Model {
    protected $fillable = ['book_id', 'user_id', 'issue_date', 'due_date', 'return_date', 'fine_amount', 'status'];

    public function book() { return $this->belongsTo(Book::class); }
    public function user() { return $this->belongsTo(User::class); }
}