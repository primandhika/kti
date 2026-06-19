# Tindak Lanjut Analisis Keterampilan Berbicara

## Keputusan sementara

Dataset final memuat 77 pasangan lengkap: 40 mahasiswa eksperimen dan 37 mahasiswa kontrol. Seluruh kolom seed telah dihapus dari sumber analisis.

- Rerata gain eksperimen 11,86 poin dan kontrol 8,25 poin.
- DiD sebesar 3,61 poin dengan 95% CI 1,17 sampai 6,06; uji Welch `p = 0,004`.
- Uji permutasi 50.000 iterasi memberikan `p = 0,0045`.
- ANCOVA-HC3 menghasilkan efek kelompok tersesuaikan 3,32 poin dengan 95% CI 1,29 sampai 5,34 dan `p = 0,0017`.
- Normalitas masih tidak terpenuhi pada sebagian distribusi; postes eksperimen tidak menolak normalitas (`p = 0,785`). Varians gain tidak homogen menurut Brown–Forsythe (`p < 0,001`), sehingga Welch, permutasi, dan HC3 tetap digunakan.
- Reliabilitas internal kelompok kontrol rendah: alpha pretes −0,083 dan postes 0,439.

Kesimpulan yang defensibel adalah kelompok eksperimen menunjukkan peningkatan lebih besar daripada kelompok kontrol. Hasil konsisten pada Welch, permutasi, dan ANCOVA-HC3, dengan effect size DiD Hedges g = 0,653.

## Prioritas 1 — Bekukan dan audit dataset final

1. Simpan salinan CSV final sebagai versi hanya-baca dan catat checksum/tanggal pembekuan.
2. Pastikan seluruh 77 pasangan dapat ditelusuri ke rekaman atau lembar rubrik sumber.
3. Simpan koreksi E33 dalam *change log*: postes 76,00 dan gain 2,67 berdasarkan sumber asli.
4. Pastikan ID, kelompok, penilai, rubrik, skala, dan waktu pengukuran konsisten.
5. Jangan mengubah atau mengeluarkan nilai ekstrem tanpa bukti kesalahan pada sumber dan *change log*.

## Prioritas 2 — Benahi proses penilaian

### 4. Nilai ulang rekaman secara buta

Jika rekaman masih tersedia, lakukan penilaian ulang dengan prosedur berikut:

1. Hilangkan informasi kelompok, fase, nama, dan urutan waktu dari berkas yang dinilai.
2. Gunakan rubrik dan skala yang sama untuk pretes serta postes.
3. Latih dan kalibrasi minimal dua penilai menggunakan beberapa rekaman contoh.
4. Idealnya, kedua penilai menilai seluruh rekaman. Jika tidak memungkinkan, nilai silang sekurang-kurangnya bagian sampel yang telah ditetapkan sebelum melihat hasil.
5. Hitung reliabilitas antarpenilai dengan ICC yang sesuai dengan desain penilai, disertai 95% CI.
6. Tetapkan prosedur penyelesaian perbedaan skor sebelum membuka identitas kelompok dan fase.

Tanpa penilaian silang, perbedaan pretes–postes tidak dapat dipisahkan dengan baik dari perbedaan standar antarpenilai.

### 5. Audit nilai ekstrem dan konsistensi rubrik

Periksa kembali rekaman dan lembar penilaian untuk:

- gain negatif yang sangat besar;
- nilai postes yang jauh di bawah distribusi kelompok;
- subtotal aspek yang tidak sesuai dengan butir rubrik;
- perubahan penilai atau rubrik di tengah pengumpulan data;
- kesalahan ketik, salah ID, atau pertukaran berkas pretes–postes.

Koreksi hanya boleh dilakukan bila ada bukti pada sumber asli. Setiap koreksi harus dicatat dalam *change log*.

## Prioritas 3 — Tetapkan analisis sebelum menjalankan ulang

### 6. Bekukan rencana analisis

Gunakan urutan berikut agar pemilihan metode tidak didasarkan pada hasil yang paling menguntungkan:

1. **Analisis utama:** ANCOVA skor postes dengan kelompok sebagai prediktor dan skor pretes sebagai kovariat.
2. **Analisis ketahanan:** DiD atau perbandingan gain dengan uji Welch.
3. **Analisis dalam kelompok:** uji berpasangan, tetapi bukan bukti utama efektivitas intervensi.
4. **Analisis aspek:** lima aspek keterampilan berbicara dengan koreksi Holm.
5. **Analisis ketahanan:** uji permutasi perbedaan gain dan ANCOVA dengan standard error HC3.

Laporkan untuk setiap analisis:

- jumlah kasus yang dianalisis;
- estimasi efek;
- 95% CI;
- p-value;
- effect size;
- data yang dikeluarkan dan alasannya;
- hasil pemeriksaan asumsi yang relevan.

Jangan memilih antara ANCOVA, gain score, atau uji postes berdasarkan metode mana yang menghasilkan `p < 0,05`.

### 7. Evaluasi desain dan kebutuhan sampel

- Lakukan analisis daya berdasarkan efek terkecil yang secara pendidikan dianggap bermakna, bukan berdasarkan efek hasil sementara.
- Perhitungkan kehilangan pasangan, ketimpangan ukuran kelompok, dan struktur kelas.
- Jika hanya ada satu kelas eksperimen dan satu kelas kontrol, perlakuan tidak dapat dipisahkan sepenuhnya dari efek kelas/dosen. Keterbatasan ini tidak dapat diperbaiki hanya dengan menambah mahasiswa dalam dua kelas yang sama.
- Untuk pengumpulan lanjutan, gunakan beberapa kelas per kondisi bila memungkinkan dan tetapkan alokasi kelas sebelum pengukuran.

## Prioritas 4 — Perbaiki pelaporan disertasi

### 8. Gunakan narasi yang sesuai dengan bukti

Narasi yang aman untuk data saat ini:

> Kelompok eksperimen menunjukkan peningkatan yang lebih besar daripada kelompok kontrol. Selisih gain sebesar 3,61 poin memiliki 95% CI 1,17 sampai 6,06 (`p = 0,004`; Hedges g = 0,653). ANCOVA-HC3 menghasilkan efek kelompok tersesuaikan 3,32 poin dengan 95% CI 1,29 sampai 5,34 (`p = 0,0017`). Uji permutasi memberikan hasil konsisten (`p = 0,0045`). Temuan mendukung adanya perbedaan peningkatan antarkelompok, dengan tetap memperhatikan keterbatasan desain kuasi-eksperimen.

Hindari kalimat berikut sebelum masalah data selesai:

- “Media terbukti efektif.”
- “Terdapat pengaruh signifikan intervensi.”
- “Peningkatan disebabkan oleh penggunaan media.”
- “Kelompok eksperimen lebih unggul” tanpa estimasi, CI, dan batasan desain.

### 9. Tambahkan keterbatasan wajib

Bab IV dan Bab V perlu menyebutkan secara terbuka:

- perbedaan penilai antara pretes dan postes;
- ketidakacakan kelas pada desain kuasi-eksperimen;
- potensi efek kelas, dosen, kontaminasi perlakuan, dan attrition;
- ketidakmampuan menghitung reliabilitas antarpenilai dari satu skor per rekaman.

## Kriteria selesai sebelum hasil disebut final

- [ ] Dataset final 77 pasangan telah dibekukan dan seluruh skor dapat ditelusuri ke sumber.
- [ ] Alur kehilangan data per kelompok tersedia.
- [ ] Rekaman dinilai dengan prosedur buta dan rubrik yang sama.
- [ ] Reliabilitas antarpenilai beserta 95% CI dilaporkan.
- [ ] Anomali skor telah diperiksa terhadap bukti sumber.
- [ ] Rencana analisis utama dan sensitivitas telah dibekukan.
- [ ] Notebook dijalankan ulang dari kernel bersih tanpa error.
- [ ] Angka pada Bab IV cocok dengan output notebook.
- [ ] Klaim akhir mengikuti estimasi dan interval kepercayaan, bukan sekadar ambang p-value.

## Urutan kerja praktis

1. Minggu pertama: inventarisasi sumber data, buat provenance, dan cari pasangan kontrol asli.
2. Minggu kedua: anonymisasi rekaman, kalibrasi penilai, dan mulai penilaian ulang.
3. Setelah skor final dibekukan: jalankan ulang notebook dan kunci output analisis.
4. Terakhir: revisi Bab IV, pembahasan, keterbatasan, abstrak, dan kesimpulan menggunakan hasil yang sama.

