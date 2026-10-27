<?php
namespace App\Services\LeadAgent;

class LanguageDetector {
    public const HI = 'hi';             // Hindi, Devanagari script
    public const HINGLISH = 'hinglish'; // Hindi written in Roman letters
    public const EN = 'en';

    private const HINGLISH_WORDS = [
        'hai','hain','hoon','tha','thi','mera','meri','mere','mera','hamara','hamari','kya','kitna','kitni','kitne',
        'kab','kaise','kaisa','kaisi','kyun','kyon','bacha','bachcha','bachche','beta','beti','ka','ki','ke','ko',
        'nahi','nahin','batao','bataiye','batao','btao','bolo','dikhao','dijiye','kijiye','chahiye','padhai','haan','han',
        'ji','aap','aapka','aapki','mujhe','hum','humein','abhi','pichle','pichla','kamzor','kamjor','mazboot','sabse',
        'baki','baaki','paise','paisa','wapas','shikayat','haziri','chutti','school mein','mein','se','par','aur','bhi',
        'wala','wali','achha','accha','theek','thik','dono','pehla','dusra','doosra','pehle','kal','aaj','kuch','sab',
    ];

    /** Returns hi | hinglish | en */
    public static function detect(?string $text, string $fallback = self::EN): string {
        $text = trim((string) $text);
        if ($text === '') return $fallback;

        if (preg_match('/\p{Devanagari}/u', $text)) return self::HI;

        $words = preg_split('/[^a-z]+/', mb_strtolower($text), -1, PREG_SPLIT_NO_EMPTY);
        if (!$words) return $fallback;

        $hits = 0;
        foreach ($words as $w) {
            if (in_array($w, self::HINGLISH_WORDS, true)) $hits++;
        }
        // 1 strong marker in a short message, or ~15% of the words, is enough
        if ($hits >= 2 || ($hits >= 1 && count($words) <= 4) || ($hits / max(1, count($words))) >= 0.15) {
            return self::HINGLISH;
        }
        // one or two bare English words ("fees", "attendance") are ambiguous: keep the parent's usual language
        return count($words) <= 3 ? $fallback : self::EN;
    }

    public static function label(string $lang): string {
        return ['hi' => 'Hindi', 'hinglish' => 'Hinglish', 'en' => 'English'][$lang] ?? 'English';
    }
}
