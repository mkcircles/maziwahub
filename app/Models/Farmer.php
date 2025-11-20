<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
        'herd_size',
        'grazing_type',
        'water_source',
        'primary_feeding_method_id',
        'supplemental_feeding_method_id',
        'feeding_last_changed_at',
        'feeding_metadata',
        'feeding_notes',
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
        'feeding_last_changed_at' => 'datetime',
        'feeding_metadata' => 'array',
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
                $location['district'] = $district->name;

                $region = $district->region;
                if ($region) {
                    $location['region'] = $region->name;
                    $country = $region->country;
                    if ($country) {
                        $location['country'] = $country->name;
                    }
                }
            }
        }
        if ($this->county) {
            $county = County::find($this->county);
            if ($county) {
                $location['county'] = $county->name;
            }
        }
        if ($this->sub_county) {
            $subcounty = Subcounty::find($this->sub_county);
            if ($subcounty) {
                $location['sub_county'] = $subcounty->name;
            }
        }
        if ($this->parish) {
            $parish = Parish::find($this->parish);
            if ($parish) {
                $location['parish'] = $parish->name;
            }
        }
        if ($this->village) {
            $village = Village::find($this->village);
            if ($village) {
                $location['village'] = $village->name;
            }
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

    public function primaryFeedingMethod(): BelongsTo
    {
        return $this->belongsTo(FeedingMethod::class, 'primary_feeding_method_id');
    }

    public function supplementalFeedingMethod(): BelongsTo
    {
        return $this->belongsTo(FeedingMethod::class, 'supplemental_feeding_method_id');
    }

    public function feedingHistories(): HasMany
    {
        return $this->hasMany(FarmerFeedingHistory::class)->orderByDesc('started_at')->orderByDesc('created_at');
    }

    public function activePrimaryFeedingHistory(): HasOne
    {
        return $this->hasOne(FarmerFeedingHistory::class)
            ->where('feeding_type', 'primary')
            ->whereNull('ended_at')
            ->latestOfMany('started_at');
    }

    public function activeSupplementalFeedingHistory(): HasOne
    {
        return $this->hasOne(FarmerFeedingHistory::class)
            ->where('feeding_type', 'supplemental')
            ->whereNull('ended_at')
            ->latestOfMany('started_at');
    }
}

