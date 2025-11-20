<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Builder;
use App\Models\MilkCollectionCenter;
use App\Models\Farmer;

class Region extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'region_name',
        'region_slug',
        'country_id',
        'name',
        'slug',
    ];

    protected $hidden = [
        'region_name',
        'region_slug',
        'created_at',
        'updated_at',
    ];

    protected $appends = [
        'name',
        'slug',
    ];
    protected $withCount = [
        'districts',
        'counties',
    ];

    
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function districts(): HasMany
    {
        return $this->hasMany(District::class);
    }

    public function counties(): HasManyThrough
    {
        return $this->hasManyThrough(County::class, District::class);
    }

    public function subcounties(): HasManyThrough
    {
        return $this->hasManyThrough(Subcounty::class, County::class);
    }

    public function parishes(): HasManyThrough
    {
        return $this->hasManyThrough(Parish::class, Subcounty::class);
    }

    public function villages(): HasManyThrough
    {
        return $this->hasManyThrough(Village::class, Parish::class);
    }

    /**
     * Get milk collection centers in this region.
     * Uses JSON district names stored on the location column.
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
     * Get farmers in this region.
     * Farmer locations are stored as plain text district names.
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

    public function getNameAttribute(): ?string
    {
        return $this->attributes['region_name'] ?? null;
    }

    public function setNameAttribute(?string $value): void
    {
        $this->attributes['region_name'] = $value;
    }

    public function getSlugAttribute(): ?string
    {
        return $this->attributes['region_slug'] ?? null;
    }

    public function setSlugAttribute(?string $value): void
    {
        $this->attributes['region_slug'] = $value;
    }
}
