<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MilkDelivery extends Model
{
    use HasFactory;

    protected $fillable = [
        'farmer_id',
        'milk_collection_center_id',
        'delivery_date',
        'volume_liters',
        'quality_grade',
        'fat_content',
        'price_per_liter',
        'total_amount',
        'recorded_by',
        'notes',
    ];

    protected $casts = [
        'delivery_date' => 'date',
        'volume_liters' => 'decimal:2',
        'fat_content' => 'decimal:2',
        'price_per_liter' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function farmer(): BelongsTo
    {
        return $this->belongsTo(Farmer::class);
    }

    public function milkCollectionCenter(): BelongsTo
    {
        return $this->belongsTo(MilkCollectionCenter::class);
    }
}
