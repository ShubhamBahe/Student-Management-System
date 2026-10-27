<?php
namespace App\Services\LeadGen;

class Phone
{
    /** Digits-only E.164 style number (no "+"), or null if it does not look like a phone number. */
    public static function normalize(?string $raw): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $raw);
        if ($digits === '' || $digits === null) {
            return null;
        }
        if (str_starts_with($digits, '00')) {
            $digits = substr($digits, 2);
        }
        if (strlen($digits) === 11 && $digits[0] === '0') {
            $digits = substr($digits, 1);
        }
        if (strlen($digits) === 10) {
            $digits = config('leadgen.default_country_code', '91') . $digits;
        }
        if (strlen($digits) < 11 || strlen($digits) > 15) {
            return null;
        }
        return $digits;
    }

    public static function last10(?string $raw): string
    {
        $digits = preg_replace('/\D+/', '', (string) $raw);
        return substr($digits, -10);
    }

    /** SQL fragment that strips spaces, dashes, plus and brackets from a column. */
    public static function cleanColumn(string $column): string
    {
        return "REPLACE(REPLACE(REPLACE(REPLACE(REPLACE({$column},' ',''),'-',''),'+',''),'(',''),')','')";
    }
}
