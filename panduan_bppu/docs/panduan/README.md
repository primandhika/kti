# Buku Panduan Sistem BPPU IKIP Siliwangi

Panduan pemakaian komprehensif untuk seluruh stakeholder sistem BPPU. Setiap file berisi langkah prosedural yang langsung dapat digunakan.

**Website:** https://bppu.ikipsiliwangi.ac.id  
**Versi Sistem:** Laravel 11 + Vue.js 3 + Inertia.js  
**Versi Panduan:** 2.0 -- Juli 2026

---

## Daftar Panduan

| No | File | Isi | Target Pembaca |
|----|------|-----|----------------|
| 00 | [Daftar Isi](00-daftar-isi.md) | Indeks seluruh panduan, daftar role, cara akses | Semua |
| 01 | [Admin & Officer](01-admin-officer.md) | Konten, barang, keuangan, penjualan, pengguna, mitra | Admin, Officer, Sysadmin |
| 02 | [Kantin & Toko](02-kantin.md) | Point of Sale, pesanan self-order, stok, rekap | Canteen, Shop |
| 03 | [Member Buyer](03-member-buyer.md) | Registrasi, pesan mandiri, bayar, poin, voucher | Member, Buyer |
| 04 | [Best Practice](04-best-practice.md) | Opname/restok, buku kas, voucher, bagi hasil, approval | Admin, Officer, Kantin |
| 05 | [Mitra Usaha](05-mitra-usaha.md) | Portal mitra, komisi, pencairan, laporan | Mitra |
| 06 | [Pimpinan](06-pimpinan.md) | Dashboard eksekutif, monitoring, analitik | Pimpinan, Head |
| 07 | [Arsitektur Teknis](07-arsitektur-teknis.md) | Tech stack, struktur kode, database, deployment, keamanan | Developer, IT |
| 08 | [Sistem Poin](08-sistem-poin.md) | Skema poin, tier membership, redeem, konfigurasi | Semua |
| 09 | [Self-Order](09-self-order.md) | Alur pemesanan mandiri, invoice, status, notifikasi | Pembeli, Operator |
| 10 | [Troubleshooting](10-troubleshooting.md) | Diagnosis masalah, FAQ per role, panduan support | Semua |
| 11 | [Alur Utama](11-alur-utama.md) | Alur harian: restok, jual, catat ke buku kas, opname | Kantin, Officer, Admin |

---

## Portal Akses

| Portal | URL | Untuk Role |
|--------|-----|------------|
| Pengelola | `/pengelola/login` | Admin, Officer, Kantin, Toko, Pimpinan, Data Clerk |
| Mitra Usaha | `/mitra/login` | Mitra |
| Self-Order Kantin | `/kantin/self-order` | Member Buyer |
| Self-Order Toko | `/belanja/self-order` | Member Buyer |
| Member Area | `/member-area` | Member Buyer |

---

## Role dalam Sistem

| Role | Cakupan Akses |
|------|---------------|
| `sysadmin` | Semua fitur tanpa kecuali |
| `officer` | Hampir semua, kecuali Log Aktivitas, Point Rules, Hasil Survey, Recycle Bin |
| `head` / `pimpinan` | Dashboard eksekutif, Buku Kas, Rekap, Laporan, Mitra (read-only) |
| `canteen` | POS, pesanan kantin, stok, pengajuan barang, rekap |
| `shop` | POS, item belanja, pesanan toko, rekap |
| `data_clerk` | Menu kantin, barang, opname, pengguna (buyer) |
| `mitra` | Dashboard mitra, komisi, pencairan, laporan |
| `member` / `buyer` | Self-order, member area, poin, voucher |

Menu di sidebar otomatis menyesuaikan role yang login.

---

## Kontak Support

- **Email:** bppu@ikipsiliwangi.ac.id
- **Konter:** BPPU IKIP Siliwangi, Jl. Terusan Jenderal Sudirman, Cimahi 40526
