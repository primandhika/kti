<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PointRule extends Model
{
    protected $fillable = [
        'name',
        'min_amount',
        'max_amount',
        'points',
        'description',
        'order',
        'is_active',
    ];

    protected $casts = [
        'min_amount' => 'integer',
        'max_amount' => 'integer',
        'points' => 'integer',
        'order' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get point rule by transaction amount
     */
    public static function getByAmount($amount)
    {
        return self::where('is_active', true)
            ->where('min_amount', '<=', $amount)
            ->where(function ($query) use ($amount) {
                $query->whereNull('max_amount')
                    ->orWhere('max_amount', '>=', $amount);
            })
            ->orderBy('order', 'asc')
            ->first();
    }

    /**
     * Calculate points for amount with tier multiplier
     */
    public static function calculatePoints($amount, $tierMultiplier = 1.0)
    {
        $rule = self::getByAmount($amount);

        if (!$rule) {
            return 0;
        }

        $basePoints = $rule->points;
        $finalPoints = floor($basePoints * $tierMultiplier);

        return $finalPoints;
    }
}
