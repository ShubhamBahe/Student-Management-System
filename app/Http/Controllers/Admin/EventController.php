<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller {
    public function index() {
        $events = Event::latest()->paginate(15);
        return view('events.index', compact('events'));
    }

    public function create() {
        return view('events.create');
    }

    public function store(Request $request) {
        $request->validate([
            'title'      => 'required|string|max:255',
            'start_date' => 'required|date',
        ]);
        Event::create($request->all());
        return redirect()->route('dashboard.events.index')
            ->with('success', 'Event created successfully!');
    }

    public function show(Event $event) {
        return view('events.show', compact('event'));
    }

    public function edit(Event $event) {
        return view('events.edit', compact('event'));
    }

    public function update(Request $request, Event $event) {
        $event->update($request->all());
        return redirect()->route('dashboard.events.index')
            ->with('success', 'Event updated successfully!');
    }

    public function destroy(Event $event) {
        $event->delete();
        if(request()->ajax()) {
            return response()->json(['success' => true]);
        }
        return redirect()->route('dashboard.events.index')
            ->with('success', 'Event deleted successfully!');
    }
}