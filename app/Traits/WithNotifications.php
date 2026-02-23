<?php

namespace App\Traits;

use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

trait WithNotifications
{
    /**
     * Create a notification for the current user.
     */
    protected function createNotification($title, $message, $type = 'info', $link = null, $icon = null)
    {
        $userId = Auth::guard('backoffice')->id();

        if (!$userId) {
            return null;
        }

        return Notification::create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'link' => $link,
            'icon' => $icon,
        ]);
    }

    /**
     * Send a success notification.
     */
    protected function notifySuccess($message, $title = 'Succès', $link = null)
    {
        $this->createNotification($title, $message, 'success', $link);
        
        return redirect()->back()->with('toast', [
            'title' => $title,
            'message' => $message,
            'dot' => '#198754',
            'delay' => 3500,
            'time' => 'now',
        ]);
    }

    /**
     * Send a success notification with redirect.
     */
    protected function notifySuccessWithRedirect($route, $message, $title = 'Succès', $params = [])
    {
        $this->createNotification($title, $message, 'success', route($route, $params));
        
        return redirect()->route($route, $params)->with('toast', [
            'title' => $title,
            'message' => $message,
            'dot' => '#198754',
            'delay' => 3500,
            'time' => 'now',
        ]);
    }

    /**
     * Send an error notification.
     */
    protected function notifyError($message, $title = 'Erreur', $link = null)
    {
        $this->createNotification($title, $message, 'error', $link);
        
        return redirect()->back()->withInput()->with('toast', [
            'title' => $title,
            'message' => $message,
            'dot' => '#dc3545',
            'delay' => 3500,
            'time' => 'now',
        ]);
    }

    /**
     * Send an info notification.
     */
    protected function notifyInfo($message, $title = 'Information', $link = null)
    {
        $this->createNotification($title, $message, 'info', $link);
        
        return redirect()->back()->with('toast', [
            'title' => $title,
            'message' => $message,
            'dot' => '#0d6efd',
            'delay' => 3500,
            'time' => 'now',
        ]);
    }

    /**
     * Send a warning notification.
     */
    protected function notifyWarning($message, $title = 'Attention', $link = null)
    {
        $this->createNotification($title, $message, 'warning', $link);
        
        return redirect()->back()->with('toast', [
            'title' => $title,
            'message' => $message,
            'dot' => '#ffc107',
            'delay' => 3500,
            'time' => 'now',
        ]);
    }
}