<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MilkCollectionCenterClaim extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'milk_collection_center_id',
        'partner_id',
        'requested_by_user_id',
        'status',
        'message',
        'responded_by_user_id',
        'responded_at',
        'response_notes',
    ];

    protected $casts = [
        'responded_at' => 'datetime',
    ];

    public function milkCollectionCenter(): BelongsTo
    {
        return $this->belongsTo(MilkCollectionCenter::class);
    }

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by_user_id');
    }

    public function respondedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responded_by_user_id');
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function approve(?User $user = null, ?string $notes = null): void
    {
        $this->status = self::STATUS_APPROVED;
        $this->responded_by_user_id = $user?->id;
        $this->response_notes = $notes;
        $this->responded_at = now();
        $this->save();
    }

    public function reject(?User $user = null, ?string $notes = null): void
    {
        $this->status = self::STATUS_REJECTED;
        $this->responded_by_user_id = $user?->id;
        $this->response_notes = $notes;
        $this->responded_at = now();
        $this->save();
    }
}
