# Panduan Self-Order Kantin BPPU

Sistem self-order memungkinkan pembeli memesan menu kantin secara mandiri melalui website, tanpa antri di kasir.

---

## A. PANDUAN UNTUK PEMBELI

### 1. Akses Halaman Self-Order

**URL:** `https://bppu.ikipsiliwangi.ac.id/kantin/self-order`

**Tampilan:**
- Katalog menu kantin dengan foto
- Menu unggulan (carousel)
- Filter kategori & sub-kategori
- Search bar untuk cari menu

---

### 2. Login / Registrasi

**PENTING:** Anda harus login sebagai **Buyer** untuk bisa memesan.

#### Jika Belum Punya Akun:
1. Klik tombol **"Daftar"** di modal login
2. Isi form registrasi:
   - Nama lengkap
   - Email
   - Password
   - Konfirmasi password
3. Klik **"Daftar"**
4. Akun otomatis dibuat sebagai Buyer

#### Jika Sudah Punya Akun:
1. Masukkan email & password
2. Klik **"Login"**

**Setelah login:**
- Content blur hilang
- Bisa mulai order
- Nama user muncul di kanan atas

---

### 3. Browse & Pilih Menu

#### Cara Mencari Menu:

**A. Gunakan Search**
1. Klik icon **search** (kaca pembesar) di kanan atas
2. Ketik nama menu atau varian (misal: "nasi goreng", "es teh")
3. Sistem akan memberi saran pencarian otomatis

**B. Filter Kategori**
Di sidebar kiri (desktop) atau mobile:
- Klik kategori: Makanan, Minuman, Snack, dll.
- Pilih sub-kategori jika ada

**C. Sort Menu**
- Urutkan berdasarkan: Harga, Nama, Urutan tampilan

---

### 4. Tambah ke Keranjang

#### Menu Tanpa Varian:
1. Klik tombol **"+ Keranjang"** di card menu
2. Item langsung masuk keranjang
3. Counter keranjang (angka merah) bertambah

#### Menu dengan Varian (misal: ukuran S/M/L):
1. Klik tombol **"Pilih Varian"** di card menu
2. Modal varian terbuka
3. Pilih varian yang diinginkan (misal: "Ukuran L")
4. Klik **"+ Tambah ke Keranjang"**

**Indikator:**
- Toast notification hijau: "Berhasil ditambahkan ke keranjang"
- Badge merah di icon keranjang menunjukkan jumlah item

---

### 5. Lihat & Edit Keranjang

#### Buka Keranjang:
1. Klik icon **keranjang** di kanan bawah (floating button)
2. Modal keranjang terbuka

#### Di Dalam Keranjang:
- Lihat semua item yang dipilih
- **Ubah jumlah:** Klik tombol **+** atau **-**
- **Hapus item:** Klik tombol **X** (silang merah)
- **Lihat subtotal** di bawah

#### Tambah Catatan (Opsional):
1. Scroll ke bawah modal keranjang
2. Isi kolom **"Catatan untuk Kantin (Opsional)"**
3. Contoh catatan: "Tidak pakai cabe", "Kurangi gula", "Pedas", dll.
4. Maksimal 255 karakter
5. Counter karakter ditampilkan di bawah textarea

---

### 6. Checkout

#### Proses Checkout:
1. Pastikan semua item sudah benar
2. Pastikan sudah login
3. Klik tombol **"Checkout"** di modal keranjang
4. Loading sebentar...
5. Otomatis redirect ke halaman **Invoice**

**Jika Gagal:**
- Periksa koneksi internet
- Pastikan masih login
- Refresh halaman dan coba lagi

---

### 7. Lihat Invoice & Nomor Antrian

**URL:** `https://bppu.ikipsiliwangi.ac.id/kantin/self-order/invoice/{id}`

**Apa yang Ditampilkan:**
- **Nomor antrian** (angka besar)
- **Status pesanan** (realtime)
- Tanggal & waktu pesan
- Nama pemesan
- Daftar item pesanan
- Total pembayaran
- Catatan (jika ada)

**Status Pesanan:**
1. **Menunggu** (kuning) - Pesanan baru masuk, menunggu kantin proses
2. **Diproses** (biru) - Kantin sedang menyiapkan pesanan
3. **Siap** (hijau) - Pesanan sudah siap diambil!
4. **Selesai** (abu-abu) - Transaksi selesai
5. **Dibatalkan** (merah) - Pesanan dibatalkan

**Fitur Realtime:**
- Status update otomatis tiap **5 detik**
- **Notifikasi browser** saat status berubah
- **Banner notifikasi** muncul di atas halaman
- Lihat **posisi antrian** (berapa antrian sebelum kamu)

**Auto-Cancel:**
- Jika **5 menit** tidak ada respons dari kantin → pesanan otomatis dibatalkan
- Warning muncul **30 detik** sebelum cancel

**PENTING:**
- **Jangan tutup halaman invoice** sampai pesanan selesai
- Aktifkan notifikasi browser agar dapat pemberitahuan
- Simpan nomor antrian

---

### 8. Ambil Pesanan

#### Saat Status "Siap":
1. Notifikasi akan muncul: **"Pesananmu sudah siap! Silakan ambil di kasir."**
2. Datang ke **kasir kantin**
3. Tunjukkan **halaman invoice** (nomor antrian)
4. Lakukan **pembayaran** di kasir
5. Terima pesanan

#### Saat Status "Selesai":
- Operator sudah menandai transaksi selesai
- Anda sudah menerima pesanan

---

### 9. Print Invoice (Opsional)

Di halaman invoice:
1. Klik tombol **"Cetak"** di toolbar atas
2. Browser akan buka print dialog
3. Pilih printer atau **Save as PDF**
4. Format struk thermal 58mm (cocok untuk struk kecil)

---

## B. PANDUAN UNTUK OPERATOR KANTIN

### 1. Akses Dashboard Pesanan

**Login:** `https://bppu.ikipsiliwangi.ac.id/pengelola/login`

**Role yang bisa akses:**
- Canteen (Operator Kantin)
- Officer (Petugas BPPU)
- Sysadmin

**Setelah Login:**
- Klik menu **"Pesanan"** di bottom navigation (icon lonceng)
- Atau langsung akses: `/pengelola/pesanan-kantin`

---

### 2. Navigasi Dashboard

#### Bottom Navigation (Mobile-Friendly):
1. **PoS** - Point of Sale (kasir manual)
2. **Pesanan** - Self-Order queue (dengan badge notifikasi)
3. **Rekap** - Laporan penjualan

#### Top Navigation:
- **Fullscreen** - Untuk fokus penuh
- **Profile** - Ganti password, logout

---

### 3. Filter Pesanan

**Pills Filter dengan Stats:**
- **Semua** - Tampilkan semua pesanan hari ini
- **Menunggu** (kuning) - Pesanan baru yang perlu diproses
- **Diproses** (biru) - Pesanan yang sedang dikerjakan
- **Siap** (hijau) - Pesanan siap diambil pembeli
- **Selesai** (coklat) - Transaksi selesai

**Angka di pills** = Jumlah pesanan dengan status tersebut

**Auto-Refresh:**
- Data refresh otomatis tiap **8 detik**
- Waktu refresh terakhir ditampilkan di header

---

### 4. Proses Pesanan

#### Card Pesanan Berisi:
- **Nomor antrian** (angka besar)
- **Nama pemesan**
- **Waktu order**
- **Status badge** (warna-warni)
- **Daftar item** dengan quantity & varian
- **Catatan** (jika ada, background kuning)
- **Total harga**
- **Tombol aksi**

---

### 5. Update Status Pesanan

#### Flow Normal:

**STEP 1: Pesanan Baru Masuk (Status: Menunggu)**
1. Notifikasi browser muncul: **"X pesanan baru masuk!"**
2. Toast hijau di dashboard
3. Badge di bottom nav bertambah
4. Card muncul dengan border **kuning**

**Action:**
- Klik tombol **"Proses"** untuk mulai kerjakan
- Atau klik **"Batal"** jika tidak bisa dikerjakan

---

**STEP 2: Pesanan Diproses**
1. Status berubah jadi **"Diproses"** (biru)
2. Pembeli dapat notifikasi: "Pesananmu sedang diproses oleh kantin!"
3. Border card berubah jadi **biru**
4. Dot animasi biru di badge status

**Action:**
- Siapkan pesanan sesuai item yang dipesan
- Perhatikan catatan khusus (jika ada)
- Setelah selesai, klik **"Siap Diambil"**

---

**STEP 3: Pesanan Siap**
1. Status berubah jadi **"Siap"** (hijau)
2. Pembeli dapat notifikasi: **"Pesananmu sudah siap! Silakan ambil di kasir."**
3. Border card berubah jadi **hijau**

**Action:**
- Tunggu pembeli datang ke kasir
- Tunjukkan nomor antrian
- Proses pembayaran
- Setelah dibayar & diterima, klik **"Selesai"**

---

**STEP 4: Pesanan Selesai**
1. Status berubah jadi **"Selesai"** (abu-abu)
2. Pembeli dapat notifikasi: "Pesananmu selesai. Terima kasih!"
3. Card tidak ada tombol aksi lagi
4. Text: "Pesanan selesai"

---

#### Tombol Batal:

**Kapan Pakai:**
- Stok habis
- Pembeli tidak datang setelah lama
- Pesanan bermasalah

**Cara:**
1. Klik tombol **"Batal"** (merah) di card
2. Status langsung berubah jadi **"Dibatalkan"**
3. Pembeli dapat notifikasi: "Maaf, pesananmu dibatalkan oleh kantin."
4. Card berubah jadi abu-abu dengan text "Pesanan dibatalkan"

**CATATAN:** Tombol batal selalu tersedia selama status belum **Selesai** atau **Dibatalkan**

---

### 6. Notifikasi

#### Browser Notification:
- Izinkan notifikasi saat pertama kali akses
- Notifikasi muncul saat:
  - Ada pesanan baru masuk
  - (Operator tidak perlu notifikasi untuk status update, pembeli yang dapat)

#### Toast Internal:
- Muncul di atas dashboard
- Hijau: Pesanan baru / sukses update
- Merah: Error
- Auto-hilang setelah 3 detik

---

### 7. Tips untuk Operator

**Best Practice:**
1. **Selalu pantau tab Menunggu** - pesanan baru prioritas
2. **Proses secepat mungkin** - pembeli menunggu max 5 menit
3. **Perhatikan catatan khusus** - background kuning
4. **Update status tepat waktu** - pembeli dapat notifikasi realtime
5. **Gunakan fullscreen** - fokus, tidak ada distraksi
6. **Aktifkan notifikasi browser** - tidak ketinggalan pesanan baru

**Keyboard Shortcut:**
- **ESC** - Keluar fullscreen

---

## C. FAQ (Frequently Asked Questions)

### Untuk Pembeli:

**Q: Apakah harus punya akun untuk order?**
A: Ya, Anda harus login sebagai Buyer. Daftar gratis di halaman self-order.

**Q: Bagaimana kalau pesanan tidak diproses 5 menit?**
A: Pesanan akan otomatis dibatalkan. Anda bisa order ulang.

**Q: Bisa pesan tanpa bayar dulu?**
A: Bisa. Pembayaran dilakukan di kasir saat ambil pesanan (status "Siap").

**Q: Bagaimana kalau salah order?**
A: Jika status masih "Menunggu", tunggu 5 menit akan auto-cancel. Atau hubungi operator.

**Q: Apakah bisa lihat history pesanan?**
A: Fitur ini belum tersedia. Untuk saat ini, simpan screenshot invoice.

---

### Untuk Operator:

**Q: Bagaimana kalau ada 2 operator akses bersamaan?**
A: Aman. Data sync otomatis tiap 8 detik. Update status dari operator manapun akan terlihat.

**Q: Apakah bisa batalkan pesanan yang sudah "Siap"?**
A: Bisa. Tombol "Batal" tersedia sampai status "Selesai".

**Q: Bagaimana kalau pembeli tidak datang ambil pesanan?**
A: Tunggu beberapa saat, lalu klik "Batal". Atau hubungi pembeli jika ada kontak.

**Q: Apakah data pesanan terhapus?**
A: Tidak. Semua pesanan tersimpan di database. Filter hanya menampilkan pesanan hari ini.

---

## D. TROUBLESHOOTING

### Masalah Umum Pembeli:

**1. Content Blur, Tidak Bisa Klik Menu**
- **Penyebab:** Belum login
- **Solusi:** Login atau daftar terlebih dahulu

**2. Tombol Checkout Tidak Muncul**
- **Penyebab:** Keranjang kosong
- **Solusi:** Tambahkan minimal 1 item ke keranjang

**3. Checkout Loading Terus**
- **Penyebab:** Koneksi internet lambat/putus
- **Solusi:** Periksa koneksi, refresh halaman, coba lagi

**4. Tidak Dapat Notifikasi Browser**
- **Penyebab:** Permission ditolak
- **Solusi:** Buka Settings browser → Site Settings → Notifications → Allow

**5. Status Tidak Update**
- **Penyebab:** Polling error atau halaman tidak aktif
- **Solusi:** Refresh halaman invoice

---

### Masalah Umum Operator:

**1. Tidak Bisa Akses Dashboard Pesanan**
- **Penyebab:** Role bukan Canteen/Officer/Sysadmin
- **Solusi:** Hubungi admin untuk update role

**2. Tombol Update Status Tidak Berfungsi**
- **Penyebab:** Loading masih berjalan atau error
- **Solusi:** Tunggu loading selesai, atau refresh halaman

**3. Badge Notifikasi Tidak Akurat**
- **Penyebab:** Polling error atau cache
- **Solusi:** Refresh halaman

**4. Data Tidak Auto-Refresh**
- **Penyebab:** JavaScript error atau browser outdated
- **Solusi:** Buka browser console, lihat error. Update browser ke versi terbaru.

---

## E. TECHNICAL INFO

### Browser Support:
- Chrome 90+ (Recommended)
- Firefox 88+
- Safari 14+
- Edge 90+

### Features:
- Realtime status update (polling 5s untuk pembeli, 8s untuk operator)
- Browser push notification
- Auto-cancel 5 menit
- Responsive mobile-friendly
- Bottom navigation (mobile app style)
- Fullscreen mode untuk operator

### Tech Stack:
- Laravel 11
- Inertia.js + Vue 3
- TailwindCSS
- Realtime polling (no websocket)

---

## F. SUPPORT

Jika mengalami kendala yang tidak tercantum di panduan ini:

1. **Screenshot error** (jika ada)
2. **Catat langkah-langkah** yang dilakukan
3. **Hubungi:** Admin BPPU atau IT Support

---

**Terakhir diupdate:** 19 Februari 2026
**Versi:** 1.0
**Platform:** BPPU IKIP Siliwangi
