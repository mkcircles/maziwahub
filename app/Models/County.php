<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class County extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'county_name',
        'county_slug',
        'county_code',
        'district_id',
        'name',
        'slug',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'county_name',
        'county_slug',
        'created_at',
        'updated_at',
    ];

    protected $appends = [
        'name',
        'slug',
    ];

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function subcounties(): HasMany
    {
        return $this->hasMany(Subcounty::class);
    }

    public function parishes(): HasManyThrough
    {
        return $this->hasManyThrough(Parish::class, Subcounty::class);
    }

    public function villages(): HasManyThrough
    {
        return $this->hasManyThrough(Village::class, Parish::class);
    }

    public function getNameAttribute(): ?string
    {
        return $this->attributes['county_name'] ?? null;
    }

    public function setNameAttribute(?string $value): void
    {
        $this->attributes['county_name'] = $value;
    }

    public function getSlugAttribute(): ?string
    {
        return $this->attributes['county_slug'] ?? null;
    }

    public function setSlugAttribute(?string $value): void
    {
        $this->attributes['county_slug'] = $value;
    }
}
