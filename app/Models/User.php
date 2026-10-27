<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable {
    use Notifiable, HasRoles;

    protected $fillable = [
        'name', 'email', 'password', 'phone', 'photo',
        'status', 'address', 'date_of_birth', 'gender',
        'blood_group', 'religion', 'nationality',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function student() { return $this->hasOne(Student::class); }
    public function teacher() { return $this->hasOne(Teacher::class); }
    public function parent()  { return $this->hasOne(ParentModel::class); }

    public function getPhotoUrlAttribute() {
        // If photo exists and file is on disk
        if ($this->photo && 
            $this->photo !== 'default.png' && 
            file_exists(storage_path('app/public/photos/' . $this->photo))) {
            return asset('storage/photos/' . $this->photo);
        }
        // Generate avatar with initials
        $name  = urlencode($this->name ?? 'User');
        $color = $this->gender === 'female' ? 'db2777' : '2563eb';
        return "https://ui-avatars.com/api/?name={$name}&background={$color}&color=fff&size=100&bold=true&font-size=0.4";
    }
}

?>