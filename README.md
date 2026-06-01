# Arsip Karya Ilmiah: Restu Bias Primandhika

Repositori ini berfungsi sebagai arsip digital terintegrasi untuk seluruh karya tulis ilmiah, naskah disertasi, dan materi pengembangan kurikulum yang disusun oleh Restu Bias Primandhika. Pengorganisasian dokumen dilakukan secara sistematis untuk mendukung transparansi data dan kemudahan pengelolaan versi naskah akademik.

## Struktur Organisasi Repositori

Repositori ini menggunakan pendekatan manajemen berbasis alur kerja (workflow-based management) yang memisahkan antara data pendukung, proses penyusunan draf, dan naskah final.

### 1. Disertasi dan Proyek Utama (/dist)
Bagian ini merupakan repositori utama untuk pengembangan naskah disertasi.
- main/: Naskah akademik yang dipisahkan per bagian atau bab (Bab I - V) serta catatan revisi tim promotor.
- data/: Seluruh instrumen penelitian, data lapangan, hasil validasi ahli, dan hasil uji coba.
- reference/: Dokumen rujukan kunci, panduan penulisan akademik, dan standar institusional.
- scripts/: Automasi pemrograman untuk pemrosesan dokumen dan ekstraksi data.

### 2. Publikasi Jurnal dan Riset Terapan
Folder terkait publikasi jurnal diorganisir berdasarkan proyek penelitian spesifik:
- Folder Penelitian Tematik: Berisi naskah yang sedang dalam tahap submisi atau review.
- Folder Analisis Riset: Berisi proses parsing teks, riset awal, dan pemetaan literatur untuk target publikasi bereputasi.

### 3. Pengembangan Kurikulum dan Media Pembelajaran (/buku_media)
Berisi dokumen perancangan kurikulum, Rencana Pembelajaran Semester (RPS), dan materi ajar berbasis ICT untuk periode akademik 2025-2026.

---

## Standar Folderisasi (Cetak Biru Proyek Akademik)

Berdasarkan analisis struktur yang efektif, setiap proyek akademik dalam repositori ini mengikuti standar organisasi berikut:

| Komponen | Nama Folder | Deskripsi Fungsi |
| :--- | :--- | :--- |
| Data | data/ | Tempat penyimpanan data mentah, CSV, dan instrumen penelitian. |
| Naskah Utama | main/ atau sections/ | Naskah dalam format Markdown yang dipisah per bagian untuk memudahkan pelacakan perubahan. |
| Hasil Akhir | outputs/ | Naskah final yang telah diformat, file PDF, atau dokumen siap submisi. |
| Panduan | template/ atau guidelines/ | Standar penulisan, template dokumen, dan panduan gaya selingkung. |
| Rujukan | references/ | Artikel referensi dan naskah dasar yang menjadi landasan riset. |
| Skrip | scripts/ | Kode sumber untuk automasi pengolahan data atau konversi dokumen. |

### Prinsip Minimum Organisasi:
Setiap sub-proyek baru diwajibkan memiliki:
1. PROJEK.md: Dokumen deskripsi status, tujuan, dan metodologi kerja secara ringkas.
2. data/: Pemisahan antara fakta penelitian dan narasi tulisan.
3. outputs/: Memastikan hasil final terisolasi dari draf kerja guna menghindari kerancuan versi.

---

## Informasi Teknis
Repositori ini dikelola dengan menggunakan sistem kontrol versi Git dengan konfigurasi SSH lokal yang terisolasi untuk memastikan keamanan akses dan integritas data.

---
Penulis:
Restu Bias Primandhika
Fokus Bidang: Media Pembelajaran, Metakognisi, ICT dalam Pendidikan Bahasa.
