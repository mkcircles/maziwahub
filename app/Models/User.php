<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'user_type',
        'milk_collection_center_id',
        'partner_id',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    // Relationships
    public function milkCollectionCenter()
    {
        return $this->belongsTo(MilkCollectionCenter::class);
    }

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    public function agent()
    {
        return $this->hasOne(Agent::class);
    }

    // Role checking methods
    public function isSuperAdmin(): bool
    {
        return $this->user_type === 'super_admin';
    }

    public function isAdmin(): bool
    {
        return $this->user_type === 'admin';
    }

    public function isPartner(): bool
    {
        return $this->user_type === 'partner';
    }

    public function isMcc(): bool
    {
        return $this->user_type === 'mcc';
    }

    public function isUser(): bool
    {
        return $this->user_type === 'user';
    }

    public function isAgent(): bool
    {
        return $this->user_type === 'agent';
    }

    public function isAdminOrSuperAdmin(): bool
    {
        return $this->isSuperAdmin() || $this->isAdmin();
    }

    public function canManageUsers(): bool
    {
        return $this->isSuperAdmin();
    }

    public function canCreateAdmins(): bool
    {
        return $this->isSuperAdmin();
    }
}
