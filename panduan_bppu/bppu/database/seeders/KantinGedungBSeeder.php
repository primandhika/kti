<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Barang;
use App\Models\WorkUnit;
use Carbon\Carbon;

class KantinGedungBSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kantinGedungB = WorkUnit::where('name', 'Kantin Gedung B')->first();

        if (!$kantinGedungB) {
            $this->command->error('Kantin Gedung B tidak ditemukan!');
            return;
        }

        $barangs = [
            [
                'kode_barang' => '8996001316009',
                'nama_barang' => 'Bonita Keju Susu',
                'stok' => 12,
                'harga_beli' => 2000,
                'harga_jual' => 2500,
                'tanggal_kadaluarsa' => Carbon::createFromFormat('dmy', '010926')->format('Y-m-d'), // 01 Sept 2026
            ],
            [
                'kode_barang' => '8996001305119',
                'nama_barang' => 'Arden Choco',
                'stok' => 10,
                'harga_beli' => 2000,
                'harga_jual' => 2500,
                'tanggal_kadaluarsa' => Carbon::createFromFormat('dmy', '011226')->format('Y-m-d'), // 01 Des 2026
            ],
            [
                'kode_barang' => '8993175563413',
                'nama_barang' => 'Chocolate Pie Nextar',
                'stok' => 12,
                'harga_beli' => 2000,
                'harga_jual' => 2500,
                'tanggal_kadaluarsa' => Carbon::createFromFormat('dmy', '300626')->format('Y-m-d'), // 30 Juni 2026
            ],
            [
                'kode_barang' => '8996001304549',
                'nama_barang' => "Slai O'Lai",
                'stok' => 10,
                'harga_beli' => 2000,
                'harga_jual' => 2500,
                'tanggal_kadaluarsa' => Carbon::createFromFormat('dmy', '011226')->format('Y-m-d'), // 01 Des 2026
            ],
        ];

        foreach ($barangs as $barangData) {
            // Check if barang already exists
            $existing = Barang::where('kode_barang', $barangData['kode_barang'])
                ->where('work_unit_id', $kantinGedungB->id)
                ->first();

            if ($existing) {
                $this->command->warn("Barang {$barangData['nama_barang']} sudah ada, skip...");
                continue;
            }

            Barang::create([
                'kode_barang' => $barangData['kode_barang'],
                'nama_barang' => $barangData['nama_barang'],
                'kategori' => 'Makanan',
                'satuan' => 'pcs',
                'stok' => $barangData['stok'],
                'harga_beli' => $barangData['harga_beli'],
                'harga_jual' => $barangData['harga_jual'],
                'tanggal_kadaluarsa' => $barangData['tanggal_kadaluarsa'],
                'work_unit_id' => $kantinGedungB->id,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->command->info("✓ Barang {$barangData['nama_barang']} berhasil ditambahkan");
        }

        $this->command->info("Seeder Kantin Gedung B selesai!");
    }
}
