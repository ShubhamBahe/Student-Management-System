<?php
namespace App\Services\LeadAgent;

class IntentDetector {
    // ----- messages that must go to the owner (AI never answers these) -----
    private const ESCALATE = [
        'refund' => [
            'refund', 'money back', 'paise wapas', 'paisa wapas', 'paise lautao', 'fees wapas', 'fee wapas', 'fees return',
            'पैसे वापस', 'पैसा वापस', 'रिफंड', 'रिफण्ड', 'फीस वापस', 'पैसे लौटा',
        ],
        'legal' => [
            'legal', 'lawyer', 'advocate', 'court', 'sue', 'lawsuit', 'police', 'fir', 'consumer forum', 'consumer court',
            'legal notice', 'vakil', 'kanooni', 'kanuni', 'case karunga', 'case karungi', 'case kar',
            'वकील', 'कोर्ट', 'कानूनी', 'पुलिस', 'एफआईआर', 'केस कर', 'नोटिस',
        ],
        'complaint' => [
            'complaint', 'complain', 'compalint', 'shikayat', 'shikaayat', 'shikayet', 'harass', 'harassment', 'bullying',
            'bullied', 'misbehave', 'misbehaved', 'abuse', 'unhappy', 'not satisfied', 'dissatisfied', 'worst', 'fed up',
            'शिकायत', 'परेशान', 'बदतमीज', 'नाराज', 'असंतुष्ट',
        ],
    ];

    private const TOPICS = [
        'greeting' => ['hi', 'hello', 'hey', 'namaste', 'namaskar', 'good morning', 'good afternoon', 'good evening',
            'नमस्ते', 'नमस्कार', 'हेलो', 'हैलो'],
        'report' => ['report', 'full report', 'overall', 'summary', 'everything', 'progress', 'how is my child', 'how is my son',
            'how is my daughter', "how's my", 'poori report', 'puri report', 'pura report', 'sab kuch', 'kaisa chal raha', 'kaisi chal rahi',
            'kaisa hai mera', 'kaisi hai meri', 'kaise chal', 'रिपोर्ट', 'पूरी रिपोर्ट', 'सब कुछ', 'कैसा चल', 'कैसी चल', 'प्रगति'],
        'attendance' => ['attendance', 'present', 'absent', 'absence', 'haziri', 'hazri', 'upasthiti', 'gair hazir', 'school aaya',
            'school aaya tha', 'chutti', 'streak', 'उपस्थिति', 'हाजिरी', 'हाज़िरी', 'गैरहाजिर', 'अनुपस्थित', 'छुट्टी'],
        'marks' => ['marks', 'mark', 'score', 'scores', 'result', 'results', 'test', 'tests', 'exam', 'exams', 'improving', 'declining',
            'trend', 'percentage', 'grade', 'pariksha', 'marks kitne', 'अंक', 'मार्क्स', 'रिज़ल्ट', 'रिजल्ट', 'परिणाम', 'परीक्षा', 'टेस्ट'],
        'strength' => ['weak', 'weakest', 'strong', 'strongest', 'best subject', 'worst subject', 'weak subject', 'kamzor', 'kamjor',
            'mazboot', 'majboot', 'sabse achha', 'sabse accha', 'sabse kamzor', 'sabse kam', 'कमजोर', 'कमज़ोर', 'मजबूत', 'सबसे अच्छा', 'सबसे कमजोर'],
        'fees' => ['fee', 'fees', 'due', 'paid', 'payment', 'balance', 'outstanding', 'pending amount', 'baki', 'baaki', 'bakaya',
            'shulk', 'kitna dena', 'फीस', 'फ़ीस', 'शुल्क', 'बाकी', 'बकाया', 'भुगतान'],
        'remarks' => ['remark', 'remarks', 'teacher', 'teachers', 'feedback', 'comment', 'comments', 'tippani', 'teacher ne',
            'टिप्पणी', 'शिक्षक', 'टीचर', 'रिमार्क'],
    ];

    /** Returns the escalation kind (refund|legal|complaint) or null. Order matters: refund > legal > complaint. */
    public static function escalation(?string $text): ?string {
        $t = self::norm($text);
        foreach (self::ESCALATE as $kind => $words) {
            if (self::containsAny($t, $words)) return $kind;
        }
        return null;
    }

    /** Topics asked about, e.g. ['attendance','fees']. Empty array = nothing recognised. */
    public static function topics(?string $text): array {
        $t = self::norm($text);
        $found = [];
        foreach (self::TOPICS as $topic => $words) {
            if (self::containsAny($t, $words)) $found[] = $topic;
        }
        // "hi, what are the marks?" => greeting is noise when something real is asked
        if (count($found) > 1) $found = array_values(array_diff($found, ['greeting']));
        // "full report" already contains everything
        if (in_array('report', $found, true)) return ['report'];
        return $found;
    }

    /** Pull a possible student id (admission no. / roll no.) out of free text. */
    public static function studentIdCandidates(?string $text): array {
        $text = (string) $text;
        $ids = [];
        if (preg_match_all('/\bADM[-\s]?\d{4}[-\s]?\d{3,6}\b/i', $text, $m)) {
            foreach ($m[0] as $x) $ids[] = strtoupper(preg_replace('/\s+/', '-', trim($x)));
        }
        if (preg_match_all('/\b(?:student\s*id|stud\s*id|id|roll\s*(?:no|number)?|admission\s*(?:no|number)?|reg(?:istration)?\s*(?:no|number)?)\s*[:#\-]?\s*([A-Za-z0-9][A-Za-z0-9\-]{2,19})\b/i', $text, $m)) {
            foreach ($m[1] as $x) $ids[] = strtoupper($x);
        }
        if (preg_match_all('/(?<![\d+])\d{4,8}(?!\d)/', $text, $m)) {
            foreach ($m[0] as $x) $ids[] = $x;
        }
        return array_values(array_unique($ids));
    }

    // ------------------------------------------------------------------
    private static function norm(?string $text): string {
        return ' ' . mb_strtolower(trim((string) $text)) . ' ';
    }

    private static function containsAny(string $haystack, array $needles): bool {
        foreach ($needles as $n) {
            if (preg_match('/\p{Devanagari}/u', $n)) {
                if (mb_strpos($haystack, $n) !== false) return true;
            } else {
                if (preg_match('/(?<![a-z])' . preg_quote($n, '/') . '(?![a-z])/u', $haystack)) return true;
            }
        }
        return false;
    }
}
