# Praktik Baik Operasional: Admin & Officer

Dokumen ini menjelaskan cara pemakaian yang benar untuk area krusial bagi admin/officer, supaya angka di sistem tetap dapat dipercaya dan dapat diaudit.

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

## 2. Pencatatan Buku Kas

### Yang perlu dipahami tentang cara kerja sistem

Penjualan yang masuk ke buku kas tidak langsung tercatat begitu saja. Ada rangkaian bertingkat yang saling mengunci, yaitu verifikasi, lalu persetujuan, lalu perekaman. Setiap tahap tidak dapat dibatalkan apabila tahap berikutnya sudah dijalankan. Sebagai contoh, sebuah transaksi tidak bisa di-unverify jika sudah terlanjur di-approve, dan tidak bisa di-unapprove jika sudah terlanjur direkam ke buku kas. Ketika perekaman dijalankan, hanya penjualan berstatus selesai, sudah disetujui, dan belum pernah direkam yang akan ikut. Mekanisme inilah yang mencegah pencatatan ganda.

### Cara yang dianjurkan

Hormati urutan verifikasi, persetujuan, lalu perekaman, dan jangan mencari jalan pintas. Verifikasi adalah pengecekan di tingkat kasir, persetujuan adalah otorisasi oleh officer, dan perekaman adalah pembukuannya. Sebaiknya orang yang memverifikasi dan orang yang menyetujui adalah pihak yang berbeda, sebagai bentuk pemisahan tugas yang sehat.

Untuk memasukkan penjualan ke buku kas, gunakan fitur "Record Penjualan by Date", bukan mengetik transaksi satu per satu secara manual. Fitur inilah yang menandai penjualan sebagai sudah direkam sehingga tidak akan tercatat dua kali. Input transaksi manual sebaiknya hanya dipakai untuk hal di luar penjualan, seperti biaya operasional atau setoran.

Apabila terjadi kesalahan perekaman, jangan mengutak-atik status penjualan secara langsung. Hapus saja transaksi kas yang bersangkutan, karena sistem akan otomatis mengembalikan status penjualan menjadi belum direkam sehingga bisa direkam ulang dengan benar. Untuk keteraturan laporan, gunakan satu buku kas per unit dan siapkan daftar kategori transaksi yang konsisten terlebih dahulu, supaya Laporan Umum akurat.

Buku kas yang terhapus sebaiknya dibiarkan berada di Recycle Bin sebagai jejak, dan penghapusan permanen hanya dilakukan oleh sysadmin. Di akhir hari, pastikan seluruh penjualan yang sudah selesai telah terekam, kemudian lakukan export untuk rekonsiliasi dengan uang fisik.

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

---

## 4. Rekap dan Bagi Hasil Mitra serta Konsinyasi

### Yang perlu dipahami tentang cara kerja sistem

Bagi hasil dihitung dari item-item penjualan yang sudah terverifikasi, dikelompokkan menurut mitra, dengan memakai skema bisnis aktif milik masing-masing mitra. Sistem mengenal beberapa skema, yaitu konsinyasi, bagi hasil dengan persentase antara vendor dan badan usaha, dropshipper, serta supplier biasa. Ada pula bentuk konsinyasi bersyarat, yaitu apabila harga sebuah item melebihi ambang minimum tertentu maka badan usaha mengambil nominal tetap dan sisanya menjadi bagian vendor, sedangkan jika harganya di bawah ambang tersebut perhitungan mengikuti konsinyasi normal berdasarkan harga konsinyasi per barang. Item yang tidak memiliki mitra akan masuk ke kelompok "Tanpa Mitra" dan seluruhnya menjadi bagian badan usaha. Riwayat pembagian sendiri memiliki tiga status, yaitu belum dicairkan, sedang mengajukan, dan sudah dicairkan.

### Cara yang dianjurkan

Pastikan setiap mitra sudah memiliki skema bisnis yang aktif sebelum barangnya dijual. Jika skema kosong atau tidak aktif, bagian vendor akan terhitung nol, sehingga mitra tidak memperoleh bayaran dan angka rekap menjadi salah. Inilah penyebab paling umum ketika rekap bagi hasil terlihat meleset. Khusus untuk konsinyasi, isilah harga konsinyasi pada setiap barang, bukan hanya harga jualnya, karena perhitungan bagian vendor bergantung pada nilai tersebut.

Karena rekap hanya menghitung penjualan yang sudah terverifikasi, kedisiplinan dalam memverifikasi penjualan secara langsung memengaruhi keakuratan bagi hasil. Oleh sebab itu, jangan memfinalisasi pembagian sebelum seluruh penjualan pada periode tersebut selesai diverifikasi. Alur pencairan yang benar dimulai dari meninjau Pembagian Mitra, lalu menyimpan riwayat untuk mengunci angka periode itu, dan kemudian menandai cair ketika dana benar-benar dibayarkan. Setelah riwayat disimpan, angka tidak akan bergeser meskipun ada koreksi penjualan di kemudian hari, dan justru itulah tujuan dari finalisasi.

Apabila mitra mengajukan pencairan melalui portalnya, prosesnya harus dijalankan lewat tombol proses hingga selesai, atau ditolak bila memang tidak disetujui. Jangan menandai cair secara manual untuk pengajuan yang datang lewat portal, karena akan menimbulkan status ganda. Untuk konsinyasi bersyarat, telitilah nilai ambang minimum dan nominal tetap pada skemanya, sebab logika perhitungannya berbeda antara item mahal yang memakai nominal tetap dan item murah yang memakai konsinyasi normal. Kesalahan pengaturan di titik ini akan membuat bagian badan usaha menjadi terlalu besar atau terlalu kecil.

Sama seperti pada selisih opname, pencairan bagian vendor tidak tercatat otomatis, sehingga setelah dana cair Anda perlu mencatatnya ke buku kas sebagai pengeluaran. Simpan pula hasil Export Pembagian Mitra dan Buku Tagihan setiap periode sebagai bukti kepada mitra sekaligus arsip audit.

---

---

## 5. Sistem Persetujuan Berjenjang (Multi-Level Approval)

Bagian ini menjelaskan secara utuh mekanisme persetujuan transaksi penjualan, karena inilah tulang punggung yang menjaga agar uang yang masuk ke pembukuan benar-benar sah dan tidak tercatat dua kali.

### Tiga tingkat yang berurutan

Sebuah transaksi penjualan harus melewati tiga tahap secara berurutan sebelum menjadi bagian resmi dari pembukuan. Tahap pertama adalah verifikasi, yaitu penegasan bahwa transaksi memang benar terjadi, lengkap dengan tanggal dan metode pembayarannya, apakah tunai, QRIS, atau transfer. Tahap kedua adalah persetujuan, yaitu otorisasi bahwa transaksi yang sudah terverifikasi itu layak dibukukan. Tahap ketiga adalah perekaman, yaitu memasukkan transaksi tersebut ke dalam buku kas tertentu sehingga resmi menjadi catatan keuangan.

Ketiganya tidak boleh dilompati. Sistem akan menolak persetujuan atas transaksi yang belum diverifikasi, dan menolak perekaman atas transaksi yang belum disetujui. Urutannya bersifat wajib dan dijaga oleh sistem, bukan sekadar anjuran.

### Siapa yang boleh melakukan apa

Kewenangan pada setiap tahap sengaja dibedakan agar terjadi pemisahan tanggung jawab.

Pada tahap verifikasi, officer dan sysadmin dapat memverifikasi transaksi mana pun. Adapun kantin dan shop hanya boleh memverifikasi transaksi yang mereka buat sendiri, tidak bisa menyentuh transaksi milik unit lain. Untuk verifikasi massal, officer, sysadmin, kantin, dan shop sama-sama diperbolehkan. Namun untuk verifikasi menyeluruh dalam satu rentang tanggal sekaligus, kewenangan itu hanya dimiliki officer dan sysadmin.

Pada tahap persetujuan, baik satuan maupun massal, hanya officer dan sysadmin yang berwenang. Kasir kantin maupun shop tidak dapat menyetujui transaksi, sekalipun transaksi itu milik mereka sendiri. Pada tahap perekaman ke buku kas, kewenangannya juga terbatas pada officer dan sysadmin. Dengan pembagian ini, kasir berperan menyatakan bahwa transaksi benar terjadi, sementara officer atau sysadmin berperan mengesahkan dan membukukannya.

### Aturan pembatalan yang melindungi pembukuan

Pembatalan pada tahap-tahap ini tidak bebas dilakukan, justru karena sistem melindungi integritas angka. Sebuah transaksi tidak dapat dibatalkan verifikasinya apabila sudah disetujui; untuk membatalkannya, persetujuannya harus dicabut lebih dulu. Demikian pula, transaksi tidak dapat dibatalkan persetujuannya apabila sudah terekam di buku kas; pencatatan di buku kas harus dihapus lebih dulu. Dengan kata lain, untuk mundur satu langkah, Anda harus membatalkan dari tingkat paling akhir menuju tingkat paling awal, tidak bisa sebaliknya.

### Cara kerja yang dianjurkan

Perlakukan ketiga tahap ini sebagai rantai pertanggungjawaban, bukan sekadar tombol berurutan. Biasakan verifikasi dilakukan segera setelah transaksi terjadi, selagi ingatan dan bukti masih segar, karena keterlambatan verifikasi akan menghambat persetujuan sekaligus membuat rekap bagi hasil mitra menjadi tidak lengkap. Sedapat mungkin, pisahkan peran antara yang memverifikasi dan yang menyetujui, sehingga tidak ada satu orang pun yang menguasai seluruh alur dari awal sampai akhir.

Manfaatkan verifikasi dan persetujuan massal untuk efisiensi ketika transaksi menumpuk, tetapi tetap sisihkan waktu untuk memeriksa transaksi bernilai besar atau yang tampak tidak wajar satu per satu. Terakhir, ingat bahwa perekaman adalah titik tidak bisa kembali yang paling menentukan; setelah sebuah transaksi terekam, membatalkannya menuntut penghapusan catatan di buku kas terlebih dahulu, jadi pastikan buku kas tujuan dan keterangannya sudah benar sebelum menekan rekam.
