<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemNotification;
use Illuminate\Http\Request;

class NotificationController extends Controller {
    public function index() {
        $notifications = SystemNotification::where('user_id', auth()->id())
            ->latest()
            ->paginate(20);
        // Mark all as read when page is opened
        SystemNotification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->update(['is_read' => true]);
        return view('notifications.index', compact('notifications'));
    }

    public function markRead($id) {
        SystemNotification::where('id', $id)
            ->where('user_id', auth()->id())
            ->update(['is_read' => true]);
        return response()->json(['success' => true]);
    }

    public function markAllRead() {
        SystemNotification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->update(['is_read' => true]);
        return response()->json(['success' => true]);
    }

    public function destroy($id) {
        SystemNotification::where('id', $id)
            ->where('user_id', auth()->id())
            ->delete();
        if(request()->ajax()) {
            return response()->json(['success' => true]);
        }
        return redirect()->back()->with('success', 'Notification deleted!');
    }

    public function destroyAll() {
        SystemNotification::where('user_id', auth()->id())->delete();
        return redirect()->back()->with('success', 'All notifications cleared!');
    }

    // AJAX — get unread count
    public function unreadCount() {
        $count = SystemNotification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->count();
        return response()->json(['count' => $count]);
    }

    // AJAX — get latest notifications for dropdown
    public function latest() {
        $notifications = SystemNotification::where('user_id', auth()->id())
            ->latest()
            ->take(5)
            ->get();
        return response()->json($notifications);
    }
}