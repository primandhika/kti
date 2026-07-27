<?php

namespace App\Services\PoS;

use App\Models\User;
use App\Services\PointService;

class BuyerService
{
    /**
     * Get buyers data with membership info
     */
    public function getBuyers()
    {
        return User::whereHas('roles', function ($query) {
                $query->where('name', 'buyer');
            })
            ->with('membershipTier')
            ->select('id', 'name', 'member_code', 'email', 'phone', 'membership_tier_id', 'total_points')
            ->orderBy('name')
            ->get()
            ->map(function ($buyer) {
                $points = $buyer->total_points ?? 0;
                return [
                    'id' => $buyer->id,
                    'name' => $buyer->name,
                    'member_code' => $buyer->member_code,
                    'email' => $buyer->email,
                    'phone' => $buyer->phone,
                    'total_points' => $points,
                    'points_in_rupiah' => PointService::pointsToRupiah($points),
                    'tier' => $buyer->membershipTier ? [
                        'name' => $buyer->membershipTier->name,
                        'color' => $buyer->membershipTier->color,
                        'discount_percentage' => $buyer->membershipTier->discount_percentage,
                    ] : null,
                ];
            });
    }

    /**
     * Get buyer points information
     */
    public function getBuyerPoints($buyerId)
    {
        $buyer = User::with('membershipTier')->find($buyerId);

        if (!$buyer || !$buyer->hasRole('buyer')) {
            return null;
        }

        $points = $buyer->total_points ?? 0;

        return [
            'buyer' => [
                'id' => $buyer->id,
                'name' => $buyer->name,
                'member_code' => $buyer->member_code,
                'total_points' => $points,
                'points_in_rupiah' => PointService::pointsToRupiah($points),
                'exchange_rate' => PointService::getExchangeRate(),
                'tier' => $buyer->membershipTier ? [
                    'name' => $buyer->membershipTier->name,
                    'color' => $buyer->membershipTier->color,
                    'discount_percentage' => $buyer->membershipTier->discount_percentage,
                    'points_multiplier' => $buyer->membershipTier->points_multiplier,
                ] : null,
            ],
        ];
    }

    /**
     * Preview redeem points to rupiah discount
     */
    public function redeemPreview($buyerId, $pointsToRedeem)
    {
        $buyer = User::find($buyerId);

        if (!$buyer || !$buyer->hasRole('buyer')) {
            return null;
        }

        $availablePoints = $buyer->total_points ?? 0;

        if ($pointsToRedeem > $availablePoints) {
            return [
                'error' => 'Poin tidak mencukupi',
                'available_points' => $availablePoints,
                'requested_points' => $pointsToRedeem,
            ];
        }

        $discountRupiah = PointService::pointsToRupiah($pointsToRedeem);

        return [
            'points_to_redeem' => $pointsToRedeem,
            'discount_rupiah' => $discountRupiah,
            'remaining_points' => $availablePoints - $pointsToRedeem,
            'exchange_rate' => PointService::getExchangeRate(),
        ];
    }
}
