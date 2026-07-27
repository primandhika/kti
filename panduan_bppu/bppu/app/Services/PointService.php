<?php

namespace App\Services;

use App\Models\User;
use App\Models\MemberPoint;
use App\Models\PointRule;
use App\Models\Penjualan;
use App\Models\Setting;

class PointService
{
    /**
     * Ambil kurs poin ke rupiah dari Setting (bukan hardcode)
     */
    public static function getKurs(): int
    {
        return (int) Setting::get('kurs_poin_ke_rupiah', 10);
    }
    /**
     * Add points to user from transaction
     */
    public function addPointsFromTransaction(User $user, Penjualan $penjualan, int $amountBeforePointsRedeem = null)
    {
        if (!$user->hasRole('buyer')) {
            return null;
        }

        // Gunakan amount sebelum potongan poin agar tier rule tidak terpengaruh redeem
        $amount = $amountBeforePointsRedeem ?? $penjualan->total;

        // Get tier multiplier
        $multiplier = $user->getPointsMultiplier();

        // Calculate points
        $points = PointRule::calculatePoints($amount, $multiplier);

        if ($points <= 0) {
            return null;
        }

        // Create point record
        $memberPoint = MemberPoint::create([
            'user_id' => $user->id,
            'points' => $points,
            'type' => 'earn',
            'transaction_amount' => $amount,
            'penjualan_id' => $penjualan->id,
            'description' => "Mendapat {$points} poin dari transaksi Rp " . number_format($amount, 0, ',', '.'),
        ]);

        // Update user total points
        $user->total_points += $points;
        $user->save();

        // Check and update tier
        $user->updateMembershipTier();

        return $memberPoint;
    }

    /**
     * Redeem points for discount (convert points to rupiah)
     * 1 poin = Rp 10
     */
    public function redeemPointsForDiscount(User $user, $pointsToRedeem, Penjualan $penjualan = null)
    {
        if (!$user->hasRole('buyer')) {
            throw new \Exception('User bukan member buyer');
        }

        if ($user->total_points < $pointsToRedeem) {
            throw new \Exception('Poin tidak mencukupi');
        }

        if ($pointsToRedeem <= 0) {
            throw new \Exception('Jumlah poin harus lebih dari 0');
        }

        // Calculate rupiah discount pakai kurs dari Setting
        $discountRupiah = $pointsToRedeem * self::getKurs();

        // Create point record
        $memberPoint = MemberPoint::create([
            'user_id' => $user->id,
            'points' => -$pointsToRedeem,
            'type' => 'redeem',
            'transaction_amount' => $penjualan ? $penjualan->total : null,
            'penjualan_id' => $penjualan ? $penjualan->id : null,
            'description' => "Menukar {$pointsToRedeem} poin untuk potongan Rp " . number_format($discountRupiah, 0, ',', '.'),
        ]);

        // Update user total points
        $user->total_points -= $pointsToRedeem;
        $user->save();

        // Check and update tier (might downgrade)
        $user->updateMembershipTier();

        return [
            'member_point' => $memberPoint,
            'discount_rupiah' => $discountRupiah,
        ];
    }

    /**
     * Get points preview for amount
     */
    public function getPointsPreview($amount, User $user = null)
    {
        $multiplier = $user ? $user->getPointsMultiplier() : 1.0;
        $rule = PointRule::getByAmount($amount);

        if (!$rule) {
            return [
                'base_points' => 0,
                'multiplier' => $multiplier,
                'final_points' => 0,
                'rule_name' => null,
            ];
        }

        $basePoints = $rule->points;
        $finalPoints = floor($basePoints * $multiplier);

        return [
            'base_points' => $basePoints,
            'multiplier' => $multiplier,
            'final_points' => $finalPoints,
            'rule_name' => $rule->name,
            'rule_description' => $rule->description,
        ];
    }

    /**
     * Convert points to rupiah
     */
    public static function pointsToRupiah($points)
    {
        return $points * self::getKurs();
    }

    public static function rupiahToPoints($rupiah)
    {
        $kurs = self::getKurs();
        return $kurs > 0 ? (int) floor($rupiah / $kurs) : 0;
    }

    public static function getExchangeRate()
    {
        return self::getKurs();
    }
}
