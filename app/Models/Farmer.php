<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Farmer extends Model
{
    use HasFactory;

    protected $fillable = [
        'farmer_id',
        'first_name',
        'last_name',
        'dob',
        'gender',
        'educational_level',
        'phone_number',
        'id_number',
        'marital_status',
        'district',
        'county',
        'sub_county',
        'parish',
        'village',
        'next_of_kin',
        'next_of_kin_contact',
        'next_of_kin_relationship',
        'no_of_household_members',
        'registered_by',
        'registered_by_agent_id',
        'milk_collection_center_id',
        'photo',
        'coordinates',
        'status',
        'validation_reason',
        'reg_type',
        'household_head',
        'family_size',
        'male_members',
        'female_members',
        'above_18',
        'below_5',
        'financial_instrument',
        'available_energy_source',
        'farm_size',
        'land_under_use',
        'land_ownership',
        'farming_type',
        'crop_production',
        'animal_production',
        'is_farmer_insured',
        'support_needed',
    ];

    protected $casts = [
        'dob' => 'date',
        'coordinates' => 'array',
        'household_head' => 'boolean',
        'family_size' => 'integer',
        'male_members' => 'integer',
        'female_members' => 'integer',
        'above_18' => 'integer',
        'below_5' => 'integer',
        'farm_size' => 'decimal:2',
        'land_under_use' => 'decimal:2',
        'is_farmer_insured' => 'boolean',
    ];
    protected $appends = [
        'location',
    ];

    public function getLocationAttribute(): array
    {
        $location = [];
        if ($this->district) {
            $district = District::find($this->district);
            if ($district) {
                $location['country'] = $district->region->country->name;
                $location['region'] = $district->region->region_name;
                $location['district'] = $district->district_name;
            }
        }
        if ($this->county) {
            $county = County::find($this->county);
            if ($county) {
                $location['county'] = $county->county_name;
            }
        }
        if ($this->sub_county) {
            $location['sub_county'] = Subcounty::find($this->sub_county)->subcounty_name;
        }
        if ($this->parish) {
            $location['parish'] = Parish::find($this->parish)->parish_name;
        }
        if ($this->village) {
            $location['village'] = Village::find($this->village)->village_name;
        }
        return $location;
    }

    public function milkCollectionCenter(): BelongsTo
    {
        return $this->belongsTo(MilkCollectionCenter::class);
    }

    public function registeredByAgent(): BelongsTo
    {
        return $this->belongsTo(Agent::class, 'registered_by_agent_id');
    }

    public function cows(): HasMany
    {
        return $this->hasMany(Cow::class);
    }

    public function cowMilkProductions(): HasManyThrough
    {
        return $this->hasManyThrough(CowMilkProduction::class, Cow::class);
    }

    public function milkDeliveries(): HasMany
    {
        return $this->hasMany(MilkDelivery::class);
    }
}

