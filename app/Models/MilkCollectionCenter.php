<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'established_date' => 'date',
        'has_testing_equipment' => 'boolean',
        'has_washing_bay' => 'boolean',
        'location' => 'array',
    ];


    public function getAreaAttribute(): string
    {
        if(! $this->location) {
            return NULL;
        }
        else{
            $area = [];
            if(isset($this->location['country_id'])) {
                $area['country'] = Country::find($this->location['country_id'])->name;
            }
            if(isset($this->location['region_id'])) {
                $area['region'] = Region::find($this->location['region_id'])->name;
            }
            if(isset($this->location['district_id'])) {
                $area['district'] = District::find($this->location['district_id'])->name;
            }
            if(isset($this->location['county_id'])) {
                $area['county'] = County::find($this->location['county_id'])->name;
            }
            if(isset($this->location['subcounty_id'])) {
                $area['subcounty'] = Subcounty::find($this->location['subcounty_id'])->name;
            }
            if(isset($this->location['parish_id'])) {
                $area['parish'] = Parish::find($this->location['parish_id'])->name;
            }
            if(isset($this->location['village_id'])) {
                $area['village'] = Village::find($this->location['village_id'])->name;
            }
            return $area;
        }
    }
}
