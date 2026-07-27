<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\MembershipTier;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class BuyerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Path to CSV file
        $csvFile = base_path('dbMember.csv');

        if (!file_exists($csvFile)) {
            $this->command->error('File dbMember.csv tidak ditemukan!');
            return;
        }

        // Ensure buyer role exists
        $buyerRole = Role::firstOrCreate(
            ['name' => 'buyer', 'guard_name' => 'web'],
            ['description' => 'Canteen member (pembeli) with ordering access and points collection.']
        );

        // Get Bronze tier (default tier)
        $bronzeTier = MembershipTier::where('slug', 'bronze')->first();

        if (!$bronzeTier) {
            $this->command->error('Bronze tier tidak ditemukan! Jalankan MembershipTierSeeder terlebih dahulu.');
            return;
        }

        // Open CSV file
        $file = fopen($csvFile, 'r');

        // Skip header row
        $header = fgetcsv($file);

        $count = 0;
        $skipped = 0;

        while (($row = fgetcsv($file)) !== false) {
            // Skip empty rows
            if (empty($row[0]) || empty($row[1])) {
                $skipped++;
                continue;
            }

            $memberId = trim($row[0]);
            $name = trim($row[1]);

            // Clean up member ID (remove quotes and single quotes)
            $memberId = str_replace(["'", '"'], '', $memberId);

            // Skip if name is too short or just "Dr."
            if (strlen($name) < 3 || $name === 'Dr.') {
                $skipped++;
                continue;
            }

            // Generate username from name (lowercase, no spaces, no special chars)
            $username = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $name));
            $username = substr($username, 0, 20); // Limit to 20 chars

            // Make username unique if needed
            $baseUsername = $username;
            $counter = 1;
            while (User::where('username', $username)->exists()) {
                $username = $baseUsername . $counter;
                $counter++;
            }

            // Generate email
            $email = $username . '@member.ikipsiliwangi.ac.id';

            // Skip if member_code already exists
            if (User::where('member_code', $memberId)->exists()) {
                $this->command->warn("Member ID $memberId sudah ada, skip: $name");
                $skipped++;
                continue;
            }

            // Create user
            $user = User::create([
                'name' => $name,
                'username' => $username,
                'email' => $email,
                'phone' => null, // Bisa diisi manual nanti
                'password' => Hash::make('password123'), // Default password
                'member_code' => $memberId,
                'membership_tier_id' => $bronzeTier->id,
                'member_since' => now(),
                'total_points' => 0,
                'must_change_password' => true,
            ]);

            // Assign buyer role
            $user->assignRole('buyer');

            $count++;
            $this->command->info("[$count] Created: $name ($memberId)");
        }

        fclose($file);

        $this->command->info("Selesai! Total buyer dibuat: $count");
        $this->command->info("Total skip: $skipped");
    }
}
