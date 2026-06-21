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

## Pembaruan 2026-06-21
- Melanjutkan penyelesaian BAB IV dengan fokus pada Seksi E sampai H, sekaligus mengecek keselarasan dengan BAB III dan aturan terbaru di `PROJEK.md`.
- Memperbaiki Seksi E.1 karena data final uji lapangan menunjukkan pasangan data lengkap untuk 77 mahasiswa pada dua konstruk utama, yaitu keterampilan berbicara dan kemampuan metakognitif.
- Menulis ulang Seksi E.3 agar hasil efektivitas kemampuan metakognitif tidak lagi dibaca sebagai data parsial, tetapi sebagai hasil uji yang lengkap, termasuk statistik deskriptif, *N-gain*, *paired t-test*, dan uji Welch antarkelompok.
- Menyesuaikan Seksi E.4 dan E.5 agar konsisten dengan data final, termasuk perumusan ulang analisis hubungan metakognitif dan keterampilan berbicara.
- Menyelesaikan Seksi G (integrasi temuan) dengan *joint display* yang menggabungkan temuan kuantitatif, observasi, wawancara dosen, dan pengalaman mahasiswa.
- Menyelesaikan Seksi H yang semula masih placeholder, mencakup pembahasan pengembangan produk, kelayakan, efektivitas, integrasi *mixed methods*, kebaruan, dan keterbatasan penelitian.
- Membersihkan bagian pembahasan agar literatur pembanding yang dipakai mengikuti aturan kebaruan, yaitu dominan tahun 2022 ke atas, sambil mempertahankan rujukan teoritis/fondasional yang memang masih diperlukan.
- Menambahkan penyesuaian kecil pada BAB III agar operasionalisasi dimensi metakognitif selaras dengan data final di BAB IV.
- Mengganti placeholder ilustrasi produk pada deskripsi media dengan tautan gambar aktual dari folder `img/` untuk 10 tampilan utama produk.
- Memperbaiki inkonsistensi jumlah peserta evaluasi formatif pada BAB IV agar selaras dengan data final uji coba, yaitu 6 mahasiswa pada tahap pertama dan 10 mahasiswa pada tahap lebih luas.
- Backup file kerja disimpan pada:
  - `main/01_BAB_IV_TEMUAN_DAN_PEMBAHASAN.md.bak_20260621_125902`
  - `main/01_BAB_III_METODOLOGI_PENELITIAN.md.bak_20260621_125902`

## Audit logika/argumen Seksi H — 2026-06-21
- Membaca ulang khusus Seksi H untuk memburu inkonsistensi argumen, klaim yang terlalu jauh dari data, dan sitasi yang tidak selaras dengan daftar pustaka.
- Memperbaiki sitasi `Park et al. (2022)` menjadi `Noetel et al. (2022)` agar konsisten dengan entri yang benar pada daftar pustaka.
- Memperhalus argumen efektivitas agar lebih defensibel untuk desain kuasi-eksperimen, terutama dengan mengganti formulasi kausal yang terlalu keras menjadi pembacaan peningkatan antarkelompok.
- Menambahkan nuansa analitis pada pembahasan metakognitif bahwa skor awal kelompok eksperimen lebih rendah daripada kontrol, sehingga pembacaan hasil tetap perlu hati-hati meskipun gain dan *N-gain* lebih tinggi.
- Memperjelas alasan mengapa kesimpulan pada keterampilan berbicara lebih kuat, yaitu karena rerata awal kedua kelompok relatif berdekatan dan perbedaan gain didukung uji Welch.
- Melembutkan klaim kebaruan agar tidak melampaui bukti pembanding yang benar-benar digunakan dalam naskah.
- Menambahkan dua subbagian baru pada Seksi F, yaitu respons mahasiswa/perubahan strategi belajar dan temuan observasi implementasi media, agar argumen pada Seksi G dan H benar-benar ditopang oleh data kualitatif yang sudah tersedia.
- Backup tambahan sebelum audit ini disimpan di:
  - `main/01_BAB_IV_TEMUAN_DAN_PEMBAHASAN.md.bak_20260621_131756`
