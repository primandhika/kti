<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admins = [
            [
                'name' => 'Restu',
                'username' => 'office_restu',
                'email' => 'restu@ikipsiliwangi.ac.id',
                'password' => Hash::make('Testing1'),
                'must_change_password' => true,
            ],
            [
                'name' => 'Yana',
                'username' => 'office_yana',
                'email' => 'yana@ikipsiliwangi.ac.id',
                'password' => Hash::make('Testing1'),
                'must_change_password' => true,
            ],
            [
                'name' => 'Ratna Dwi Nur',
                'username' => 'office_ratna',
                'email' => 'ratnadwinur@ikipsiliwangi.ac.id',
                'password' => Hash::make('Testing1'),
                'must_change_password' => true,
            ],
        ];

        foreach ($admins as $admin) {
            $user = User::updateOrCreate(
                ['email' => $admin['email']],
                $admin
            );

            // Assign officer role to all office users
            if (!$user->hasRole('officer')) {
                $user->assignRole('officer');
            }
        }

        $this->command->info('Admin users created successfully with officer role!');
    }
}
