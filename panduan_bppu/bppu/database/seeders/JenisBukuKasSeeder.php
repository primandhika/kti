<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\JenisBukuKas;

class JenisBukuKasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jenisBukuKas = [
            [
                'nama' => 'Kas Umum',
                'kode' => 'KU',
                'deskripsi' => 'Buku kas untuk pencatatan transaksi umum sehari-hari',
                'warna' => '#996600',
                'display_order' => 1,
            ],
            [
                'nama' => 'Buku Besar',
                'kode' => 'BB',
                'deskripsi' => 'Buku besar untuk rekap seluruh transaksi keuangan',
                'warna' => '#2563eb',
                'display_order' => 2,
            ],
            [
                'nama' => 'Kas Kecil',
                'kode' => 'KK',
                'deskripsi' => 'Buku kas untuk pengeluaran operasional harian dalam jumlah kecil',
                'warna' => '#16a34a',
                'display_order' => 3,
            ],
            [
                'nama' => 'Kas Bank',
                'kode' => 'KB',
                'deskripsi' => 'Buku kas untuk pencatatan transaksi melalui bank',
                'warna' => '#dc2626',
                'display_order' => 4,
            ],
            [
                'nama' => 'Jurnal Umum',
                'kode' => 'JU',
                'deskripsi' => 'Jurnal untuk mencatat semua transaksi keuangan secara kronologis',
                'warna' => '#9333ea',
                'display_order' => 5,
            ],
            [
                'nama' => 'Jurnal Penerimaan',
                'kode' => 'JP',
                'deskripsi' => 'Jurnal khusus untuk mencatat penerimaan kas dan setara kas',
                'warna' => '#0891b2',
                'display_order' => 6,
            ],
            [
                'nama' => 'Jurnal Pengeluaran',
                'kode' => 'JK',
                'deskripsi' => 'Jurnal khusus untuk mencatat pengeluaran kas dan setara kas',
                'warna' => '#ea580c',
                'display_order' => 7,
            ],
            [
                'nama' => 'Kas Proyek',
                'kode' => 'KP',
                'deskripsi' => 'Buku kas khusus untuk pencatatan keuangan proyek tertentu',
                'warna' => '#7c3aed',
                'display_order' => 8,
            ],
        ];

        foreach ($jenisBukuKas as $jenis) {
            JenisBukuKas::create($jenis);
        }
    }
}
