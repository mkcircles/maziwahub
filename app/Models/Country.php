<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

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

    public function regions(): HasMany
    {
        return $this->hasMany(Region::class);
    }

    public function districts(): HasManyThrough
    {
        return $this->hasManyThrough(District::class, Region::class);
    }
}
