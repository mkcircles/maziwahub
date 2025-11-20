<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vet extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'license_number',
        'license_expiry_date',
        'phone_number',
        'email',
        'specialization',
        'employer',
        'milk_collection_center_id',
        'bio',
        'is_active',
    ];

    protected $casts = [
        'license_expiry_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function milkCollectionCenter(): BelongsTo
    {
        return $this->belongsTo(MilkCollectionCenter::class);
    }

    public function treatments(): HasMany
    {
        return $this->hasMany(CowTreatment::class);
    }

    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }
}
