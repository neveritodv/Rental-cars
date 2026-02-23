<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class TransactionCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $table = 'transaction_categories';

    protected $fillable = [
        'agency_id',
        'name',
        'type',
    ];

    /**
     * Get the agency that owns the category.
     */
    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }

    /**
     * Get the transactions for this category.
     */
    public function transactions()
    {
        return $this->hasMany(FinancialTransaction::class, 'transaction_category_id');
    }

    /**
     * Get type badge class.
     */
    public function getTypeBadgeClassAttribute(): string
    {
        return match($this->type) {
            'income' => 'badge-success',
            'expense' => 'badge-danger',
            'both' => 'badge-info',
            default => 'badge-secondary',
        };
    }

    /**
     * Get type text.
     */
    public function getTypeTextAttribute(): string
    {
        return match($this->type) {
            'income' => 'Revenu',
            'expense' => 'Dépense',
            'both' => 'Les deux',
            default => $this->type,
        };
    }

    /**
     * Get transactions count.
     */
    public function getTransactionsCountAttribute(): int
    {
        return $this->transactions()->count();
    }

    /**
     * Get total amount for this category.
     */
    public function getTotalAmountAttribute(): float
    {
        return $this->transactions()->sum('amount');
    }
}