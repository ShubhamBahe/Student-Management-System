<?php
namespace App\Http\Controllers\Admin\LeadGeneration;

use App\Http\Controllers\Controller;
use App\Models\Lead\{LeadAuthAttempt, LeadOwnerAlert, LeadQueryLog};
use Illuminate\Http\Request;

class LogController extends Controller {
    public function index(Request $request) {
        $tab = $request->tab === 'alerts' ? 'alerts' : ($request->tab === 'blocked' ? 'blocked' : 'queries');

        $logs = LeadQueryLog::with('student.user')
            ->when($request->status, fn ($q, $v) => $q->where('status', $v))
            ->when($request->channel, fn ($q, $v) => $q->where('channel', $v))
            ->when($request->search, fn ($q, $v) => $q->where(fn ($w) => $w->where('phone', 'like', "%$v%")->orWhere('question', 'like', "%$v%")->orWhere('reply', 'like', "%$v%")))
            ->latest()->paginate(20, ['*'], 'lp')->withQueryString();

        $alerts  = LeadOwnerAlert::latest()->paginate(15, ['*'], 'ap')->withQueryString();
        $blocked = LeadAuthAttempt::where('blocked_until', '>', now())->orderByDesc('blocked_until')->get();
        $newAlerts = LeadOwnerAlert::where('status', 'new')->count();
        return view('lead-generation.logs', compact('tab', 'logs', 'alerts', 'blocked', 'newAlerts'));
    }

    public function handleAlert(LeadOwnerAlert $alert) {
        $alert->update(['status' => $alert->status === 'new' ? 'handled' : 'new']);
        return back()->with('success', 'Alert updated.');
    }

    public function unblock(LeadAuthAttempt $attempt) {
        $attempt->delete();
        return back()->with('success', 'Number unblocked.');
    }

    public function export(Request $request) {
        $name = 'query_log_' . now()->format('Ymd_His') . '.csv';
        return response()->streamDownload(function () {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['Time', 'Phone', 'Student', 'Channel', 'Language', 'Intent', 'Status', 'Question', 'Reply', 'Error']);
            LeadQueryLog::with('student.user')->orderBy('id')->chunk(500, function ($rows) use ($out) {
                foreach ($rows as $r) {
                    fputcsv($out, [$r->created_at, $r->phone, $r->student->user->name ?? '', $r->channel, $r->language, $r->intent, $r->status, $r->question, $r->reply, $r->error]);
                }
            });
            fclose($out);
        }, $name, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
