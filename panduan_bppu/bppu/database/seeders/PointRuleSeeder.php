<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PointRule;

class PointRuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rules = [
            [
                'name' => 'Transaksi Kecil',
                'min_amount' => 0,
                'max_amount' => 4999.99,
                'points' => 1,
                'description' => 'Transaksi di bawah Rp 5.000 mendapat 1 poin',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Transaksi Sedang',
                'min_amount' => 5000,
                'max_amount' => 9999.99,
                'points' => 5,
                'description' => 'Transaksi Rp 5.000 - Rp 9.999 mendapat 5 poin',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Transaksi Menengah',
                'min_amount' => 10000,
                'max_amount' => 24999.99,
                'points' => 15,
                'description' => 'Transaksi Rp 10.000 - Rp 24.999 mendapat 15 poin',
                'order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Transaksi Besar',
                'min_amount' => 25000,
                'max_amount' => 49999.99,
                'points' => 40,
                'description' => 'Transaksi Rp 25.000 - Rp 49.999 mendapat 40 poin',
                'order' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Transaksi VIP',
                'min_amount' => 50000,
                'max_amount' => null,
                'points' => 100,
                'description' => 'Transaksi Rp 50.000 ke atas mendapat 100 poin (BONUS!)',
                'order' => 5,
                'is_active' => true,
            ],
        ];

        foreach ($rules as $rule) {
            PointRule::updateOrCreate(
                ['name' => $rule['name']],
                $rule
            );
        }

        $this->command->info('Point rules created successfully!');
        $this->command->info('');
        $this->command->info('Skema Poin:');
        $this->command->info('- < Rp 5.000: 1 poin');
        $this->command->info('- Rp 5.000 - 9.999: 5 poin');
        $this->command->info('- Rp 10.000 - 24.999: 15 poin');
        $this->command->info('- Rp 25.000 - 49.999: 40 poin');
        $this->command->info('- ≥ Rp 50.000: 100 poin');
        $this->command->info('');
        $this->command->info('Poin akan dikalikan dengan tier multiplier:');
        $this->command->info('- Bronze: 1.0x');
        $this->command->info('- Silver: 1.2x');
        $this->command->info('- Gold: 1.5x');
        $this->command->info('- Platinum: 2.0x');
    }
}
