# Panduan Alur Utama Operasional

Panduan ini berisi langkah-langkah kegiatan sehari-hari dari awal hingga akhir. Silakan ikuti panduan ini secara berurutan agar catatan dan laporan keuangan kita selalu tepat. 

## ALUR 1: Tambah Stok Barang (Saat Barang Baru Datang)

Saat barang baru datang dari pemasok atau mitra, Anda harus mencatatnya ke dalam sistem supaya jumlah stok bertambah dan siap dijual.

### Langkah-langkah Menambah Stok:
1. Masuk ke halaman login di `/pengelola/login`
2. Buka menu **Opname Stock** pada menu di sebelah kiri
3. Pilih tempat Anda bertugas (Kantin atau Toko)
4. Buka bagian **Barang**
5. Cari nama barang yang baru saja datang
6. Klik barang tersebut, lalu tambahkan jumlah stok sesuai dengan jumlah barang yang datang
7. Jika barang tersebut adalah **BARANG BARU** (belum pernah dijual sebelumnya), klik tombol **Tambah**, kemudian isi data berikut: nama barang, PLU (bisa dibuat secara otomatis), harga beli, harga jual, dan kategori
8. Terakhir, klik **Simpan**

**Penting:** Tolong jangan menggunakan fitur Stock Opname untuk mencatat barang yang baru datang. Fitur Opname hanya digunakan untuk menghitung ulang barang fisik, bukan untuk menambah stok barang masuk.

### Cara Kantin Mengajukan Tambahan Barang:
1. Buka data barang yang ingin diajukan, lalu pilih **Pengajuan**, isi jumlah yang dibutuhkan, dan kirim
2. Tunggu sampai pengajuan disetujui oleh admin
3. Setelah disetujui, jumlah stok akan bertambah secara otomatis

---

## ALUR 2: Penjualan (Kasir Melayani Pembeli)

Saat ada pembeli yang datang dan membayar, kasir bertugas mencatat transaksi agar uang dan barang tercatat dengan baik.

Ada 2 cara dalam melayani penjualan:

### A. Lewat Kasir (Jual Langsung)
1. Buka menu **Point of Sale**
2. Cari barang dengan mengetik nama barang atau membaca kode unik pada barang
3. Klik barang tersebut agar masuk ke daftar belanjaan (keranjang)
4. Atur jumlah barang yang dibeli dengan menekan tombol **+** (tambah) atau **-** (kurang)
5. Jika pembeli memiliki kartu anggota, klik **Cari Member**, lalu pilih nama pembeli. Poin dan potongan harga akan muncul dengan sendirinya
6. Jika pembeli membawa voucher potongan harga, masukkan kode voucher tersebut
7. Masukkan jumlah uang tunai yang diberikan oleh pembeli
8. Klik **Simpan** atau **Bayar**
9. Bukti pembayaran akan tersimpan, dan jumlah stok barang otomatis berkurang

### B. Lewat Pesanan Mandiri (Pembeli Pesan dari HP)
1. Pesanan akan masuk dengan sendirinya ke halaman **Pesanan**
2. Lihat rincian pesanan dan catatan khusus dari pembeli
3. Klik **Proses** sebagai tanda Anda mulai menyiapkan pesanan
4. Setelah pesanan selesai disiapkan, klik **Siap Diambil**
5. Saat pembeli datang ke meja kasir untuk mengambil pesanan dan membayar
6. Klik **Selesai**

---

## ALUR 3: Dari Penjualan Masuk ke Buku Kas

Setelah barang berhasil dijual, uang hasil penjualan harus dimasukkan ke dalam catatan resmi (Buku Kas) agar laporan keuangan kita benar dan rapi.

**INI ADALAH ALUR YANG SANGAT PENTING** dan harus dilakukan secara berurutan:

### Tahap 1: Pemeriksaan (dilakukan oleh kasir atau penjaga kantin)
Tujuan tahap ini adalah untuk memastikan bahwa transaksi tersebut benar-benar terjadi.
1. Buka menu **Rekap Penjualan**
2. Pilih tanggal hari ini
3. Lihat daftar transaksi yang ada
4. Klik **Verify** pada setiap transaksi yang sudah dipastikan kebenarannya
5. Atau Anda bisa klik **Verify All** untuk memeriksa semua transaksi sekaligus

### Tahap 2: Persetujuan (dilakukan oleh atasan atau admin)
Tujuan tahap ini adalah agar atasan menyetujui transaksi yang sudah diperiksa.
1. Buka menu **Rekap Penjualan**
2. Lihat daftar transaksi yang sudah diperiksa pada tahap sebelumnya
3. Klik **Approve** pada transaksi yang akan disetujui
4. Atau klik **Approve All** untuk menyetujui semuanya sekaligus

**Penting:** Orang yang melakukan pemeriksaan sebaiknya berbeda dengan orang yang memberikan persetujuan agar saling mengawasi.

### Tahap 3: Pencatatan ke Buku Kas (dilakukan oleh atasan atau admin)
Tujuan tahap ini adalah memasukkan uang hasil penjualan ke dalam buku keuangan resmi kita.
1. Buka menu **Buku Kas**
2. Pilih buku kas untuk tempat yang bersangkutan (Kantin atau Toko)
3. Klik **Record Penjualan by Date** (Catat Penjualan per Tanggal)
4. Pilih tanggal transaksi yang akan dicatat
5. Sistem akan dengan sendirinya memasukkan semua transaksi penjualan yang sudah disetujui
6. **Jangan** mencatat uang masuk satu per satu secara manual, selalu gunakan fitur ini agar tidak ada uang yang tercatat dua kali

Jika ada kesalahan dalam pencatatan:
- Hapus catatan transaksi yang salah di dalam **Buku Kas**
- Sistem akan mengembalikan status penjualan menjadi 'belum dicatat'
- Lakukan pencatatan ulang dengan cara yang benar

---

## ALUR 4: Pemeriksaan Fisik Barang (Stock Opname)

Secara berkala (setiap minggu atau bulan), kita harus menghitung jumlah fisik barang yang ada di rak, lalu membandingkannya dengan catatan yang ada di sistem komputer.

### Langkah-langkah Memeriksa Fisik Barang:
1. Buka menu **Opname Stock**, lalu pilih tempat (Kantin atau Toko)
2. Buka bagian **Stock Opname**
3. Klik **Tambah** atau **Auto Import** (sistem akan mengambil data sisa barang secara otomatis)
4. Hitung barang yang ada di rak satu per satu
5. Masukkan jumlah stok yang benar-benar Anda lihat dan hitung
6. Klik **Simpan** — sistem akan mengganti angka jumlah barang yang lama dengan angka yang baru Anda masukkan
7. Lihat selisihnya: jika ada tanda minus, itu berarti ada barang yang hilang atau rusak
8. Unduh laporan hasil hitungan: pilih **Download PDF** atau **Export CSV**

**Penting:**
- Lakukan penghitungan barang pada saat toko atau kantin **TUTUP** atau sedang sepi, agar tidak ada barang terjual yang terlewat dari hitungan
- Jika ada hasil hitungan yang minus (barang hilang), Anda juga harus mencatatnya ke dalam Buku Kas sebagai pengeluaran dengan kategori **Kerugian Stok**
- Simpan hasil cetakan laporan sebagai bukti fisik

---

## Ringkasan Kegiatan Harian

Berikut adalah ringkasan kegiatan dari pagi sampai malam yang perlu dilakukan:

| Waktu | Kegiatan | Siapa yang Melakukan |
|---|---|---|
| Pagi | Memeriksa stok barang, menambah stok jika ada barang baru datang | Kantin / Toko |
| Siang - Sore | Melayani penjualan melalui kasir langsung atau menerima pesanan mandiri | Kantin / Toko |
| Sore / Malam | Memeriksa (memverifikasi) semua transaksi penjualan hari ini | Kantin / Toko |
| Sore / Malam | Menyetujui (approve) transaksi yang sudah selesai diperiksa | Admin |
| Sore / Malam | Mencatat seluruh penjualan hari ini ke Buku Kas (Record Penjualan by Date) | Admin |
| Mingguan / Bulanan | Menghitung fisik barang di rak (Stock Opname) | Kantin dan Admin |
| Bulanan | Menghitung laporan bagi hasil dengan mitra dan memproses pembayarannya | Admin |
