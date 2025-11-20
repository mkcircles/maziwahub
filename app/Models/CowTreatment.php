<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CowTreatment extends Model
{
    use HasFactory;

    protected $fillable = [
        'cow_id',
        'vet_id',
        'recorded_by_id',
        'treatment_date',
        'diagnosis',
        'reason',
        'medication',
        'dosage',
        'dosage_unit',
        'route',
        'follow_up_date',
        'status',
        'outcome',
        'next_steps',
        'cost',
        'notes',
        'attachment_path',
        'life_cycle_status',
    ];

    protected $casts = [
        'treatment_date' => 'date',
        'follow_up_date' => 'date',
        'cost' => 'decimal:2',
    ];

    public function cow(): BelongsTo
    {
        return $this->belongsTo(Cow::class);
    }

    public function vet(): BelongsTo
    {
        return $this->belongsTo(Vet::class);
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by_id');
    }
}
