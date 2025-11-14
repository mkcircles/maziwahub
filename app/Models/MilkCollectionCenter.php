<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MilkCollectionCenter extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'registration_number',
        'physical_address',
        'latitude',
        'longitude',
        'location',
        'established_date',
        'manager_name',
        'manager_phone',
        'staff_count',
        'power_source',
        'cooler_capacity_liters',
        'has_testing_equipment',
        'has_washing_bay',
        'partner_id',
    ];

    protected $appends = ['area'];
    protected $hidden = ['location'];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'established_date' => 'date',
        'has_testing_equipment' => 'boolean',
        'has_washing_bay' => 'boolean',
        'location' => 'array',
    ];

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function agents(): HasMany
    {
        return $this->hasMany(Agent::class);
    }

    public function farmers(): HasMany
    {
        return $this->hasMany(Farmer::class);
    }

    public function milkDeliveries(): HasMany
    {
        return $this->hasMany(MilkDelivery::class);
    }

    public function getAreaAttribute(): ?array
    {
        if (! $this->location) {
            return null;
        }

        $area = [];

        if (isset($this->location['country_id'])) {
            $area['country'] = optional(Country::find($this->location['country_id']))->name;
        }
        if (isset($this->location['region_id'])) {
            $area['region'] = optional(Region::find($this->location['region_id']))->name;
        }
        if (isset($this->location['district_id'])) {
            $area['district'] = optional(District::find($this->location['district_id']))->name;
        }
        if (isset($this->location['county_id'])) {
            $area['county'] = optional(County::find($this->location['county_id']))->name;
        }
        if (isset($this->location['subcounty_id'])) {
            $area['subcounty'] = optional(Subcounty::find($this->location['subcounty_id']))->name;
        }
        if (isset($this->location['parish_id'])) {
            $area['parish'] = optional(Parish::find($this->location['parish_id']))->name;
        }
        if (isset($this->location['village_id'])) {
            $area['village'] = optional(Village::find($this->location['village_id']))->name;
        }

        return array_filter($area);
    }
}
