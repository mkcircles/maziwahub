<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\Builder;
use App\Models\MilkCollectionCenter;
use App\Models\Farmer;
use App\Models\District;
use App\Models\Region;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
class Country extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'iso_code',
        'slug',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $withCount = [
        'regions',
        //'subcounties',
      //  'farmers',
    ];

    public function regions(): HasMany
    {
        return $this->hasMany(Region::class);
    }

    public function districts(): HasManyThrough
    {
        return $this->hasManyThrough(District::class, Region::class);
    }
    public function subcounties(): HasManyThrough
    {
        return $this->hasManyThrough(Subcounty::class, County::class);
    }
    public function parishes(): HasManyThrough
    {
        return $this->hasManyThrough(Parish::class, Subcounty::class);
    }

    /**
     * Get milk collection centers in this country
     * Note: This queries based on the location JSON field
     */
    public function milkCenters(): Builder
    {
        $districtIds = $this->districts()->pluck('districts.id')->toArray();

        if (empty($districtIds)) {
            return MilkCollectionCenter::query()->whereRaw('1 = 0');
        }

        $districtIdsAsStrings = array_map('strval', $districtIds);

        return MilkCollectionCenter::query()->where(function ($query) use ($districtIdsAsStrings) {
            foreach ($districtIdsAsStrings as $districtId) {
                $query->orWhereJsonContains('location->district', $districtId);
            }
        });
    }

    /**
     * Get farmers in this country
     * Farmers are linked through: Country -> Region -> District -> Farmer (via district field)
     * Since district is stored as a string (district ID), we use a custom relationship
     */
    public function farmers(): Builder
    {
        $districtIds = $this->districts()->pluck('districts.id')->toArray();

        if (empty($districtIds)) {
            return Farmer::query()->whereRaw('1 = 0');
        }

        $districtIdsAsStrings = array_map('strval', $districtIds);

        return Farmer::query()->whereIn('district', $districtIdsAsStrings);
    }
}
