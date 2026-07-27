<?php

namespace Database\Seeders;

use App\Models\Barang;
use Illuminate\Database\Seeder;

class MenuSubKategoriSeeder extends Seeder
{
    public function run(): void
    {
        // Update sub_kategori berdasarkan pattern nama menu
        $patterns = [
            'Aneka Nasi' => ['nasi', 'goreng', 'uduk', 'kuning', 'liwet'],
            'Ayam & Bebek' => ['ayam', 'bebek', 'chicken'],
            'Berkuah' => ['soto', 'bakso', 'mie', 'kwetiau', 'sop', 'ceker'],
            'Tradisional' => ['pecel', 'gado', 'lotek', 'karedok', 'tahu', 'tempe'],
            'Camilan' => ['roti', 'gorengan', 'pisang', 'cireng', 'batagor', 'siomay', 'snack', 'keripik'],
            'Minuman' => ['teh', 'kopi', 'jus', 'susu', 'air', 'mineral', 'soda', 'es'],
        ];

        $menusWithoutSubCategory = Barang::where('is_menu_item', true)
            ->whereNull('sub_kategori')
            ->get();

        foreach ($menusWithoutSubCategory as $menu) {
            $namaLower = strtolower($menu->nama_barang);
            $assigned = false;

            foreach ($patterns as $subKategori => $keywords) {
                foreach ($keywords as $keyword) {
                    if (str_contains($namaLower, $keyword)) {
                        $menu->update(['sub_kategori' => $subKategori]);
                        $assigned = true;
                        break 2;
                    }
                }
            }

            if (!$assigned) {
                $menu->update(['sub_kategori' => 'Lain-lain']);
            }
        }

        // Set 6 menu sebagai featured (menu unggulan untuk carousel)
        $featuredMenus = Barang::where('is_menu_item', true)
            ->whereIn('sub_kategori', ['Aneka Nasi', 'Ayam & Bebek', 'Minuman', 'Berkuah'])
            ->orderBy('harga_jual', 'desc')
            ->take(6)
            ->get();

        foreach ($featuredMenus as $menu) {
            $menu->update(['is_featured' => true]);
        }

        $this->command->info('Sub kategori dan featured menu berhasil diupdate!');
    }
}
