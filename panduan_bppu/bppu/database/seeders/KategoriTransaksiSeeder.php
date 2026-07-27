<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KategoriTransaksiSeeder extends Seeder
{
    /**
     * Run the database seeds for BPPU IKIP Siliwangi
     */
    public function run(): void
    {
        // Truncate existing data
        DB::table('kategori_transaksi')->truncate();

        $kategori = [
            // ==================== PEMASUKAN ====================
            // Pendapatan Operasional (4-1xx)
            [
                'nama' => 'Penjualan Makanan & Minuman',
                'jenis' => 'pemasukan',
                'kode_akun' => '4-100',
                'deskripsi' => 'Pendapatan dari penjualan makanan dan minuman di kantin',
                'urutan' => 1,
            ],
            [
                'nama' => 'Penjualan ATK & Fotokopi',
                'jenis' => 'pemasukan',
                'kode_akun' => '4-110',
                'deskripsi' => 'Pendapatan dari penjualan alat tulis kantor dan jasa fotokopi',
                'urutan' => 2,
            ],
            [
                'nama' => 'Penjualan Buku & Diktat',
                'jenis' => 'pemasukan',
                'kode_akun' => '4-120',
                'deskripsi' => 'Pendapatan dari penjualan buku dan diktat perkuliahan',
                'urutan' => 3,
            ],
            [
                'nama' => 'Penjualan Merchandise Kampus',
                'jenis' => 'pemasukan',
                'kode_akun' => '4-130',
                'deskripsi' => 'Pendapatan dari penjualan merchandise (jaket, kaos, topi almamater)',
                'urutan' => 4,
            ],
            [
                'nama' => 'Jasa Printing & Percetakan',
                'jenis' => 'pemasukan',
                'kode_akun' => '4-140',
                'deskripsi' => 'Pendapatan dari jasa printing, fotokopi, jilid, laminating',
                'urutan' => 5,
            ],
            [
                'nama' => 'Sewa/Rental Fasilitas',
                'jenis' => 'pemasukan',
                'kode_akun' => '4-150',
                'deskripsi' => 'Pendapatan dari sewa fasilitas kampus',
                'urutan' => 6,
            ],
            [
                'nama' => 'Pendapatan Lain-lain',
                'jenis' => 'pemasukan',
                'kode_akun' => '4-900',
                'deskripsi' => 'Pendapatan lainnya yang tidak termasuk kategori di atas',
                'urutan' => 7,
            ],

            // Modal (3-xxx)
            [
                'nama' => 'Modal Awal/Setoran Modal',
                'jenis' => 'pemasukan',
                'kode_akun' => '3-100',
                'deskripsi' => 'Setoran modal awal atau tambahan modal usaha',
                'urutan' => 8,
            ],

            // ==================== PENGELUARAN ====================
            // Pembelian & Persediaan (5-1xx)
            [
                'nama' => 'Pembelian Bahan Baku Kantin',
                'jenis' => 'pengeluaran',
                'kode_akun' => '5-100',
                'deskripsi' => 'Pembelian bahan baku untuk kantin (bahan makanan, bumbu, dll)',
                'urutan' => 10,
            ],
            [
                'nama' => 'Pembelian Barang Dagangan',
                'jenis' => 'pengeluaran',
                'kode_akun' => '5-110',
                'deskripsi' => 'Pembelian barang untuk dijual kembali (ATK, snack, minuman)',
                'urutan' => 11,
            ],
            [
                'nama' => 'Pembelian Perlengkapan Operasional',
                'jenis' => 'pengeluaran',
                'kode_akun' => '5-120',
                'deskripsi' => 'Pembelian perlengkapan operasional (tissue, plastik, kemasan)',
                'urutan' => 12,
            ],

            // Biaya Operasional - SDM (6-1xx)
            [
                'nama' => 'Gaji & Upah Karyawan',
                'jenis' => 'pengeluaran',
                'kode_akun' => '6-100',
                'deskripsi' => 'Pembayaran gaji dan upah karyawan',
                'urutan' => 20,
            ],
            [
                'nama' => 'Tunjangan & Insentif',
                'jenis' => 'pengeluaran',
                'kode_akun' => '6-110',
                'deskripsi' => 'Tunjangan karyawan, bonus, insentif',
                'urutan' => 21,
            ],

            // Biaya Operasional - Utilitas (6-2xx)
            [
                'nama' => 'Biaya Listrik & Air',
                'jenis' => 'pengeluaran',
                'kode_akun' => '6-200',
                'deskripsi' => 'Pembayaran tagihan listrik dan air',
                'urutan' => 30,
            ],
            [
                'nama' => 'Biaya Gas & Bahan Bakar',
                'jenis' => 'pengeluaran',
                'kode_akun' => '6-210',
                'deskripsi' => 'Pembelian gas LPG dan bahan bakar lainnya',
                'urutan' => 31,
            ],

            // Biaya Operasional - Umum (6-3xx)
            [
                'nama' => 'Biaya Transportasi & Logistik',
                'jenis' => 'pengeluaran',
                'kode_akun' => '6-300',
                'deskripsi' => 'Biaya transportasi, pengiriman, dan logistik',
                'urutan' => 40,
            ],
            [
                'nama' => 'Biaya Kebersihan & Sanitasi',
                'jenis' => 'pengeluaran',
                'kode_akun' => '6-310',
                'deskripsi' => 'Biaya kebersihan, sanitasi, dan bahan pembersih',
                'urutan' => 41,
            ],
            [
                'nama' => 'Biaya Perawatan & Perbaikan',
                'jenis' => 'pengeluaran',
                'kode_akun' => '6-320',
                'deskripsi' => 'Biaya perawatan dan perbaikan peralatan/fasilitas',
                'urutan' => 42,
            ],
            [
                'nama' => 'Biaya Administrasi & Umum',
                'jenis' => 'pengeluaran',
                'kode_akun' => '6-330',
                'deskripsi' => 'Biaya administrasi, ATK kantor, materai, dll',
                'urutan' => 43,
            ],
            [
                'nama' => 'Biaya Komunikasi & Internet',
                'jenis' => 'pengeluaran',
                'kode_akun' => '6-340',
                'deskripsi' => 'Biaya pulsa, paket data, internet, telepon',
                'urutan' => 44,
            ],
            [
                'nama' => 'Biaya Promosi & Pemasaran',
                'jenis' => 'pengeluaran',
                'kode_akun' => '6-350',
                'deskripsi' => 'Biaya promosi, iklan, spanduk, brosur',
                'urutan' => 45,
            ],

            // Investasi & Aset (1-2xx)
            [
                'nama' => 'Pembelian Peralatan & Mesin',
                'jenis' => 'pengeluaran',
                'kode_akun' => '1-200',
                'deskripsi' => 'Pembelian peralatan operasional (kompor, kulkas, mesin fotokopi)',
                'urutan' => 50,
            ],
            [
                'nama' => 'Pembelian Furniture & Perlengkapan',
                'jenis' => 'pengeluaran',
                'kode_akun' => '1-210',
                'deskripsi' => 'Pembelian meja, kursi, rak, etalase, dll',
                'urutan' => 51,
            ],

            // Pajak & Kewajiban (6-4xx)
            [
                'nama' => 'Pajak & Retribusi',
                'jenis' => 'pengeluaran',
                'kode_akun' => '6-400',
                'deskripsi' => 'Pembayaran pajak dan retribusi',
                'urutan' => 60,
            ],
            [
                'nama' => 'Iuran & Kontribusi',
                'jenis' => 'pengeluaran',
                'kode_akun' => '6-410',
                'deskripsi' => 'Iuran BPJS, kontribusi sosial, dll',
                'urutan' => 61,
            ],

            // Lainnya (6-9xx)
            [
                'nama' => 'Pengeluaran Lain-lain',
                'jenis' => 'pengeluaran',
                'kode_akun' => '6-900',
                'deskripsi' => 'Pengeluaran lainnya yang tidak termasuk kategori di atas',
                'urutan' => 90,
            ],

            // ==================== BOTH ====================
            [
                'nama' => 'Transfer Antar Kas',
                'jenis' => 'both',
                'kode_akun' => '2-100',
                'deskripsi' => 'Transfer dana antar buku kas atau unit kerja',
                'urutan' => 100,
            ],
            [
                'nama' => 'Koreksi/Penyesuaian',
                'jenis' => 'both',
                'kode_akun' => '9-100',
                'deskripsi' => 'Koreksi atau penyesuaian pencatatan',
                'urutan' => 101,
            ],
        ];

        foreach ($kategori as $item) {
            $item['is_active'] = true;
            $item['created_at'] = now();
            $item['updated_at'] = now();
            DB::table('kategori_transaksi')->insert($item);
        }

        $this->command->info('✅ Berhasil menambahkan ' . count($kategori) . ' kategori transaksi untuk BPPU IKIP Siliwangi');
    }
}
