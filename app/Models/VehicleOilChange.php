<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VehicleOilChange extends Model
{
    use HasFactory, SoftDeletes;
    protected $dates = ['deleted_at'];
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'vehicle_oil_changes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'vehicle_id',
        'date',
        'amount',
        'mileage',
        'next_mileage',
        'mechanic_name',
        'observations',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
        'mileage' => 'integer',
        'next_mileage' => 'integer',
    ];

    /**
     * Get the vehicle that owns the oil change.
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
     * Get formatted mileage with km.
     */
    public function getFormattedMileageAttribute(): string
    {
        return number_format($this->mileage, 0, ',', ' ') . ' km';
    }

    /**
     * Get formatted next mileage with km.
     */
    public function getFormattedNextMileageAttribute(): string
    {
        return number_format($this->next_mileage, 0, ',', ' ') . ' km';
    }

    /**
     * Get remaining mileage until next change.
     */
    public function getRemainingMileageAttribute(): int
    {
        return max(0, $this->next_mileage - $this->mileage);
    }

    /**
     * Get formatted remaining mileage.
     */
    public function getFormattedRemainingMileageAttribute(): string
    {
        return number_format($this->remaining_mileage, 0, ',', ' ') . ' km';
    }

    /**
     * Check if oil change is due soon (within 1000 km).
     */
    public function getIsDueSoonAttribute(): bool
    {
        return $this->remaining_mileage <= 1000;
    }

    /**
     * Check if oil change is overdue.
     */
    public function getIsOverdueAttribute(): bool
    {
        return $this->remaining_mileage <= 0;
    }

    /**
     * Get status badge class.
     */
    public function getStatusBadgeClassAttribute(): string
    {
        if ($this->is_overdue) {
            return 'bg-danger';
        } elseif ($this->is_due_soon) {
            return 'bg-warning';
        }
        return 'bg-success';
    }

    /**
     * Get status text.
     */
    public function getStatusTextAttribute(): string
    {
        if ($this->is_overdue) {
            return 'Dépassé';
        } elseif ($this->is_due_soon) {
            return 'Bientôt';
        }
        return 'OK';
    }
}