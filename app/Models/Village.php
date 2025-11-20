<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Village extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'village_name',
        'village_slug',
        'village_code',
        'parish_id',
        'name',
        'slug',
    ];

    protected $hidden = [
        'village_name',
        'village_slug',
        'created_at',
        'updated_at',
    ];

    protected $appends = [
        'name',
        'slug',
    ];
    
    public function parish(): BelongsTo
    {
        return $this->belongsTo(Parish::class);
    }

    public function getNameAttribute(): ?string
    {
        return $this->attributes['village_name'] ?? null;
    }

    public function setNameAttribute(?string $value): void
    {
        $this->attributes['village_name'] = $value;
    }

    public function getSlugAttribute(): ?string
    {
        return $this->attributes['village_slug'] ?? null;
    }

    public function setSlugAttribute(?string $value): void
    {
        $this->attributes['village_slug'] = $value;
    }
}
