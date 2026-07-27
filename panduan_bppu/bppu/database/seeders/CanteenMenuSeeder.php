<?php

namespace Database\Seeders;

use App\Models\CanteenMenu;
use App\Models\MenuCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CanteenMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $anekaNasi = MenuCategory::where('slug', 'aneka-nasi')->first();
        $cemilan = MenuCategory::where('slug', 'cemilan')->first();
        $makananManis = MenuCategory::where('slug', 'makanan-manis')->first();
        $alaCarte = MenuCategory::where('slug', 'a-la-carte')->first();
        $minuman = MenuCategory::where('slug', 'minuman')->first();

        $menus = [
            // Aneka Nasi
            [
                'category_id' => $anekaNasi->id,
                'name' => 'Nasi Ayam Goreng',
                'description' => 'Ayam goreng crispy dengan bumbu rempah pilihan, disajikan dengan nasi hangat dan sambal',
                'price' => 15000,
                'image' => 'https://placehold.co/600x400/e8a838/ffffff?text=Ayam+Goreng',
                'badges' => [
                    [
                        'text' => 'Populer',
                        'color' => 'orange',
                        'icon' => 'fire'
                    ]
                ],
                'available_days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'],
                'is_active' => true,
                'display_order' => 1,
            ],
            [
                'category_id' => $anekaNasi->id,
                'name' => 'Nasi Rendang',
                'description' => 'Rendang daging sapi empuk dengan kuah bumbu rempah khas Minang, nikmat disantap dengan nasi panas',
                'price' => 18000,
                'image' => 'https://placehold.co/600x400/92400e/ffffff?text=Rendang',
                'badges' => [
                    [
                        'text' => 'Rekomendasi',
                        'color' => 'blue',
                        'icon' => 'star'
                    ]
                ],
                'available_days' => ['monday', 'wednesday', 'friday'],
                'is_active' => true,
                'display_order' => 2,
            ],
            [
                'category_id' => $anekaNasi->id,
                'name' => 'Nasi Ikan Bandeng',
                'description' => 'Ikan bandeng segar digoreng garing dengan bumbu kuning, cocok untuk menu makan siang',
                'price' => 20000,
                'image' => 'https://placehold.co/600x400/16a34a/ffffff?text=Ikan+Bandeng',
                'badges' => [],
                'available_days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'],
                'is_active' => true,
                'display_order' => 3,
            ],
            [
                'category_id' => $anekaNasi->id,
                'name' => 'Nasi Pecel',
                'description' => 'Nasi dengan sayuran rebus, rempeyek, dan bumbu pecel kacang yang gurih dan pedas',
                'price' => 10000,
                'image' => 'https://placehold.co/600x400/65a30d/ffffff?text=Nasi+Pecel',
                'badges' => [
                    [
                        'text' => 'Hemat',
                        'color' => 'green',
                        'icon' => 'currency-dollar'
                    ]
                ],
                'available_days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'],
                'is_active' => true,
                'display_order' => 4,
            ],
            [
                'category_id' => $anekaNasi->id,
                'name' => 'Nasi Goreng Spesial',
                'description' => 'Nasi goreng dengan telur, ayam, bakso, dan sayuran, bumbu gurih dan sedikit pedas',
                'price' => 14000,
                'image' => 'https://placehold.co/600x400/ea580c/ffffff?text=Nasi+Goreng',
                'badges' => [],
                'available_days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'],
                'is_active' => true,
                'display_order' => 5,
            ],

            // A La Carte
            [
                'category_id' => $alaCarte->id,
                'name' => 'Soto Madura',
                'description' => 'Soto khas Madura dengan kuah bening yang segar, dilengkapi daging sapi empuk dan koya',
                'price' => 18000,
                'image' => 'https://placehold.co/600x400/e85d04/ffffff?text=Soto+Madura',
                'badges' => [
                    [
                        'text' => 'Hanya Jumat',
                        'color' => 'purple',
                        'icon' => 'calendar'
                    ]
                ],
                'available_days' => ['friday'],
                'is_active' => true,
                'display_order' => 1,
            ],
            [
                'category_id' => $alaCarte->id,
                'name' => 'Mie Tek-Tek',
                'description' => 'Mie goreng spesial dengan topping telur, sayuran segar dan bumbu rahasia yang menggugah selera',
                'price' => 12000,
                'image' => 'https://placehold.co/600x400/dc2626/ffffff?text=Mie+Tek-Tek',
                'badges' => [
                    [
                        'text' => 'Rekomendasi',
                        'color' => 'blue',
                        'icon' => 'star'
                    ]
                ],
                'available_days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'],
                'is_active' => true,
                'display_order' => 2,
            ],
            [
                'category_id' => $alaCarte->id,
                'name' => 'Bakso Spesial',
                'description' => 'Bakso sapi kenyal dengan mie, tahu, dan kuah kaldu sapi yang gurih',
                'price' => 13000,
                'image' => 'https://placehold.co/600x400/7c2d12/ffffff?text=Bakso',
                'badges' => [],
                'available_days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'],
                'is_active' => true,
                'display_order' => 3,
            ],
            [
                'category_id' => $alaCarte->id,
                'name' => 'Gado-Gado',
                'description' => 'Sayuran segar dengan bumbu kacang, lontong, telur rebus dan kerupuk',
                'price' => 12000,
                'image' => 'https://placehold.co/600x400/15803d/ffffff?text=Gado-Gado',
                'badges' => [],
                'available_days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'],
                'is_active' => true,
                'display_order' => 4,
            ],

            // Cemilan
            [
                'category_id' => $cemilan->id,
                'name' => 'Pisang Goreng',
                'description' => 'Pisang goreng crispy dengan taburan gula atau coklat meses',
                'price' => 5000,
                'image' => 'https://placehold.co/600x400/fbbf24/ffffff?text=Pisang+Goreng',
                'badges' => [
                    [
                        'text' => 'Hemat',
                        'color' => 'green',
                        'icon' => 'currency-dollar'
                    ]
                ],
                'available_days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'],
                'is_active' => true,
                'display_order' => 1,
            ],
            [
                'category_id' => $cemilan->id,
                'name' => 'Tahu Isi',
                'description' => 'Tahu goreng isi sayuran dengan saus kacang pedas',
                'price' => 6000,
                'image' => 'https://placehold.co/600x400/d97706/ffffff?text=Tahu+Isi',
                'badges' => [],
                'available_days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'],
                'is_active' => true,
                'display_order' => 2,
            ],
            [
                'category_id' => $cemilan->id,
                'name' => 'Cireng',
                'description' => 'Cireng goreng crispy dengan isian bumbu pedas, cocok untuk camilan',
                'price' => 5000,
                'image' => 'https://placehold.co/600x400/f59e0b/ffffff?text=Cireng',
                'badges' => [],
                'available_days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'],
                'is_active' => true,
                'display_order' => 3,
            ],
            [
                'category_id' => $cemilan->id,
                'name' => 'Siomay',
                'description' => 'Siomay ikan tenggiri dengan bumbu kacang, kecap manis dan sambal',
                'price' => 10000,
                'image' => 'https://placehold.co/600x400/fb923c/ffffff?text=Siomay',
                'badges' => [
                    [
                        'text' => 'Populer',
                        'color' => 'orange',
                        'icon' => 'fire'
                    ]
                ],
                'available_days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'],
                'is_active' => true,
                'display_order' => 4,
            ],

            // Makanan Manis
            [
                'category_id' => $makananManis->id,
                'name' => 'Es Krim Goreng',
                'description' => 'Es krim vanilla digoreng dengan lapisan roti tawar crispy, disajikan dengan topping coklat',
                'price' => 12000,
                'image' => 'https://placehold.co/600x400/a855f7/ffffff?text=Es+Krim+Goreng',
                'badges' => [
                    [
                        'text' => 'Unik',
                        'color' => 'purple',
                        'icon' => 'sparkles'
                    ]
                ],
                'available_days' => ['tuesday', 'thursday', 'saturday'],
                'is_active' => true,
                'display_order' => 1,
            ],
            [
                'category_id' => $makananManis->id,
                'name' => 'Martabak Mini Coklat Keju',
                'description' => 'Martabak manis ukuran mini dengan topping coklat dan keju yang melimpah',
                'price' => 8000,
                'image' => 'https://placehold.co/600x400/78350f/ffffff?text=Martabak+Mini',
                'badges' => [],
                'available_days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'],
                'is_active' => true,
                'display_order' => 2,
            ],
            [
                'category_id' => $makananManis->id,
                'name' => 'Kue Putu',
                'description' => 'Kue putu tradisional dengan isian gula merah, disajikan dengan kelapa parut',
                'price' => 4000,
                'image' => 'https://placehold.co/600x400/84cc16/ffffff?text=Kue+Putu',
                'badges' => [],
                'available_days' => ['monday', 'wednesday', 'friday'],
                'is_active' => true,
                'display_order' => 3,
            ],
            [
                'category_id' => $makananManis->id,
                'name' => 'Klepon',
                'description' => 'Kue klepon isi gula merah dengan taburan kelapa parut, manis dan lembut',
                'price' => 5000,
                'image' => 'https://placehold.co/600x400/22c55e/ffffff?text=Klepon',
                'badges' => [
                    [
                        'text' => 'Tradisional',
                        'color' => 'green',
                        'icon' => 'heart'
                    ]
                ],
                'available_days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'],
                'is_active' => true,
                'display_order' => 4,
            ],

            // Minuman
            [
                'category_id' => $minuman->id,
                'name' => 'Es Teh Manis',
                'description' => 'Es teh manis segar, cocok untuk menemani makan siang',
                'price' => 3000,
                'image' => 'https://placehold.co/600x400/92400e/ffffff?text=Es+Teh',
                'badges' => [
                    [
                        'text' => 'Hemat',
                        'color' => 'green',
                        'icon' => 'currency-dollar'
                    ]
                ],
                'available_days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'],
                'is_active' => true,
                'display_order' => 1,
            ],
            [
                'category_id' => $minuman->id,
                'name' => 'Es Jeruk',
                'description' => 'Jus jeruk segar dengan es batu, menyegarkan di siang hari',
                'price' => 5000,
                'image' => 'https://placehold.co/600x400/fb923c/ffffff?text=Es+Jeruk',
                'badges' => [],
                'available_days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'],
                'is_active' => true,
                'display_order' => 2,
            ],
            [
                'category_id' => $minuman->id,
                'name' => 'Cappuccino',
                'description' => 'Kopi cappuccino dengan foam susu yang lembut, sempurna untuk teman belajar',
                'price' => 10000,
                'image' => 'https://placehold.co/600x400/78350f/ffffff?text=Cappuccino',
                'badges' => [],
                'available_days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'],
                'is_active' => true,
                'display_order' => 3,
            ],
            [
                'category_id' => $minuman->id,
                'name' => 'Es Campur',
                'description' => 'Es campur dengan berbagai topping seperti kolang kaling, nata de coco, dan sirup',
                'price' => 8000,
                'image' => 'https://placehold.co/600x400/ec4899/ffffff?text=Es+Campur',
                'badges' => [
                    [
                        'text' => 'Segar',
                        'color' => 'blue',
                        'icon' => 'sparkles'
                    ]
                ],
                'available_days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'],
                'is_active' => true,
                'display_order' => 4,
            ],
            [
                'category_id' => $minuman->id,
                'name' => 'Jus Alpukat',
                'description' => 'Jus alpukat segar dengan susu coklat dan es krim vanilla',
                'price' => 12000,
                'image' => 'https://placehold.co/600x400/15803d/ffffff?text=Jus+Alpukat',
                'badges' => [
                    [
                        'text' => 'Populer',
                        'color' => 'orange',
                        'icon' => 'fire'
                    ]
                ],
                'available_days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'],
                'is_active' => true,
                'display_order' => 5,
            ],
        ];

        foreach ($menus as $menu) {
            CanteenMenu::create($menu);
        }
    }
}
