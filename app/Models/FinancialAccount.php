<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinancialAccount extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'financial_accounts';
  protected $dates = ['deleted_at'];
    protected $fillable = [
        'agency_id',
        'name',
        'type',
        'rib',
        'initial_balance',
        'current_balance',
        'is_default',
    ];

    protected $casts = [
        'initial_balance' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'is_default' => 'boolean',
    ];

    /**
     * Get the agency that owns the account.
     */
    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }

    /**
     * Get the transactions for this account.
     */
    public function transactions()
    {
        return $this->hasMany(FinancialTransaction::class, 'financial_account_id');
    }

    /**
     * Get formatted current balance.
     */
    public function getFormattedCurrentBalanceAttribute(): string
    {
        return number_format($this->current_balance, 2, ',', ' ') . ' MAD';
    }

    /**
     * Get formatted initial balance.
     */
    public function getFormattedInitialBalanceAttribute(): string
    {
        return number_format($this->initial_balance, 2, ',', ' ') . ' MAD';
    }

    /**
     * Get type badge class.
     */
    public function getTypeBadgeClassAttribute(): string
    {
        return match($this->type) {
            'bank' => 'badge-info',
            'cash' => 'badge-success',
            default => 'badge-secondary',
        };
    }

    /**
     * Get type text.
     */
    public function getTypeTextAttribute(): string
    {
        return match($this->type) {
            'bank' => 'Banque',
            'cash' => 'Caisse',
            default => 'Autre',
        };
    }

    /**
     * Get total income for this account.
     */
    public function getTotalIncomeAttribute(): float
    {
        return $this->transactions()->where('type', 'income')->sum('amount');
    }

    /**
     * Get total expense for this account.
     */
    public function getTotalExpenseAttribute(): float
    {
        return $this->transactions()->where('type', 'expense')->sum('amount');
    }

    /**
     * Update current balance based on transactions.
     */
    public function updateBalance(): void
    {
        $income = $this->total_income;
        $expense = $this->total_expense;
        $this->current_balance = $this->initial_balance + $income - $expense;
        $this->saveQuietly();
    }
}