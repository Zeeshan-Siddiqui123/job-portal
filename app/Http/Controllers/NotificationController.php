<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        return view('notifications.index', [
            'notifications' => $request->user()->userNotifications()
                ->latest()->orderByDesc('id')->paginate(15),
        ]);
    }

    public function markRead(Request $request, int $id): RedirectResponse
    {
        $request->user()->userNotifications()->findOrFail($id)->update(['is_read' => true]);

        return back()->with('success', 'Notification marked as read.');
    }
}
