# Panduan Sistem BPPU IKIP Siliwangi

**Versi:** 2.0  
**Terakhir diperbarui:** 28 Juli 2026  
**Platform:** https://bppu.ikipsiliwangi.ac.id

---

## Tentang Dokumen Ini

Selamat datang di Buku Panduan Resmi Sistem BPPU (Badan Pengelola dan Pengembangan Usaha) IKIP Siliwangi!

Dokumen ini disusun khusus sebagai pegangan utama Anda dalam menjalankan operasional harian menggunakan sistem BPPU. Kami menyadari bahwa pencatatan secara digital mungkin terasa sedikit berbeda pada awalnya. Oleh karena itu, seluruh panduan di dalam buku ini ditulis dengan bahasa yang sangat sederhana, langsung pada intinya (tinggal klik dan ikuti), serta menghindari istilah-istilah rumit agar nyaman dibaca oleh siapa saja, termasuk para pengurus koperasi dan staf yang bertugas di lapangan.

Di dalam buku panduan ini, Anda akan menemukan langkah-langkah praktis untuk seluruh siklus kerja Anda. Mulai dari cara mengelola barang yang baru datang (restok), melayani pembeli langsung di kasir, memproses pesanan mandiri (self-order) dari pembeli, hingga proses persetujuan agar uang yang diterima benar-benar tercatat aman dan rapi di Buku Kas. 

Selain panduan klik per klik, kami juga menyertakan bagian khusus bernama "Praktik Baik" (sebelumnya dikenal sebagai Best Practice). Bagian ini tidak hanya memberitahu Anda *apa* yang harus diklik, tetapi juga *mengapa* hal itu perlu dilakukan dengan cara tertentu. Tujuannya adalah untuk menjaga keakuratan laporan keuangan dan memastikan bagi hasil dengan mitra usaha berjalan lancar tanpa selisih.

Silakan jadikan dokumen ini sebagai teman andalan Anda saat bertugas. Jangan ragu untuk membuka kembali halaman-halamannya kapan pun Anda merasa ragu atau lupa. Selamat bekerja, dan semoga sistem ini semakin mempermudah serta melancarkan tugas harian Anda!

---

## Daftar Isi

| No | Dokumen | Isi |
|----|---------|-----|
| 00 | [Daftar Isi](00-daftar-isi.md) | Halaman ini — indeks seluruh panduan |
| 01 | [Panduan Admin & Officer](01-admin-officer.md) | Pengelolaan konten, barang, keuangan, penjualan, pengguna, mitra |
| 02 | [Panduan Kantin & Toko](02-kantin.md) | Point of Sale, pesanan self-order, stok, rekap |
| 03 | [Panduan Member Buyer](03-member-buyer.md) | Registrasi, pesan mandiri, bayar, poin, voucher |
| 04a | [Praktik Baik: Kantin](04a-praktik-baik-kantin.md) | Opname dan voucher untuk kantin |
| 04b | [Praktik Baik: Admin](04b-praktik-baik-admin.md) | SOP pembukuan, bagi hasil, persetujuan |
| 04c | [Praktik Baik: Mitra](04c-praktik-baik-mitra.md) | Aturan konsinyasi dan bagi hasil |
| 05 | [Panduan Mitra Usaha](05-mitra-usaha.md) | Portal mitra, skema bisnis, pencairan, laporan |
| 06 | [Panduan Pimpinan](06-pimpinan.md) | Dashboard eksekutif, monitoring, analitik |
| 07 | [Arsitektur & Teknis Sistem](07-arsitektur-teknis.md) | Tech stack, struktur kode, database, deployment, keamanan |
| 08 | [Sistem Poin & Membership](08-sistem-poin.md) | Skema poin, tier, redeem, konfigurasi |
| 09 | [Self-Order Kantin & Toko](09-self-order.md) | Alur pemesanan mandiri, invoice, status, notifikasi |
| 10 | [Troubleshooting & FAQ](10-troubleshooting.md) | Diagnosis masalah umum, FAQ per role, panduan support |
| 11 | [Alur Utama Operasional](11-alur-utama.md) | Alur harian: restok, penjualan, pencatatan ke buku kas, stock opname |

---

## Role yang Ada di Sistem

| Role | Cakupan Akses | Portal Login |
|------|---------------|--------------|
| `sysadmin` | Semua fitur tanpa kecuali | `/pengelola/login` |
| `officer` | Hampir semua, kecuali Log Aktivitas, Point Rules, Hasil Survey, Recycle Bin | `/pengelola/login` |
| `head` / `pimpinan` | Dashboard eksekutif, lihat Buku Kas, Rekap, Laporan, Mitra (read-only) | `/pengelola/login` |
| `canteen` (kantin) | POS, pesanan kantin, stok, pengajuan barang, rekap | `/pengelola/login` |
| `shop` (toko) | POS, item belanja, pesanan toko, rekap | `/pengelola/login` |
| `data_clerk` | Menu kantin, barang, opname, pengguna (buyer) | `/pengelola/login` |
| `mitra` | Dashboard mitra, komisi, penarikan, laporan | `/mitra/login` |
| `member` / `buyer` | Self-order, member area, poin | Modal login di halaman self-order |

---

## Cara Akses

- **Portal Pengelola (admin/officer/kantin/pimpinan):** https://bppu.ikipsiliwangi.ac.id/pengelola/login
- **Portal Mitra Usaha:** https://bppu.ikipsiliwangi.ac.id/mitra/login
- **Self-Order Kantin:** https://bppu.ikipsiliwangi.ac.id/kantin/self-order
- **Self-Order Toko/Belanja:** https://bppu.ikipsiliwangi.ac.id/belanja/self-order
- **Member Area:** https://bppu.ikipsiliwangi.ac.id/member-area

---

## Catatan Penting

- Menu di sidebar otomatis menyesuaikan role yang login.
- Warna utama sistem mengacu pada identitas IKIP Siliwangi: **#996600** (emas).
- Sistem berjalan di environment **production** — jangan pernah menjalankan `migrate:fresh`.
