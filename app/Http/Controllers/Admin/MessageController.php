<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Message;

class MessageController extends Controller {
    public function index() {
        $messages = Message::latest()->paginate(15);
        Message::where('status', 'unread')->update(['status' => 'read']);
        return view('messages.index', compact('messages'));
    }

    public function show(Message $message) {
        $message->update(['status' => 'read']);
        return view('messages.show', compact('message'));
    }

    public function destroy(Message $message) {
        $message->delete();
        return redirect()->route('dashboard.messages.index')->with('success', 'Message deleted!');
    }
}