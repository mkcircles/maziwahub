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
        'name',
        'district_id',
        'county_code',
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
}
