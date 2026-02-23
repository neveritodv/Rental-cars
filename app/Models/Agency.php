<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Agency extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;
      protected $dates = ['deleted_at'];

    protected $fillable = [
        'name',
        'legal_name',
        'tp_number',
        'rc_number',
        'if_number',
        'ice_number',
        'vat_number',
        'creation_date',
        'description',
        'email',
        'website',
        'phone',
        'address',
        'city',
        'country',
        'settings',
        'default_currency',
    ];

    protected $casts = [
        'settings' => 'array',
        'creation_date' => 'date',
    ];

    /* =======================
     |  MEDIA LIBRARY
     ======================= */

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('logo')
            ->useDisk('media')
            ->singleFile();

        $this->addMediaCollection('signature')
            ->useDisk('media')
            ->singleFile();

        $this->addMediaCollection('stamp')
            ->useDisk('media')
            ->singleFile();
    }

    /* =======================
     |  RELATIONSHIPS
     ======================= */

    // Utilisateurs liés à l’agence
    public function users()
    {
        return $this->hasMany(User::class);
    }

    // Abonnement SaaS (1 agence = 1 abonnement)
    public function subscription()
    {
        return $this->hasOne(AgencySubscription::class);
    }

    /* =======================
     |  HELPERS (optionnel)
     ======================= */

    public function isActive(): bool
    {
        return $this->subscription?->is_active ?? false;
    }

    public function onTrial(): bool
    {
        return $this->subscription?->trial_ends_at !== null
            && now()->lt($this->subscription->trial_ends_at);
    }

    /**
     * Get a nested setting value using dot notation
     * Example: getSetting('system.default_currency') or getSetting('invoice_template')
     */
    public function getSetting(string $key, $default = null)
    {
        $settings = $this->settings ?? [];

        if (strpos($key, '.') === false) {
            return $settings[$key] ?? $default;
        }

        $keys = explode('.', $key);
        $value = $settings;

        foreach ($keys as $k) {
            if (is_array($value) && isset($value[$k])) {
                $value = $value[$k];
            } else {
                return $default;
            }
        }

        return $value;
    }
}
