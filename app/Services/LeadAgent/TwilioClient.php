<?php
namespace App\Services\LeadAgent;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TwilioClient {
    public function configured(): bool {
        return (bool) (config('leadagent.twilio.sid') && config('leadagent.twilio.token'));
    }
    public function canCall(): bool {
        return $this->configured() && (bool) config('leadagent.twilio.voice_from');
    }

    /** Send WhatsApp (preferred) or SMS to a 10-digit Indian number / E.164. */
    public function send(string $to, string $body): bool {
        if (!$this->configured()) return false;
        $to = str_starts_with($to, '+') ? $to : PhoneNormalizer::e164($to);
        $wa = config('leadagent.twilio.whatsapp_from');
        $sms = config('leadagent.twilio.sms_from');
        if (!$wa && !$sms) return false;

        $payload = $wa
            ? ['From' => 'whatsapp:' . $wa, 'To' => 'whatsapp:' . $to, 'Body' => mb_substr($body, 0, 1500)]
            : ['From' => $sms, 'To' => $to, 'Body' => mb_substr($body, 0, 1500)];

        $r = Http::asForm()->withBasicAuth(config('leadagent.twilio.sid'), config('leadagent.twilio.token'))
            ->timeout(15)->post($this->url('Messages.json'), $payload);
        return $r->successful();
    }

    /** Start an outbound call. Returns the call SID or throws. */
    public function call(string $to, string $twimlUrl, string $statusUrl): string {
        $to = str_starts_with($to, '+') ? $to : PhoneNormalizer::e164($to);
        $r = Http::asForm()->withBasicAuth(config('leadagent.twilio.sid'), config('leadagent.twilio.token'))
            ->timeout(20)->post($this->url('Calls.json'), [
                'To' => $to, 'From' => config('leadagent.twilio.voice_from'),
                'Url' => $twimlUrl, 'Method' => 'POST',
                'StatusCallback' => $statusUrl, 'StatusCallbackMethod' => 'POST',
                'StatusCallbackEvent' => ['completed'], 'Timeout' => 30,
            ]);
        if (!$r->successful()) {
            throw new \RuntimeException('Twilio error: ' . ($r->json('message') ?? $r->status()));
        }
        return (string) $r->json('sid');
    }

    /** Validate X-Twilio-Signature (HMAC-SHA1 of URL + sorted POST params). */
    public function validSignature(Request $request): bool {
        $token = config('leadagent.twilio.token');
        $sig = $request->header('X-Twilio-Signature');
        if (!$token || !$sig) return false;
        $params = $request->post();
        ksort($params);
        $suffix = '';
        foreach ($params as $k => $v) $suffix .= $k . (is_array($v) ? implode('', $v) : $v);

        // behind a proxy / ngrok the request may look like http while Twilio signed https
        $full = $request->fullUrl();
        $candidates = array_unique([$full, preg_replace('#^http://#', 'https://', $full), preg_replace('#^https://#', 'http://', $full)]);
        foreach ($candidates as $url) {
            $expected = base64_encode(hash_hmac('sha1', $url . $suffix, $token, true));
            if (hash_equals($expected, $sig)) return true;
        }
        return false;
    }

    // ---- TwiML helpers --------------------------------------------------
    public static function say(string $text, ?string $lang = null): string {
        $lang = $lang ?: config('leadagent.twilio.speech_lang', 'hi-IN');
        $voice = config('leadagent.twilio.voice', 'Polly.Aditi');
        return '<Say voice="' . e($voice) . '" language="' . e($lang) . '">' . e(self::speakable($text)) . '</Say>';
    }

    public static function gather(string $action, string $prompt, ?string $lang = null): string {
        $lang = $lang ?: config('leadagent.twilio.speech_lang', 'hi-IN');
        return '<Gather input="speech" action="' . e($action) . '" method="POST" language="' . e($lang)
            . '" speechTimeout="auto" timeout="6">' . self::say($prompt, $lang) . '</Gather>';
    }

    public static function twiml(string $inner): \Illuminate\Http\Response {
        return response('<?xml version="1.0" encoding="UTF-8"?><Response>' . $inner . '</Response>', 200, ['Content-Type' => 'text/xml']);
    }

    /** Make text friendlier for text-to-speech (numbering, line breaks). */
    public static function speakable(string $text): string {
        $text = preg_replace('/^\s*\d+\.\s+/m', '', $text);
        $text = str_replace(["\r", "\n"], ' ', $text);
        return trim(preg_replace('/\s+/', ' ', $text));
    }

    private function url(string $path): string {
        return 'https://api.twilio.com/2010-04-01/Accounts/' . config('leadagent.twilio.sid') . '/' . $path;
    }
}
