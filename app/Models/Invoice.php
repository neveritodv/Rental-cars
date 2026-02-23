<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use HasFactory, SoftDeletes;
  protected $dates = ['deleted_at'];
    protected $table = 'invoices';

    protected $fillable = [
        'agency_id',
        'invoice_number',
        'invoice_date',
        'rental_contract_id',
        'client_id',
        'company_name',
        'company_address',
        'company_phone',
        'company_email',
        'vat_rate',
        'total_ht',
        'total_vat',
        'total_ttc',
        'status',
        'notes',
        'currency',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'vat_rate' => 'decimal:2',
        'total_ht' => 'decimal:2',
        'total_vat' => 'decimal:2',
        'total_ttc' => 'decimal:2',
    ];

    /**
     * Get the agency that owns the invoice.
     */
    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }

    /**
 * Get the payments for this invoice.
 */
public function payments()
{
    return $this->hasMany(Payment::class);
}

    /**
     * Get the rental contract associated with the invoice.
     */
    public function rentalContract()
    {
        return $this->belongsTo(RentalContract::class);
    }

    /**
     * Get the client associated with the invoice.
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the items for this invoice.
     */
    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    /**
     * Get formatted invoice number.
     */
    public function getFormattedInvoiceNumberAttribute(): string
    {
        return $this->invoice_number;
    }

    /**
     * Get formatted invoice date.
     */
    public function getFormattedInvoiceDateAttribute(): string
    {
        return $this->invoice_date->format('d/m/Y');
    }

    /**
     * Get formatted total HT.
     */
    public function getFormattedTotalHtAttribute(): string
    {
        return number_format($this->total_ht, 2, ',', ' ') . ' ' . $this->currency;
    }

    /**
     * Get formatted total VAT.
     */
    public function getFormattedTotalVatAttribute(): string
    {
        return number_format($this->total_vat, 2, ',', ' ') . ' ' . $this->currency;
    }

    /**
     * Get formatted total TTC.
     */
    public function getFormattedTotalTtcAttribute(): string
    {
        return number_format($this->total_ttc, 2, ',', ' ') . ' ' . $this->currency;
    }

    /**
     * Get status badge class.
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'draft' => 'badge-secondary',
            'sent' => 'badge-info',
            'paid' => 'badge-success',
            'partially_paid' => 'badge-warning',
            'cancelled' => 'badge-danger',
            default => 'badge-secondary',
        };
    }

    /**
     * Get status text.
     */
    public function getStatusTextAttribute(): string
    {
        return match($this->status) {
            'draft' => 'Brouillon',
            'sent' => 'Envoyée',
            'paid' => 'Payée',
            'partially_paid' => 'Partiellement payée',
            'cancelled' => 'Annulée',
            default => $this->status,
        };
    }

    /**
     * Generate invoice number.
     */
    public static function generateInvoiceNumber(): string
    {
        $year = date('Y');
        $month = date('m');
        $prefix = "INV-{$year}{$month}-";
        
        $lastInvoice = self::where('invoice_number', 'like', $prefix . '%')
            ->orderBy('invoice_number', 'desc')
            ->first();
        
        if ($lastInvoice) {
            $lastNumber = intval(substr($lastInvoice->invoice_number, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }
        
        return $prefix . $newNumber;
    }
}