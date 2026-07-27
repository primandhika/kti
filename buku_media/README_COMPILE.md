# Panduan Kompilasi Buku (`compile_docx.py`)

Script Python untuk mengompilasi seluruh file `.md` di direktori `buku_media` menjadi satu file `.docx` siap cetak.

## Prasyarat

```bash
# Buat virtual environment (sekali saja)
python3 -m venv .venv

# Instal dependensi
.venv/bin/pip install python-docx
```

## Penggunaan Cepat

```bash
# Kompilasi default → A5, siap cetak buku
.venv/bin/python compile_docx.py

# Kompilasi ke A4 (untuk review/draf di layar)
.venv/bin/python compile_docx.py --ukuran A4

# Simpan dengan nama lain
.venv/bin/python compile_docx.py -o Draf_v2.docx
```

## Opsi Lengkap

### Ukuran Halaman (`--ukuran`)

| Preset | Dimensi         | Kegunaan Utama                          |
|--------|----------------|-----------------------------------------|
| `A5`   | 14.8 × 21.0 cm | **Default.** Cetak buku standar ISBN     |
| `B5`   | 17.6 × 25.0 cm | Cetak buku akademik/jurnal               |
| `A4`   | 21.0 × 29.7 cm | Draf review, tugas cetak biasa           |
| `custom`| bebas          | Wajib sertakan `--lebar` dan `--tinggi`  |

```bash
# B5
.venv/bin/python compile_docx.py --ukuran B5

# Ukuran kustom (misal 16 × 24 cm, umum untuk buku UNESCO)
.venv/bin/python compile_docx.py --ukuran custom --lebar 16 --tinggi 24
```

### Margin (`--margin-*`)

Setiap preset sudah memiliki margin default yang sesuai untuk cetak buku. Margin menggunakan istilah **dalam** (sisi jilid/gutter) dan **luar** (sisi potong) agar tidak bergantung pada orientasi halaman.

| Argumen           | Satuan | Keterangan                        | Default A5 |
|-------------------|--------|-----------------------------------|------------|
| `--margin-top`    | cm     | Margin atas                        | 1.5        |
| `--margin-bottom` | cm     | Margin bawah                       | 1.5        |
| `--margin-dalam`  | cm     | Margin sisi jilid (gutter)         | 2.0        |
| `--margin-luar`   | cm     | Margin sisi potong                 | 1.5        |

```bash
# A5 dengan gutter lebih besar untuk jilid lem tebal
.venv/bin/python compile_docx.py --margin-dalam 2.5

# A5 dengan margin seragam
.venv/bin/python compile_docx.py --margin-dalam 2.0 --margin-luar 2.0
```

### Mirror Margin (`--mirror`)

Untuk cetak bolak-balik (dua sisi), aktifkan mirror margin agar gutter selalu berada di sisi jilid:

- Halaman ganjil → gutter di **kiri**
- Halaman genap → gutter di **kanan**

```bash
.venv/bin/python compile_docx.py --mirror
```

> **Catatan:** Tanpa `--mirror`, margin dalam selalu di kiri dan luar di kanan (cocok untuk cetak satu sisi atau review di layar).

### Tipografi (`--font-body`, `--spasi`)

Setiap preset sudah menyesuaikan ukuran font dan spasi secara proporsional. Override jika diperlukan:

```bash
# Font isi 10pt dengan spasi 1.15 (lebih padat)
.venv/bin/python compile_docx.py --font-body 10 --spasi 1.15

# Font isi 12pt dengan spasi 1.5 (lebih longgar)
.venv/bin/python compile_docx.py --font-body 12 --spasi 1.5
```

## Tabel Perbandingan Preset

| Parameter           | A5 (default)   | B5             | A4             |
|---------------------|----------------|----------------|----------------|
| **Halaman**         | 14.8 × 21.0 cm | 17.6 × 25.0 cm | 21.0 × 29.7 cm |
| **Margin atas**     | 1.5 cm         | 2.0 cm         | 2.54 cm        |
| **Margin bawah**    | 1.5 cm         | 2.0 cm         | 2.54 cm        |
| **Margin dalam**    | 2.0 cm         | 2.5 cm         | 3.0 cm         |
| **Margin luar**     | 1.5 cm         | 1.8 cm         | 2.54 cm        |
| **Font isi**        | 11 pt          | 11 pt          | 12 pt          |
| **Font H1 (bab)**   | 14 pt          | 15 pt          | 16 pt          |
| **Font H2 (seksi)** | 12 pt          | 13 pt          | 14 pt          |
| **Font tabel**      | 9 pt           | 9 pt           | 10 pt          |
| **Font kode**       | 8 pt           | 8.5 pt         | 9 pt           |
| **Spasi baris**     | 1.3            | 1.4            | 1.5            |
| **Indent paragraf** | 1.0 cm         | 1.0 cm         | 1.27 cm        |

## Urutan Kompilasi

Script mengompilasi file dalam urutan berikut:

```
 1. COVER.md                ← halaman judul
 2. HAK_CIPTA.md            ← halaman editorial / hak cipta
 3. PENGANTAR.md            ← kata pengantar
 4. DAFTAR_ISI.md           ← daftar isi
 5. DAFTAR_GAMBAR.md        ← daftar gambar
 6. DAFTAR_TABEL.md         ← daftar tabel
 7. CARA_MENGGUNAKAN_BUKU.md← panduan penggunaan buku
 8. bab/01_*.md             ← Bab 1
 9. bab/02_*.md             ← Bab 2
10. bab/03_*.md             ← Bab 3
11. bab/04_*.md             ← Bab 4
12. bab/05_*.md             ← Bab 5
13. bab/06_*.md             ← Bab 6
14. bab/07_*.md             ← Bab 7
15. bab/08_*.md             ← Bab 8
16. bab/09_*.md             ← Bab 9
17. bab/10_*.md             ← Bab 10
18. GLOSARIUM.md            ← glosarium
19. DAFTAR_PUSTAKA.md       ← daftar pustaka
20. INDEKS.md               ← indeks / placeholder indeks
21. TENTANG_PENULIS.md      ← biografi penulis
```

Setiap bagian otomatis mendapat *page break* di awal (kecuali sampul).

## Fitur Format yang Diproses

| Elemen Markdown              | Hasil di DOCX                                      |
|------------------------------|-----------------------------------------------------|
| `# Heading 1`               | Judul bab, centered, bold                            |
| `## Heading 2`              | Judul seksi, centered, bold                          |
| `### Heading 3`             | Subjudul, rata kiri, bold                            |
| `**bold**`                   | **Tebal**                                            |
| `*italic*` / `_italic_`     | *Miring*                                             |
| `` `kode` ``                 | Font Consolas, ukuran lebih kecil                    |
| Tabel `\| ... \| ... \|`     | Tabel bergaris, header berwarna biru muda            |
| `- item` / `* item`         | Daftar bullet (•)                                    |
| `1. item`                    | Daftar bernomor                                      |
| ` ``` blok kode ``` `        | Blok kode Consolas                                   |
| `[deskripsi gambar]`         | Placeholder abu-abu italic (centered)                |
| `Gambar X.Y Judul`          | Caption gambar italic (centered)                     |

## Contoh Resep Umum

### Cetak buku A5 standar (penerbit lokal)
```bash
.venv/bin/python compile_docx.py --ukuran A5 --mirror -o Buku_Cetak_A5.docx
```

### Draf review dosen (A4, spasi 1.5)
```bash
.venv/bin/python compile_docx.py --ukuran A4 -o Draf_Review.docx
```

### Cetak buku B5 dengan gutter besar
```bash
.venv/bin/python compile_docx.py --ukuran B5 --mirror --margin-dalam 3.0 -o Buku_B5.docx
```

### Cetak buku A5 padat (font kecil, spasi rapat)
```bash
.venv/bin/python compile_docx.py --font-body 10 --spasi 1.15 -o Buku_Padat.docx
```

## Struktur Direktori

```
buku_media/
├── compile_docx.py          ← script kompilasi ini
├── README_COMPILE.md        ← dokumentasi ini
├── .venv/                   ← virtual environment Python
├── COVER.md
├── HAK_CIPTA.md
├── PENGANTAR.md
├── DAFTAR_ISI.md
├── DAFTAR_GAMBAR.md
├── DAFTAR_TABEL.md
├── CARA_MENGGUNAKAN_BUKU.md
├── GLOSARIUM.md
├── DAFTAR_PUSTAKA.md
├── INDEKS.md
├── TENTANG_PENULIS.md
├── ATURAN_BUKU.md           ← pedoman penulisan (tidak dikompilasi)
├── bab/
│   ├── 01_*.md
│   ├── 02_*.md
│   ├── ...
│   └── 10_*.md
└── Buku_Media_Pembelajaran.docx   ← output hasil kompilasi
```
