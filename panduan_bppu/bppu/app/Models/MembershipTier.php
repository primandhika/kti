<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MembershipTier extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'min_points',
        'discount_percentage',
        'points_multiplier',
        'order',
        'color',
        'is_active',
    ];

    protected $casts = [
        'min_points' => 'integer',
        'discount_percentage' => 'decimal:2',
        'points_multiplier' => 'decimal:2',
        'order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'membership_tier_id');
    }

    public static function determineByPoints($points)
    {
        return self::where('is_active', true)
            ->where('min_points', '<=', $points)
            ->orderBy('min_points', 'desc')
            ->first();
    }
}
