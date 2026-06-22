# Cara Compile Disertasi

Panduan ini menjelaskan cara menggabungkan berkas Markdown utama di folder
`main/` menjadi DOCX dan PDF sesuai aturan dalam `FORMAT.md`.

## 1. Ringkasan

Compiler terdiri atas:

- `compile.sh`: perintah utama yang dijalankan pengguna;
- `scripts/compile_disertasi.py`: implementasi compiler;
- `FORMAT.md`: spesifikasi ukuran halaman, font, margin, heading, dan spasi;
- `main/*.md`: sumber dokumen;
- `outputs/`: lokasi keluaran default.

Pipeline yang digunakan:

```text
Markdown → Pandoc → DOCX/OpenXML → LibreOffice → PDF → validasi PDF
```

## 2. Prasyarat

Compiler memerlukan:

- Python 3;
- modul Python `lxml`;
- Pandoc;
- LibreOffice dengan mode headless untuk membuat PDF;
- font Times New Roman;
- `pdfinfo` dari Poppler untuk memvalidasi PDF.

Periksa dependency dengan perintah berikut:

```bash
python3 --version
python3 -c 'import lxml; print(lxml.__version__)'
pandoc --version
/snap/bin/libreoffice --headless --version
fc-match 'Times New Roman'
pdfinfo -v
```

Dalam workspace saat dokumentasi ini dibuat, semua dependency utama tersebut
sudah tersedia.

## 3. Compile DOCX dan PDF

Jalankan perintah dari root proyek:

```bash
cd /home/primandhika/artikel/dist
./compile.sh
```

Perintah tersebut menghasilkan:

```text
outputs/disertasi-restu.docx
outputs/disertasi-restu.pdf
```

Keluaran lama dengan nama yang sama akan ditimpa.

## 4. Compile salah satu format

Hanya DOCX:

```bash
./compile.sh --target docx
```

Hanya PDF:

```bash
./compile.sh --target pdf
```

Target `pdf` tetap membuat DOCX sementara karena PDF dirender dari DOCX, tetapi
DOCX tersebut dihapus setelah PDF selesai.

Target default adalah `both`:

```bash
./compile.sh --target both
```

## 5. Mengubah nama dan lokasi keluaran

Mengubah nama dasar keluaran:

```bash
./compile.sh --name disertasi-final
```

Hasilnya:

```text
outputs/disertasi-final.docx
outputs/disertasi-final.pdf
```

Mengubah folder keluaran:

```bash
./compile.sh --output-dir /tmp/hasil-disertasi
```

Menggabungkan keduanya:

```bash
./compile.sh \
  --name disertasi-final \
  --output-dir outputs/final \
  --target both
```

## 6. Opsi compiler

Tampilkan bantuan:

```bash
./compile.sh --help
```

Opsi yang tersedia:

| Opsi | Fungsi |
|---|---|
| `--target docx` | Menghasilkan DOCX saja. |
| `--target pdf` | Menghasilkan PDF saja. |
| `--target both` | Menghasilkan DOCX dan PDF. Ini nilai default. |
| `--name NAMA` | Mengubah nama dasar keluaran. Jangan sertakan ekstensi. |
| `--output-dir PATH` | Mengubah folder keluaran. |
| `--without-appendices` | Tidak menyertakan berkas lampiran. |
| `--keep-intermediate` | Menyimpan gabungan Markdown dan reference DOCX untuk debugging. |
| `--soffice PATH` | Menentukan executable LibreOffice secara manual. |

Contoh debugging:

```bash
./compile.sh \
  --target docx \
  --keep-intermediate \
  --name debug-disertasi
```

Selain DOCX, perintah tersebut menyimpan:

```text
outputs/debug-disertasi.combined.md
outputs/debug-disertasi.reference.docx
```

## 7. Urutan dokumen

Compiler tidak memakai glob bebas. Dokumen utama disusun dengan urutan tetap:

1. `main/00_Cover.md`
2. `main/00_Lembar_Persetujuan.md`
3. `main/00_Kata_Pengantar.md`
4. `main/00_Daftar_Isi.md`
5. `main/01_BAB_I_PENDAHULUAN.md`
6. `main/01_BAB_II_TINJAUAN_TEORI_KERANGKA_TEORETIK.md`
7. `main/01_BAB_III_METODOLOGI_PENELITIAN.md`
8. `main/01_BAB_IV_TEMUAN_DAN_PEMBAHASAN.md`
9. `main/01_BAB_V_KESIMPULAN_IMPLIKASI_DAN_SARAN.md`
10. `main/01_DAFTAR_PUSTAKA.md`
11. `main/02_Lampiran_*.md` yang tidak kosong, berdasarkan urutan nama file.

File berikut tidak ikut dikompilasi:

- file `.bak_*`;
- subdirektori `main/_origins/`, `main/_notes/`, dan `main/_rev/`;
- lampiran berukuran 0 byte;
- berkas lain yang tidak tercantum dalam manifest compiler.

Compiler berhenti dengan error jika salah satu dokumen utama wajib tidak ada.

## 8. Format Markdown yang dikenali

Gunakan hierarki berikut. Untuk konversi satu bab, jalankan `./compile.sh --target docx --single PATH --name NAMA --output-dir outputs`. Dua baris pembuka menjadi satu Heading 1; `## A.` menjadi Heading 2; `### 1.` menjadi Heading 3; dan `#### a.` menjadi Heading 4. Label dipertahankan sebagai teks heading.


```markdown
# BAB I
# PENDAHULUAN

## A. Latar Belakang Masalah

### 1. Sub-subbab

#### a. Tingkat Keempat
```

Tabel:

```markdown
**Tabel 1.1** Judul Tabel

| Kolom A | Kolom B |
|---|---|
| Isi | Isi |
```

Gambar:

```markdown
![Deskripsi gambar](../img/nama-gambar.png)

Gambar 1.1 Judul Gambar
```

Jangan membuat indentasi dengan spasi atau tab manual. Compiler menerapkan
indentasi paragraf, spasi, dan style melalui OpenXML.

## 9. Perilaku gambar

Placeholder Bab IV dengan pola berikut dipetakan otomatis ke folder `img/`:

```markdown
*[Ilustrasi 4.1: Halaman Login]*
```

Compiler mencari berkas dengan pola:

```text
img/gambar_4_01_*
```

Berkas gambar harus memiliki isi. File 0 byte dianggap tidak tersedia,
placeholder tetap dipertahankan, dan compiler menampilkan warning.

## 10. Daftar isi

Daftar isi mengambil konten dari:

```text
main/00_Daftar_Isi.md
```

Saat ini LibreOffice headless tidak menghitung field TOC DOCX secara konsisten.
Karena itu, isi daftar telah ikut dikompilasi, tetapi nomor halaman dinamis dan
leader titik belum dibuat otomatis.

Untuk dokumen final, perbarui daftar isi setelah pagination stabil. Pada
Microsoft Word, daftar isi dapat dibuat atau diperbarui dari menu
**References → Table of Contents**.

## 11. Warning yang normal

### Lampiran kosong

```text
[warn] skipping 21 empty appendix file(s)
```

Artinya file lampiran ditemukan tetapi berukuran 0 byte. File tersebut tidak
ikut dikompilasi.

### Gambar tidak ditemukan atau kosong

```text
[warn] image for Ilustrasi 4.1 was not found
```

Pastikan file gambar tersedia, namanya sesuai pola, dan ukurannya bukan 0 byte.

## 12. Validasi otomatis

Compiler memeriksa:

- struktur ZIP/OpenXML DOCX;
- keberadaan `document.xml`, `styles.xml`, header, dan footer;
- jumlah section DOCX;
- semua section memakai A4;
- PDF berhasil dibuat;
- ukuran halaman PDF sekitar 595,28 × 841,89 pt.

Pesan sukses terlihat seperti berikut:

```text
[ok] DOCX validated: .../outputs/disertasi-restu.docx
[ok] PDF validated: A4, 169 pages: .../outputs/disertasi-restu.pdf
[done] compilation completed
```

Jumlah halaman bersifat informasional dan dapat berubah saat isi diperbarui.

## 13. Troubleshooting

### `Permission denied` saat menjalankan `compile.sh`

```bash
chmod +x compile.sh scripts/compile_disertasi.py
```

### Pandoc tidak ditemukan

```text
[error] Required tool not found: pandoc
```

Pastikan `pandoc` tersedia di `PATH`.

### LibreOffice tidak ditemukan

Tentukan executable secara manual:

```bash
./compile.sh --soffice /snap/bin/libreoffice
```

### PDF gagal dibuat

Uji DOCX terlebih dahulu:

```bash
./compile.sh --target docx --keep-intermediate --name debug
```

Jika DOCX valid, periksa LibreOffice:

```bash
/snap/bin/libreoffice --headless --version
```

### Ingin memeriksa hasil PDF

```bash
pdfinfo outputs/disertasi-restu.pdf
pdffonts outputs/disertasi-restu.pdf
pdftotext -layout outputs/disertasi-restu.pdf - | less
```

## 14. Alur kerja yang disarankan

1. Edit dokumen di `main/`.
2. Pastikan heading mengikuti hierarki Markdown.
3. Jalankan `./compile.sh --target docx` untuk pemeriksaan cepat.
4. Buka DOCX dan periksa tabel, gambar, serta perpindahan halaman.
5. Jalankan `./compile.sh` untuk menghasilkan DOCX dan PDF final.
6. Periksa warning aset/lampiran.
7. Perbarui nomor halaman daftar isi setelah pagination final.
8. Simpan hasil final dengan nama eksplisit menggunakan `--name`.
