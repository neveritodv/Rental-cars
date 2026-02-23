<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes;
  protected $dates = ['deleted_at'];
    protected $table = 'payments';

    protected $fillable = [
        'agency_id',
        'invoice_id',
        'rental_contract_id',
        'financial_transaction_id',
        'financial_account_id',
        'payment_date',
        'amount',
        'method',
        'status',
        'reference',
        'currency',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
    ];

    /**
     * Get the agency that owns the payment.
     */
    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }

    /**
     * Get the invoice associated with the payment.
     */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Get the rental contract associated with the payment.
     */
    public function rentalContract()
    {
        return $this->belongsTo(RentalContract::class);
    }

    /**
     * Get the financial transaction associated with the payment.
     */
    public function financialTransaction()
    {
        return $this->belongsTo(FinancialTransaction::class);
    }

    /**
     * Get the financial account associated with the payment.
     */
    public function financialAccount()
    {
        return $this->belongsTo(FinancialAccount::class);
    }

    /**
     * Get formatted amount.
     */
    public function getFormattedAmountAttribute(): string
    {
        return number_format($this->amount, 2, ',', ' ') . ' ' . $this->currency;
    }

    /**
     * Get formatted payment date.
     */
    public function getFormattedPaymentDateAttribute(): string
    {
        return $this->payment_date->format('d/m/Y');
    }

    /**
     * Get method badge class.
     */
    public function getMethodBadgeClassAttribute(): string
    {
        return match($this->method) {
            'cash' => 'badge-success',
            'card' => 'badge-info',
            'bank_transfer' => 'badge-primary',
            'cheque' => 'badge-warning',
            default => 'badge-secondary',
        };
    }

    /**
     * Get method text.
     */
    public function getMethodTextAttribute(): string
    {
        return match($this->method) {
            'cash' => 'Espèces',
            'card' => 'Carte bancaire',
            'bank_transfer' => 'Virement',
            'cheque' => 'Chèque',
            default => 'Autre',
        };
    }

    /**
     * Get status badge class.
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'pending' => 'badge-warning',
            'confirmed' => 'badge-success',
            'refunded' => 'badge-danger',
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
            'refunded' => 'Remboursé',
            default => $this->status,
        };
    }
}