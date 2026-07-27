<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'phone',
        'password',
        'must_change_password',
        'member_code',
        'membership_tier_id',
        'total_points',
        'member_since',
        'default_meja_id',
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
            'must_change_password' => 'boolean',
            'member_since' => 'date',
        ];
    }

    /**
     * Get default meja (titik antar) user
     */
    public function defaultMeja()
    {
        return $this->belongsTo(\App\Models\Meja::class, 'default_meja_id');
    }

    /**
     * Get work units yang ditangani oleh user ini
     */
    public function workUnits()
    {
        return $this->belongsToMany(WorkUnit::class, 'user_work_unit');
    }

    /**
     * Get penjualan yang dilakukan oleh user ini
     */
    public function penjualans()
    {
        return $this->hasMany(Penjualan::class);
    }

    /**
     * Get member points history
     */
    public function memberPoints()
    {
        return $this->hasMany(MemberPoint::class);
    }

    /**
     * Get membership tier
     */
    public function membershipTier()
    {
        return $this->belongsTo(MembershipTier::class);
    }

    /**
     * Check if user can access a specific work unit
     */
    public function canAccessWorkUnit($workUnitId)
    {
        // Sysadmin can access all work units
        if ($this->hasRole('sysadmin')) {
            return true;
        }

        // Check if user is assigned to this work unit
        return $this->workUnits()->where('work_units.id', $workUnitId)->exists();
    }

    /**
     * Check if user is a member (buyer role)
     */
    public function isMember()
    {
        return $this->hasRole('buyer');
    }

    /**
     * Get member's available points
     */
    public function getAvailablePoints()
    {
        return $this->total_points ?? 0;
    }

    /**
     * Update membership tier based on total points
     */
    public function updateMembershipTier()
    {
        if (!$this->hasRole('buyer')) {
            return;
        }

        $tier = MembershipTier::determineByPoints($this->total_points);

        if ($tier && $this->membership_tier_id !== $tier->id) {
            $this->membership_tier_id = $tier->id;
            $this->save();
        }
    }

    /**
     * Get discount percentage from membership tier
     */
    public function getDiscountPercentage()
    {
        return $this->membershipTier ? $this->membershipTier->discount_percentage : 0;
    }

    /**
     * Get points multiplier from membership tier
     */
    public function getPointsMultiplier()
    {
        return $this->membershipTier ? $this->membershipTier->points_multiplier : 1.00;
    }
}
