<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SiliwangiMandiriShopObatSeeder extends Seeder
{
    public function run(): void
    {
        $workUnitId = 4; // Siliwangi Mandiri Shop
        $supplierId = 1; // BPPU IKIP Siliwangi
        $kategoriId = 7; // Kesehatan & Kebersihan

        $barangs = [
            ['kode_barang' => '206502', 'nama_barang' => 'Komix Rasa Jeruk Nipis @ 7 ml',                    'stok' => 6,  'satuan' => 'SCHT', 'harga_beli' => 1500,  'harga_jual' => 2000],
            ['kode_barang' => '401290', 'nama_barang' => 'Komix Rasa Peppermint @ 7 ml',                     'stok' => 4,  'satuan' => 'SCHT', 'harga_beli' => 1500,  'harga_jual' => 2000],
            ['kode_barang' => '206501', 'nama_barang' => 'Komix Rasa Jahe @ 7 ml',                           'stok' => 5,  'satuan' => 'SCHT', 'harga_beli' => 1500,  'harga_jual' => 2000],
            ['kode_barang' => '201475', 'nama_barang' => 'Paramex Nyeri Otot 25',                            'stok' => 2,  'satuan' => 'STR',  'harga_beli' => 2000,  'harga_jual' => 2500],
            ['kode_barang' => '200006', 'nama_barang' => 'Betadine Antiseptic Solution 15 ml',               'stok' => 1,  'satuan' => 'BTL',  'harga_beli' => 17000, 'harga_jual' => 20000],
            ['kode_barang' => '201822', 'nama_barang' => 'Norit Lang',                                       'stok' => 2,  'satuan' => 'TUB',  'harga_beli' => 18000, 'harga_jual' => 22000],
            ['kode_barang' => '997295', 'nama_barang' => 'Decolgen Tab Box 25 Strip @ 4 Tablet',             'stok' => 5,  'satuan' => 'STR',  'harga_beli' => 2500,  'harga_jual' => 3000],
            ['kode_barang' => '203547', 'nama_barang' => 'Antimo Tab 50mg Box, 72 Pak @ 1 Str @ 10 Tab',    'stok' => 3,  'satuan' => 'STR',  'harga_beli' => 5500,  'harga_jual' => 6500],
            ['kode_barang' => '997218', 'nama_barang' => 'Sangobion Capsul Box 25 Strip @ 10 Capsul',        'stok' => 2,  'satuan' => 'STR',  'harga_beli' => 23500, 'harga_jual' => 28000],
            ['kode_barang' => '201820', 'nama_barang' => 'GPU Cair Sereh 30 ml',                             'stok' => 2,  'satuan' => 'BTL',  'harga_beli' => 10000, 'harga_jual' => 12000],
            ['kode_barang' => '997108', 'nama_barang' => 'Salonpas Hangat Koyo Box 10 Pcs 5x2 Lembar',      'stok' => 3,  'satuan' => 'PCS',  'harga_beli' => 7500,  'harga_jual' => 9000],
            ['kode_barang' => '997226', 'nama_barang' => 'Mixagrip Flu Tablet Box 25 Strip @ 4 Tablet',     'stok' => 5,  'satuan' => 'STR',  'harga_beli' => 3000,  'harga_jual' => 3500],
            ['kode_barang' => '997157', 'nama_barang' => 'Ultraflu Tablet Box 25 Strip @ 4 Tablet',         'stok' => 5,  'satuan' => 'STR',  'harga_beli' => 4000,  'harga_jual' => 5000],
            ['kode_barang' => '206208', 'nama_barang' => 'Diapet NR 1 Str @ 4 Kapsul',                      'stok' => 5,  'satuan' => 'STR',  'harga_beli' => 4000,  'harga_jual' => 5000],
            ['kode_barang' => '997117', 'nama_barang' => 'Promag Tablet Kunyah Box @ 3 Strip',              'stok' => 10, 'satuan' => 'STR',  'harga_beli' => 10000, 'harga_jual' => 12000],
            ['kode_barang' => '997162', 'nama_barang' => 'Panadol Extra Box 10 Strip @ 10 Kaplet',          'stok' => 2,  'satuan' => 'STR',  'harga_beli' => 13500, 'harga_jual' => 16000],
            ['kode_barang' => '997305', 'nama_barang' => 'Panadol Box 10 Strip @ 10 Kaplet (Biru)',         'stok' => 2,  'satuan' => 'STR',  'harga_beli' => 12000, 'harga_jual' => 14500],
            ['kode_barang' => '997320', 'nama_barang' => 'Rohto Cool Eye Drop 7 ml',                        'stok' => 1,  'satuan' => 'BTL',  'harga_beli' => 21000, 'harga_jual' => 25000],
            ['kode_barang' => '200974', 'nama_barang' => 'Insto Regular 15 ml',                             'stok' => 2,  'satuan' => 'BTL',  'harga_beli' => 24000, 'harga_jual' => 29000],
            ['kode_barang' => '996120', 'nama_barang' => 'Fresh Care Smash Merah 6 ml',                     'stok' => 1,  'satuan' => 'BTL',  'harga_beli' => 13500, 'harga_jual' => 16000],
            ['kode_barang' => '997161', 'nama_barang' => 'Paramex Tablet Box 50 Strip @ 4 Tablet',          'stok' => 10, 'satuan' => 'STR',  'harga_beli' => 2500,  'harga_jual' => 3000],
            ['kode_barang' => '996468', 'nama_barang' => 'Fresh Care Smash Matcha 6 ml',                    'stok' => 1,  'satuan' => 'BTL',  'harga_beli' => 13500, 'harga_jual' => 16000],
            ['kode_barang' => '997041', 'nama_barang' => 'Fresh Care Smash Fruity',                         'stok' => 1,  'satuan' => 'BTL',  'harga_beli' => 13500, 'harga_jual' => 16000],
            ['kode_barang' => '997368', 'nama_barang' => 'Bodrex Tab 500mg Box 2 Str @ 10 Tab',             'stok' => 5,  'satuan' => 'BOX',  'harga_beli' => 10000, 'harga_jual' => 12000],
        ];

        $now = now();

        foreach ($barangs as $barang) {
            // Skip kalau kode sudah ada di unit kerja ini
            $exists = DB::table('barangs')
                ->where('work_unit_id', $workUnitId)
                ->where('kode_barang', $barang['kode_barang'])
                ->exists();

            if ($exists) {
                $this->command->line("  Skip (sudah ada): {$barang['kode_barang']} - {$barang['nama_barang']}");
                continue;
            }

            DB::table('barangs')->insert([
                'work_unit_id'   => $workUnitId,
                'kode_barang'    => $barang['kode_barang'],
                'nama_barang'    => $barang['nama_barang'],
                'kategori_id'    => $kategoriId,
                'satuan'         => $barang['satuan'],
                'stok'           => $barang['stok'],
                'harga_beli'     => $barang['harga_beli'],
                'harga_jual'     => $barang['harga_jual'],
                'supplier_id'    => $supplierId,
                'jenis_barang'   => 'jual_langsung',
                'is_active'      => 1,
                'minimum_stok'   => 0,
                'created_at'     => $now,
                'updated_at'     => $now,
            ]);

            $this->command->line("  Inserted: {$barang['kode_barang']} - {$barang['nama_barang']}");
        }

        $this->command->info('Seeding SiliwangiMandiriShopObat selesai.');
    }
}
