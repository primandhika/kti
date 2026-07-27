<?php

namespace Database\Seeders;

use App\Models\MenuCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MenuCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Aneka Nasi',
                'slug' => 'aneka-nasi',
                'description' => 'Menu nasi dengan berbagai lauk pilihan',
                'icon' => 'square-3-stack-3d',
                'display_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Cemilan',
                'slug' => 'cemilan',
                'description' => 'Aneka cemilan dan snack',
                'icon' => 'shopping-bag',
                'display_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Makanan Manis',
                'slug' => 'makanan-manis',
                'description' => 'Dessert dan makanan manis',
                'icon' => 'cake',
                'display_order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'A La Carte',
                'slug' => 'a-la-carte',
                'description' => 'Menu spesial dan makanan berat',
                'icon' => 'fire',
                'display_order' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Minuman',
                'slug' => 'minuman',
                'description' => 'Aneka minuman segar',
                'icon' => 'beaker',
                'display_order' => 5,
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            MenuCategory::create($category);
        }
    }
}
