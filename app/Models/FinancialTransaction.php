<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinancialTransaction extends Model
{
    use HasFactory, SoftDeletes;
  protected $dates = ['deleted_at'];
    protected $table = 'financial_transactions';

    protected $fillable = [
        'agency_id',
        'financial_account_id',
        'transaction_category_id',
        'date',
        'amount',
        'type',
        'description',
        'reference',
        'related_type',
        'related_id',
        'created_by',
        'currency',
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
    ];

    /**
     * Get the agency that owns the transaction.
     */
    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }

    /**
     * Get the account for this transaction.
     */
    public function account()
    {
        return $this->belongsTo(FinancialAccount::class, 'financial_account_id');
    }

    /**
     * Get the category for this transaction.
     */
    public function category()
    {
        return $this->belongsTo(TransactionCategory::class, 'transaction_category_id');
    }

    /**
     * Get the user who created the transaction.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the related model (polymorphic).
     */
    public function related()
    {
        return $this->morphTo('related', 'related_type', 'related_id');
    }

    /**
     * Get formatted amount.
     */
    public function getFormattedAmountAttribute(): string
    {
        $prefix = $this->type === 'income' ? '+' : '-';
        return $prefix . ' ' . number_format($this->amount, 2, ',', ' ') . ' ' . $this->currency;
    }

    /**
     * Get formatted date.
     */
    public function getFormattedDateAttribute(): string
    {
        return $this->date->format('d/m/Y');
    }

    /**
     * Get type badge class.
     */
    public function getTypeBadgeClassAttribute(): string
    {
        return $this->type === 'income' ? 'badge-success' : 'badge-danger';
    }

    /**
     * Get type text.
     */
    public function getTypeTextAttribute(): string
    {
        return $this->type === 'income' ? 'Revenu' : 'Dépense';
    }

    /**
     * Scope a query to only include income.
     */
    public function scopeIncome($query)
    {
        return $query->where('type', 'income');
    }

    /**
     * Scope a query to only include expenses.
     */
    public function scopeExpense($query)
    {
        return $query->where('type', 'expense');
    }

    /**
     * Scope a query to filter by date range.
     */
    public function scopeInDateRange($query, $start, $end)
    {
        return $query->whereBetween('date', [$start, $end]);
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function ($transaction) {
            if ($transaction->account) {
                $transaction->account->updateBalance();
            }
        });

        static::updated(function ($transaction) {
            if ($transaction->account) {
                $transaction->account->updateBalance();
            }
        });

        static::deleted(function ($transaction) {
            if ($transaction->account) {
                $transaction->account->updateBalance();
            }
        });
    }
}