<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Barang;
use App\Models\WorkUnit;

class MieGacorKantinISeeder extends Seeder
{
    public function run(): void
    {
        $workUnit = WorkUnit::find(3); // Kantin Gedung I

        if (!$workUnit) {
            $this->command->error('Kantin Gedung I (id=3) tidak ditemukan!');
            return;
        }

        // kategori_id=1 (F&B / Makanan), kategori_id=9 (Kudapan/SN), kategori_id=23 (Minuman/DR)
        $menus = [
            // Mie
            ['kode' => 'MG-001', 'nama' => 'Mie Gacor Level 0',                      'harga_beli' =>  6600, 'harga_jual' => 12000, 'jenis' => 'Makanan',  'sub' => 'Mie',         'kategori_id' => 1,  'kategori' => 'F&B'],
            ['kode' => 'MG-002', 'nama' => 'Mie Gacor Level 1-2',                     'harga_beli' =>  7800, 'harga_jual' => 14000, 'jenis' => 'Makanan',  'sub' => 'Mie',         'kategori_id' => 1,  'kategori' => 'F&B'],
            ['kode' => 'MG-003', 'nama' => 'Mie Gacor Level 3-5',                     'harga_beli' =>  8400, 'harga_jual' => 15000, 'jenis' => 'Makanan',  'sub' => 'Mie',         'kategori_id' => 1,  'kategori' => 'F&B'],
            ['kode' => 'MG-004', 'nama' => 'Mie Gacor Keju Lekoh Tidak Pedas',        'harga_beli' =>  7800, 'harga_jual' => 14000, 'jenis' => 'Makanan',  'sub' => 'Mie Keju',    'kategori_id' => 1,  'kategori' => 'F&B'],
            ['kode' => 'MG-005', 'nama' => 'Mie Gacor Keju Lekoh Pedas/Chili Oil',   'harga_beli' =>  9000, 'harga_jual' => 16000, 'jenis' => 'Makanan',  'sub' => 'Mie Keju',    'kategori_id' => 1,  'kategori' => 'F&B'],
            // Toping
            ['kode' => 'MG-006', 'nama' => 'Toping Telur Rebus 1/2',                  'harga_beli' =>  1500, 'harga_jual' =>  3500, 'jenis' => 'Tambahan', 'sub' => 'Toping',      'kategori_id' => 1,  'kategori' => 'F&B'],
            ['kode' => 'MG-007', 'nama' => 'Toping Baso',                              'harga_beli' =>  1800, 'harga_jual' =>  4000, 'jenis' => 'Tambahan', 'sub' => 'Toping',      'kategori_id' => 1,  'kategori' => 'F&B'],
            ['kode' => 'MG-008', 'nama' => 'Toping Suki',                              'harga_beli' =>  2100, 'harga_jual' =>  4500, 'jenis' => 'Tambahan', 'sub' => 'Toping',      'kategori_id' => 1,  'kategori' => 'F&B'],
            // Basreng
            ['kode' => 'MG-009', 'nama' => 'Basreng Chili Oil',                        'harga_beli' =>  6600, 'harga_jual' => 12000, 'jenis' => 'Cemilan',  'sub' => 'Basreng',     'kategori_id' => 9,  'kategori' => 'SN'],
            ['kode' => 'MG-010', 'nama' => 'Basreng Ayam Bawang',                      'harga_beli' =>  6600, 'harga_jual' => 12000, 'jenis' => 'Cemilan',  'sub' => 'Basreng',     'kategori_id' => 9,  'kategori' => 'SN'],
            ['kode' => 'MG-011', 'nama' => 'Basreng Keju',                             'harga_beli' =>  6600, 'harga_jual' => 12000, 'jenis' => 'Cemilan',  'sub' => 'Basreng',     'kategori_id' => 9,  'kategori' => 'SN'],
            // Bakpao
            ['kode' => 'MG-012', 'nama' => 'Bapau Taro Pandan',                        'harga_beli' =>  6600, 'harga_jual' => 12000, 'jenis' => 'Cemilan',  'sub' => 'Bakpao',      'kategori_id' => 9,  'kategori' => 'SN'],
            // Nasi Goreng
            ['kode' => 'MG-013', 'nama' => 'Nasi Goreng Daging Kebab',                 'harga_beli' => 15000, 'harga_jual' => 26000, 'jenis' => 'Makanan',  'sub' => 'Nasi Goreng', 'kategori_id' => 1,  'kategori' => 'F&B'],
            ['kode' => 'MG-014', 'nama' => 'Nasi Goreng Daging Ayam',                  'harga_beli' => 12000, 'harga_jual' => 21000, 'jenis' => 'Makanan',  'sub' => 'Nasi Goreng', 'kategori_id' => 1,  'kategori' => 'F&B'],
            ['kode' => 'MG-015', 'nama' => 'Nasi Goreng Biasa',                        'harga_beli' =>  9000, 'harga_jual' => 16000, 'jenis' => 'Makanan',  'sub' => 'Nasi Goreng', 'kategori_id' => 1,  'kategori' => 'F&B'],
            // Roti Bakar & Kebab
            ['kode' => 'MG-016', 'nama' => 'Roti Kebab Beef',                          'harga_beli' =>  9000, 'harga_jual' => 16000, 'jenis' => 'Cemilan',  'sub' => 'Roti Bakar',  'kategori_id' => 9,  'kategori' => 'SN'],
            ['kode' => 'MG-017', 'nama' => 'Roti Kebab Beef Cheesy',                   'harga_beli' => 10800, 'harga_jual' => 19000, 'jenis' => 'Cemilan',  'sub' => 'Roti Bakar',  'kategori_id' => 9,  'kategori' => 'SN'],
            ['kode' => 'MG-018', 'nama' => 'Roti Kebab Beef Mozarella',                'harga_beli' => 11400, 'harga_jual' => 20000, 'jenis' => 'Cemilan',  'sub' => 'Roti Bakar',  'kategori_id' => 9,  'kategori' => 'SN'],
            ['kode' => 'MG-019', 'nama' => 'Roti Bakar Strawberry',                    'harga_beli' =>  4800, 'harga_jual' =>  9000, 'jenis' => 'Cemilan',  'sub' => 'Roti Bakar',  'kategori_id' => 9,  'kategori' => 'SN'],
            ['kode' => 'MG-020', 'nama' => 'Roti Bakar Tiramisu',                      'harga_beli' =>  4800, 'harga_jual' =>  9000, 'jenis' => 'Cemilan',  'sub' => 'Roti Bakar',  'kategori_id' => 9,  'kategori' => 'SN'],
            ['kode' => 'MG-021', 'nama' => 'Roti Bakar Coklat',                        'harga_beli' =>  4800, 'harga_jual' =>  9000, 'jenis' => 'Cemilan',  'sub' => 'Roti Bakar',  'kategori_id' => 9,  'kategori' => 'SN'],
            ['kode' => 'MG-022', 'nama' => 'Roti Bakar Abon',                          'harga_beli' =>  6000, 'harga_jual' => 11000, 'jenis' => 'Cemilan',  'sub' => 'Roti Bakar',  'kategori_id' => 9,  'kategori' => 'SN'],
            ['kode' => 'MG-023', 'nama' => 'Roti Bakar Keju',                          'harga_beli' =>  4800, 'harga_jual' =>  9000, 'jenis' => 'Cemilan',  'sub' => 'Roti Bakar',  'kategori_id' => 9,  'kategori' => 'SN'],
            // Dimsum
            ['kode' => 'MG-024', 'nama' => 'Dimsum Keju Mozarella',                    'harga_beli' =>  7800, 'harga_jual' => 14000, 'jenis' => 'Cemilan',  'sub' => 'Dimsum',      'kategori_id' => 9,  'kategori' => 'SN'],
            ['kode' => 'MG-025', 'nama' => 'Dimsum Toping Beef',                       'harga_beli' =>  7800, 'harga_jual' => 14000, 'jenis' => 'Cemilan',  'sub' => 'Dimsum',      'kategori_id' => 9,  'kategori' => 'SN'],
            ['kode' => 'MG-026', 'nama' => 'Dimsum Mix',                               'harga_beli' =>  7800, 'harga_jual' => 14000, 'jenis' => 'Cemilan',  'sub' => 'Dimsum',      'kategori_id' => 9,  'kategori' => 'SN'],
            ['kode' => 'MG-027', 'nama' => 'Chiken Shrimp Roll',                       'harga_beli' =>  7800, 'harga_jual' => 14000, 'jenis' => 'Cemilan',  'sub' => 'Dimsum',      'kategori_id' => 9,  'kategori' => 'SN'],
            ['kode' => 'MG-028', 'nama' => 'Dimsum Mentai',                            'harga_beli' => 12000, 'harga_jual' => 21000, 'jenis' => 'Cemilan',  'sub' => 'Dimsum',      'kategori_id' => 9,  'kategori' => 'SN'],
            // Minuman
            ['kode' => 'MG-029', 'nama' => 'Es Teh Manis',                             'harga_beli' =>  4800, 'harga_jual' =>  9000, 'jenis' => 'Minuman',  'sub' => 'Dingin',      'kategori_id' => 23, 'kategori' => 'DR'],
            ['kode' => 'MG-030', 'nama' => 'Es Lemon Tea',                             'harga_beli' =>  5400, 'harga_jual' => 10000, 'jenis' => 'Minuman',  'sub' => 'Dingin',      'kategori_id' => 23, 'kategori' => 'DR'],
            ['kode' => 'MG-031', 'nama' => 'Es Lyche Tea',                             'harga_beli' =>  5400, 'harga_jual' => 10000, 'jenis' => 'Minuman',  'sub' => 'Dingin',      'kategori_id' => 23, 'kategori' => 'DR'],
            ['kode' => 'MG-032', 'nama' => 'Es Lemonade',                              'harga_beli' =>  5400, 'harga_jual' => 10000, 'jenis' => 'Minuman',  'sub' => 'Dingin',      'kategori_id' => 23, 'kategori' => 'DR'],
            ['kode' => 'MG-033', 'nama' => 'Es Strawberry',                            'harga_beli' =>  5400, 'harga_jual' => 10000, 'jenis' => 'Minuman',  'sub' => 'Dingin',      'kategori_id' => 23, 'kategori' => 'DR'],
            ['kode' => 'MG-034', 'nama' => 'Es Cokelat Premium',                       'harga_beli' =>  6600, 'harga_jual' => 12000, 'jenis' => 'Minuman',  'sub' => 'Dingin',      'kategori_id' => 23, 'kategori' => 'DR'],
        ];

        $created = 0;
        $skipped = 0;

        foreach ($menus as $data) {
            $existing = Barang::where('work_unit_id', $workUnit->id)
                ->where('kode_barang', $data['kode'])
                ->first();

            if ($existing) {
                $this->command->warn("Skip (sudah ada): {$data['kode']} - {$data['nama']}");
                $skipped++;
                continue;
            }

            Barang::create([
                'work_unit_id'  => $workUnit->id,
                'kode_barang'   => $data['kode'],
                'nama_barang'   => $data['nama'],
                'kategori'      => $data['kategori'],
                'kategori_id'   => $data['kategori_id'],
                'sub_kategori'  => $data['sub'],
                'jenis_barang'  => 'jual_langsung',
                'satuan'        => 'porsi',
                'stok'          => 999,
                'harga_beli'    => $data['harga_beli'],
                'harga_jual'    => $data['harga_jual'],
                'minimum_stok'  => 0,
                'is_menu_item'  => true,
                'is_active'     => true,
            ]);

            $this->command->info("Dibuat: {$data['kode']} - {$data['nama']}");
            $created++;
        }

        $this->command->info("Selesai! Dibuat: {$created}, Dilewati: {$skipped}");
    }
}
