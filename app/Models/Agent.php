<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Agent extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;
      protected $dates = ['deleted_at'];

    protected $fillable = [
        'agency_id',
        'user_id',
        'full_name',
        'email',
        'phone',
        'notes',
        'avatar',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /* =======================
     |  MEDIA LIBRARY
     ======================= */

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('agent_avatar')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'])
            ->useFallbackUrl('/images/placeholder-agent.jpg')
            ->useFallbackPath('/images/placeholder-agent.jpg');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(100)
            ->height(100)
            ->sharpen(10)
            ->nonQueued();
    }

    /* =======================
     |  ACCESSORS
     ======================= */

    public function getAvatarUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('agent_avatar') ?: null;
    }

    public function getAvatarThumbUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('agent_avatar', 'thumb') ?: null;
    }

    public function getAvatarMediaAttribute()
    {
        return $this->getFirstMedia('agent_avatar');
    }

    public function getAvatarInitialsAttribute()
    {
        return strtoupper(mb_substr($this->full_name, 0, 2));
    }
}
