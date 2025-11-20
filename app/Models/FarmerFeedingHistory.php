<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FarmerFeedingHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'farmer_id',
        'feeding_method_id',
        'feeding_type',
        'started_at',
        'ended_at',
        'notes',
        'metadata',
        'recorded_by_id',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function farmer(): BelongsTo
    {
        return $this->belongsTo(Farmer::class);
    }

    public function feedingMethod(): BelongsTo
    {
        return $this->belongsTo(FeedingMethod::class);
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by_id');
    }
}


