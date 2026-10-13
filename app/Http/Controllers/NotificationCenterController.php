<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationCenterController extends Controller
{
    /**
     * Display the Notification Centre for the authenticated user.
     */
    public function index(Request $request): View
    {
        $userId = $request->user()->id;
        $query = Notification::forUser($userId);

        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        if ($request->filled('priority') && $request->priority !== 'all') {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('status')) {
            if ($request->status === 'unread') {
                $query->unread();
            } elseif ($request->status === 'read') {
                $query->whereNotNull('read_at');
            }
        }

        $notifications = $query->latest()->paginate(15)->withQueryString();

        $unreadCounts = [
            'total'      => Notification::forUser($userId)->unread()->count(),
            'workflow'   => Notification::forUser($userId)->unread()->where('category', 'workflow')->count(),
            'budget'     => Notification::forUser($userId)->unread()->where('category', 'budget')->count(),
            'review'     => Notification::forUser($userId)->unread()->where('category', 'review')->count(),
            'compliance' => Notification::forUser($userId)->unread()->where('category', 'compliance')->count(),
            'system'     => Notification::forUser($userId)->unread()->where('category', 'system')->count(),
        ];

        $filters = $request->only(['category', 'priority', 'status']);

        return view('notifications.index', compact('notifications', 'unreadCounts', 'filters'));
    }

    /**
     * Mark a single notification as read and redirect to action_url if present.
     */
    public function markRead(Request $request, int $id): RedirectResponse
    {
        $notification = Notification::forUser($request->user()->id)->findOrFail($id);
        $notification->update(['read_at' => now()]);

        if ($notification->action_url) {
            return redirect($notification->action_url);
        }

        return back()->with('success', 'Notification marked as read.');
    }

    /**
     * Mark all notifications as read for current user.
     */
    public function markAllRead(Request $request): RedirectResponse
    {
        NotificationService::markAllRead($request->user()->id);

        return back()->with('success', 'All notifications marked as read.');
    }

    /**
     * Delete a notification.
     */
    public function destroy(Request $request, int $id): RedirectResponse
    {
        $notification = Notification::forUser($request->user()->id)->findOrFail($id);
        $notification->delete();

        return back()->with('success', 'Notification removed.');
    }
}
