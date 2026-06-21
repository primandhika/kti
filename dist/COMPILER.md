# Compiler Disertasi

Compiler membaca dokumen utama dalam `main/` memakai urutan tetap dan
menghasilkan DOCX/PDF sesuai `FORMAT.md`.

```bash
./compile.sh                         # DOCX + PDF
./compile.sh --target docx           # DOCX saja
./compile.sh --target pdf            # PDF saja
./compile.sh --name nama-keluaran
./compile.sh --without-appendices
```

Keluaran default:

- `outputs/disertasi-restu.docx`
- `outputs/disertasi-restu.pdf`

Dependency: Python 3, Pandoc, `lxml`, Times New Roman, LibreOffice headless,
dan `pdfinfo`. File backup, subdirektori internal, serta lampiran kosong tidak
ikut dikompilasi. Compiler memberi peringatan untuk lampiran kosong.

Daftar isi saat ini mengikuti isi `main/00_Daftar_Isi.md`. Word/LibreOffice
headless tidak menghitung field TOC DOCX secara konsisten, sehingga nomor
halaman daftar isi perlu diperbarui setelah isi final atau melalui tahap
pagination dua-pass tersendiri.
