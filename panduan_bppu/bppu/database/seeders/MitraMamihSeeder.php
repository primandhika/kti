<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class MitraMamihSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create mitra_mamih account
        $user = User::updateOrCreate(
            ['username' => 'mitra_mamih'],
            [
                'name' => 'Mitra Mamih',
                'username' => 'mitra_mamih',
                'email' => 'mitra.mamih@example.com',
                'password' => Hash::make('buatbaru123'),
                'must_change_password' => false,
            ]
        );

        // Assign tenant role
        $user->syncRoles(['tenant']);

        $this->command->info('✓ Mitra Mamih account created successfully');
        $this->command->info('  Username: mitra_mamih');
        $this->command->info('  Password: buatbaru123');
        $this->command->info('  Role: tenant');
    }
}
