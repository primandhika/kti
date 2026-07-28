# Panduan Admin & Officer

Portal pengelola. Login di `/pengelola/login`. Menu sidebar menyesuaikan role.

---

## Login

1. Buka `/pengelola/login`.
2. Isi email dan password.
3. Klik **Masuk**. Diarahkan ke **Dasbor**.

---

## Dasbor

1. Klik **Beranda** di sidebar.
2. Lihat ringkasan: penjualan, transaksi, saldo kas, aktivitas terbaru.
3. Untuk unduh laporan eksekutif, klik tombol **Export** di kanan atas.

---

## Pos Berita

Kelola artikel berita publik.

**Tambah berita:**
1. Sidebar > **Pos Berita** > **Tambah**.
2. Isi judul, kategori, konten, tag, dan gambar sampul.
3. Untuk sisipkan gambar di isi, gunakan tombol upload gambar pada editor.
4. Pilih status **Published** (tampil publik) atau **Draft**.
5. Klik **Simpan**.

**Edit / hapus:** klik ikon edit atau hapus pada baris berita.

---

## Halaman

Kelola halaman statis (profil, kontak, dll).

1. Sidebar > **Halaman** > **Tambah**.
2. Isi judul, slug, kategori (opsional), dan konten.
3. Klik **Simpan**.

---

## Menu Navbar

Atur item menu di navbar publik.

1. Sidebar > **Menu Navbar**.
2. Klik **Tambah** untuk item baru; isi label dan tautan.
3. Seret item untuk mengubah urutan.
4. Klik **Simpan**.

---

## Media Arsip

Simpan file/gambar yang dipakai ulang.

1. Sidebar > **Media Arsip**.
2. Klik **Upload**, pilih file.
3. Untuk cek pemakaian file, klik **Usage** pada item.
4. Hapus hanya jika tidak dipakai.

---

## Menu Kantin

Atur menu yang tampil di self-order kantin.

1. Sidebar > **Menu Kantin**.
2. Untuk tambah menu baru, klik **Buat**, isi nama, harga, kategori, sub-kategori, foto, deskripsi.
3. Untuk atur tampilan (foto/deskripsi display), klik menu lalu edit bagian **Display**.
4. Toggle **Tersedia / Habis** dengan tombol availability pada tiap item.
5. Seret untuk atur urutan tampil.
6. Klik **Download** untuk ekspor daftar menu.

---

## Item Belanja

Sama seperti Menu Kantin, untuk katalog toko/belanja.

1. Sidebar > **Item Belanja**.
2. Atur display, harga, dan status **Tersedia / Habis** per item.

---

## Meja

Kelola meja dan lokasi meja untuk self-order.

**Lokasi meja:**
1. Sidebar > **Meja** > tab **Lokasi**.
2. Klik **Tambah**, isi nama lokasi (mis. "Lantai 1"). Simpan.

**Meja:**
1. Di tab **Meja**, klik **Tambah**.
2. Isi nomor meja, pilih lokasi. Simpan.
3. Toggle aktif/nonaktif per meja.
4. Klik **Cetak PDF** untuk mencetak QR/kartu meja.

---

## Buku Kas

Catat pemasukan dan pengeluaran per unit. (Officer/Sysadmin bisa tulis; Head hanya lihat.)

**Buat buku kas:**
1. Sidebar > **Buku Kas** > **Tambah**.
2. Isi nama buku kas dan saldo awal. Simpan.

**Catat transaksi:**
1. Klik buku kas untuk membuka detail.
2. Klik **Tambah Transaksi**.
3. Pilih jenis (masuk/keluar), kategori, nominal, keterangan, tanggal. Simpan.
4. Untuk catat penjualan per tanggal otomatis, gunakan **Record Penjualan by Date**.

**Ekspor:** klik **Export CSV** atau **Export XLSX**.

**Recycle Bin (Sysadmin):** buka daftar terhapus, klik **Restore** atau **Hapus Permanen**.

---

## Opname Stock

Kelola stok, barang, dan pengajuan per unit kerja.

**Pilih unit:**
1. Sidebar > **Opname Stock**.
2. Pilih unit kerja yang akan dikelola.

**Kelola barang:**
1. Tab **Barang** > **Tambah**, isi nama, PLU (bisa **Generate PLU**), harga beli, harga jual, kategori.
2. Import massal via **Import CSV**.
3. Upload gambar barang pada tiap item bila perlu.
4. Cetak label harga via **Print Price Tags**.

**Stock opname:**
1. Tab **Stock Opname** > **Tambah** atau **Auto Import** dari penjualan.
2. Isi stok fisik hasil hitung. Simpan.
3. Lihat selisih di **Summary**; unduh via **Download PDF** / **Export CSV**.

**Proses pengajuan barang (dari kantin):**
1. Buka **Pengajuan** pada barang terkait.
2. Setujui satu per satu, atau **Bulk Approve**.
3. Pengajuan tambah barang baru: tab **Pengajuan Tambah** > **Proses**.

---

## Point of Sale (POS)

Lihat panduan kasir lengkap di bagian Panduan Kantin & Toko. Officer/Sysadmin punya akses penuh POS.

---

## Rekap Penjualan

1. Sidebar > **Rekap Penjualan**.
2. Pilih rentang tanggal dan unit.
3. Ekspor: **Export Item Summary** atau **Download PDF** (officer/sysadmin).

**Buku Tagihan (mitra):**
1. Buka **Buku Tagihan**, pilih periode. Ekspor CSV/PDF.

**Pembagian Mitra:**
1. Buka **Pembagian Mitra**, pilih periode.
2. Klik **Simpan Riwayat** untuk mengunci pembagian.
3. Tandai **Cair** saat dana dibayarkan; bisa **Batalkan Cair**.
4. Proses **Pengajuan Pencairan** dari mitra: **Proses** > **Selesai** atau **Tolak**.

---

## Live Transaction

1. Sidebar > **Live Transaction**.
2. Pilih unit; masukkan passcode bila diminta.
3. Pantau transaksi masuk secara real-time.

---

## Laporan Penjualan

1. Sidebar > **Laporan Penjualan**.
2. Pilih rentang tanggal.
3. Klik baris untuk **detail transaksi barang**.
4. Ekspor via **Export** atau **PDF Mitra-Produk**.

---

## Laporan Umum

1. Sidebar > **Laporan Umum**.
2. Pilih periode untuk lihat ringkasan keuangan.
3. Klik **Detail** untuk rincian; **Export CSV** untuk unduh.

---

## Diskon & Voucher

**Buat diskon:**
1. Sidebar > **Diskon & Voucher** > **Tambah**.
2. Isi nama, tipe (persen/nominal), nilai, syarat, masa berlaku. Simpan.

**Generate voucher:**
1. Buka diskon terkait > **Vouchers**.
2. Klik **Generate**, tentukan jumlah kode.
3. **Assign** ke member (satuan atau **Assign Bulk**).
4. Cetak: **Print**, **Print All**, atau **Print PDF**.
5. Import kode via **Import CSV**; ekspor via **Export Voucher**.

**Redeem Poin:**
1. Buka **Diskon & Voucher** > **Redeem Poin**.
2. Lihat daftar penukaran poin member.
3. **Catat ke Buku Kas** untuk membukukan; **Export** untuk unduh.

---

## Hasil Survey (Sysadmin)

1. Sidebar > **Hasil Survey**.
2. Lihat jawaban survey saat registrasi member.

---

## Pengguna

Kelola akun. (Data clerk hanya bisa kelola buyer.)

1. Sidebar > **Pengguna**.
2. Klik **Tambah**, isi nama, email, password, role, unit (bila perlu). Simpan.
3. Edit atau hapus lewat ikon pada baris.
4. Cari member via **Search Member**.

**Sub-menu di Pengguna:**
- **Unit Kerja** (sysadmin): tambah/edit unit, toggle aktif, atur urutan.
- **Kategori Transaksi**: kategori untuk buku kas.
- **Kategori Barang**: kategori untuk barang/menu.
- **Sub Kategori Menu / Toko**: sub-kategori tampilan katalog.

---

## Log Aktivitas (Sysadmin)

1. Sidebar > **Log Aktivitas**.
2. Telusuri riwayat aksi pengguna di sistem.

---

## Mitra Usaha

1. Sidebar > **Mitra Usaha**.
2. Klik **Tambah**, isi nama, kontak, logo, bagi hasil. Simpan. (Head hanya lihat.)
3. Unduh daftar via **Download PDF**.

---

## Setting

1. Sidebar > **Setting** (bukan untuk role kantin).
2. Atur:
   - **Report Settings**: format/isi laporan.
   - **Points Settings**: skema poin dan tier member.
   - **Self-Order Settings**: opsi checkout, passcode, dsb.
3. **Point Rules** (sysadmin): tambah/edit aturan perolehan poin, toggle aktif.
4. Klik **Simpan** di tiap tab.

---

## Ganti Password

1. Klik avatar/nama di kiri bawah sidebar > **Ganti Password**.
2. Isi password lama dan baru. Simpan.

---

## Logout

Klik avatar/nama di sidebar > **Logout**.
