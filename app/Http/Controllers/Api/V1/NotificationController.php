<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\NotificationResource;
use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class NotificationController
 *
 * Provides API access to in-app alerts and notifications with read status management.
 */
class NotificationController extends ApiController
{
    /**
     * Get paginated notifications for the authenticated user.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $notifications = Notification::forUser($user->id)
            ->latest()
            ->paginate(20);

        return $this->success([
            'items'        => NotificationResource::collection($notifications->items()),
            'unread_count' => Notification::forUser($user->id)->unread()->count(),
            'pagination'   => [
                'current_page' => $notifications->currentPage(),
                'last_page'    => $notifications->lastPage(),
                'total'        => $notifications->total(),
            ],
        ], 'Notifications retrieved successfully.');
    }

    /**
     * Return unread notification count for lightweight polling.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function unreadCount(Request $request): JsonResponse
    {
        $count = Notification::forUser($request->user()->id)->unread()->count();

        return $this->success([
            'unread_count' => $count,
        ], 'Unread count retrieved.');
    }

    /**
     * Poll latest notifications and unread counter.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function poll(Request $request): JsonResponse
    {
        $user = $request->user();
        $count = Notification::forUser($user->id)->unread()->count();
        $recent = Notification::forUser($user->id)->latest()->limit(5)->get();

        return $this->success([
            'unread_count' => $count,
            'recent'       => NotificationResource::collection($recent),
        ], 'Notifications polled successfully.');
    }

    /**
     * Mark a specific notification as read.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return JsonResponse
     */
    public function markAsRead(Request $request, int $id): JsonResponse
    {
        $notification = Notification::forUser($request->user()->id)->find($id);

        if (!$notification) {
            return $this->error('Notification not found.', null, 404);
        }

        $notification->update(['read_at' => now()]);

        return $this->success(new NotificationResource($notification), 'Notification marked as read.');
    }

    /**
     * Mark all notifications as read for current user.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function markAllRead(Request $request): JsonResponse
    {
        NotificationService::markAllRead($request->user()->id);

        return $this->success(null, 'All notifications marked as read.');
    }
}
