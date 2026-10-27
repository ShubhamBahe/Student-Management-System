<?php
namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Lead\LeadCall;
use App\Services\LeadAgent\{AgentService, LanguageDetector, ReplyBuilder, TwilioClient};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

/** Twilio webhooks: WhatsApp messages, inbound calls and outbound (scripted) calls. */
class LeadWebhookController extends Controller {
    public function __construct(private AgentService $agent) {}

    // ---------------- WhatsApp ----------------
    public function whatsapp(Request $request) {
        $media = null;
        if ((int) $request->input('NumMedia', 0) > 0) {
            $type = strtolower((string) $request->input('MediaContentType0', ''));
            $media = str_starts_with($type, 'audio') ? 'voice' : (str_starts_with($type, 'image') ? 'image' : 'other');
        }
        $res = $this->agent->handle([
            'phone' => $request->input('From'), 'text' => (string) $request->input('Body', ''),
            'channel' => 'whatsapp', 'media' => $media,
        ]);
        return TwilioClient::twiml('<Message>' . e($res['reply']) . '</Message>');
    }

    // ---------------- Inbound phone call ----------------
    public function voiceIncoming(Request $request) {
        $school = setting('school_name', 'School');
        $hello = "नमस्ते, {$school} पैरेंट हेल्पलाइन में आपका स्वागत है। कृपया अपना सवाल पूछिए।";
        return TwilioClient::twiml(TwilioClient::gather(route('lead.webhook.voice-respond'), $hello)
            . TwilioClient::say('कोई आवाज़ नहीं मिली। धन्यवाद, नमस्ते।') . '<Hangup/>');
    }

    public function voiceRespond(Request $request) {
        $speech = trim((string) $request->input('SpeechResult', ''));
        $action = route('lead.webhook.voice-respond');
        if ($speech === '') {
            return TwilioClient::twiml(TwilioClient::gather($action, 'मैं सुन नहीं पाया। कृपया दोबारा बोलिए।')
                . TwilioClient::say('धन्यवाद, नमस्ते।') . '<Hangup/>');
        }
        $res = $this->agent->handle(['phone' => $request->input('From'), 'text' => $speech, 'channel' => 'voice']);
        $sayLang = ($res['language'] ?? 'hi') === LanguageDetector::EN ? 'en-IN' : 'hi-IN';
        return TwilioClient::twiml(
            TwilioClient::say($res['reply'], $sayLang)
            . TwilioClient::gather($action, ($sayLang === 'en-IN' ? 'Anything else you would like to know?' : 'क्या आप कुछ और जानना चाहेंगे?'), $sayLang)
            . TwilioClient::say($sayLang === 'en-IN' ? 'Thank you. Goodbye.' : 'धन्यवाद, नमस्ते।') . '<Hangup/>'
        );
    }

    // ---------------- Outbound scripted call ----------------
    public function outbound(Request $request, LeadCall $call) {
        $call->update(['status' => 'calling']);
        $lang = $call->script && $call->script->language === 'en' ? 'en-IN' : 'hi-IN';
        $parts = preg_split('/\n\s*\n/', (string) $call->script_text) ?: [(string) $call->script_text];

        $xml = '';
        foreach ($parts as $p) {
            if (trim($p) !== '') $xml .= TwilioClient::say($p, $lang) . '<Pause length="1"/>';
        }
        $this->appendTranscript($call, 'AGENT', (string) $call->script_text);

        $prompt = $lang === 'en-IN' ? 'Do you have any question? You can ask me now.' : 'क्या आपका कोई सवाल है? आप अभी पूछ सकते हैं।';
        $xml .= TwilioClient::gather($this->respondUrl($call, 1), $prompt, $lang)
            . TwilioClient::say($lang === 'en-IN' ? 'Thank you. Goodbye.' : 'धन्यवाद, नमस्ते।') . '<Hangup/>';
        return TwilioClient::twiml($xml);
    }

    public function outboundRespond(Request $request, LeadCall $call) {
        $lang = $call->script && $call->script->language === 'en' ? 'en-IN' : 'hi-IN';
        $bye = TwilioClient::say($lang === 'en-IN' ? 'Thank you. Goodbye.' : 'धन्यवाद, नमस्ते।') . '<Hangup/>';
        $turn = (int) $request->query('n', 1);
        $speech = trim((string) $request->input('SpeechResult', ''));

        if ($speech === '' || $turn > 4 || $this->isClosing($speech)) {
            if ($speech !== '') $this->appendTranscript($call, 'PARENT', $speech);
            return TwilioClient::twiml($bye);
        }
        $this->appendTranscript($call, 'PARENT', $speech);

        $res = $this->agent->handle(['phone' => $call->phone, 'text' => $speech, 'channel' => 'outbound', 'student_id' => $call->student_id]);
        $this->appendTranscript($call, 'AGENT', $res['reply']);

        $sayLang = ($res['language'] ?? 'hi') === LanguageDetector::EN ? 'en-IN' : 'hi-IN';
        return TwilioClient::twiml(
            TwilioClient::say($res['reply'], $sayLang)
            . TwilioClient::gather($this->respondUrl($call, $turn + 1), $sayLang === 'en-IN' ? 'Anything else?' : 'कुछ और पूछना है?', $sayLang)
            . $bye
        );
    }

    public function voiceStatus(Request $request, LeadCall $call) {
        $status = match ($request->input('CallStatus')) {
            'completed' => 'completed', 'busy', 'no-answer' => 'no_answer', 'failed', 'canceled' => 'failed', default => $call->status,
        };
        $call->update(['status' => $status, 'duration' => (int) $request->input('CallDuration', 0) ?: $call->duration]);
        return response('', 204);
    }

    // ----------------------------------------------------------
    private function respondUrl(LeadCall $call, int $n): string {
        URL::forceRootUrl(rtrim(config('app.url'), '/'));
        try {
            return URL::signedRoute('lead.webhook.outbound-respond', ['call' => $call->id, 'n' => $n]);
        } finally {
            URL::forceRootUrl(null);
        }
    }

    private function appendTranscript(LeadCall $call, string $who, string $text): void {
        $call->update(['transcript' => trim(($call->transcript ? $call->transcript . "\n\n" : '') . $who . ': ' . $text)]);
    }

    private function isClosing(string $s): bool {
        $t = mb_strtolower(trim($s));
        if (str_word_count($t) > 5 && !preg_match('/\p{Devanagari}/u', $t)) return false;
        return (bool) preg_match('/^(no|nope|nahi|nahin|nahi ji|bas|thanks|thank you|thank you very much|dhanyavaad|shukriya|that is all|nothing|ok bye|bye)\b|^(नहीं|बस|धन्यवाद|शुक्रिया|कुछ नहीं)/u', $t);
    }
}
