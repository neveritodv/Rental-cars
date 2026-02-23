<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VehicleControl extends Model
{
    use HasFactory, SoftDeletes;
    protected $dates = ['deleted_at'];
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'vehicle_controls';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'agency_id',
        'rental_contract_id',
        'control_number',
        'vehicle_id',
        'start_mileage',
        'end_mileage',
        'notes',
        'performed_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_mileage' => 'integer',
        'end_mileage' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the agency that owns the vehicle control.
     */
    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }

    /**
     * Get the rental contract associated with the control.
     */
    public function rentalContract()
    {
        return $this->belongsTo(RentalContract::class);
    }

    /**
     * Get the vehicle being controlled.
     */
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Get the user who performed the control.
     */
    public function performer()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }

    /**
     * Get formatted start mileage with KM.
     */
    public function getFormattedStartMileageAttribute(): string
    {
        return number_format($this->start_mileage, 0, ',', ' ') . ' KM';
    }

    /**
     * Get formatted end mileage with KM.
     */
    public function getFormattedEndMileageAttribute(): string
    {
        return $this->end_mileage ? number_format($this->end_mileage, 0, ',', ' ') . ' KM' : 'Non renseigné';
    }

    /**
     * Get total distance traveled.
     */
    public function getTotalDistanceAttribute(): ?int
    {
        if ($this->end_mileage) {
            return $this->end_mileage - $this->start_mileage;
        }
        return null;
    }

    /**
     * Get formatted total distance.
     */
    public function getFormattedTotalDistanceAttribute(): string
    {
        $distance = $this->total_distance;
        return $distance ? number_format($distance, 0, ',', ' ') . ' KM' : 'Non disponible';
    }

    /**
     * Check if control is completed (has end mileage).
     */
    public function getIsCompletedAttribute(): bool
    {
        return !is_null($this->end_mileage);
    }

    /**
     * Get status badge class.
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return $this->is_completed ? 'bg-success' : 'bg-warning';
    }

    /**
     * Get status text.
     */
    public function getStatusTextAttribute(): string
    {
        return $this->is_completed ? 'Terminé' : 'En cours';
    }

    /**
     * Get formatted creation date.
     */
    public function getFormattedCreatedAtAttribute(): string
    {
        return $this->created_at->format('d/m/Y H:i');
    }

    /**
     * Scope a query to only include completed controls.
     */
    public function scopeCompleted($query)
    {
        return $query->whereNotNull('end_mileage');
    }

    /**
     * Scope a query to only include pending controls.
     */
    public function scopePending($query)
    {
        return $query->whereNull('end_mileage');
    }

    /**
     * Scope a query to filter by agency.
     */
    public function scopeByAgency($query, $agencyId)
    {
        return $query->where('agency_id', $agencyId);
    }

    /**
     * Scope a query to filter by vehicle.
     */
    public function scopeByVehicle($query, $vehicleId)
    {
        return $query->where('vehicle_id', $vehicleId);
    }
}