# Pemecahan Modul dan Kompilasi DOCX

Tanggal: 16 September 2026.

## Sumber dan tujuan

Sumber: `Fonologi Bahasa Indonesia - Modul.docx`. Penataan mengikuti pola folder serta nama berkas buku linguistik komputasional agar naskah dapat diteruskan dalam Markdown dan dikompilasi kembali untuk memperbarui DOCX utama.

SHA-256 sumber awal: `f427071c5ef263a0a49a118f96a6017ea0a337d697c86be2f84f96c7628150c1`.

## Perubahan

- Membuat 16 berkas aktif di `main/`: halaman awal, daftar isi, Bab 1–13, dan daftar pustaka.
- Memindahkan isi Bab 1–7 dari modul; menyiapkan kerangka Bab 8–13 dari daftar isi sumber. Uraian bab baru belum ditulis.
- Membetulkan penomoran Bab 7–8, judul Bab 6, dan judul subbab 7.3. Urutan topik buku dipertahankan.
- Menata heading dan latihan; menambahkan tempat pengisian tujuan, rangkuman, serta istilah tanpa mengarang materi yang belum tersedia.
- Menghimpun 31 entri bibliografi setelah penggabungan duplikasi format. Inventaris mentah menyimpan 41 butir dari daftar rujukan bab, termasuk satu URL terpisah. Metadata belum diverifikasi dan format belum difinalkan ke APA 7.
- Menyimpan salinan DOCX asli, ekstraksi utuh, daftar isi sumber, serta ketiga gambar di `reference/`.
- Menulis peta pengembangan, catatan editorial, penyesuaian aturan proyek, glosarium kerja, dan panduan kompilasi.
- Menambahkan skrip Python dan filter Pandoc untuk menggabungkan Markdown ke DOCX. Opsi `--update-source` mencadangkan berkas lama sebelum memperbarui DOCX utama.
- Membuat `output/Fonologi Bahasa Indonesia - Draf.docx`. DOCX utama belum ditimpa pada tahap ini.

## Pemeriksaan

Kompilasi menggunakan Pandoc 3.1.3 berhasil tanpa peringatan. Delapan tabel diperiksa sel demi sel dan ketiga gambar dibandingkan berdasarkan hash dengan sumber. Sebanyak 205 paragraf isi yang panjangnya lebih dari 120 karakter dicocokkan setelah normalisasi spasi, kapitalisasi, dan tanda baca. Semua cocok.

Sebanyak 15 tautan daftar isi menunjuk ke penanda internal yang tersedia pada DOCX. Komentar TODO tidak masuk ke isi Word. Tautan lokal Markdown dan gambar valid. Hash DOCX utama serta salinan arsip tetap sama dengan sumber awal.

Opsi pembaruan diuji pada salinan proyek di direktori sementara: cadangan identik dengan dokumen sebelumnya, hasil kompilasi menggantikan DOCX utama salinan, dan arsip asli tetap utuh. Pengujian gambar hilang memastikan kompilasi gagal sebelum DOCX utama diganti atau cadangan baru dibuat.

Pemeriksaan ini memvalidasi konversi dan kelengkapan isi, belum memvalidasi klaim akademik, metadata rujukan, atau tampilan setiap halaman di Word. Tata letak perlu diperiksa saat finalisasi cetak.
