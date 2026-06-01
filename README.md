# Arsip Karya Ilmiah: Restu Bias Primandhika

Selamat datang di repositori arsip digital karya ilmiah saya. Repositori ini berisi kumpulan artikel jurnal, disertasi, buku media, dan materi pembelajaran yang disusun secara sistematis untuk keperluan akademik dan penelitian.

## 🗂 Struktur Proyek

Repositori ini mengikuti pola organisasi berbasis "Workflows" untuk memisahkan antara sumber data, proses draf, dan hasil akhir.

### 1. Disertasi & Proyek Besar (`/dist`)
Folder ini merupakan pusat dari pengerjaan Disertasi.
- **`main/`**: Berisi naskah utama per bab (Bab I - V) serta revisi dari promotor/kopromotor.
- **`data/`**: Data penelitian (validasi ahli, uji coba, dan data lapangan).
- **`reference/`**: Dokumen referensi kunci dan panduan penulisan.
- **`scripts/`**: Automasi (Python) untuk konversi dokumen dari PDF ke Markdown.

### 2. Publikasi Jurnal (`/jq1-3`, `/rep-jurnQ1`, `/habituation`)
Setiap folder mewakili satu artikel atau target publikasi tertentu.
- **`habituation/`**: Penelitian tentang *Metacognitive Awareness* pada siswa sekolah dasar.
- **`jq1-3/`**: Pengembangan artikel dari *Base Article* terkait *Microlearning* dan teknik *Feynman*.
- **`rep-jurnQ1/`**: Riset dan parsing teks untuk target jurnal Q1.

### 3. Pengembangan Kurikulum (`/buku_media`)
Berisi dokumen Rencana Pembelajaran Semester (RPS) untuk mata kuliah Media Pembelajaran Bahasa Berbasis ICT (2025-2026).

---

## 🛠 Pola Folderisasi (Blueprint untuk Proyek Mendatang)

Berdasarkan analisis repositori ini, berikut adalah standar organisasi yang digunakan:

| Komponen | Nama Folder | Deskripsi |
| :--- | :--- | :--- |
| **Data** | `data/` | CSV, hasil kuesioner, atau transkrip wawancara. |
| **Naskah Utama** | `main/` atau `sections/` | File Markdown (`.md`) yang dipisah per bab/bagian untuk memudahkan kontrol versi. |
| **Output** | `outputs/` | Hasil konversi, PDF final, atau draf yang siap dikirim (submission-ready). |
| **Template** | `template/` atau `guidelines/` | Panduan gaya selingkung jurnal (Author Guidelines) dan file `.docx` template. |
| **Referensi** | `references/` | PDF artikel rujukan atau naskah asli (Base Article). |
| **Automasi** | `scripts/` | Skrip pengolah data atau pengonversi format (biasanya Python). |

### Filosofi "Always Exist" (Standard Minimal):
Setiap proyek baru sebaiknya memiliki setidaknya:
1. **`PROJEK.md`**: Ringkasan singkat status dan tujuan pengerjaan.
2. **`data/`**: Untuk menjaga integritas data terpisah dari narasi.
3. **`outputs/`**: Agar file final tidak bercampur dengan file draf/kerja.

---

## 🔐 Konfigurasi Keamanan
Repositori ini menggunakan SSH key lokal (`.ssh/id_ed25519`) yang dikonfigurasi secara spesifik untuk folder ini agar proses *deployment* ke GitHub tetap aman dan terisolasi.

---
**Author:**
*Restu Bias Primandhika*  
*Fokus Penelitian: Media Pembelajaran, Metakognisi, ICT dalam Pendidikan Bahasa.*
