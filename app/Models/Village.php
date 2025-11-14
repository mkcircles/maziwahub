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
        'parish_id',
        'village_code',
        'village_slug',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];
    
    public function parish(): BelongsTo
    {
        return $this->belongsTo(Parish::class);
    }
}
