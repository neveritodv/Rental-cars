<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VehicleControlItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $dates = ['deleted_at'];
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'vehicle_control_items';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'vehicle_control_id',
        'item_key',
        'label',
        'status',
        'comment',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => 'string',
    ];

    /**
     * Get the vehicle control that owns the item.
     */
    public function vehicleControl()
    {
        return $this->belongsTo(VehicleControl::class);
    }

    /**
     * Get status badge class.
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'yes' => 'bg-success',
            'no' => 'bg-danger',
            'na' => 'bg-secondary',
            default => 'bg-light'
        };
    }

    /**
     * Get status text in French.
     */
    public function getStatusTextAttribute(): string
    {
        return match($this->status) {
            'yes' => 'Oui',
            'no' => 'Non',
            'na' => 'N/A',
            default => $this->status
        };
    }

    /**
     * Get status icon.
     */
    public function getStatusIconAttribute(): string
    {
        return match($this->status) {
            'yes' => 'ti ti-check',
            'no' => 'ti ti-x',
            'na' => 'ti ti-minus',
            default => 'ti ti-question-mark'
        };
    }

    /**
     * Scope a query to filter by control.
     */
    public function scopeByControl($query, $controlId)
    {
        return $query->where('vehicle_control_id', $controlId);
    }

    /**
     * Scope a query to filter by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}