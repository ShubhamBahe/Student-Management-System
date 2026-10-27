<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class FeeType extends Model {
    protected $fillable = ['name', 'amount', 'frequency', 'description'];
    public function fees() { return $this->hasMany(Fee::class); }
}