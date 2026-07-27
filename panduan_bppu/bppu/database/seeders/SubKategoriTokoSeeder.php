<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubKategoriTokoSeeder extends Seeder
{
    public function run(): void
    {
        $kategoris = DB::table('kategori_barangs')->pluck('id', 'kode');

        $subKategoris = [
            // Makanan & Minuman (F&B)
            ['kategori' => 'F&B', 'nama' => 'Minuman Ringan', 'urutan' => 1],
            ['kategori' => 'F&B', 'nama' => 'Kopi & Teh', 'urutan' => 2],
            ['kategori' => 'F&B', 'nama' => 'Air Mineral', 'urutan' => 3],

            // Kudapan (SN)
            ['kategori' => 'SN', 'nama' => 'Biskuit & Wafer', 'urutan' => 4],
            ['kategori' => 'SN', 'nama' => 'Keripik & Snack', 'urutan' => 5],
            ['kategori' => 'SN', 'nama' => 'Coklat & Permen', 'urutan' => 6],

            // Bahan Pokok (BP)
            ['kategori' => 'BP', 'nama' => 'Beras & Tepung', 'urutan' => 7],
            ['kategori' => 'BP', 'nama' => 'Gula & Garam', 'urutan' => 8],
            ['kategori' => 'BP', 'nama' => 'Mie Instan', 'urutan' => 9],

            // Bumbu & Penyedap (BPY)
            ['kategori' => 'BPY', 'nama' => 'Bumbu Racik', 'urutan' => 10],
            ['kategori' => 'BPY', 'nama' => 'Saus & Kecap', 'urutan' => 11],

            // Minyak & Margarin (MYK)
            ['kategori' => 'MYK', 'nama' => 'Minyak Goreng', 'urutan' => 12],
            ['kategori' => 'MYK', 'nama' => 'Margarin & Mentega', 'urutan' => 13],

            // Produk Susu (PSU)
            ['kategori' => 'PSU', 'nama' => 'Susu Cair', 'urutan' => 14],
            ['kategori' => 'PSU', 'nama' => 'Susu Bubuk', 'urutan' => 15],

            // Perawatan Pribadi (PPR)
            ['kategori' => 'PPR', 'nama' => 'Sabun & Shampo', 'urutan' => 16],
            ['kategori' => 'PPR', 'nama' => 'Pasta Gigi', 'urutan' => 17],
            ['kategori' => 'PPR', 'nama' => 'Tissue & Pembalut', 'urutan' => 18],

            // Kebersihan Rumah (KBR)
            ['kategori' => 'KBR', 'nama' => 'Sabun Cuci', 'urutan' => 19],
            ['kategori' => 'KBR', 'nama' => 'Pembersih Rumah', 'urutan' => 20],

            // Kesehatan & Kebersihan (KKB)
            ['kategori' => 'KKB', 'nama' => 'Obat & Vitamin', 'urutan' => 21],
            ['kategori' => 'KKB', 'nama' => 'Hand Sanitizer', 'urutan' => 22],

            // Produk Bayi (BBY)
            ['kategori' => 'BBY', 'nama' => 'Susu Formula', 'urutan' => 23],
            ['kategori' => 'BBY', 'nama' => 'Popok Bayi', 'urutan' => 24],

            // Alat Tulis Kantor (ATK)
            ['kategori' => 'ATK', 'nama' => 'Alat Tulis', 'urutan' => 25],
            ['kategori' => 'ATK', 'nama' => 'Buku & Kertas', 'urutan' => 26],

            // Peralatan Rumah Tangga (PRT)
            ['kategori' => 'PRT', 'nama' => 'Alat Makan', 'urutan' => 27],
            ['kategori' => 'PRT', 'nama' => 'Alat Dapur', 'urutan' => 28],

            // Frozen Food (FRZ)
            ['kategori' => 'FRZ', 'nama' => 'Nugget & Sosis', 'urutan' => 29],
            ['kategori' => 'FRZ', 'nama' => 'Frozen Lainnya', 'urutan' => 30],

            // Roti & Kue (RKU)
            ['kategori' => 'RKU', 'nama' => 'Roti', 'urutan' => 31],
            ['kategori' => 'RKU', 'nama' => 'Kue & Bakery', 'urutan' => 32],

            // Rokok & Tembakau (RKK)
            ['kategori' => 'RKK', 'nama' => 'Rokok Filter', 'urutan' => 33],
            ['kategori' => 'RKK', 'nama' => 'Rokok Kretek', 'urutan' => 34],

            // Pakaian & Aksesoris (PKA)
            ['kategori' => 'PKA', 'nama' => 'Pakaian', 'urutan' => 35],
            ['kategori' => 'PKA', 'nama' => 'Aksesoris', 'urutan' => 36],

            // Perlengkapan Olahraga (OLH)
            ['kategori' => 'OLH', 'nama' => 'Alat Olahraga', 'urutan' => 37],

            // Souvernir (SV)
            ['kategori' => 'SV', 'nama' => 'Souvenir IKIP', 'urutan' => 38],

            // Lainnya (LN)
            ['kategori' => 'LN', 'nama' => 'Lainnya', 'urutan' => 39],
        ];

        foreach ($subKategoris as $subKategori) {
            $kategoriId = $kategoris[$subKategori['kategori']] ?? null;

            if ($kategoriId) {
                DB::table('sub_kategori_toko')->updateOrInsert(
                    ['nama' => $subKategori['nama']],
                    [
                        'kategori_barang_id' => $kategoriId,
                        'urutan' => $subKategori['urutan'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }
    }
}
