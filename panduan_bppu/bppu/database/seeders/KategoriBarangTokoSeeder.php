<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KategoriBarangTokoSeeder extends Seeder
{
    public function run(): void
    {
        $kategoris = [
            ['nama' => 'Makanan & Minuman', 'kode' => 'F&B', 'deskripsi' => 'Produk makanan dan minuman siap konsumsi', 'is_active' => true],
            ['nama' => 'Kudapan', 'kode' => 'SN', 'deskripsi' => 'Makanan ringan dan camilan', 'is_active' => true],
            ['nama' => 'Bahan Pokok', 'kode' => 'BP', 'deskripsi' => 'Bahan makanan kebutuhan sehari-hari', 'is_active' => true],
            ['nama' => 'Bumbu & Penyedap', 'kode' => 'BPY', 'deskripsi' => 'Bumbu masak dan penyedap rasa', 'is_active' => true],
            ['nama' => 'Minyak & Margarin', 'kode' => 'MYK', 'deskripsi' => 'Minyak goreng, margarin, dan lemak', 'is_active' => true],
            ['nama' => 'Produk Susu', 'kode' => 'PSU', 'deskripsi' => 'Susu dan produk turunannya', 'is_active' => true],
            ['nama' => 'Kesehatan & Kebersihan', 'kode' => 'KKB', 'deskripsi' => 'Produk kesehatan dan kebersihan', 'is_active' => true],
            ['nama' => 'Kebersihan Rumah', 'kode' => 'KBR', 'deskripsi' => 'Produk pembersih rumah tangga', 'is_active' => true],
            ['nama' => 'Perawatan Pribadi', 'kode' => 'PPR', 'deskripsi' => 'Sabun, shampo, dan perawatan tubuh', 'is_active' => true],
            ['nama' => 'Alat Tulis Kantor', 'kode' => 'ATK', 'deskripsi' => 'Alat tulis dan perlengkapan kantor', 'is_active' => true],
            ['nama' => 'Perlengkapan Olahraga', 'kode' => 'OLH', 'deskripsi' => 'Alat dan perlengkapan olahraga', 'is_active' => true],
            ['nama' => 'Pakaian & Aksesoris', 'kode' => 'PKA', 'deskripsi' => 'Pakaian, sepatu, dan aksesoris', 'is_active' => true],
            ['nama' => 'Souvernir', 'kode' => 'SV', 'deskripsi' => 'Souvenir dan cinderamata', 'is_active' => true],
            ['nama' => 'Rokok & Tembakau', 'kode' => 'RKK', 'deskripsi' => 'Rokok dan produk tembakau', 'is_active' => true],
            ['nama' => 'Produk Bayi', 'kode' => 'BBY', 'deskripsi' => 'Produk untuk bayi dan balita', 'is_active' => true],
            ['nama' => 'Peralatan Rumah Tangga', 'kode' => 'PRT', 'deskripsi' => 'Peralatan dan perlengkapan rumah tangga', 'is_active' => true],
            ['nama' => 'Frozen Food', 'kode' => 'FRZ', 'deskripsi' => 'Makanan beku dan frozen', 'is_active' => true],
            ['nama' => 'Roti & Kue', 'kode' => 'RKU', 'deskripsi' => 'Roti, kue, dan bakery', 'is_active' => true],
            ['nama' => 'Lainnya', 'kode' => 'LN', 'deskripsi' => 'Produk lain-lain', 'is_active' => true],
        ];

        foreach ($kategoris as $kategori) {
            DB::table('kategori_barangs')->updateOrInsert(
                ['kode' => $kategori['kode']],
                array_merge($kategori, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
