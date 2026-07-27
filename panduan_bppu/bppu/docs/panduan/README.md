# Buku Panduan Sistem BPPU IKIP Siliwangi

Panduan pemakaian per role. Setiap file berisi langkah prosedural langsung pakai.

## Daftar Panduan

| Role | File | Isi |
|------|------|-----|
| Admin & Officer | [01-admin-officer.md](01-admin-officer.md) | Pengelolaan konten, barang, keuangan, penjualan, pengguna, mitra |
| Kantin | [02-kantin.md](02-kantin.md) | Point of Sale, pesanan self-order, stok, rekap |
| Member Buyer | [03-member-buyer.md](03-member-buyer.md) | Registrasi, pesan mandiri, bayar, poin, voucher |
| Best Practice Operasional | [04-best-practice.md](04-best-practice.md) | Opname/restok, buku kas, potongan/voucher, bagi hasil mitra, persetujuan berjenjang |

## Cara Akses

- **Portal pengelola (admin/officer/kantin):** `/pengelola/login`
- **Member buyer:** login lewat modal di halaman `/kantin/self-order` atau `/belanja/self-order`
- **Portal mitra usaha:** `/mitra/login`

## Role yang Ada di Sistem

| Role | Cakupan Akses |
|------|---------------|
| `sysadmin` | Semua fitur |
| `officer` | Hampir semua, kecuali Log Aktivitas, Point Rules, Hasil Survey, Recycle Bin |
| `head` | Hanya lihat (view) Buku Kas, Rekap, Laporan, Mitra |
| `canteen` (kantin) | POS, pesanan kantin, stok, pengajuan barang, rekap |
| `shop` (toko) | POS, item belanja, pesanan toko, rekap |
| `data_clerk` | Menu kantin, barang, opname, pengguna (buyer) |

Catatan: menu di sidebar otomatis menyesuaikan role yang login.
