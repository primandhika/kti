<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SiliwangiMandiriShopATKSeeder extends Seeder
{
    public function run(): void
    {
        $workUnitId = 4; // Siliwangi Mandiri Shop
        $supplierId = 44; // RBP (konsinyasi)
        $now = Carbon::now();

        $barangs = [
            ['kode_barang' => '101', 'nama_barang' => 'SN Spidol WB Htm BG12',                         'stok' => 12, 'satuan' => 'PCS', 'harga_beli' => 7250,  'harga_jual' => 8000],
            ['kode_barang' => '102', 'nama_barang' => 'SN Spidol WB Bru BG12',                         'stok' => 12, 'satuan' => 'PCS', 'harga_beli' => 7250,  'harga_jual' => 8000],
            ['kode_barang' => '103', 'nama_barang' => 'JK Eraser 526 B40BL',                           'stok' => 40, 'satuan' => 'PCS', 'harga_beli' => 875,   'harga_jual' => 1000],
            ['kode_barang' => '104', 'nama_barang' => 'STD Pen Boldliner',                             'stok' => 48, 'satuan' => 'PCS', 'harga_beli' => 14500, 'harga_jual' => 16000],
            ['kode_barang' => '105', 'nama_barang' => 'ZB Pen Kokoro Htm',                             'stok' => 12, 'satuan' => 'PCS', 'harga_beli' => 5500,  'harga_jual' => 6000],
            ['kode_barang' => '106', 'nama_barang' => 'ZB Pen Kokoro Light Blue',                      'stok' => 12, 'satuan' => 'PCS', 'harga_beli' => 6000,  'harga_jual' => 6500],
            ['kode_barang' => '107', 'nama_barang' => 'ZB Pen Kokoro Pink',                            'stok' => 12, 'satuan' => 'PCS', 'harga_beli' => 5500,  'harga_jual' => 6000],
            ['kode_barang' => '108', 'nama_barang' => 'JK Pen GP-265 Htm',                             'stok' => 12, 'satuan' => 'PCS', 'harga_beli' => 4000,  'harga_jual' => 4500],
            ['kode_barang' => '109', 'nama_barang' => 'JK Pen GP-265 Bru',                             'stok' => 12, 'satuan' => 'PCS', 'harga_beli' => 4000,  'harga_jual' => 4500],
            ['kode_barang' => '110', 'nama_barang' => 'JK Pensil P-88ER',                              'stok' => 12, 'satuan' => 'PCS', 'harga_beli' => 708,   'harga_jual' => 800],
            ['kode_barang' => '111', 'nama_barang' => 'Post-It S Note 657',                            'stok' => 2,  'satuan' => 'PCS', 'harga_beli' => 25500, 'harga_jual' => 28000],
            ['kode_barang' => '112', 'nama_barang' => 'JK S Notes MMS-1',                              'stok' => 1,  'satuan' => 'PCS', 'harga_beli' => 25500, 'harga_jual' => 28000],
            ['kode_barang' => '113', 'nama_barang' => 'JK Glue Stick GS-104',                          'stok' => 2,  'satuan' => 'PCS', 'harga_beli' => 3500,  'harga_jual' => 4000],
            ['kode_barang' => '114', 'nama_barang' => 'JK Glue Stick GS-25',                           'stok' => 1,  'satuan' => 'PCS', 'harga_beli' => 7500,  'harga_jual' => 8000],
            ['kode_barang' => '115', 'nama_barang' => 'Vanco Stabilo HL-6830',                         'stok' => 1,  'satuan' => 'PCS', 'harga_beli' => 19500, 'harga_jual' => 21000],
            ['kode_barang' => '116', 'nama_barang' => 'Pcs XLJBD 814',                                 'stok' => 1,  'satuan' => 'PCS', 'harga_beli' => 41500, 'harga_jual' => 45000],
            ['kode_barang' => '117', 'nama_barang' => 'UHU Glue Stick 21gr',                           'stok' => 1,  'satuan' => 'PCS', 'harga_beli' => 25500, 'harga_jual' => 28000],
            ['kode_barang' => '118', 'nama_barang' => 'Bola Dunia FC 80 A4',                           'stok' => 3,  'satuan' => 'PCS', 'harga_beli' => 64500, 'harga_jual' => 70000],
            ['kode_barang' => '119', 'nama_barang' => 'Deli Notebook A5 Spiral LA560-06',              'stok' => 4,  'satuan' => 'PCS', 'harga_beli' => 14900, 'harga_jual' => 16000],
            ['kode_barang' => '120', 'nama_barang' => 'Kenko Note Book A5-8922',                       'stok' => 1,  'satuan' => 'PCS', 'harga_beli' => 15500, 'harga_jual' => 17000],
            ['kode_barang' => '121', 'nama_barang' => 'Orenji Notebk A5-LN LGPRP HC-NBA5005-S',       'stok' => 1,  'satuan' => 'PCS', 'harga_beli' => 16500, 'harga_jual' => 18000],
            ['kode_barang' => '122', 'nama_barang' => 'Orenji Notebk MN A5-LN PK PP-NBA5001-S',       'stok' => 1,  'satuan' => 'PCS', 'harga_beli' => 15900, 'harga_jual' => 17000],
            ['kode_barang' => '123', 'nama_barang' => 'HVS Paperone 75 A4',                            'stok' => 4,  'satuan' => 'PCS', 'harga_beli' => 55500, 'harga_jual' => 60000],
            ['kode_barang' => '124', 'nama_barang' => 'Kingmion File Bag EVA DBL LYR A4-271',          'stok' => 1,  'satuan' => 'PCS', 'harga_beli' => 21500, 'harga_jual' => 23500],
            ['kode_barang' => '125', 'nama_barang' => 'Kenko Sharpener A-5 AD',                        'stok' => 1,  'satuan' => 'PCS', 'harga_beli' => 98900, 'harga_jual' => 107000],
            ['kode_barang' => '126', 'nama_barang' => 'Alligator Binder Clip No.200 41mm',             'stok' => 2,  'satuan' => 'PCS', 'harga_beli' => 17500, 'harga_jual' => 19000],
            ['kode_barang' => '127', 'nama_barang' => 'Kenko Pensil 2B Artclub Drawing',               'stok' => 1,  'satuan' => 'PCS', 'harga_beli' => 15900, 'harga_jual' => 17000],
            ['kode_barang' => '128', 'nama_barang' => 'Kenko Mini Cutter KY-CS815B',                   'stok' => 1,  'satuan' => 'PCS', 'harga_beli' => 10500, 'harga_jual' => 11500],
            ['kode_barang' => '129', 'nama_barang' => 'Snowman Brush Pen 12 W',                        'stok' => 1,  'satuan' => 'PCS', 'harga_beli' => 99500, 'harga_jual' => 107500],
            ['kode_barang' => '130', 'nama_barang' => 'Kenko Correction Tape CT-2020MR',               'stok' => 4,  'satuan' => 'PCS', 'harga_beli' => 10900, 'harga_jual' => 12000],
            ['kode_barang' => '131', 'nama_barang' => 'Jepitan Buldog',                                'stok' => 4,  'satuan' => 'PCS', 'harga_beli' => 12500, 'harga_jual' => 13500],
        ];

        foreach ($barangs as $barang) {
            $existing = DB::table('barangs')
                ->where('kode_barang', $barang['kode_barang'])
                ->where('work_unit_id', $workUnitId)
                ->first();

            if ($existing) {
                DB::table('barangs')
                    ->where('id', $existing->id)
                    ->update([
                        'nama_barang'  => $barang['nama_barang'],
                        'stok'         => $barang['stok'],
                        'satuan'       => $barang['satuan'],
                        'harga_beli'   => $barang['harga_beli'],
                        'harga_jual'   => $barang['harga_jual'],
                        'supplier_id'  => $supplierId,
                        'supplier'     => 'RBP',
                        'jenis_barang' => 'konsinyasi',
                        'updated_at'   => $now,
                    ]);
            } else {
                DB::table('barangs')->insert([
                    'work_unit_id' => $workUnitId,
                    'kode_barang'  => $barang['kode_barang'],
                    'nama_barang'  => $barang['nama_barang'],
                    'stok'         => $barang['stok'],
                    'satuan'       => $barang['satuan'],
                    'harga_beli'   => $barang['harga_beli'],
                    'harga_jual'   => $barang['harga_jual'],
                    'supplier_id'  => $supplierId,
                    'supplier'     => 'RBP',
                    'jenis_barang' => 'konsinyasi',
                    'is_active'    => 1,
                    'created_at'   => $now,
                    'updated_at'   => $now,
                ]);
            }
        }
    }
}
