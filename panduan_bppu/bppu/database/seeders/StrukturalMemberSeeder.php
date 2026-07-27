<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\MembershipTier;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class StrukturalMemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tsvFile = base_path('datastruktural.tsv');

        if (!file_exists($tsvFile)) {
            $this->command->error('File datastruktural.tsv tidak ditemukan!');
            return;
        }

        $buyerRole = Role::firstOrCreate(
            ['name' => 'buyer', 'guard_name' => 'web'],
            ['description' => 'Canteen member (pembeli) with ordering access and points collection.']
        );

        $bronzeTier = MembershipTier::where('slug', 'bronze')->first();

        if (!$bronzeTier) {
            $this->command->error('Bronze tier tidak ditemukan! Jalankan MembershipTierSeeder terlebih dahulu.');
            return;
        }

        $file = fopen($tsvFile, 'r');

        $count = 0;
        $skipped = 0;

        while (($row = fgetcsv($file, 0, "\t")) !== false) {
            if (empty($row[0]) || empty($row[1])) {
                $skipped++;
                continue;
            }

            $nomorInduk = trim($row[0]);
            $name = trim($row[1]);

            if (strlen($name) < 3) {
                $skipped++;
                continue;
            }

            if (User::where('member_code', $nomorInduk)->exists()) {
                $this->command->warn("Member code $nomorInduk sudah ada, skip: $name");
                $skipped++;
                continue;
            }

            if (User::where('username', $nomorInduk)->exists()) {
                $this->command->warn("Username $nomorInduk sudah ada, skip: $name");
                $skipped++;
                continue;
            }

            $email = $nomorInduk . '@struktural.ikipsiliwangi.ac.id';

            $user = User::create([
                'name' => $name,
                'username' => $nomorInduk,
                'email' => $email,
                'phone' => null,
                'password' => Hash::make('password123'),
                'member_code' => $nomorInduk,
                'membership_tier_id' => $bronzeTier->id,
                'member_since' => now(),
                'total_points' => 0,
                'must_change_password' => true,
            ]);

            $user->assignRole('buyer');

            $count++;
            $this->command->info("[$count] Created: $name ($nomorInduk)");
        }

        fclose($file);

        $this->command->info("Selesai! Total member struktural dibuat: $count");
        $this->command->info("Total skip: $skipped");
    }
}
