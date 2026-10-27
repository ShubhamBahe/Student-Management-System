<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notice;
use Illuminate\Http\Request;

class NoticeController extends Controller {
    public function index() {
        $notices = Notice::with('publisher')->latest()->paginate(15);
        return view('notices.index', compact('notices'));
    }

    public function create() {
        return view('notices.create');
    }

    public function store(Request $request) {
        $request->validate([
            'title'   => 'required|string|max:255',
            'content' => 'required|string',
            'audience'=> 'required',
        ]);
        Notice::create([
            'title'        => $request->title,
            'content'      => $request->content,
            'audience'     => $request->audience,
            'publish_date' => $request->publish_date ?? now()->toDateString(),
            'status'       => $request->status ?? 'active',
            'published_by' => auth()->id(),
        ]);
        return redirect()->route('dashboard.notices.index')
            ->with('success', 'Notice published successfully!');
    }

    public function show(Notice $notice) {
        return view('notices.show', compact('notice'));
    }

    public function edit(Notice $notice) {
        return view('notices.edit', compact('notice'));
    }

    public function update(Request $request, Notice $notice) {
        $notice->update($request->all());
        return redirect()->route('dashboard.notices.index')
            ->with('success', 'Notice updated successfully!');
    }

    public function destroy(Notice $notice) {
        $notice->delete();
        if(request()->ajax()) {
            return response()->json(['success' => true]);
        }
        return redirect()->route('dashboard.notices.index')
            ->with('success', 'Notice deleted successfully!');
    }
}