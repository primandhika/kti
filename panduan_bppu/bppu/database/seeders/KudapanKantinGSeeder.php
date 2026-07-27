<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Barang;
use App\Models\BarangVarian;
use App\Models\WorkUnit;

class KudapanKantinGSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kantinG = WorkUnit::find(1); // Kantin Gedung G

        if (!$kantinG) {
            $this->command->error('Work Unit Kantin Gedung G tidak ditemukan!');
            return;
        }

        // Array barang dengan varian
        $barangData = [
            // Makanan berkuah
            [
                'nama' => 'Bakso/Baso',
                'kategori' => 'Kudapan',
                'satuan' => 'porsi',
                'harga_beli' => 8000,
                'harga_jual' => 10000,
                'stok' => 0,
                'minimum_stok' => 5,
                'jenis_barang' => 'konsinyasi',
                'is_menu_item' => true,
            ],
            [
                'nama' => 'Soto',
                'kategori' => 'Kudapan',
                'satuan' => 'porsi',
                'harga_beli' => 9000,
                'harga_jual' => 12000,
                'stok' => 0,
                'minimum_stok' => 5,
                'jenis_barang' => 'konsinyasi',
                'is_menu_item' => true,
            ],
            [
                'nama' => 'Nasi Ayam',
                'kategori' => 'Kudapan',
                'satuan' => 'porsi',
                'harga_beli' => 10000,
                'harga_jual' => 13000,
                'stok' => 0,
                'minimum_stok' => 5,
                'jenis_barang' => 'konsinyasi',
                'is_menu_item' => true,
            ],
            [
                'nama' => 'Pindang',
                'kategori' => 'Kudapan',
                'satuan' => 'porsi',
                'harga_beli' => 7000,
                'harga_jual' => 9000,
                'stok' => 0,
                'minimum_stok' => 5,
                'jenis_barang' => 'konsinyasi',
                'is_menu_item' => true,
                'deskripsi' => 'Pindang tanpa nasi',
            ],

            // Roti dan kue
            [
                'nama' => 'Roti',
                'kategori' => 'Kudapan',
                'satuan' => 'pcs',
                'harga_beli' => 3000,
                'harga_jual' => 4000,
                'stok' => 0,
                'minimum_stok' => 10,
                'jenis_barang' => 'konsinyasi',
            ],
            [
                'nama' => 'Peyek',
                'kategori' => 'Kudapan',
                'satuan' => 'pcs',
                'harga_beli' => 1000,
                'harga_jual' => 2000,
                'stok' => 0,
                'minimum_stok' => 20,
                'jenis_barang' => 'konsinyasi',
            ],

            // Keripik dan snack - dengan varian
            [
                'nama' => 'Keripik Usus',
                'kategori' => 'Kudapan',
                'satuan' => 'pcs',
                'harga_beli' => 2000,
                'harga_jual' => 3000,
                'stok' => 0,
                'minimum_stok' => 15,
                'jenis_barang' => 'konsinyasi',
                'varians' => [
                    ['nama' => 'Pedas', 'harga_jual' => 3000, 'display_order' => 1],
                    ['nama' => 'Asin', 'harga_jual' => 3000, 'display_order' => 2],
                ],
            ],
            [
                'nama' => 'Keripik Pisang',
                'kategori' => 'Kudapan',
                'satuan' => 'pcs',
                'harga_beli' => 2500,
                'harga_jual' => 3500,
                'stok' => 0,
                'minimum_stok' => 15,
                'jenis_barang' => 'konsinyasi',
                'varians' => [
                    ['nama' => 'Pedas', 'harga_jual' => 3500, 'display_order' => 1],
                    ['nama' => 'Asin', 'harga_jual' => 3500, 'display_order' => 2],
                ],
            ],
            [
                'nama' => 'Basreng',
                'kategori' => 'Kudapan',
                'satuan' => 'pcs',
                'harga_beli' => 2000,
                'harga_jual' => 3000,
                'stok' => 0,
                'minimum_stok' => 20,
                'jenis_barang' => 'konsinyasi',
                'varians' => [
                    ['nama' => 'Pedas', 'harga_jual' => 3000, 'display_order' => 1],
                    ['nama' => 'Asin', 'harga_jual' => 3000, 'display_order' => 2],
                ],
            ],
            [
                'nama' => 'Basreng Usus',
                'kategori' => 'Kudapan',
                'satuan' => 'pcs',
                'harga_beli' => 2500,
                'harga_jual' => 3500,
                'stok' => 0,
                'minimum_stok' => 15,
                'jenis_barang' => 'konsinyasi',
                'varians' => [
                    ['nama' => 'Pedas', 'harga_jual' => 3500, 'display_order' => 1],
                ],
            ],
            [
                'nama' => 'Usus Cahaya',
                'kategori' => 'Kudapan',
                'satuan' => 'pcs',
                'harga_beli' => 2500,
                'harga_jual' => 3500,
                'stok' => 0,
                'minimum_stok' => 15,
                'jenis_barang' => 'konsinyasi',
                'varians' => [
                    ['nama' => 'Asin', 'harga_jual' => 3500, 'display_order' => 1],
                    ['nama' => 'Pedas', 'harga_jual' => 3500, 'display_order' => 2],
                ],
            ],
            [
                'nama' => 'Basreng Cahaya',
                'kategori' => 'Kudapan',
                'satuan' => 'pcs',
                'harga_beli' => 2500,
                'harga_jual' => 3500,
                'stok' => 0,
                'minimum_stok' => 15,
                'jenis_barang' => 'konsinyasi',
                'varians' => [
                    ['nama' => 'Asin', 'harga_jual' => 3500, 'display_order' => 1],
                ],
            ],
            [
                'nama' => 'Keripik Kaca Cahaya',
                'kategori' => 'Kudapan',
                'satuan' => 'pcs',
                'harga_beli' => 2500,
                'harga_jual' => 3500,
                'stok' => 0,
                'minimum_stok' => 15,
                'jenis_barang' => 'konsinyasi',
                'varians' => [
                    ['nama' => 'Pedas', 'harga_jual' => 3500, 'display_order' => 1],
                ],
            ],
            [
                'nama' => 'Sistik Cahaya',
                'kategori' => 'Kudapan',
                'satuan' => 'pcs',
                'harga_beli' => 2500,
                'harga_jual' => 3500,
                'stok' => 0,
                'minimum_stok' => 15,
                'jenis_barang' => 'konsinyasi',
                'varians' => [
                    ['nama' => 'Pedas', 'harga_jual' => 3500, 'display_order' => 1],
                    ['nama' => 'Asin', 'harga_jual' => 3500, 'display_order' => 2],
                ],
            ],
            [
                'nama' => 'Kerupuk Beton Cahaya',
                'kategori' => 'Kudapan',
                'satuan' => 'pcs',
                'harga_beli' => 2500,
                'harga_jual' => 3500,
                'stok' => 0,
                'minimum_stok' => 15,
                'jenis_barang' => 'konsinyasi',
                'varians' => [
                    ['nama' => 'Pedas', 'harga_jual' => 3500, 'display_order' => 1],
                ],
            ],
            [
                'nama' => 'Mie Gulung',
                'kategori' => 'Kudapan',
                'satuan' => 'pcs',
                'harga_beli' => 2500,
                'harga_jual' => 3500,
                'stok' => 0,
                'minimum_stok' => 15,
                'jenis_barang' => 'konsinyasi',
                'varians' => [
                    ['nama' => 'Pedas', 'harga_jual' => 3500, 'display_order' => 1],
                    ['nama' => 'Keju', 'harga_jual' => 4000, 'display_order' => 2],
                ],
            ],
            [
                'nama' => 'Kerupuk Gulung',
                'kategori' => 'Kudapan',
                'satuan' => 'pcs',
                'harga_beli' => 2000,
                'harga_jual' => 3000,
                'stok' => 0,
                'minimum_stok' => 15,
                'jenis_barang' => 'konsinyasi',
            ],
            [
                'nama' => 'Keripik Talas',
                'kategori' => 'Kudapan',
                'satuan' => 'pcs',
                'harga_beli' => 2500,
                'harga_jual' => 3500,
                'stok' => 0,
                'minimum_stok' => 15,
                'jenis_barang' => 'konsinyasi',
            ],
            [
                'nama' => 'Usus Baper',
                'kategori' => 'Kudapan',
                'satuan' => 'pcs',
                'harga_beli' => 2500,
                'harga_jual' => 3500,
                'stok' => 0,
                'minimum_stok' => 15,
                'jenis_barang' => 'konsinyasi',
                'varians' => [
                    ['nama' => 'Pedas', 'harga_jual' => 3500, 'display_order' => 1],
                ],
            ],

            // Seblak kering
            [
                'nama' => 'Seblak Kering',
                'kategori' => 'Kudapan',
                'satuan' => 'pcs',
                'harga_beli' => 2500,
                'harga_jual' => 3500,
                'stok' => 0,
                'minimum_stok' => 15,
                'jenis_barang' => 'konsinyasi',
                'varians' => [
                    ['nama' => 'Kerupuk', 'harga_jual' => 3500, 'display_order' => 1],
                    ['nama' => 'Makaroni Bantet', 'harga_jual' => 3500, 'display_order' => 2],
                    ['nama' => 'Makaroni Pedas', 'harga_jual' => 3500, 'display_order' => 3],
                    ['nama' => 'Basreng', 'harga_jual' => 3500, 'display_order' => 4],
                ],
            ],
        ];

        foreach ($barangData as $data) {
            // Extract varians jika ada
            $varians = $data['varians'] ?? [];
            unset($data['varians']);

            // Generate kode barang
            $lastBarang = Barang::where('work_unit_id', $kantinG->id)
                ->orderBy('id', 'desc')
                ->first();

            $nextNumber = 1;
            if ($lastBarang && $lastBarang->kode_barang) {
                preg_match('/\d+$/', $lastBarang->kode_barang, $matches);
                if (!empty($matches)) {
                    $nextNumber = intval($matches[0]) + 1;
                }
            }

            $kodeBarang = 'G-KDP-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

            // Create barang
            $barang = Barang::create([
                'work_unit_id' => $kantinG->id,
                'kode_barang' => $kodeBarang,
                'nama_barang' => $data['nama'],
                'kategori' => $data['kategori'],
                'satuan' => $data['satuan'],
                'harga_beli' => $data['harga_beli'],
                'harga_jual' => $data['harga_jual'],
                'stok' => $data['stok'],
                'minimum_stok' => $data['minimum_stok'],
                'jenis_barang' => $data['jenis_barang'],
                'is_menu_item' => $data['is_menu_item'] ?? false,
                'is_active' => true,
                'deskripsi' => $data['deskripsi'] ?? null,
            ]);

            // Create varians jika ada
            if (!empty($varians)) {
                foreach ($varians as $varian) {
                    BarangVarian::create([
                        'barang_id' => $barang->id,
                        'nama_varian' => $varian['nama'],
                        'harga_jual' => $varian['harga_jual'],
                        'harga_grosir' => $varian['harga_grosir'] ?? null,
                        'hpp_tambahan' => $varian['hpp_tambahan'] ?? 0,
                        'is_active' => true,
                        'display_order' => $varian['display_order'],
                    ]);
                }

                $this->command->info("Created: {$barang->nama_barang} with " . count($varians) . " variants");
            } else {
                $this->command->info("Created: {$barang->nama_barang}");
            }
        }

        $this->command->info('Kudapan Kantin Gedung G berhasil di-seed!');
    }
}
