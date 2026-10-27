<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemNotification extends Model {
    protected $table = 'system_notifications';

    protected $fillable = [
        'user_id', 'title', 'message',
        'icon', 'color', 'url', 'is_read', 'type',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    // Helper: create notification for a user
    public static function notify($userId, $title, $message, $url = null, $icon = 'fas fa-bell', $color = '#2563eb', $type = 'general') {
        return self::create([
            'user_id' => $userId,
            'title'   => $title,
            'message' => \Illuminate\Support\Str::limit((string) $message, 250, ''),
            'url'     => $url,
            'icon'    => $icon,
            'color'   => $color,
            'type'    => $type,
            'is_read' => false,
        ]);
    }

    // Helper: notify all admins
    public static function notifyAdmins($title, $message, $url = null, $icon = 'fas fa-bell', $color = '#2563eb') {
        $admins = User::role(['super_admin', 'admin'])->get();
        foreach ($admins as $admin) {
            self::notify($admin->id, $title, $message, $url, $icon, $color);
        }
    }
}