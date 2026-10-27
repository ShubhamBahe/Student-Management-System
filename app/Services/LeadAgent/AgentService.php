<?php
namespace App\Services\LeadAgent;

use App\Models\Lead\LeadAuthAttempt;
use App\Models\Lead\LeadGuardian;
use App\Models\Lead\LeadQueryLog;
use App\Models\Lead\LeadSession;
use App\Models\Student;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

/**
 * The AI voice agent / chatbot brain. One entry point for every channel
 * (admin console, WhatsApp, inbound call, outbound call).
 *
 * handle([
 *   'phone'      => '+91 98765 43210',   // who is talking
 *   'text'       => 'Mera bachcha kaisa hai?',
 *   'channel'    => 'web|whatsapp|voice|outbound',
 *   'media'      => null|'voice'|'image'|'other',   // attachment sent instead of text
 *   'student_id' => null|int,            // outbound call about one specific child
 * ])
 */
class AgentService {
    public function __construct(
        private StudentDataService $data,
        private OwnerNotifier $owner,
        private LlmClient $llm,
    ) {}

    public function handle(array $in): array {
        $phoneRaw = (string) ($in['phone'] ?? '');
        $phone    = PhoneNormalizer::normalize($phoneRaw) ?? ($phoneRaw !== '' ? $phoneRaw : 'unknown');
        $text     = trim((string) ($in['text'] ?? ''));
        $channel  = $in['channel'] ?? 'web';
        $media    = $in['media'] ?? null;
        $lang     = LanguageDetector::EN;
        $result   = ['reply' => '', 'status' => 'error', 'language' => $lang, 'topics' => [], 'student_ids' => []];

        try {
            $session = LeadSession::firstOrNew(['phone' => $phone]);
            $usual = $session->language
                ?: LeadGuardian::where('phone', $phone)->where('is_active', true)->value('preferred_language')
                ?: LanguageDetector::EN;
            $lang = LanguageDetector::detect($text, $usual);
            $result['language'] = $lang;

            $result = $this->process($phone, $text, $lang, $channel, $media, $in['student_id'] ?? null, $session, $result);
        } catch (\Throwable $e) {
            Log::error('Lead agent failure: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            $result['reply']  = ReplyBuilder::simple('system_issue', $lang);
            $result['status'] = 'error';
            $result['error']  = mb_substr($e->getMessage(), 0, 500);
            $this->owner->alert('system_error', 'AI Voice Agent system issue',
                $e->getMessage() . "\nChannel: " . $channel, $phone);
        }

        $this->log($phone, $channel, $text !== '' ? $text : ($media ? '[' . $media . ' attachment]' : ''), $result);
        return $result;
    }

    // ------------------------------------------------------------------
    private function process(string $phone, string $text, string $lang, string $channel, ?string $media, $studentHint, LeadSession $session, array $r): array {
        $max   = (int) LeadSettings::get('max_failed_attempts', 3);
        $block = (int) LeadSettings::get('block_minutes', 60);

        // 1) blocked number?
        if ($mins = LeadAuthAttempt::blockedMinutes($phone)) {
            return $this->done($r, ReplyBuilder::simple('blocked', $lang, $mins), 'blocked', $lang);
        }

        // 2) voice note / image without text -> ask for text
        if ($media && in_array($media, ['voice', 'image', 'other'], true) && $text === '') {
            return $this->done($r, ReplyBuilder::simple('need_text', $lang), 'needs_text', $lang);
        }

        $students = $this->data->studentsForPhone($phone);

        // 3) complaint / refund / legal -> owner only, AI never answers
        if ($kind = IntentDetector::escalation($text)) {
            $who = $students->isNotEmpty()
                ? 'Parent of ' . $students->map(fn ($s) => ($s->user->name ?? '?') . ' (' . trim(($s->class->name ?? '') . ' ' . ($s->section->name ?? '')) . ')')->implode(', ')
                : 'Unregistered number';
            $this->owner->alert($kind, ucfirst($kind) . ' message from a parent', $who . "\n\nMessage: " . $text, $phone,
                $students->isNotEmpty() ? $this->data->guardianName($phone, $students->first()->id) : null);
            return $this->done($r, ReplyBuilder::simple('escalated', $lang), 'escalated', $lang, [$kind]);
        }

        // 4) is this phone number known?
        if ($students->isEmpty()) {
            return $this->unregistered($phone, $text, $lang, $max, $block, $r);
        }
        LeadAuthAttempt::reset($phone);

        // 5) which child, what topic
        $pending = $this->validPending($session);
        $topics  = IntentDetector::topics($text);
        $chosen  = $this->resolveChildren($text, $students);
        $question = $text;

        if ($studentHint) {
            $chosen = $students->where('id', (int) $studentHint)->values();
        }
        if ($pending && $chosen->isNotEmpty() && !array_diff($topics, ['greeting'])) {
            // the parent just answered "which child?" -> continue the stored question
            $topics   = $pending['topics'];
            $question = ($pending['question'] ?? '') . ' [' . $text . ']';
            $lang     = $pending['language'] ?? $lang;
        }
        $session->pending = null;

        $names = $students->map(fn ($s) => $s->user->name ?? 'your child')->all();

        // greeting / not understood -> friendly menu, no child needed
        if (!$topics || $topics === ['greeting']) {
            $reply = $topics === ['greeting']
                ? ReplyBuilder::greeting($lang, setting('school_name', 'School'), $names)
                : ReplyBuilder::help($lang, $names);
            $this->saveSession($session, $phone, $lang);
            return $this->done($r, $reply, 'ok', $lang, $topics ?: ['help']);
        }

        // two (or more) children and no clear choice -> ask
        if ($chosen->isEmpty()) {
            if ($students->count() === 1) {
                $chosen = $students;
            } else {
                $session->pending = ['question' => $text, 'topics' => $topics, 'language' => $lang, 'at' => now()->timestamp];
                $this->saveSession($session, $phone, $lang);
                $profiles = $students->map(fn ($s) => $this->data->profile($s))->all();
                return $this->done($r, ReplyBuilder::askChild($lang, $profiles), 'clarify', $lang, $topics);
            }
        }

        // 6) answer
        $factsList = $chosen->map(fn ($s) => $this->data->all($s))->all();
        if (LeadSettings::aiActive()) {
            $reply = $this->llm->reply($question, $lang, $factsList, $topics, setting('school_name', 'School'), $channel);
        } else {
            $reply = implode("\n\n----\n\n", array_map(fn ($f) => ReplyBuilder::answer($topics, $f, $lang), $factsList));
            if (count($factsList) === 1) $reply = ReplyBuilder::answer($topics, $factsList[0], $lang);
        }

        $this->saveSession($session, $phone, $lang);
        $r = $this->done($r, $reply, 'ok', $lang, $topics);
        $r['student_ids'] = $chosen->pluck('id')->all();
        return $r;
    }

    // ------------------------------------------------------------------
    private function unregistered(string $phone, string $text, string $lang, int $max, int $block, array $r): array {
        $candidates = IntentDetector::studentIdCandidates($text);
        $reply = ReplyBuilder::simple('not_registered', $lang);

        if ($candidates) {
            $found = null;
            foreach ($candidates as $c) {
                if ($s = $this->data->findByIdentifier($c)) { $found = $s; break; }
            }
            if ($found) {
                $reply = ReplyBuilder::simple('id_mismatch', $lang);
            } else {
                $reply = ReplyBuilder::simple('id_not_found', $lang);
                $this->owner->alert('unknown_student_id', 'Unknown student ID typed',
                    'Someone typed a student ID that does not exist: ' . implode(', ', $candidates) . "\nMessage: " . $text, $phone);
            }
        }

        $fail = LeadAuthAttempt::fail($phone, $max, $block);
        if ($fail['blocked']) {
            $this->owner->alert('blocked_number', 'Number blocked after failed attempts',
                "{$phone} was blocked for {$block} minutes after {$fail['count']} failed attempts.", $phone);
            $reply = ReplyBuilder::simple('blocked_now', $lang, $block);
            return $this->done($r, $reply, 'blocked', $lang);
        }
        return $this->done($r, $reply, 'unregistered', $lang);
    }

    /** Which child(ren) does the text point to? Empty collection = unclear. */
    private function resolveChildren(string $text, Collection $students): Collection {
        if ($students->count() === 1) return $students;
        $t = ' ' . mb_strtolower($text) . ' ';

        if (preg_match('/\b(both|all|dono|donon|sabhi|sab bachche)\b|दोनों|सभी|सब बच्चे/u', $t)) return $students->values();

        if (preg_match('/^\s*(\d{1,2})\s*[.)]?\s*$/', $text, $m)) {
            $i = (int) $m[1] - 1;
            return isset($students->values()[$i]) ? collect([$students->values()[$i]]) : collect();
        }
        $ordinals = [
            0 => '/\b(first|1st|pehla|pehli|pehle)\b|पहला|पहली|पहले/u',
            1 => '/\b(second|2nd|dusra|dusri|doosra|doosri)\b|दूसरा|दूसरी/u',
            2 => '/\b(third|3rd|teesra|teesri)\b|तीसरा|तीसरी/u',
        ];
        foreach ($ordinals as $i => $re) {
            if (preg_match($re, $t) && isset($students->values()[$i])) return collect([$students->values()[$i]]);
        }

        $hits = $students->filter(function ($s) use ($t) {
            $name = mb_strtolower($s->user->name ?? '');
            if ($name === '') return false;
            if (mb_strpos($t, $name) !== false) return true;
            foreach (preg_split('/\s+/u', $name) as $part) {
                if (mb_strlen($part) >= 3 && preg_match('/(?<![\p{L}\p{M}])' . preg_quote($part, '/') . '(?![\p{L}\p{M}])/u', $t)) return true;
            }
            return false;
        })->values();

        foreach ($students as $s) {
            foreach (array_filter([$s->roll_number, $s->admission_number]) as $id) {
                if (mb_stripos($t, (string) $id) !== false) $hits->push($s);
            }
        }
        return $hits->unique('id')->values();
    }

    private function validPending(LeadSession $session): ?array {
        $p = $session->pending;
        if (!$p || empty($p['topics'])) return null;
        return (now()->timestamp - ($p['at'] ?? 0)) <= 900 ? $p : null;   // 15 minutes
    }

    private function saveSession(LeadSession $session, string $phone, string $lang): void {
        $session->phone = $phone;
        $session->language = $lang;
        $session->save();
    }

    private function done(array $r, string $reply, string $status, string $lang, array $topics = []): array {
        return array_merge($r, ['reply' => $reply, 'status' => $status, 'language' => $lang, 'topics' => $topics]);
    }

    /** Query_Log: never allowed to break the reply. */
    private function log(string $phone, string $channel, string $question, array $r): void {
        try {
            LeadQueryLog::create([
                'phone' => $phone, 'channel' => $channel, 'language' => $r['language'] ?? null,
                'intent' => implode(',', $r['topics'] ?? []) ?: null,
                'student_id' => ($r['student_ids'][0] ?? null),
                'status' => $r['status'] ?? 'error', 'question' => $question,
                'reply' => $r['reply'] ?? null, 'error' => $r['error'] ?? null,
            ]);
        } catch (\Throwable $e) {
            Log::error('Lead Query_Log write failed: ' . $e->getMessage());
        }
    }
}
