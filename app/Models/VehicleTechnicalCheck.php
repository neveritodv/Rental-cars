<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VehicleTechnicalCheck extends Model
{
    use HasFactory, SoftDeletes;
    protected $dates = ['deleted_at'];
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'vehicle_technical_checks';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'vehicle_id',
        'date',
        'amount',
        'next_check_date',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date' => 'date',
        'next_check_date' => 'date',
        'amount' => 'decimal:2',
    ];

    /**
     * Get the vehicle that owns the technical check.
     */
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Get formatted amount with currency (MAD).
     */
    public function getFormattedAmountAttribute(): string
    {
        return number_format($this->amount, 2, ',', ' ') . ' DH';
    }

    /**
     * Get formatted date.
     */
    public function getFormattedDateAttribute(): string
    {
        return $this->date->format('d/m/Y');
    }

    /**
     * Get formatted next check date.
     */
    public function getFormattedNextDateAttribute(): string
    {
        return $this->next_check_date->format('d/m/Y');
    }

    /**
     * Check if technical check is expiring soon (within 30 days).
     */
    public function getIsExpiringSoonAttribute(): bool
    {
        return $this->next_check_date->isFuture() && 
               $this->next_check_date->diffInDays(now()) <= 30;
    }

    /**
     * Check if technical check is expired.
     */
    public function getIsExpiredAttribute(): bool
    {
        return $this->next_check_date->isPast();
    }

    /**
     * Get status badge class.
     */
    public function getStatusBadgeClassAttribute(): string
    {
        if ($this->is_expired) {
            return 'bg-danger';
        } elseif ($this->is_expiring_soon) {
            return 'bg-warning';
        }
        return 'bg-success';
    }

    /**
     * Get status text.
     */
    public function getStatusTextAttribute(): string
    {
        if ($this->is_expired) {
            return 'Expiré';
        } elseif ($this->is_expiring_soon) {
            return 'Expire bientôt';
        }
        return 'Valide';
    }
}