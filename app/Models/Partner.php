<?php

namespace App\Models;

use App\Models\MilkCollectionCenterClaim;
use App\Models\PartnerInvitation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Partner extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'registration_number',
        'description',
        'is_active',
        'contact_name',
        'contact_title',
        'website',
        'country',
        'city',
        'postal_code',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
    
 

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function agents(): HasMany
    {
        return $this->hasMany(Agent::class);
    }

    public function milkCollectionCenters(): HasMany
    {
        return $this->hasMany(MilkCollectionCenter::class);
    }

    public function invitations(): HasMany
    {
        return $this->hasMany(PartnerInvitation::class);
    }

    public function milkCollectionCenterClaims(): HasMany
    {
        return $this->hasMany(MilkCollectionCenterClaim::class);
    }
}

