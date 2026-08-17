<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\NotificationPreference;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class NotificationController extends Controller
{
    public function index(Request $request): Response
    {
        $notifications = Notification::where('user_id', $request->user()->id)
            ->latest()
            ->limit(50)
            ->get();

        $preferences = NotificationPreference::firstOrCreate(
            ['user_id' => $request->user()->id],
            [
                'expense_reminder_enabled' => true,
                'expense_reminder_time' => '21:00',
                'budget_alert_enabled' => true,
                'budget_alert_threshold' => 80,
            ]
        );

        return Inertia::render('notifications/Index', [
            'notifications' => $notifications,
            'preferences' => $preferences,
        ]);
    }

    public function markRead(Request $request, Notification $notification): RedirectResponse
    {
        if ($notification->user_id !== $request->user()->id) {
            abort(403);
        }

        $notification->update(['read_at' => now()]);

        return back();
    }

    public function markAllRead(Request $request): RedirectResponse
    {
        Notification::where('user_id', $request->user()->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return back();
    }

    public function updatePreferences(Request $request): RedirectResponse
    {
        $preferences = NotificationPreference::firstOrCreate(
            ['user_id' => $request->user()->id]
        );

        $preferences->update($request->validate([
            'expense_reminder_enabled' => ['boolean'],
            'expense_reminder_time' => ['string', 'regex:/^\d{2}:\d{2}$/'],
            'budget_alert_enabled' => ['boolean'],
            'budget_alert_threshold' => ['integer', 'min:1', 'max:100'],
        ]));

        Inertia::flash('toast', ['type' => 'success', 'message' => 'تم حفظ التفضيلات']);

        return back();
    }
}
