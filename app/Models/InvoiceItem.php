<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    use HasFactory;

    protected $table = 'invoice_items';
  protected $dates = ['deleted_at'];
    protected $fillable = [
        'invoice_id',
        'description',
        'days_count',
        'unit_price_ttc',
        'quantity',
        'total_ttc',
        'total_ht',
        'vat_rate',
    ];

    protected $casts = [
        'days_count' => 'integer',
        'unit_price_ttc' => 'decimal:2',
        'quantity' => 'decimal:2',
        'total_ttc' => 'decimal:2',
        'total_ht' => 'decimal:2',
        'vat_rate' => 'decimal:2',
    ];

    /**
     * Get the invoice that owns the item.
     */
/**
 * Get the invoice that owns the item.
 */
public function invoice()
{
    return $this->belongsTo(Invoice::class);
}

    /**
     * Get formatted total TTC.
     */
    public function getFormattedTotalTtcAttribute(): string
    {
        return number_format($this->total_ttc, 2, ',', ' ') . ' ' . ($this->invoice->currency ?? 'MAD');
    }

    /**
     * Get formatted total HT.
     */
    public function getFormattedTotalHtAttribute(): string
    {
        return number_format($this->total_ht, 2, ',', ' ') . ' ' . ($this->invoice->currency ?? 'MAD');
    }

    /**
     * Get formatted unit price.
     */
    public function getFormattedUnitPriceAttribute(): string
    {
        return $this->unit_price_ttc ? number_format($this->unit_price_ttc, 2, ',', ' ') . ' ' . ($this->invoice->currency ?? 'MAD') : '—';
    }

    /**
     * Calculate totals based on unit price, quantity and VAT.
     */
    public function calculateTotals(): void
    {
        if ($this->unit_price_ttc && $this->quantity) {
            $this->total_ttc = $this->unit_price_ttc * $this->quantity;
            
            if ($this->vat_rate) {
                $coefficient = 1 + ($this->vat_rate / 100);
                $this->total_ht = $this->total_ttc / $coefficient;
            } else {
                $this->total_ht = $this->total_ttc;
            }
        }
    }
}