# Panduan Kantin

Untuk role `canteen` (kantin) dan `shop` (toko). Login di `/pengelola/login`. Menu utama: Point of Sale, Pesanan, Stok, Rekap.

---

## Login

1. Buka `/pengelola/login`.
2. Isi email dan password. Klik **Masuk**.

---

## Point of Sale (Kasir)

Untuk transaksi jual langsung.

1. Sidebar > **Point of Sale**.
2. Cari barang: ketik nama/PLU di kolom cari, atau klik kartu barang.
3. Klik barang untuk menambah ke keranjang. Atur jumlah dengan tombol **+ / -**.
4. (Opsional) Kaitkan member:
   - Klik **Cari Member**, pilih pembeli.
   - Poin dan tier member muncul otomatis.
5. (Opsional) Terapkan diskon/voucher:
   - Masukkan kode voucher, sistem cek validitas.
   - Untuk tukar poin, gunakan **Redeem** (lihat preview potongan sebelum konfirmasi).
6. Isi nominal **Bayar** (tunai). Kembalian dihitung otomatis.
7. Klik **Simpan / Bayar**. Struk transaksi tersimpan.

**Catatan penting:**
- Barang habis stok bisa disembunyikan lewat toggle "Sembunyikan barang habis".
- Klik **Refresh Barang** bila daftar barang belum ter-update.

---

## Pesanan Kantin (Self-Order Masuk)

Menangani pesanan yang dikirim member lewat self-order.

1. Sidebar > **Point of Sale** > **Pesanan Kantin** (atau **Pesanan Toko** untuk shop).
2. Pesanan baru muncul otomatis (ada notifikasi bunyi/pop-up).
3. Untuk tiap pesanan:
   - **Lihat detail** item dan catatan pembeli.
   - **Edit item** bila perlu koreksi (tambah/kurang).
   - Cek bukti bayar yang di-upload pembeli.
4. Tandai status:
   - **Tandai Bayar** jika pembayaran sudah masuk.
   - **Update Status** (diproses / siap / selesai).
5. Jika pesanan dibatalkan pembeli/kasir, gunakan **Cancel** atau **Cancel with Penjualan** (batal sekaligus catat ke penjualan bila sudah terlanjur dibuat).

---

## Live Order

Pantau pesanan masuk real-time (layar dapur/counter).

1. Buka `/live-order` atau pilih per unit.
2. Masukkan passcode bila diminta.
3. Layar menampilkan antrian pesanan yang perlu disiapkan.

---

## Stok Barang (Opname Stock)

1. Sidebar > **Opname Stock** > pilih unit.
2. **Lihat/ubah stok** di tab Stock Opname: isi stok fisik hasil hitung, simpan.
3. **Ajukan barang** (bila stok perlu ditambah): buka barang > **Pengajuan**, isi jumlah, kirim. Menunggu persetujuan officer.
4. **Ajukan barang baru**: tab **Pengajuan Tambah** > isi data barang baru > kirim.

Catatan: kantin hanya mengajukan; persetujuan dilakukan officer/sysadmin.

---

## Verifikasi & Rekap Penjualan

1. Sidebar > **Rekap Penjualan**.
2. Pilih rentang tanggal.
3. Periksa daftar transaksi. Untuk tiap transaksi bisa:
   - **Verify** (tandai sudah dicek) atau **Verify All / Bulk**.
   - Lihat detail dan bukti foto.
4. Ekspor ringkasan via **Export Item Summary**.

Catatan: approve final dan pencatatan ke buku kas dilakukan officer/sysadmin.

---

## Ganti Password

Klik nama di sidebar > **Ganti Password**. Isi password lama dan baru. Simpan.

---

## Logout

Klik nama di sidebar > **Logout**.
