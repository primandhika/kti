<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MembershipTier;

class MembershipTierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tiers = [
            [
                'name' => 'Bronze',
                'slug' => 'bronze',
                'description' => 'Tier awal untuk member baru. Nikmati poin dari setiap transaksi.',
                'min_points' => 0,
                'discount_percentage' => 0,
                'points_multiplier' => 1.00,
                'order' => 1,
                'color' => '#CD7F32',
                'is_active' => true,
            ],
            [
                'name' => 'Silver',
                'slug' => 'silver',
                'description' => 'Member loyal dengan diskon 5% dan bonus poin 1.2x.',
                'min_points' => 100,
                'discount_percentage' => 5.00,
                'points_multiplier' => 1.20,
                'order' => 2,
                'color' => '#C0C0C0',
                'is_active' => true,
            ],
            [
                'name' => 'Gold',
                'slug' => 'gold',
                'description' => 'Member premium dengan diskon 10% dan bonus poin 1.5x.',
                'min_points' => 500,
                'discount_percentage' => 10.00,
                'points_multiplier' => 1.50,
                'order' => 3,
                'color' => '#FFD700',
                'is_active' => true,
            ],
            [
                'name' => 'Platinum',
                'slug' => 'platinum',
                'description' => 'Member VIP dengan diskon 15% dan bonus poin 2x.',
                'min_points' => 1000,
                'discount_percentage' => 15.00,
                'points_multiplier' => 2.00,
                'order' => 4,
                'color' => '#E5E4E2',
                'is_active' => true,
            ],
        ];

        foreach ($tiers as $tier) {
            MembershipTier::updateOrCreate(
                ['slug' => $tier['slug']],
                $tier
            );
        }

        $this->command->info('Membership tiers created successfully!');
    }
}
