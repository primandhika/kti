# Praktik Baik Operasional: Kantin & Toko

Dokumen ini menjelaskan cara pemakaian yang benar untuk area yang rawan salah bagi petugas kantin/toko. Fokusnya adalah mengapa sebuah alur harus dijalankan dengan cara tertentu.

---

## 1. Restok Barang dan Stock Opname

### Yang perlu dipahami tentang cara kerja sistem

Ketika sebuah opname disimpan, sistem tidak menambahkan angka yang Anda masukkan ke stok yang sudah ada. Sistem justru **menimpa** nilai stok dengan angka fisik hasil hitungan Anda, jadi stok akhir sama persis dengan yang Anda ketik. Selisih dihitung sebagai stok fisik dikurangi stok sistem, dan nilai rupiah selisih itu memakai **harga beli**, bukan harga jual. Hal penting lainnya: selisih hasil opname **tidak** ikut tercatat ke buku kas secara otomatis. Artinya, jika ada barang hilang atau rusak, kerugiannya tidak akan pernah muncul di pembukuan kecuali Anda mencatatnya sendiri.

### Cara yang dianjurkan

Pisahkan dengan tegas antara kegiatan restok dan kegiatan opname. Restok adalah proses ketika barang baru datang, dan itu dilakukan lewat menu Barang (menambah stok) atau lewat persetujuan pengajuan dari kantin. Opname adalah proses menghitung fisik untuk menemukan selisih. Jangan pernah memakai opname untuk memasukkan barang yang baru datang, karena sistem akan membaca tambahan itu sebagai "kelebihan stok" dan selisihnya menjadi tidak bermakna.

Lakukan opname pada waktu yang tenang, misalnya saat toko tutup atau di awal shift, dan bukan ketika transaksi sedang ramai. Karena stok ditimpa, jika ada penjualan yang terjadi di sela-sela antara Anda menghitung dan menyimpan, angka akhirnya akan meleset.

Manfaatkan fitur Auto Import sebagai titik awal opname. Sistem akan menarik data barang terjual dari penjualan sehingga Anda tinggal mengoreksi hasil hitungan fisik, bukan mengetik ulang dari nol. Untuk setiap selisih minus, selalu isi kolom keterangan dengan penyebabnya, misalnya rusak, kadaluarsa, atau hilang, dan perhatikan tanggal kadaluarsa untuk barang yang mendekati batas.

Terakhir, dan ini yang sering terlewat: tindak lanjuti selisih rupiah yang minus dengan mencatatnya ke Buku Kas sebagai pengeluaran, misalnya dengan kategori "Kerugian Stok" atau "Penyusutan". Karena sistem tidak melakukan ini secara otomatis, laba akan tampak lebih besar dari kenyataan jika Anda melewatkannya. Simpan juga hasil Download PDF atau Export CSV setiap periode sebagai arsip audit.

---

---

## 3. Potongan dan Voucher untuk Kantin dan Shop

### Yang perlu dipahami tentang cara kerja sistem

Saat kasir memeriksa sebuah voucher, sistem menjalankan validasi secara berurutan. Pertama, kode voucher harus ada. Kedua, statusnya harus sudah dicetak, bukan masih draft dan bukan sudah terpakai. Ketiga, program potongannya harus dalam keadaan aktif. Keempat, tanggal transaksi harus berada dalam rentang masa berlaku. Kelima, total transaksi harus memenuhi minimum yang ditetapkan. Sebuah potongan juga bisa dibatasi hanya untuk barang tertentu, dan untuk potongan berjenis persentase terdapat batas maksimum nominal potongan.

### Cara yang dianjurkan

Voucher harus dicetak lebih dulu agar dapat digunakan, sebab voucher yang masih berstatus draft akan ditolak oleh kasir. Alur yang benar adalah membuat voucher, mencetaknya, lalu menetapkannya kepada member. Jangan membagikan kode yang belum dicetak.

Di sisi kasir, selalu gunakan tombol pemeriksaan voucher dan jangan pernah memotong harga secara manual. Nilai potongan, termasuk penerapan batas maksimum, dihitung oleh sistem. Apabila voucher ditolak, bacalah pesan yang muncul, karena penyebab tersering adalah total transaksi belum memenuhi minimum atau voucher sudah kadaluarsa, bukan karena kode yang salah.

Satu kode voucher hanya berlaku untuk sekali pemakaian dan langsung berubah menjadi terpakai setelah digunakan. Karena itu, jangan membuat satu kode untuk banyak orang. Gunakan fitur penetapan per member atau penetapan massal. Pada setiap program potongan, tetapkan nilai minimum transaksi dan batas maksimum potongan agar diskon berjenis persentase tidak membengkak di transaksi bernilai besar. Jika promo hanya berlaku untuk item tertentu, batasi lewat daftar barang supaya tidak keliru terpakai pada seluruh isi keranjang.

Perlu dibedakan bahwa penukaran poin bukanlah voucher. Penukaran poin member dicatat terpisah pada menu Redeem Poin, dan officer perlu mencatatnya ke buku kas secara manual agar nilainya terbukukan sebagai beban promosi. Langkah ini mudah terlewat, jadi jadikan bagian dari rutinitas. Bila sebuah program potongan ingin dihentikan, matikan lewat pengaturan aktif dan jangan menghapus potongan yang vouchernya sudah beredar atau sudah terpakai, karena riwayat transaksi akan kehilangan rujukannya.

---
