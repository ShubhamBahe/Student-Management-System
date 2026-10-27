<?php
namespace App\Services\LeadAgent;

use App\Models\Setting;

/** Settings stored in the `settings` table (prefix lead_) with config/leadagent.php as fallback. */
class LeadSettings {
    public const KEYS = ['owner_name', 'owner_phone', 'max_failed_attempts', 'block_minutes', 'ai_enabled', 'call_start', 'call_end', 'call_language'];

    public static function get(string $key, $default = null) {
        $v = Setting::where('key', 'lead_' . $key)->value('value');
        if ($v !== null && $v !== '') return $v;
        return match ($key) {
            'owner_name'          => config('leadagent.owner_name'),
            'owner_phone'         => config('leadagent.owner_phone'),
            'max_failed_attempts' => config('leadagent.max_failed_attempts'),
            'block_minutes'       => config('leadagent.block_minutes'),
            'ai_enabled'          => config('leadagent.ai.enabled') ? '1' : '0',
            'call_start'          => config('leadagent.call_window.start'),
            'call_end'            => config('leadagent.call_window.end'),
            default               => $default,
        } ?? $default;
    }

    public static function save(array $values): void {
        foreach (self::KEYS as $k) {
            if (array_key_exists($k, $values)) {
                Setting::updateOrCreate(['key' => 'lead_' . $k], ['value' => (string) $values[$k]]);
            }
        }
    }

    public static function aiActive(): bool {
        return (bool) config('leadagent.ai.key') && self::get('ai_enabled', '1') === '1';
    }
}
