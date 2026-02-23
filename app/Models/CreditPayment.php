<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CreditPayment extends Model
{
    use HasFactory, SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'vehicle_credit_id',
        'payment_id',
        'payment_number',
        'due_date',
        'paid_date',
        'amount',
        'principal',
        'interest',
        'penalty',
        'status',
        'notes',
    ];

    protected $casts = [
        'due_date' => 'date',
        'paid_date' => 'date',
        'amount' => 'decimal:2',
        'principal' => 'decimal:2',
        'interest' => 'decimal:2',
        'penalty' => 'decimal:2',
    ];

    public function credit()
    {
        return $this->belongsTo(VehicleCredit::class, 'vehicle_credit_id');
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function markAsPaid($paymentId = null, $paidDate = null)
    {
        $this->update([
            'status' => 'paid',
            'paid_date' => $paidDate ?? now(),
            'payment_id' => $paymentId,
        ]);
        
        $this->credit->updateRemainingAmount();
    }

    public function getIsLateAttribute()
    {
        return $this->status === 'pending' && $this->due_date < now();
    }

    public function getLateDaysAttribute()
    {
        if (!$this->is_late) return 0;
        return now()->diffInDays($this->due_date);
    }
}