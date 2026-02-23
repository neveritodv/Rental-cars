<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class ClientController extends Model
{
    use HasFactory, SoftDeletes;
  protected $dates = ['deleted_at'];
    protected $table = 'clients';
    
    protected $fillable = [
        'agency_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'address',
        'city',
        'country',
        'nationality',
        'birth_date',
        'cin_number',
        'cin_valid_until',
        'passport_number',
        'passport_issue_date',
        'driving_license_number',
        'driving_license_issue_date',
        'status',
        'rating_average',
        'rating_count',
        'notes',
        'avatar',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'cin_valid_until' => 'date',
        'passport_issue_date' => 'date',
        'driving_license_issue_date' => 'date',
        'rating_average' => 'decimal:2',
        'rating_count' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getInitialsAttribute()
    {
        $first = substr($this->first_name, 0, 1);
        $last = substr($this->last_name, 0, 1);
        return strtoupper($first . $last);
    }

    public function getAvatarUrlAttribute()
    {
        if ($this->avatar && Storage::disk('public')->exists($this->avatar)) {
            return Storage::url($this->avatar);
        }
        return null;
    }

    public function hasAvatar()
    {
        return !is_null($this->avatar) && Storage::disk('public')->exists($this->avatar);
    }
}