<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ShopItem;

class ShopItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            [
                'name' => 'Mug IKIP Siliwangi',
                'description' => 'Mug keramik berkualitas tinggi dengan logo IKIP Siliwangi. Cocok untuk minum kopi atau teh favorit Anda. Kapasitas 350ml dengan desain elegant.',
                'category' => 'Souvenir',
                'price' => 45000,
                'stock' => 25,
                'image' => 'https://placehold.co/400x300/996600/FFFFFF/png?text=Mug+IKIP+Siliwangi',
                'is_active' => true,
                'display_order' => 1,
            ],
            [
                'name' => 'Pin IKIP Siliwangi',
                'description' => 'Pin enamel premium dengan logo IKIP Siliwangi. Material berkualitas dengan finishing glossy. Cocok untuk dikoleksi atau sebagai hadiah.',
                'category' => 'Souvenir',
                'price' => 15000,
                'stock' => 50,
                'image' => 'https://placehold.co/400x300/996600/FFFFFF/png?text=Pin+IKIP+Siliwangi',
                'is_active' => true,
                'display_order' => 2,
            ],
            [
                'name' => 'Tas Belanja IKIP Siliwangi',
                'description' => 'Tas belanja dari bahan kanvas tebal dan kuat dengan logo IKIP Siliwangi. Ramah lingkungan dan dapat digunakan berulang kali. Ukuran 40x35cm.',
                'category' => 'Souvenir',
                'price' => 35000,
                'stock' => 30,
                'image' => 'https://placehold.co/400x300/996600/FFFFFF/png?text=Tas+Belanja+IKIP',
                'is_active' => true,
                'display_order' => 3,
            ],
        ];

        foreach ($items as $item) {
            ShopItem::create($item);
        }
    }
}
