<?php
namespace App\Http\Controllers\Admin\LeadGen;

use App\Http\Controllers\Controller;
use App\Jobs\PlaceLeadCall;
use App\Jobs\SendLeadWhatsApp;
use App\Models\Lead;
use App\Models\LeadScript;
use App\Models\Message;
use App\Services\LeadGen\Phone;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use PhpOffice\PhpSpreadsheet\IOFactory;

class LeadController extends Controller
{
    public function index(Request $request)
    {
        $query = Lead::query()->latest('updated_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('source')) {
            $query->where('source', $request->source);
        } else {
            $query->where('source', '!=', 'parent'); // enrolled families are not prospects
        }
        if ($request->boolean('needs_human')) {
            $query->where('needs_human', true);
        }
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($w) use ($q) {
                $w->where('name', 'like', "%{$q}%")
                  ->orWhere('phone', 'like', '%' . preg_replace('/\D+/', '', $q) . '%')
                  ->orWhere('student_name', 'like', "%{$q}%");
            });
        }

        return view('leadgen.leads.index', [
            'leads'    => $query->paginate(20)->withQueryString(),
            'whatsapp' => LeadScript::where('channel', 'whatsapp')->get(),
            'calls'    => LeadScript::where('channel', 'call')->get(),
        ]);
    }

    public function create()
    {
        return view('leadgen.leads.form', ['lead' => new Lead(['status' => 'new', 'consent' => true, 'ai_enabled' => true])]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['phone']  = Phone::normalize($data['phone']);
        if (!$data['phone']) {
            return back()->withInput()->withErrors(['phone' => 'Enter a valid phone number.']);
        }
        if (Lead::where('phone', $data['phone'])->exists()) {
            return back()->withInput()->withErrors(['phone' => 'A lead with this phone number already exists.']);
        }
        $lead = Lead::create($data + ['source' => 'manual']);

        return redirect()->route('dashboard.leadgen.leads.show', $lead)->with('success', 'Lead added.');
    }

    public function show(Lead $lead)
    {
        return view('leadgen.leads.show', [
            'lead'     => $lead,
            'messages' => $lead->messages()->orderBy('id')->get(),
            'whatsapp' => LeadScript::where('channel', 'whatsapp')->get(),
            'calls'    => LeadScript::where('channel', 'call')->get(),
        ]);
    }

    public function edit(Lead $lead)
    {
        return view('leadgen.leads.form', compact('lead'));
    }

    public function update(Request $request, Lead $lead)
    {
        $data  = $this->validated($request);
        $phone = Phone::normalize($data['phone']);
        if (!$phone) {
            return back()->withInput()->withErrors(['phone' => 'Enter a valid phone number.']);
        }
        if (Lead::where('phone', $phone)->where('id', '!=', $lead->id)->exists()) {
            return back()->withInput()->withErrors(['phone' => 'Another lead already uses this phone number.']);
        }
        $data['phone'] = $phone;
        $lead->update($data);

        return redirect()->route('dashboard.leadgen.leads.show', $lead)->with('success', 'Lead updated.');
    }

    public function destroy(Lead $lead)
    {
        $lead->delete();
        return redirect()->route('dashboard.leadgen.leads.index')->with('success', 'Lead deleted.');
    }

    // ---------------------------------------------------------------- actions

    /** Staff sends a manual WhatsApp message (pauses the AI for this lead). */
    public function sendMessage(Request $request, Lead $lead)
    {
        $request->validate(['text' => 'required|string|max:2000']);
        if (!$lead->canBeContacted()) {
            return back()->with('error', 'This lead has opted out or has no consent to be contacted.');
        }

        $lead->update(['ai_enabled' => false, 'needs_human' => false]);
        SendLeadWhatsApp::dispatch($lead->id, $request->text);

        return back()->with('success', 'Message queued. AI replies are paused for this lead — turn them back on whenever you like.');
    }

    public function call(Request $request, Lead $lead)
    {
        if (!$lead->canBeContacted()) {
            return back()->with('error', 'This lead has opted out or has no consent to be contacted.');
        }
        PlaceLeadCall::dispatch($lead->id, $request->integer('script_id') ?: null);

        return back()->with('success', 'AI call queued. The transcript will appear here when the call ends.');
    }

    public function toggleAi(Lead $lead)
    {
        $lead->update(['ai_enabled' => !$lead->ai_enabled, 'needs_human' => false]);
        return back()->with('success', $lead->ai_enabled ? 'AI replies turned on.' : 'AI replies turned off — you are handling this chat.');
    }

    public function resolveHandoff(Lead $lead)
    {
        $lead->update(['needs_human' => false]);
        return back()->with('success', 'Marked as handled.');
    }

    /** Select leads in the list → send WhatsApp / place AI calls. */
    public function bulk(Request $request)
    {
        $request->validate([
            'ids'       => 'required|array|min:1',
            'ids.*'     => 'integer',
            'action'    => ['required', Rule::in(['whatsapp', 'call'])],
            'script_id' => 'nullable|integer',
        ]);

        $leads = Lead::whereIn('id', $request->ids)->get();
        $queued = 0;
        $skipped = 0;

        foreach ($leads as $lead) {
            if (!$lead->canBeContacted()) {
                $skipped++;
                continue;
            }
            // Space messages out so we do not burst the providers
            $delay = now()->addSeconds($queued * ($request->action === 'call' ? 30 : 4));
            if ($request->action === 'whatsapp') {
                SendLeadWhatsApp::dispatch($lead->id, null, $request->integer('script_id') ?: null)->delay($delay);
            } else {
                PlaceLeadCall::dispatch($lead->id, $request->integer('script_id') ?: null)->delay($delay);
            }
            $queued++;
        }

        $msg = "{$queued} " . ($request->action === 'call' ? 'call(s)' : 'message(s)') . ' queued.';
        if ($skipped) {
            $msg .= " {$skipped} skipped (opted out / no consent).";
        }
        return back()->with('success', $msg);
    }

    // ---------------------------------------------------------------- import

    public function importForm()
    {
        return view('leadgen.import', [
            'pending' => Message::where('subject', 'like', 'Admission Application%')->count(),
        ]);
    }

    public function template()
    {
        $csv = "name,phone,email,student_name,class,notes\nRahul Sharma,9876543210,rahul@example.com,Aarav Sharma,Class 5,Found us via Instagram\n";
        return response($csv, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="leads-template.csv"',
        ]);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file'    => 'required|file|mimes:xlsx,xls,csv,txt|max:5120',
            'consent' => 'accepted',
        ], ['consent.accepted' => 'Please confirm these contacts have agreed to be contacted.']);

        $sheet = IOFactory::load($request->file('file')->getRealPath())->getActiveSheet();
        $rows  = $sheet->toArray(null, true, true, false);
        if (count($rows) < 2) {
            return back()->with('error', 'The file has no data rows.');
        }

        // Map header names (case-insensitive, flexible)
        $aliases = [
            'name'           => ['name', 'parent', 'parent_name', 'parent name', 'contact', 'full name'],
            'phone'          => ['phone', 'mobile', 'number', 'phone number', 'mobile number', 'whatsapp', 'contact number'],
            'email'          => ['email', 'e-mail', 'email address'],
            'student_name'   => ['student_name', 'student', 'student name', 'child', 'child name'],
            'class_interest' => ['class', 'grade', 'class_interest', 'class interest', 'admission for'],
            'notes'          => ['notes', 'note', 'remarks', 'comment', 'comments'],
        ];
        $header = array_map(fn ($h) => strtolower(trim((string) $h)), $rows[0]);
        $map = [];
        foreach ($aliases as $field => $names) {
            foreach ($header as $i => $h) {
                if (in_array($h, $names, true)) {
                    $map[$field] = $i;
                    break;
                }
            }
        }
        if (!isset($map['phone'])) {
            return back()->with('error', 'Could not find a "phone" column. Download the template to see the expected headers.');
        }

        $created = $duplicates = $invalid = 0;
        foreach (array_slice($rows, 1) as $row) {
            $get   = fn ($f) => isset($map[$f]) ? trim((string) ($row[$map[$f]] ?? '')) : '';
            // Excel often turns phone numbers into floats like 9.8765E+9
            $raw   = $get('phone');
            if (is_numeric($raw) && stripos($raw, 'e') !== false) {
                $raw = number_format((float) $raw, 0, '', '');
            }
            if ($raw === '') {
                continue;
            }
            $phone = Phone::normalize($raw);
            if (!$phone) {
                $invalid++;
                continue;
            }
            if (Lead::where('phone', $phone)->exists()) {
                $duplicates++;
                continue;
            }
            Lead::create([
                'name'           => $get('name') ?: ('+' . $phone),
                'phone'          => $phone,
                'email'          => $get('email') ?: null,
                'student_name'   => $get('student_name') ?: null,
                'class_interest' => $get('class_interest') ?: null,
                'notes'          => $get('notes') ?: null,
                'source'         => 'excel',
                'consent'        => true,
            ]);
            $created++;
        }

        return redirect()->route('dashboard.leadgen.leads.index')
            ->with('success', "Import finished: {$created} added, {$duplicates} duplicate(s) skipped, {$invalid} invalid number(s) skipped.");
    }

    /** Turn old website admission enquiries (Messages) into leads. */
    public function syncAdmissions()
    {
        $created = 0;
        Message::where('subject', 'like', 'Admission Application%')->each(function (Message $m) use (&$created) {
            $phone = Phone::normalize($m->phone);
            if (!$phone || Lead::where('phone', $phone)->exists()) {
                return;
            }
            preg_match('/Student Name:\s*(.+)/', (string) $m->message, $sn);
            $class = trim(str_replace('Admission Application', '', (string) $m->subject), " —-");
            Lead::create([
                'name'           => $m->name,
                'phone'          => $phone,
                'email'          => $m->email,
                'student_name'   => isset($sn[1]) ? trim($sn[1]) : null,
                'class_interest' => $class ?: null,
                'source'         => 'admission_form',
                'consent'        => true,
                'message_id'     => $m->id,
            ]);
            $created++;
        });

        return back()->with('success', "{$created} admission enquiry(ies) imported as leads.");
    }

    protected function validated(Request $request): array
    {
        $data = $request->validate([
            'name'              => 'required|string|max:255',
            'phone'             => 'required|string|max:25',
            'email'             => 'nullable|email|max:255',
            'student_name'      => 'nullable|string|max:255',
            'class_interest'    => 'nullable|string|max:255',
            'status'            => ['required', Rule::in(array_keys(Lead::STATUSES))],
            'notes'             => 'nullable|string|max:5000',
            'next_follow_up_at' => 'nullable|date',
        ]);
        $data['consent']    = $request->boolean('consent');
        $data['ai_enabled'] = $request->boolean('ai_enabled');
        return $data;
    }
}
