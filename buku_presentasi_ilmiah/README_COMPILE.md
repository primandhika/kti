# Panduan Kompilasi Buku (`compile_docx.py`)

Script Python ini dipakai untuk mengompilasi seluruh file `.md` di direktori `buku_presentasi_ilmiah` menjadi satu file `.docx` siap review atau siap cetak.

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

# Kompilasi ke A4 untuk review
.venv/bin/python compile_docx.py --ukuran A4

# Simpan dengan nama lain
.venv/bin/python compile_docx.py -o Draf_Presentasi_Ilmiah.docx
```

## Opsi Utama

### Ukuran Halaman (`--ukuran`)

- `A5` untuk cetak buku standar.
- `B5` untuk buku akademik berukuran lebih lega.
- `A4` untuk review di layar atau cetak biasa.
- `custom` jika ingin menentukan `--lebar` dan `--tinggi` sendiri.

### Margin

Gunakan:
- `--margin-top`
- `--margin-bottom`
- `--margin-dalam`
- `--margin-luar`
- `--mirror`

Contoh:

```bash
.venv/bin/python compile_docx.py --ukuran A5 --mirror
```

### Tipografi

Gunakan:
- `--font-body`
- `--spasi`

Contoh:

```bash
.venv/bin/python compile_docx.py --font-body 10 --spasi 1.15
```

## Urutan Kompilasi

Script akan mencoba menyusun file dalam urutan berikut:

1. `COVER.md`
2. `HAK_CIPTA.md`
3. `PENGANTAR.md`
4. `DAFTAR_ISI.md`
5. `DAFTAR_GAMBAR.md`
6. `DAFTAR_TABEL.md`
7. `CARA_MENGGUNAKAN_BUKU.md`
8. Semua file `.md` di folder `bab/` dalam urutan nama file
9. `GLOSARIUM.md`
10. `DAFTAR_PUSTAKA.md`
11. `INDEKS.md`
12. `TENTANG_PENULIS.md`

Jika sebagian file belum ada, script akan memberi peringatan dan meminta konfirmasi untuk melanjutkan.

## Struktur Direktori yang Disarankan

```text
buku_presentasi_ilmiah/
├── compile_docx.py
├── README_COMPILE.md
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
├── ATURAN_BUKU.md
└── bab/
    ├── 01_*.md
    ├── 02_*.md
    ├── ...
    └── 11_*.md
```

## Catatan

- Script ini diadaptasi dari workflow `buku_media`.
- Heading, tabel, list, blok kode, placeholder gambar, dan caption gambar akan diformat otomatis ke DOCX.
- File output default adalah `Buku_Mahir_Presentasi_Ilmiah.docx`.
