<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cow extends Model
{
    use HasFactory;

    protected $fillable = [
        'farmer_id',
        'tag_number',
        'breed',
        'date_of_birth',
        'acquired_date',
        'milk_capacity_liters',
        'health_status',
        'notes',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'acquired_date' => 'date',
        'milk_capacity_liters' => 'decimal:2',
    ];

    public function farmer(): BelongsTo
    {
        return $this->belongsTo(Farmer::class);
    }

    public function milkProductions(): HasMany
    {
        return $this->hasMany(CowMilkProduction::class);
    }
}
