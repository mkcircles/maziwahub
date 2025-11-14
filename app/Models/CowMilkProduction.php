<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CowMilkProduction extends Model
{
    use HasFactory;

    protected $fillable = [
        'cow_id',
        'recorded_date',
        'morning_volume_liters',
        'midday_volume_liters',
        'evening_volume_liters',
        'total_volume_liters',
        'recorded_by',
        'notes',
    ];

    protected $casts = [
        'recorded_date' => 'date',
        'morning_volume_liters' => 'decimal:2',
        'midday_volume_liters' => 'decimal:2',
        'evening_volume_liters' => 'decimal:2',
        'total_volume_liters' => 'decimal:2',
    ];

    public function cow(): BelongsTo
    {
        return $this->belongsTo(Cow::class);
    }
}
