<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FeedingMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'category',
        'description',
        'requires_details',
        'metadata',
        'is_active',
    ];

    protected $casts = [
        'metadata' => 'array',
        'requires_details' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function primaryFarmers(): HasMany
    {
        return $this->hasMany(Farmer::class, 'primary_feeding_method_id');
    }

    public function supplementalFarmers(): HasMany
    {
        return $this->hasMany(Farmer::class, 'supplemental_feeding_method_id');
    }

    public function feedingHistories(): HasMany
    {
        return $this->hasMany(FarmerFeedingHistory::class);
    }
}


