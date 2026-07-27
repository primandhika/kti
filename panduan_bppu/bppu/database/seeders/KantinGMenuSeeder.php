<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Barang;

class KantinGMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $workUnitId = 1; // Kantin Gedung G
        $kategoriId = 1; // F&B (Makanan & Minuman)

        $menus = [
            ['menu' => 'Ayam Goreng Komplit', 'harga' => 20000],
            ['menu' => 'Ikan Goreng Komplit', 'harga' => 20000],
            ['menu' => 'Pindang Presto', 'harga' => 20000],
            ['menu' => 'Ayam Geprek Cabe Hijau', 'harga' => 17000],
            ['menu' => 'Ayam Goreng', 'harga' => 17000],
            ['menu' => 'Ikan Goreng', 'harga' => 17000],
            ['menu' => 'Ikan Mas Presto', 'harga' => 17000],
            ['menu' => 'Ikan Asin', 'harga' => 4000],
            ['menu' => 'Telor Dadar', 'harga' => 5000],
            ['menu' => 'Telor Ceplok', 'harga' => 5000],
            ['menu' => 'Telor Geprek', 'harga' => 6000],
            ['menu' => 'Ikan Bawal', 'harga' => 17000],
            ['menu' => 'Ikan Kembung', 'harga' => 17000],
            ['menu' => 'Pepes Tahu', 'harga' => 4000],
            ['menu' => 'Pepes Ayam', 'harga' => 17000],
            ['menu' => 'Pepes Ikan', 'harga' => 17000],
            ['menu' => 'Pepes Jamur', 'harga' => 5000],
            ['menu' => 'Pepes Peda', 'harga' => 5000],
            ['menu' => 'Soto Bandung + Nasi', 'harga' => 23000],
            ['menu' => 'Soto Ayam + Nasi', 'harga' => 15000],
            ['menu' => 'Garang Asam Daging + Nasi', 'harga' => 25000],
            ['menu' => 'Garang Asam Ayam + Nasi', 'harga' => 25000],
            ['menu' => 'Ayam Woku', 'harga' => 18000],
            ['menu' => 'Ayam Rica-rica', 'harga' => 18000],
            ['menu' => 'Sop Ayam', 'harga' => 15000],
            ['menu' => 'Oseng Kangkung', 'harga' => 2000],
            ['menu' => 'Oseng Genjer', 'harga' => 3000],
            ['menu' => 'Oseng Pakcoy', 'harga' => 3000],
            ['menu' => 'Oseng Toge Tahu', 'harga' => 4000],
            ['menu' => 'Oseng Buncis', 'harga' => 4000],
            ['menu' => 'Oseng Kacang Panjang', 'harga' => 4000],
            ['menu' => 'Urek Ienca', 'harga' => 4000],
            ['menu' => 'Urap Sayur', 'harga' => 6000],
            ['menu' => 'Lotek', 'harga' => 10000],
            ['menu' => 'Sayur Lodeh', 'harga' => 6000],
            ['menu' => 'Sayur Asam', 'harga' => 6000],
        ];

        foreach ($menus as $index => $menuData) {
            // Generate kode barang berformat KG-001, KG-002, dst
            $kodeBarang = 'KG-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT);

            // Check if already exists
            $existing = Barang::where('work_unit_id', $workUnitId)
                ->where('kode_barang', $kodeBarang)
                ->first();

            if (!$existing) {
                Barang::create([
                    'work_unit_id' => $workUnitId,
                    'kode_barang' => $kodeBarang,
                    'nama_barang' => $menuData['menu'],
                    'kategori' => 'F&B',
                    'kategori_id' => $kategoriId,
                    'satuan' => 'porsi',
                    'stok' => 0, // stock untuk makanan biasanya 0 atau daily stock
                    'harga_beli' => $menuData['harga'] * 0.7, // assume 70% dari harga jual
                    'harga_jual' => $menuData['harga'],
                    'minimum_stok' => 0,
                    'deskripsi' => 'Menu ' . $menuData['menu'] . ' - Kantin Gedung G',
                    'is_menu_item' => true, // Mark as menu item
                ]);

                echo "✓ Created: {$kodeBarang} - {$menuData['menu']}\n";
            } else {
                // Update existing items to mark as menu items
                $existing->update(['is_menu_item' => true]);
                echo "- Updated (exists): {$kodeBarang} - {$menuData['menu']}\n";
            }
        }

        echo "\nTotal menu: " . count($menus) . "\n";
    }
}
