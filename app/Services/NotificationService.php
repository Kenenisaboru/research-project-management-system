<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;

class NotificationService
{
    /**
     * Store a notification for a specific user and attempt an email copy.
     * Email delivery degrades gracefully on the LAN when mail is not configured.
     */
    public static function notifyUser(
        int $userId,
        string $type,
        string $message,
        string $category = 'workflow',
        string $priority = 'normal',
        ?string $actionUrl = null,
        ?string $icon = null
    ): Notification {
        $notification = Notification::create([
            'user_id'    => $userId,
            'type'       => $type,
            'category'   => $category,
            'priority'   => $priority,
            'message'    => $message,
            'action_url' => $actionUrl,
            'icon'       => $icon ?: self::defaultIcon($category),
        ]);

        static::sendEmailToUser($userId, $type, $message);

        return $notification;
    }

    /**
     * Determine default Bootstrap icon for category.
     */
    public static function defaultIcon(string $category): string
    {
        return match (strtolower($category)) {
            'budget'     => 'bi-cash-coin',
            'review'     => 'bi-pencil-square',
            'compliance' => 'bi-shield-check',
            'system'     => 'bi-gear',
            default      => 'bi-bell',
        };
    }

    /**
     * Notify all users holding one of the given roles (admin fallback).
     */
    public static function notifyManagers(
        string $type,
        string $message,
        array $roleNames = ['admin'],
        string $category = 'workflow',
        string $priority = 'normal',
        ?string $actionUrl = null
    ): void {
        $roleNames = array_merge($roleNames, ['admin']);

        $users = User::whereIn('role', $roleNames)
            ->where('status', 'active')
            ->get();

        foreach ($users as $user) {
            static::notifyUser((int) $user->id, $type, $message, $category, $priority, $actionUrl);
        }
    }

    public static function notifyAdmins(
        string $type,
        string $message,
        string $category = 'system',
        string $priority = 'normal',
        ?string $actionUrl = null
    ): void {
        static::notifyManagers($type, $message, ['admin'], $category, $priority, $actionUrl);
    }

    public static function markAllRead(int $userId): int
    {
        return Notification::forUser($userId)->unread()->update(['read_at' => now()]);
    }

    public static function unreadCount(int $userId): int
    {
        return Notification::forUser($userId)->unread()->count();
    }

    public static function recent(int $userId, int $limit = 10)
    {
        return Notification::forUser($userId)->latest()->limit($limit)->get();
    }

    private static function sendEmailToUser(int $userId, string $type, string $message): void
    {
        try {
            $user = User::find($userId);
            if (! $user || empty($user->email)) {
                return;
            }

            \Illuminate\Support\Facades\Mail::to($user->email)
                ->queue(new \App\Mail\ProjectStatusMail($type, $message, $user->name));
        } catch (\Throwable $e) {
            // LAN deployments frequently lack SMTP; in-app notification still delivered.
            report($e);
        }
    }
}