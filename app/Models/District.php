<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class District extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'district_name',
        'district_slug',
        'district_code',
        'region_id',
        'name',
        'slug',
    ];

    protected $hidden = [
        'district_name',
        'district_slug',
        'created_at',
        'updated_at',
    ];

    protected $appends = [
        'name',
        'slug',
    ];


    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function counties(): HasMany
    {
        return $this->hasMany(County::class);
    }

    public function subcounties(): HasManyThrough
    {
        return $this->hasManyThrough(Subcounty::class, County::class);
    }

    public function villages(): HasManyThrough
    {
        return $this->hasManyThrough(Village::class, Parish::class);
    }

    public function getNameAttribute(): ?string
    {
        return $this->attributes['district_name'] ?? null;
    }

    public function setNameAttribute(?string $value): void
    {
        $this->attributes['district_name'] = $value;
    }

    public function getSlugAttribute(): ?string
    {
        return $this->attributes['district_slug'] ?? null;
    }

    public function setSlugAttribute(?string $value): void
    {
        $this->attributes['district_slug'] = $value;
    }
}
