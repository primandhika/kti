<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DataClerkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::updateOrCreate(
            ['username' => '27022026_clerk'],
            [
                'name' => 'Data Clerk',
                'username' => '27022026_clerk',
                'email' => 'clerk@bppu.ikipsiliwangi.ac.id',
                'phone' => null,
                'password' => Hash::make('Testing1'),
                'must_change_password' => false,
            ]
        );

        $user->assignRole('data_clerk');

        $this->command->info('Data Clerk user created successfully!');
        $this->command->info('Username: 27022026_clerk');
        $this->command->info('Password: Testing1');
    }
}
