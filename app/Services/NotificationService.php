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
    public static function notifyUser(int $userId, string $type, string $message): void
    {
        Notification::create([
            'user_id' => $userId,
            'type'    => $type,
            'message' => $message,
        ]);

        static::sendEmailToUser($userId, $type, $message);
    }

    /**
     * Notify all users holding one of the given roles (admin fallback).
     */
    public static function notifyManagers(string $type, string $message, array $roleNames = ['admin']): void
    {
        $roleNames = array_merge($roleNames, ['admin']);

        $users = User::whereIn('role', $roleNames)
            ->where('status', 'active')
            ->get();

        foreach ($users as $user) {
            static::notifyUser((int) $user->id, $type, $message);
        }
    }

    public static function notifyAdmins(string $type, string $message): void
    {
        static::notifyManagers($type, $message, ['admin']);
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

            $subject = 'RPMS Notification: ' . ucwords(strtolower(str_replace('_', ' ', $type)));

            \Illuminate\Support\Facades\Mail::raw(
                "{$message}\n\n---\nResearch Project Management System\nGambella University",
                function ($mail) use ($user, $subject) {
                    $mail->to($user->email)->subject($subject);
                }
            );
        } catch (\Throwable $e) {
            // LAN deployments frequently lack SMTP; in-app notification still delivered.
            report($e);
        }
    }
}