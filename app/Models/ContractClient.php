<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractClient extends Model
{
    use HasFactory;

    protected $table = 'contract_clients';
  protected $dates = ['deleted_at'];
    protected $fillable = [
        'rental_contract_id',
        'client_id',
        'role',
        'order',
    ];

    protected $casts = [
        'order' => 'integer',
    ];

    /**
     * Get the rental contract associated with this client relationship.
     */
    public function rentalContract()
    {
        return $this->belongsTo(RentalContract::class);
    }

    /**
     * Get the client associated with this relationship.
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get role badge class.
     */
    public function getRoleBadgeClassAttribute(): string
    {
        return match($this->role) {
            'primary' => 'badge-primary',
            'secondary' => 'badge-secondary',
            default => 'badge-other',
        };
    }

    /**
     * Get role text.
     */
    public function getRoleTextAttribute(): string
    {
        return match($this->role) {
            'primary' => 'Principal',
            'secondary' => 'Secondaire',
            default => 'Autre',
        };
    }
}