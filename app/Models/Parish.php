<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Parish extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'parish_name',
        'parish_slug',
        'parish_code',
        'subcounty_id',
        'name',
        'slug',
    ];

    protected $hidden = [
        'parish_name',
        'parish_slug',
        'created_at',
        'updated_at',
    ];

    protected $appends = [
        'name',
        'slug',
    ];

    public function subcounty(): BelongsTo
    {
        return $this->belongsTo(Subcounty::class);
    }

    public function villages(): HasMany
    {
        return $this->hasMany(Village::class);
    }

    public function getNameAttribute(): ?string
    {
        return $this->attributes['parish_name'] ?? null;
    }

    public function setNameAttribute(?string $value): void
    {
        $this->attributes['parish_name'] = $value;
    }

    public function getSlugAttribute(): ?string
    {
        return $this->attributes['parish_slug'] ?? null;
    }

    public function setSlugAttribute(?string $value): void
    {
        $this->attributes['parish_slug'] = $value;
    }
}
