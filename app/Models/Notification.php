<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'icon',
        'link',
        'is_read',
        'is_archived',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'is_archived' => 'boolean',
    ];

    /**
     * Get the user that owns the notification.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user who performed the action.
     */
    public function performer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get performer name.
     */
    public function getPerformerNameAttribute(): string
    {
        return $this->performer?->name ?? 'System';
    }

    /**
     * Get notification icon based on type.
     */
    public function getIconAttribute($value)
    {
        if ($value) {
            return $value;
        }

        return match($this->type) {
            'success' => 'ti ti-circle-check',
            'error' => 'ti ti-circle-x',
            'warning' => 'ti ti-alert-triangle',
            'info' => 'ti ti-info-circle',
            'login' => 'ti ti-login',
            'logout' => 'ti ti-logout',
            default => 'ti ti-bell',
        };
    }

    /**
     * Get notification color based on type.
     */
/**
 * Get notification color based on type.
 */
public function getColorAttribute(): string
{
    return match($this->type) {
        'success' => '#198754',
        'error' => '#dc3545',
        'warning' => '#ffc107',
        'info' => '#0d6efd',
        'login' => '#0d6efd',   // Add this
        'logout' => '#6c757d',   // Add this
        default => '#0d6efd',
    };
}

    /**
     * Get formatted time.
     */
    public function getFormattedTimeAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Get time in format like "4 Min Ago"
     */
    public function getTimeAgoAttribute(): string
    {
        $diff = $this->created_at->diffInMinutes(now());
        
        if ($diff < 1) return 'Just now';
        if ($diff < 60) return $diff . ' Min Ago';
        if ($diff < 1440) return floor($diff / 60) . ' Hrs Ago';
        return floor($diff / 1440) . ' Days Ago';
    }

    /**
     * Scope for unread notifications.
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false)->where('is_archived', false);
    }

    /**
     * Scope for read notifications.
     */
    public function scopeRead($query)
    {
        return $query->where('is_read', true)->where('is_archived', false);
    }

    /**
     * Scope for archived notifications.
     */
    public function scopeArchived($query)
    {
        return $query->where('is_archived', true);
    }

    /**
     * Scope for active (not archived) notifications.
     */
    public function scopeActive($query)
    {
        return $query->where('is_archived', false);
    }
}