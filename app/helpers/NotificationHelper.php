<?php

namespace App\Helpers;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class NotificationHelper
{
    /**
     * Send a notification to a specific user
     */
    public static function send($userId, $title, $message, $type = 'info', $link = null)
    {
        return Notification::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'link' => $link,
            'is_read' => false,
            'is_archived' => false,
        ]);
    }

    /**
     * Send a notification to all users in an agency
     */
    public static function sendToAgency($agencyId, $title, $message, $type = 'info', $link = null)
    {
        $users = User::where('agency_id', $agencyId)->get();
        
        foreach ($users as $user) {
            self::send($user->id, $title, $message, $type, $link);
        }
    }

    /**
     * Send a notification to all super admins
     */
    public static function sendToAdmins($title, $message, $type = 'info', $link = null)
    {
        $admins = User::whereHas('roles', function($q) {
            $q->where('name', 'super-admin');
        })->get();
        
        foreach ($admins as $admin) {
            self::send($admin->id, $title, $message, $type, $link);
        }
    }
}