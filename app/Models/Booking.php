<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Booking extends Model
{
    use HasFactory, SoftDeletes;
  protected $dates = ['deleted_at'];
    protected $table = 'bookings';

    protected $fillable = [
        'agency_id',
        'client_id',
        'vehicle_id',
        'start_date',
        'end_date',
        'pickup_location',
        'dropoff_location',
        'booked_days',
        'estimated_total',
        'status',
        'source',
        'start_at',
        'end_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'booked_days' => 'integer',
        'estimated_total' => 'decimal:2',
    ];

    /**
     * Get the agency that owns the booking.
     */
    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }

    /**
     * Get the client associated with the booking.
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the vehicle associated with the booking.
     */
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Get formatted start date.
     */
    public function getFormattedStartDateAttribute(): string
    {
        return $this->start_date ? $this->start_date->format('d/m/Y') : 'N/A';
    }

    /**
     * Get formatted end date.
     */
    public function getFormattedEndDateAttribute(): string
    {
        return $this->end_date ? $this->end_date->format('d/m/Y') : 'N/A';
    }

    /**
     * Get formatted estimated total.
     */
    public function getFormattedEstimatedTotalAttribute(): string
    {
        return $this->estimated_total ? number_format($this->estimated_total, 2, ',', ' ') . ' MAD' : '—';
    }

    /**
     * Get status badge class.
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'pending' => 'badge-warning',
            'confirmed' => 'badge-success',
            'cancelled' => 'badge-danger',
            'converted' => 'badge-info',
            default => 'badge-secondary',
        };
    }

    /**
     * Get status text.
     */
    public function getStatusTextAttribute(): string
    {
        return match($this->status) {
            'pending' => 'En attente',
            'confirmed' => 'Confirmé',
            'cancelled' => 'Annulé',
            'converted' => 'Converti en contrat',
            default => $this->status,
        };
    }

    /**
     * Get source icon.
     */
    public function getSourceIconAttribute(): string
    {
        return match($this->source) {
            'website' => 'ti ti-world',
            'mobile' => 'ti ti-device-mobile',
            'backoffice' => 'ti ti-device-laptop',
            default => 'ti ti-file',
        };
    }

    /**
     * Get source text.
     */
    public function getSourceTextAttribute(): string
    {
        return match($this->source) {
            'website' => 'Site web',
            'mobile' => 'Application mobile',
            'backoffice' => 'Backoffice',
            default => 'Autre',
        };
    }

    /**
     * Calculate estimated total based on vehicle daily rate.
     */
    public function calculateEstimatedTotal(): ?float
    {
        if ($this->vehicle && $this->vehicle->daily_rate) {
            return $this->vehicle->daily_rate * $this->booked_days;
        }
        return null;
    }

    /**
     * Scope a query to only include pending bookings.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include confirmed bookings.
     */
    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    /**
     * Scope a query to only include bookings for a specific date range.
     */
    public function scopeInDateRange($query, $start, $end)
    {
        return $query->where(function($q) use ($start, $end) {
            $q->whereBetween('start_date', [$start, $end])
              ->orWhereBetween('end_date', [$start, $end])
              ->orWhere(function($sub) use ($start, $end) {
                  $sub->where('start_date', '<=', $start)
                       ->where('end_date', '>=', $end);
              });
        });
    }
}