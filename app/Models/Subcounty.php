<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subcounty extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'county_id',
        'subcounty_code',
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
}
