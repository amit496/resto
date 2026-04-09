<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\NotificationLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(): View
    {
        return view('backend.notifications.index', [
            'notifications' => NotificationLog::query()->latest()->paginate(20),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:120'],
            'message' => ['required', 'string'],
            'channel' => ['required', 'in:dashboard,email,sms,push'],
            'audience' => ['required', 'in:admin,staff,customer,all'],
        ]);

        NotificationLog::query()->create([
            ...$data,
            'sent_at' => now(),
            'created_by' => auth()->id(),
        ]);

        return back()->with('success', 'Notification logged.');
    }

    public function markRead(NotificationLog $notification): RedirectResponse
    {
        $notification->update([
            'is_read' => true,
        ]);

        return back()->with('success', 'Notification marked as read.');
    }
}

