<?php
namespace App\Http\Controllers\Admin\LeadGen;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\LeadMessage;
use App\Models\LeadScript;
use App\Models\Setting;
use App\Services\LeadGen\AiAgent;
use App\Services\LeadGen\StudentLookup;
use App\Services\LeadGen\VoiceService;
use App\Services\LeadGen\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LeadGenController extends Controller
{
    public function overview()
    {
        $prospects = Lead::where('source', '!=', 'parent');

        $byStatus = (clone $prospects)->selectRaw('status, COUNT(*) as c')->groupBy('status')->pluck('c', 'status');
        $total    = (int) $byStatus->sum();
        $enrolled = (int) ($byStatus['enrolled'] ?? 0);

        return view('leadgen.overview', [
            'total'       => $total,
            'byStatus'    => $byStatus,
            'conversion'  => $total > 0 ? round($enrolled / $total * 100, 1) : 0,
            'needsHuman'  => Lead::where('needs_human', true)->orderByDesc('updated_at')->limit(8)->get(),
            'newThisWeek' => (clone $prospects)->where('created_at', '>=', now()->subDays(7))->count(),
            'waToday'     => LeadMessage::where('channel', 'whatsapp')->where('created_at', '>=', today())->count(),
            'callsToday'  => LeadMessage::where('channel', 'call')->where('created_at', '>=', today())->count(),
            'recent'      => LeadMessage::with('lead')->latest('id')->limit(10)->get(),
            'bySource'    => (clone $prospects)->selectRaw('source, COUNT(*) as c')->groupBy('source')->pluck('c', 'source'),
        ]);
    }

    // ---------------------------------------------------------------- scripts

    public function scripts()
    {
        return view('leadgen.scripts', ['scripts' => LeadScript::orderBy('channel')->orderByDesc('is_default')->get()]);
    }

    public function storeScript(Request $request)
    {
        $data = $this->scriptData($request);
        $script = LeadScript::create($data);
        $this->singleDefault($script);
        return back()->with('success', 'Script saved.');
    }

    public function updateScript(Request $request, LeadScript $script)
    {
        $script->update($this->scriptData($request));
        $this->singleDefault($script);
        return back()->with('success', 'Script updated.');
    }

    public function destroyScript(LeadScript $script)
    {
        $script->delete();
        return back()->with('success', 'Script deleted.');
    }

    protected function scriptData(Request $request): array
    {
        $data = $request->validate([
            'name'    => 'required|string|max:255',
            'channel' => ['required', Rule::in(['whatsapp', 'call'])],
            'content' => 'required|string|max:5000',
        ]);
        $data['is_default'] = $request->boolean('is_default');
        return $data;
    }

    protected function singleDefault(LeadScript $script): void
    {
        if ($script->is_default) {
            LeadScript::where('channel', $script->channel)->where('id', '!=', $script->id)->update(['is_default' => false]);
        }
    }

    // ---------------------------------------------------------------- settings

    public function settings()
    {
        return view('leadgen.settings', [
            'settings' => Setting::where('key', 'like', 'leadgen_%')->pluck('value', 'key'),
            'checks'   => [
                'AI (Claude API key)' => app(AiAgent::class)->configured(),
                'WhatsApp Cloud API'  => (bool) app(WhatsAppService::class)->configured(),
                'WhatsApp webhook secret' => (bool) config('leadgen.whatsapp.app_secret') && (bool) config('leadgen.whatsapp.verify_token'),
                'WhatsApp first-contact template' => (bool) config('leadgen.whatsapp.template_name'),
                'Voice calling (Vapi)' => app(VoiceService::class)->configured(),
                'Voice webhook secret' => (bool) config('leadgen.voice.webhook_secret'),
            ],
        ]);
    }

    public function saveSettings(Request $request)
    {
        $request->validate([
            'leadgen_agent_name'         => 'required|string|max:60',
            'leadgen_knowledge'          => 'nullable|string|max:12000',
            'leadgen_instructions'       => 'nullable|string|max:4000',
            'leadgen_call_first_message' => 'nullable|string|max:500',
        ]);

        $values = [
            'leadgen_agent_name'         => $request->leadgen_agent_name,
            'leadgen_knowledge'          => $request->leadgen_knowledge,
            'leadgen_instructions'       => $request->leadgen_instructions,
            'leadgen_call_first_message' => $request->leadgen_call_first_message,
            'leadgen_enabled'            => $request->boolean('leadgen_enabled') ? '1' : '0',
            'leadgen_auto_welcome'       => $request->boolean('leadgen_auto_welcome') ? '1' : '0',
            'leadgen_auto_followup'      => $request->boolean('leadgen_auto_followup') ? '1' : '0',
        ];
        foreach ($values as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        return back()->with('success', 'AI settings saved.');
    }

    // ---------------------------------------------------------------- sandbox

    /** Test the AI from the browser without using WhatsApp. */
    public function sandbox()
    {
        return view('leadgen.sandbox', ['configured' => app(AiAgent::class)->configured()]);
    }

    public function sandboxSend(Request $request, AiAgent $ai)
    {
        $request->validate([
            'messages'           => 'required|array|min:1|max:40',
            'messages.*.role'    => ['required', Rule::in(['user', 'assistant'])],
            'messages.*.content' => 'required|string|max:2000',
            'phone'              => 'nullable|string|max:25',
        ]);

        if (!$ai->configured()) {
            return response()->json(['error' => 'ANTHROPIC_API_KEY is not set in .env'], 422);
        }

        // Unsaved lead: tools that write to the database do nothing in test mode.
        $lead = new Lead([
            'name'   => 'Test Contact',
            'phone'  => preg_replace('/\D+/', '', (string) $request->phone) ?: '0000000000',
            'source' => 'sandbox',
        ]);

        $reply = $ai->reply($lead, $request->messages);
        if ($reply === null) {
            return response()->json(['error' => 'The AI did not return an answer. Check storage/logs/laravel.log.'], 502);
        }

        $verified = $request->filled('phone')
            ? StudentLookup::forPhone($request->phone)->map(fn ($s) => ($s->user->name ?? 'Student') . ' (' . ($s->class->name ?? '?') . ')')->values()
            : collect();

        return response()->json(['reply' => $reply, 'verified' => $verified]);
    }
}
