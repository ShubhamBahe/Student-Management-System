<?php
namespace App\Services\LeadAgent;

class PhoneNormalizer {
    /** "whatsapp:+91 98765-43210" => "9876543210" (last 10 digits). Null if it does not look like a phone. */
    public static function normalize(?string $raw): ?string {
        $digits = preg_replace('/\D+/', '', (string) $raw);
        if ($digits === '' || strlen($digits) < 7) return null;
        return strlen($digits) > 10 ? substr($digits, -10) : $digits;
    }

    /** E.164 for sending: 9876543210 => +919876543210 */
    public static function e164(string $phone10, ?string $cc = null): string {
        $cc = $cc ?: config('leadagent.default_country_code', '91');
        return '+' . $cc . $phone10;
    }
}
