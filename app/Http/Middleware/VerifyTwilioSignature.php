<?php
namespace App\Http\Middleware;

use App\Services\LeadAgent\TwilioClient;
use Closure;
use Illuminate\Http\Request;

/** Only genuine Twilio requests may reach the Lead Generation webhooks. */
class VerifyTwilioSignature {
    public function __construct(private TwilioClient $twilio) {}

    public function handle(Request $request, Closure $next) {
        if (!$this->twilio->configured()) abort(503, 'Phone/WhatsApp provider is not configured.');
        if (config('leadagent.twilio.verify_signature', true) && !$this->twilio->validSignature($request)) {
            abort(403, 'Invalid signature.');
        }
        return $next($request);
    }
}
