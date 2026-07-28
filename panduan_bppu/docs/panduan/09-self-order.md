# Panduan Pesan Sendiri (Self-Order) di Kantin dan Toko

Panduan ini berisi cara memesan makanan, minuman, atau barang di kantin dan toko kampus tanpa harus antri di kasir. Panduan ini juga menjelaskan cara bagi penjaga kantin untuk menerima pesanan.

---

## Penjelasan Singkat

Fitur pesan sendiri ini memungkinkan Anda memesan makanan dari HP atau komputer. Anda bisa memilih menu, mengatur pesanan (misal: "tanpa es" atau "pedas"), dan melihat apakah pesanan sudah siap diambil atau belum.

Bagi ibu/bapak penjaga kantin, pesanan akan langsung muncul di layar sehingga bisa langsung dibuatkan tanpa menunggu pembeli datang ke kasir.

---

## Alamat Web yang Digunakan

Ada tiga alamat web yang bisa digunakan sesuai kebutuhan:

| Keperluan | Alamat Web | Penjelasan |
| :--- | :--- | :--- |
| **Beli di Kantin** | `/kantin/self-order` | Untuk pesan makanan, minuman, dan jajanan. |
| **Beli di Toko** | `/belanja/self-order` | Untuk beli alat tulis atau barang toko lainnya. |
| **Layar Penjaga** | `/pengelola/pesanan-kantin` | Layar khusus penjaga kantin/toko untuk melihat daftar pesanan yang masuk. |

---

## Panduan untuk Pembeli

### Cara Masuk (Login)

Anda harus masuk (login) dulu sebelum bisa memesan.

> [!IMPORTANT]
> Kalau belum login, gambar makanan akan terlihat buram dan Anda tidak bisa memencet tombol pesan.

1. Buka alamat web kantin atau toko.
2. **Jika belum punya akun:**
   - Pencet tombol **Daftar**.
   - Isi nama, email, dan kata sandi (password).
   - Pencet **Daftar**.
3. **Jika sudah punya akun:**
   - Masukkan email dan kata sandi Anda.
   - Pencet **Login**.
4. Kalau sudah berhasil, gambar makanan akan terlihat jelas dan Anda sudah bisa mulai memesan.

---

### Cara Mencari Makanan atau Barang

Anda bisa mencari menu dengan cara berikut:

1. **Kolom Pencarian:**
   - Pencet gambar kaca pembesar di bagian atas.
   - Ketik nama makanan, misalnya "Nasi Goreng".
2. **Pilih Kategori:**
   - Anda bisa langsung memilih kelompok menu seperti Makanan, Minuman, atau Snack.
3. **Menu Pilihan:**
   - Geser gambar besar di atas halaman untuk melihat menu favorit.
4. **Urutkan Harga:**
   - Anda bisa mengatur agar menu yang muncul mulai dari harga paling murah atau paling mahal.

---

### Memasukkan ke Keranjang

#### 1. Cara Menambah Pesanan

- **Menu Biasa:**
  - Pencet tombol **+ Keranjang** di bawah gambar makanan. Makanan akan langsung masuk ke keranjang.
- **Menu dengan Pilihan (Misal: Ukuran atau Rasa):**
  - Pencet tombol **Pilih Varian**.
  - Akan muncul pilihan di layar. Pilih yang Anda mau (misalnya ukuran besar atau rasa coklat).
  - Pencet **+ Tambah ke Keranjang**.

> [!NOTE]
> Kalau berhasil, akan muncul pesan warna hijau dan angka di gambar keranjang akan bertambah.

#### 2. Cara Mengubah Isi Keranjang

- Pencet gambar keranjang di pojok kanan bawah layar.
- **Tambah/Kurangi Jumlah:** Pencet tanda **+** untuk menambah atau **-** untuk mengurangi.
- **Batal Beli:** Pencet tanda silang (**X**) pada barang yang tidak jadi dibeli.
- **Tambah Catatan (Penting):**
  - Anda bisa menulis pesan khusus untuk penjaga kantin, misalnya "Jangan pakai sambal" atau "Dibungkus saja". Tulis di kolom catatan sebelum lanjut bayar.

---

### Cara Membayar (Checkout)

1. Buka keranjang dan pastikan pesanan Anda sudah benar.
2. Pencet tombol **Checkout**.
3. Layar akan otomatis berpindah ke halaman **Nota (Invoice)**.

> [!WARNING]
> Kalau halaman tidak berpindah, pastikan internet Anda lancar.

---

### Nota dan Nomor Antrian

Setelah selesai memesan, Anda akan melihat halaman nota. Di halaman ini ada:
- **Nomor Antrian:** Angka besar di bagian atas.
- **Status Pesanan:** Kasih tahu pesanan Anda sudah sampai mana.
- **Rincian Pesanan:** Daftar makanan dan total yang harus dibayar.

> [!IMPORTANT]
> **JANGAN tutup halaman ini** sebelum pesanan selesai. Halaman ini adalah bukti untuk mengambil pesanan. Status pesanan akan diperbarui otomatis setiap beberapa detik, jadi tidak perlu di-refresh atau dimuat ulang.

**Perhatian:** Kalau dalam **5 menit** pesanan belum dibuatkan oleh penjaga kantin, pesanan akan batal dengan sendirinya.

---

### Arti Status Pesanan

Anda bisa melihat warna pesanan untuk tahu sudah sampai mana prosesnya:

| Status | Warna | Artinya |
| :--- | :--- | :--- |
| **Menunggu** | Kuning | Pesanan baru masuk, menunggu penjaga kantin merespons. |
| **Diproses** | Biru | Penjaga kantin sedang memasak atau menyiapkan pesanan Anda. |
| **Siap** | Hijau | Pesanan sudah selesai dibuat dan siap diambil di meja kasir. |
| **Selesai** | Abu-abu | Anda sudah membayar dan mengambil makanan. Selesai. |
| **Dibatalkan** | Merah | Pesanan batal, entah karena ditolak penjaga kantin atau sudah lewat 5 menit. |

---

### Cara Mengambil Pesanan

1. Tunggu sampai status pesanan berubah menjadi **Siap** (warna hijau).
2. Datang ke meja kasir kantin atau toko.
3. Tunjukkan layar HP yang berisi **Nomor Antrian** kepada kasir.
4. Bayar sesuai total harga yang ada di layar.
5. Ambil pesanan Anda.

---

## Panduan untuk Penjaga Kantin dan Toko (Operator)

### Cara Buka Layar Pesanan

Layar ini dipakai untuk melihat pesanan yang masuk dari HP pembeli.

1. Buka alamat web `/pengelola/login`.
2. Masukkan email dan kata sandi khusus pengelola.
3. Pilih menu **Pesanan** di bagian bawah layar.

---

### Isi Layar Pesanan

Di layar ini, Anda bisa melihat beberapa tombol:
- **POS:** Untuk mencatat pembelian biasa (pembeli yang langsung datang ke meja).
- **Pesanan:** Tempat melihat daftar pesanan online yang masuk.
- **Rekap:** Laporan penjualan hari ini.

Anda juga bisa memencet tombol **Layar Penuh (Fullscreen)** di atas agar layar lebih besar dan enak dilihat saat berjaga.

---

### Cara Mengelola Pesanan Masuk

Pesanan yang masuk akan tampil dalam bentuk kotak-kotak (kartu). Anda bisa menekan tombol di atas layar untuk menyaring pesanan:
- **Semua:** Melihat semua pesanan hari ini.
- **Menunggu (Kuning):** Pesanan baru yang harus cepat dibuatkan.
- **Diproses (Biru):** Pesanan yang sedang Anda buat.
- **Siap (Hijau):** Pesanan yang tinggal diambil pembeli.
- **Selesai (Abu-abu):** Pesanan yang sudah selesai dibayar.

> [!NOTE]
> Layar ini akan memperbarui daftar pesanan sendiri setiap beberapa detik. Anda tidak perlu sering-sering memuat ulang layar (refresh).

---

### Langkah-langkah Melayani Pesanan

Setiap kali ada pesanan baru, ikuti urutan ini:

1. **Ada Pesanan Masuk (Kuning)**
   - Saat ada pembeli pesan, akan ada pemberitahuan muncul di layar. Kotak pesanan berwarna kuning.
   - **Tugas Anda:** Pencet tombol **Proses** tanda Anda mulai memasak.

2. **Mulai Memasak (Biru)**
   - Kotak berubah biru. Pembeli di HP-nya akan tahu Anda sedang memasak.
   - **Tugas Anda:** Buat makanannya. Jangan lupa baca catatan dari pembeli (warna kuning), misal "Tidak pedas". Kalau sudah matang, pencet tombol **Siap Diambil**.

3. **Makanan Siap (Hijau)**
   - Kotak berubah hijau. Pembeli akan datang ke meja Anda membawa Nomor Antrian.
   - **Tugas Anda:** Terima uangnya, berikan makanannya, lalu pencet tombol **Selesai**.

4. **Selesai (Abu-abu)**
   - Transaksi selesai dan masuk ke laporan penjualan.

---

### Cara Membatalkan Pesanan

Kalau makanan habis atau pembeli lama tidak datang mengambil, Anda bisa membatalkan pesanan.

- Pencet tombol **Batal** yang warna merah.
- Pesanan akan langsung batal dan pembeli akan diberitahu di HP mereka.

---

### Pesan Tambahan untuk Penjaga Kantin

1. **Sering Cek Pesanan Baru:** Jangan biarkan pesanan kuning terlalu lama. Kalau lewat 5 menit tidak diproses, pesanan akan batal sendiri.
2. **Baca Catatan Pembeli:** Selalu perhatikan kotak kuning di pesanan supaya tidak salah buat.
3. **Jangan Lupa Pencet Tombol:** Disiplin pencet tombol "Proses", "Siap Diambil", dan "Selesai" supaya pembeli tidak bingung menunggu.

---

## Masalah yang Sering Terjadi dan Solusinya

### Untuk Pembeli

| Masalah | Penyebabnya | Cara Memperbaiki |
| :--- | :--- | :--- |
| Gambar menu buram dan tidak bisa dipencet. | Belum masuk (login). | Silakan login atau daftar akun dulu. |
| Tidak bisa lanjut bayar (Checkout). | Keranjang kosong. | Masukkan minimal satu barang ke keranjang. |
| Layar berputar-putar saja. | Internet putus atau lambat. | Cek kuota atau sinyal, lalu muat ulang halamannya. |

### Untuk Penjaga Kantin

| Masalah | Penyebabnya | Cara Memperbaiki |
| :--- | :--- | :--- |
| Tombol tidak bisa dipencet. | Internet sedang lambat. | Tunggu sebentar atau muat ulang (refresh) halaman. |
| Daftar pesanan tidak jalan sendiri. | HP atau layar terlalu lama menyala tanpa disentuh. | Sentuh layarnya atau muat ulang halamannya. |

---

## Catatan Tambahan (Teknis)

_Bagian ini hanya untuk pengembang sistem._
- Sistem memakai teknologi pembaruan otomatis tanpa perlu refresh layar (interval 5 detik untuk pembeli, 8 detik untuk penjaga).
- Dirancang untuk jalan di browser terbaru seperti Chrome, Edge, Safari, dan Firefox.
- Bekerja baik di HP maupun komputer.
