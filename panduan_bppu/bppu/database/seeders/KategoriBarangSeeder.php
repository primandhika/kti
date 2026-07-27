<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KategoriBarangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kategoris = [
            [
                'nama' => 'Makanan & Minuman',
                'kode' => 'F&B',
                'deskripsi' => 'Kategori untuk produk makanan dan minuman',
                'is_active' => true,
                'display_order' => 1,
            ],
            [
                'nama' => 'Alat Tulis Kantor',
                'kode' => 'ATK',
                'deskripsi' => 'Kategori untuk alat tulis dan perlengkapan kantor',
                'is_active' => true,
                'display_order' => 2,
            ],
            [
                'nama' => 'Elektronik',
                'kode' => 'ELK',
                'deskripsi' => 'Kategori untuk peralatan elektronik',
                'is_active' => true,
                'display_order' => 3,
            ],
            [
                'nama' => 'Buku & Majalah',
                'kode' => 'BKM',
                'deskripsi' => 'Kategori untuk buku, majalah, dan publikasi',
                'is_active' => true,
                'display_order' => 4,
            ],
            [
                'nama' => 'Pakaian & Aksesoris',
                'kode' => 'PKA',
                'deskripsi' => 'Kategori untuk pakaian dan aksesoris',
                'is_active' => true,
                'display_order' => 5,
            ],
            [
                'nama' => 'Perlengkapan Olahraga',
                'kode' => 'OLH',
                'deskripsi' => 'Kategori untuk perlengkapan olahraga',
                'is_active' => true,
                'display_order' => 6,
            ],
            [
                'nama' => 'Kesehatan & Kebersihan',
                'kode' => 'KKB',
                'deskripsi' => 'Kategori untuk produk kesehatan dan kebersihan',
                'is_active' => true,
                'display_order' => 7,
            ],
            [
                'nama' => 'Lainnya',
                'kode' => 'LN',
                'deskripsi' => 'Kategori untuk barang lain yang tidak termasuk kategori di atas',
                'is_active' => true,
                'display_order' => 99,
            ],
        ];

        foreach ($kategoris as $kategori) {
            \App\Models\KategoriBarang::create($kategori);
        }
    }
}
