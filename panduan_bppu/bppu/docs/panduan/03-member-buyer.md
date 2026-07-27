# Panduan Member Buyer

Untuk pembeli yang memesan mandiri lewat website. Berlaku untuk self-order kantin (`/kantin/self-order`) dan belanja toko (`/belanja/self-order`).

---

## Registrasi

1. Buka `/member/register` (atau klik **Daftar** pada modal login di halaman self-order).
2. Isi nama lengkap, email, password, konfirmasi password.
3. (Bila diminta) isi survey singkat lalu kirim.
4. Cek email untuk kode/verifikasi, buka `/member/verify-email`, masukkan kode.
5. Akun aktif sebagai buyer.

---

## Login

1. Di halaman self-order, klik tombol login.
2. Masukkan email dan password. Klik **Login**.
3. Setelah login, katalog aktif dan nama muncul di kanan atas.

---

## Memesan (Self-Order Kantin / Belanja)

1. Buka `/kantin/self-order` (menu makanan) atau `/belanja/self-order` (barang toko).
2. Cari menu: pakai **search**, filter **kategori/sub-kategori**, atau **sort** harga/nama.
3. Tambah ke keranjang:
   - Tanpa varian: klik **+ Keranjang**.
   - Dengan varian: klik **Pilih Varian**, pilih, lalu **Tambah ke Keranjang**.
4. Buka keranjang (ikon di pojok):
   - Ubah jumlah dengan **+ / -**, hapus item dengan **X**.
   - Isi **Catatan untuk Kantin** bila perlu (mis. "tidak pedas"), maks 255 karakter.
5. Pastikan sudah login, lalu klik **Checkout**.
6. Otomatis diarahkan ke halaman **Invoice**.

---

## Invoice & Nomor Antrian

1. Halaman invoice menampilkan **nomor antrian**, status, item, dan total.
2. Status berubah otomatis tiap 5 detik:
   - **Menunggu** > **Diproses** > **Siap** > **Selesai** (atau **Dibatalkan**).
3. Aktifkan notifikasi browser agar dapat pemberitahuan saat status berubah.
4. **Jangan tutup halaman invoice** sampai pesanan selesai.

Catatan: jika 5 menit tidak diproses kantin, pesanan bisa otomatis batal (ada peringatan 30 detik sebelumnya).

---

## Bayar & Upload Bukti

1. Lakukan pembayaran sesuai metode yang tersedia.
2. Bila diminta, klik **Upload Bukti Bayar** pada halaman invoice, pilih foto bukti transfer.
3. Tunggu kantin memverifikasi.

---

## Ambil Pesanan

1. Saat status **Siap**, muncul notifikasi "Pesanan siap".
2. Datang ke kasir, tunjukkan halaman invoice (nomor antrian).
3. Selesaikan pembayaran bila belum, terima pesanan.

---

## Batalkan Pesanan

1. Di halaman invoice, selama status masih **Menunggu**, klik **Batalkan**.
2. Konfirmasi. Pesanan berpindah ke status **Dibatalkan**.

---

## Cetak Invoice (Opsional)

1. Di halaman invoice, klik **Cetak**.
2. Pilih printer atau **Save as PDF** (format struk 58mm).

---

## Member Area

Buka `/member-area` setelah login.

- **Profil:** ubah nama/data diri, klik **Simpan**.
- **Password:** isi password lama dan baru, klik **Simpan**.
- **Meja default:** pilih meja langganan agar tidak perlu pilih tiap pesan.

---

## Poin & Membership

- Poin bertambah otomatis tiap transaksi (semakin besar transaksi, semakin besar poin).
- Tier: **Bronze > Silver > Gold > Platinum**. Naik tier berdasarkan total poin, dapat diskon dan pengali poin lebih tinggi.
- Cek saldo poin di **Member Area**.
- Rincian skema poin ada di [POINT_SYSTEM.md](../POINT_SYSTEM.md).

---

## Voucher

1. Voucher yang di-assign ke akun muncul saat transaksi.
2. Saat bayar di kasir/checkout, sebutkan atau masukkan kode voucher.
3. Potongan diterapkan bila voucher valid dan memenuhi syarat.

---

## Logout

Klik nama/menu akun di kanan atas, pilih **Logout**.
