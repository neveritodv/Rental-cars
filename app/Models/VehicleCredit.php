<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VehicleCredit extends Model
{
    use HasFactory, SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'vehicle_id',
        'agency_id',
        'created_by',
        'credit_number',
        'creditor_name',
        'total_amount',
        'down_payment',
        'monthly_payment',
        'duration_months',
        'interest_rate',
        'start_date',
        'end_date',
        'remaining_amount',
        'remaining_months',
        'status',
        'contract_file',
        'documents',
        'notes',
    ];

    protected $casts = [
        'total_amount' => 'float',
        'down_payment' => 'float',
        'monthly_payment' => 'float',
        'interest_rate' => 'float',
        'remaining_amount' => 'float',
        'duration_months' => 'integer',
        'remaining_months' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date',
        'documents' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($credit) {
            if (empty($credit->credit_number)) {
                $credit->credit_number = 'CRD-' . date('Y') . '-' . str_pad(static::whereYear('created_at', date('Y'))->count() + 1, 4, '0', STR_PAD_LEFT);
            }
            if (empty($credit->agency_id) && auth()->check() && auth()->user()->agency) {
                $credit->agency_id = auth()->user()->agency->id;
            }
            if (empty($credit->created_by) && auth()->check()) {
                $credit->created_by = auth()->id();
            }
            
            // S'assurer que duration_months est un entier
            $credit->duration_months = (int)$credit->duration_months;
            
            // Calculer la date de fin
            if ($credit->start_date && $credit->duration_months) {
                $credit->end_date = $credit->start_date->copy()->addMonths($credit->duration_months);
            }
            
            // S'assurer que les montants sont des nombres
            $credit->total_amount = (float)$credit->total_amount;
            $credit->down_payment = (float)($credit->down_payment ?? 0);
            $credit->monthly_payment = (float)$credit->monthly_payment;
            $credit->interest_rate = (float)($credit->interest_rate ?? 0);
            
            // Montant restant initial
            $credit->remaining_amount = $credit->total_amount - $credit->down_payment;
            $credit->remaining_months = $credit->duration_months;
        });
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function payments()
    {
        return $this->hasMany(CreditPayment::class, 'vehicle_credit_id');
    }

    public function getProgressPercentageAttribute()
    {
        $totalPaid = $this->payments()->where('status', 'paid')->sum('amount');
        $totalToPay = $this->total_amount - $this->down_payment;
        
        if ($totalToPay <= 0) return 100;
        
        return round(($totalPaid / $totalToPay) * 100, 2);
    }

    public function getPaidMonthsAttribute()
    {
        return $this->payments()->where('status', 'paid')->count();
    }

    public function getLatePaymentsAttribute()
    {
        return $this->payments()
            ->where('status', 'pending')
            ->where('due_date', '<', now())
            ->count();
    }

    public function updateRemainingAmount()
    {
        $totalPaid = $this->payments()->where('status', 'paid')->sum('amount');
        $this->remaining_amount = ($this->total_amount - $this->down_payment) - $totalPaid;
        $this->remaining_months = $this->duration_months - $this->paid_months;
        
        if ($this->remaining_amount <= 0) {
            $this->status = 'completed';
        }
        
        $this->saveQuietly();
    }

    public function generatePaymentSchedule()
    {
        $payments = [];
        $principal = $this->total_amount - $this->down_payment;
        $monthlyInterest = $this->interest_rate / 100 / 12;
        
        for ($i = 1; $i <= $this->duration_months; $i++) {
            $dueDate = $this->start_date->copy()->addMonths($i);
            $interestAmount = $principal * $monthlyInterest;
            $principalAmount = $this->monthly_payment - $interestAmount;
            
            $payments[] = [
                'payment_number' => $i,
                'due_date' => $dueDate,
                'amount' => $this->monthly_payment,
                'principal' => $principalAmount,
                'interest' => $interestAmount,
                'status' => 'pending',
            ];
            
            $principal -= $principalAmount;
        }
        
        return $payments;
    }
}