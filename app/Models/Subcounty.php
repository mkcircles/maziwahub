<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Subcounty extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'subcounty_name',
        'subcounty_slug',
        'subcounty_code',
        'county_id',
        'name',
        'slug',
    ];

    protected $hidden = [
        'subcounty_name',
        'subcounty_slug',
        'created_at',
        'updated_at',
    ];

    protected $appends = [
        'name',
        'slug',
    ];

    public function county(): BelongsTo
    {
        return $this->belongsTo(County::class);
    }

    public function parishes(): HasMany
    {
        return $this->hasMany(Parish::class);
    }

    public function villages(): HasManyThrough
    {
        return $this->hasManyThrough(Village::class, Parish::class);
    }

    public function getNameAttribute(): ?string
    {
        return $this->attributes['subcounty_name'] ?? null;
    }

    public function setNameAttribute(?string $value): void
    {
        $this->attributes['subcounty_name'] = $value;
    }

    public function getSlugAttribute(): ?string
    {
        return $this->attributes['subcounty_slug'] ?? null;
    }

    public function setSlugAttribute(?string $value): void
    {
        $this->attributes['subcounty_slug'] = $value;
    }
}
