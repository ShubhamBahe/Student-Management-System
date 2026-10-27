<?php
namespace App\Models\Lead;

use Illuminate\Database\Eloquent\Model;

class LeadAuthAttempt extends Model {
    protected $fillable = ['phone', 'failed_count', 'last_failed_at', 'blocked_until'];
    protected $casts = ['last_failed_at' => 'datetime', 'blocked_until' => 'datetime'];

    /** Minutes left on an active block, 0 when not blocked. */
    public static function blockedMinutes(string $phone): int {
        $row = static::where('phone', $phone)->first();
        if (!$row || !$row->blocked_until || $row->blocked_until->isPast()) return 0;
        return max(1, (int) ceil(now()->diffInSeconds($row->blocked_until) / 60));
    }

    /** Register a failed attempt. Returns ['count' => n, 'blocked' => bool, 'minutes' => n]. */
    public static function fail(string $phone, int $max, int $blockMinutes): array {
        $row = static::firstOrNew(['phone' => $phone]);
        // an expired block (or a very old streak) starts fresh
        if ($row->blocked_until && $row->blocked_until->isPast()) {
            $row->failed_count = 0;
            $row->blocked_until = null;
        }
        if ($row->last_failed_at && $row->last_failed_at->lt(now()->subMinutes($blockMinutes))) {
            $row->failed_count = 0;
        }
        $row->failed_count = (int) $row->failed_count + 1;
        $row->last_failed_at = now();
        $blocked = $row->failed_count >= $max;
        if ($blocked) $row->blocked_until = now()->addMinutes($blockMinutes);
        $row->save();
        return ['count' => $row->failed_count, 'blocked' => $blocked, 'minutes' => $blocked ? $blockMinutes : 0];
    }

    public static function reset(string $phone): void {
        static::where('phone', $phone)->delete();
    }
}
