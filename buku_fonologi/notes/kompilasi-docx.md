# Kompilasi Markdown ke DOCX

Naskah di `main/` menjadi sumber penulisan. Python 3 menjalankan Pandoc melalui `scripts/compile_docx.py`. Tidak ada pustaka Python tambahan yang diperlukan.

## Membuat draf

Dari folder buku:

```bash
python3 scripts/compile_docx.py
```

Keluaran: `output/Fonologi Bahasa Indonesia - Draf.docx`.

Untuk lokasi keluaran lain:

```bash
python3 scripts/compile_docx.py --output 'output/Fonologi Bahasa Indonesia - Revisi.docx'
```

Skrip dapat dipanggil dari direktori lain dengan jalur lengkap. Jalur sumber tetap dihitung dari lokasi skrip; jalur relatif pada `--output` dihitung dari direktori tempat perintah dijalankan.

## Memperbarui DOCX yang sekarang

```bash
python3 scripts/compile_docx.py --update-source
```

Urutan kerjanya:

1. Membaca dan menggabungkan Markdown, kemudian membuat DOCX sementara.
2. Memeriksa keberhasilan Pandoc serta struktur arsip DOCX, lalu menyimpan draf hasil kompilasi.
3. Menyalin `Fonologi Bahasa Indonesia - Modul.docx` ke `_rev/backup_docx/` dengan cap waktu.
4. Mengganti DOCX utama dengan hasil kompilasi yang sudah berhasil dibuat.

Jika kompilasi gagal, DOCX utama belum diganti. Salinan edisi awal di `reference/modul-asli.docx` tetap dipakai sebagai acuan gaya dokumen. Cadangan setiap pembaruan tersimpan terpisah.

## Urutan dan cakupan isi

Skrip menggabungkan `00-hal-awal.md`, `00-daftar-isi.md`, seluruh berkas bab bernomor 01–98 secara berurutan, lalu `99-daftar-pustaka.md`. Penomoran bab tidak boleh terputus atau ganda. Berkas dalam `notes/`, `reference/`, dan `_rev/` tidak menjadi isi buku.

Setiap berkas utama harus mempunyai tepat satu judul `#`. Subbab menggunakan `##`, lalu `###` untuk tingkat berikutnya. Skrip memulai bagian utama pada halaman baru dan mengubah tautan daftar isi ke penanda internal Word. Daftar isi mengikuti berkas Markdown, belum berupa daftar isi otomatis dengan nomor halaman.

Tabel, sitasi tertulis, simbol fonetik Unicode, dan gambar lokal ikut dikonversi. Komentar HTML, termasuk `TODO`, tidak terlihat pada DOCX. Bab yang belum ditulis tetap menampilkan keterangan bahwa isinya berupa kerangka. Gaya cetak simbol IPA bergantung pada dukungan font di aplikasi pembuka DOCX.

## Gambar dan pemformatan

Simpan gambar di `reference/media/` lalu tautkan dari Markdown, misalnya:

```markdown
![Deskripsi gambar](../reference/media/nama-gambar.png){width="3.5in"}

Gambar 2.1 Judul gambar
```

Contoh tersebut hanya menunjukkan pola penulisan jalur dan ukuran, bukan gambar yang sudah tersedia. Gunakan berkas yang benar-benar ada. Lebar gambar dan tabel perlu disesuaikan dengan halaman sumber yang berukuran mendekati A5.

Kompilasi menggunakan DOCX asli sebagai acuan gaya serta ukuran halaman. Tata letak halaman, pemenggalan tabel, posisi objek, dan nomor halaman dapat berubah karena naskah disusun ulang dari Markdown. Periksa tampilan hasil di Word atau LibreOffice sebelum finalisasi cetak.

Perubahan teks sebaiknya dilakukan di Markdown lalu dikompilasi kembali. Perubahan yang hanya dibuat pada DOCX tidak otomatis masuk ke Markdown dan akan tergantikan pada kompilasi berikutnya.
