# Fonologi Bahasa Indonesia

Naskah kerja Markdown untuk melanjutkan *Fonologi Bahasa Indonesia - Modul.docx*. Susunan folder mengikuti proyek buku linguistik komputasional. Bab 1–7 berisi naskah hasil ekstraksi; Bab 8–13 berupa kerangka sesuai daftar isi sumber.

## Mulai dari sini

- [Daftar isi aktif](main/00-daftar-isi.md): urutan dan tautan semua bab.
- [Peta pengembangan](notes/peta-pengembangan.md): status, kekurangan, dan tugas per bab.
- [Catatan editorial](notes/catatan-editorial.md): persoalan yang perlu ditinjau saat revisi isi.
- [Aturan proyek](notes/penyesuaian-aturan-proyek.md): penerapan aturan buku untuk fonologi.
- [Cara kompilasi](notes/kompilasi-docx.md): membuat DOCX dan memperbarui berkas sumber.

## Susunan berkas

| Lokasi | Fungsi |
|---|---|
| `main/` | Sumber naskah aktif: halaman awal, daftar isi, 13 bab, dan daftar pustaka |
| `reference/` | Salinan DOCX asli, ekstraksi utuh, daftar isi asli, dan tiga gambar sumber |
| `notes/` | Rencana penulisan, inventaris rujukan, glosarium kerja, dan catatan editorial |
| `_rev/` | Riwayat penataan serta cadangan DOCX saat diperbarui |
| `scripts/` | Skrip kompilasi Markdown ke DOCX |
| `output/` | Hasil kompilasi; sunting naskah di `main/` agar perubahan tidak hilang |

## Melanjutkan penulisan

Untuk melanjutkan materi setelah modul, mulai dari [Bab 8. Realisasi Fonem](main/08-realisasi-fonem.md). Kerangka Bab 8–13 sudah memuat subbab sumber dan arahan pengisian. Untuk menyiapkan naskah siap terbit, tinjau Bab 1–7 bertahap sesuai catatan editorial.

Komentar `<!-- TODO: ... -->` menandai pekerjaan penulis dan tidak ditampilkan di DOCX. Kerangka belum merupakan bab selesai. Rujukan hasil ekstraksi juga belum diverifikasi atau difinalkan ke APA 7.

## Membuat DOCX

Jalankan dari folder buku dengan Python 3 dan Pandoc yang sudah terpasang:

```bash
python3 scripts/compile_docx.py
```

Hasilnya `output/Fonologi Bahasa Indonesia - Draf.docx`.

Untuk memperbarui `Fonologi Bahasa Indonesia - Modul.docx` dari naskah Markdown:

```bash
python3 scripts/compile_docx.py --update-source
```

Skrip membuat cadangan berkas lama di `_rev/backup_docx/` sebelum menggantinya. Salinan edisi awal tetap tersimpan di `reference/modul-asli.docx`.
