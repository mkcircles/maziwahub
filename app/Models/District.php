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
        'name',
        'region_id',
        'district_code',
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
}
