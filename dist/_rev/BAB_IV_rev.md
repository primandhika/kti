# Catatan Revisi BAB IV

Tanggal revisi: 2026-06-08

## Fokus revisi
Menyesuaikan Seksi B (Hasil Validasi Produk) pada `main/BAB_IV.md` dengan data aktual yang tersedia di folder `data/validasi/` dan `outputs/validasi/`, serta merapikan narasi agar selaras dengan struktur contoh disertasi referensi.

## Ringkasan perubahan
- Mengganti seluruh isi placeholder validasi pada BAB IV dengan hasil validasi aktual.
- Menetapkan bahwa tahap validasi yang terdokumentasi pada naskah ini melibatkan:
  - 1 validator ahli materi
  - 1 validator ahli media
- Menuliskan ulang bagian pembuka validasi agar konsisten dengan data yang benar-benar tersedia.
- Menambahkan catatan metodologis bahwa nilai Aiken's V tetap dihitung, tetapi dibaca secara deskriptif dan hati-hati karena setiap instrumen hanya terisi oleh satu validator.

## Data yang dipakai
- `outputs/validasi/validasi_rekap.csv`
- `outputs/validasi/validasi_ringkasan_aspek.csv`
- `outputs/validasi/validasi_detail_butir.csv`
- `outputs/validasi/ringkasan_validasi.md`
- `data/validasi/validasi_materi_catatan.csv`
- `data/validasi/validasi_media_catatan.csv`
- `data/validasi/revisi_produk.csv`

## Struktur hasil validasi yang ditulis ulang
1. Hasil Validasi Ahli Materi
   - tabel rekap per aspek
   - narasi interpretatif
   - tabel catatan dan saran ahli materi
2. Hasil Validasi Ahli Media
   - tabel rekap per aspek
   - narasi interpretatif
   - tabel catatan dan saran ahli media
3. Rekapitulasi Hasil Validasi
   - tabel rekap total tertimbang
4. Revisi Produk Berdasarkan Masukan Validator
   - tabel revisi berdasarkan ahli materi, ahli media, dan masukan seminar kemajuan yang relevan dengan instrumen

## Angka kunci yang dimasukkan ke naskah
- Validasi materi: 119/120 = 99,17% ; Aiken's V = 0,989 ; kategori sangat valid
- Validasi media: 57/60 = 95,00% ; Aiken's V = 0,933 ; kategori sangat valid
- Total tertimbang: 176/180 = 97,78% ; kategori sangat valid

## Backup
- Backup file sebelum revisi disimpan di:
  - `main/_origins/BAB_IV_20260608_195148.md`

## Catatan lanjutan
- Jika nanti data validasi ahli bahasa atau praktisi benar-benar tersedia, Seksi B masih bisa diperluas lagi agar lebih lengkap.
- Jika nama validator sudah final, identitas validator dapat ditambahkan pada kalimat pengantar masing-masing subbagian.

## Pembaruan gaya bahasa 2026-06-08
- Melakukan *humanising* terbatas pada Section B di `main/BAB_IV.md` tanpa mengubah data, tabel, maupun substansi hasil validasi.
- Narasi sesudah tabel dibuat lebih natural, lebih interpretatif, dan mengurangi pengulangan pola kalimat yang terlalu mekanis.
- Transisi antarsubbagian divariasikan agar alur pembacaan lebih halus, tetapi tetap mempertahankan gaya akademik disertasi.

## Pembaruan 2026-06-09
- Menghitung ulang `data/validasi/olah_validasi.ipynb` karena data validasi kini diisi oleh 3 validator pada instrumen materi dan media.
- Memperbarui notebook agar memfilter baris kosong pada CSV, menyesuaikan catatan metodologis untuk skema multi-validator, dan menyimpan ulang output eksekusi notebook.
- Menghasilkan ulang file ringkasan pada `outputs/validasi/`, termasuk `validasi_rekap.csv`, `validasi_ringkasan_aspek.csv`, dan `ringkasan_validasi.md`.
- Menyesuaikan Seksi B pada `main/BAB_IV.md` dengan hasil hitung terbaru:
  - validasi materi: 351/360 = 97,50% ; Aiken's V = 0,967
  - validasi media: 166/180 = 92,22% ; Aiken's V = 0,896
  - total tertimbang: 517/540 = 95,74% ; Aiken's V gabungan = 0,943
- Memperbaiki narasi pengantar validasi agar konsisten dengan fakta bahwa penilaian dilakukan oleh 3 ahli materi dan 3 ahli media, bukan lagi 1 validator per instrumen.
