<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed roles and permissions first
        $this->call(RolePermissionSeeder::class);

        // Then seed admin users with roles
        $this->call(AdminUserSeeder::class);

        // Seed menu categories and canteen menus
        $this->call(MenuCategorySeeder::class);
        $this->call(CanteenMenuSeeder::class);

        // Seed profil page
        $this->call(ProfilPageSeeder::class);
    }
}
