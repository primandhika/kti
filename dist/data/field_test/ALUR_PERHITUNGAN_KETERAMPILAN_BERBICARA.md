# Alur Perhitungan Analisis Keterampilan Berbicara

## Catatan penting penyelarasan dengan BAB III

Dokumen ini adalah catatan teknis pengolahan data keterampilan berbicara. Namun, untuk pelaporan resmi pada naskah utama, alur analisis harus mengikuti `main/01_BAB_III_METODOLOGI_PENELITIAN.md` yang saat ini berlaku.

Catatan pentingnya sebagai berikut.

1. Analisis utama pada naskah sebaiknya dibaca melalui urutan: statistik deskriptif, uji normalitas, uji homogenitas, *paired sample t-test*, *independent sample t-test* atau Welch bila varians tidak homogen, *N-gain*, dan *effect size*.
2. ANCOVA, uji permutasi, dan koreksi multipel dapat dipakai sebagai analisis tambahan atau uji ketahanan, tetapi tidak perlu dijadikan poros utama pembahasan apabila hasil utama sudah cukup jelas.
3. Dalam BAB III, skor metakognitif dibaca melalui instrumen terpisah. Karena itu, analisis keterampilan berbicara pada naskah utama tidak boleh dipresentasikan seolah-olah menggabungkan skor angket metakognitif ke dalam satu skor berbicara.
4. Pada berkas teknis saat ini masih ada nama kolom `pre_metakognitif` dan `post_metakognitif`. Nama tersebut diperlakukan sebagai label teknis lama pada file keterampilan berbicara dan harus diselaraskan secara hati-hati pada tahap pelaporan agar tidak bertentangan dengan metodologi yang sudah ditetapkan.
5. Untuk keperluan naskah utama, aspek kelima rubrik berbicara harus dibaca konsisten dengan redaksi BAB III, yaitu **dampak penyampaian**. Jika label teknis di dataset belum berubah, hal itu dicatat sebagai masalah penamaan data, bukan otomatis sebagai dasar penggabungan konstruk.

## 1. Tujuan analisis

Analisis digunakan untuk menjawab pertanyaan berikut:

1. Apakah skor keterampilan berbicara berubah dari pretes ke postes pada setiap kelompok?
2. Apakah peningkatan kelompok eksperimen lebih besar daripada kelompok kontrol?
3. Seberapa besar peningkatan yang terjadi jika dibaca melalui *gain*, *N-gain*, dan *effect size*?
4. Pada aspek keterampilan berbicara mana perbedaan peningkatan paling tampak?

Desain penelitian adalah kuasi-eksperimen pretes-postes dengan kelompok kontrol:

| Kelompok | n |
|---|---:|
| Eksperimen | 40 |
| Kontrol | 37 |
| Total | 77 |

## 2. Sumber data

Analisis membaca dua berkas:

- `keterampilan_berbicara_pretes.csv`
- `keterampilan_berbicara_postes.csv`

Pasangan pengukuran dihubungkan menggunakan kolom `id`. Nama dan NIM tidak digunakan dalam perhitungan maupun ditampilkan dalam output notebook.

Secara konseptual, lima aspek skor utama pada naskah utama dibaca sebagai berikut:

1. pengorganisasian;
2. kejelasan;
3. ketepatan;
4. strategi;
5. dampak penyampaian.

Setiap aspek memiliki skor maksimum 15. Skor akhir skala 0-100 dihitung dengan:

```text
skor akhir = (jumlah lima aspek / 75) × 100
```

### Catatan teknis penamaan kolom

Pada dataset saat ini, aspek kelima masih muncul dengan nama kolom `pre_metakognitif` dan `post_metakognitif`. Untuk menjaga konsistensi dengan BAB III:

- nama kolom tersebut tidak boleh langsung dipresentasikan sebagai skor angket metakognitif;
- pada tahap penulisan metodologi dan hasil utama, redaksinya harus diselaraskan menjadi aspek kelima rubrik berbicara, yaitu dampak penyampaian;
- skor metakognitif yang berasal dari angket tetap dianalisis pada jalur analisis tersendiri.

## 3. Validasi sebelum analisis

Notebook menghentikan proses jika ditemukan:

- ID duplikat;
- mahasiswa tanpa pasangan pretes atau postes;
- label kelompok yang berubah;
- kelompok selain `eksperimen` atau `kontrol`;
- skor aspek di luar rentang 0-15;
- skor akhir yang tidak sesuai dengan agregasi lima aspek rubrik berbicara.

Toleransi selisih agregasi adalah 0,05 poin untuk mengakomodasi pembulatan desimal.

Hasil validasi dataset terakhir:

```text
Pretes  : 77 baris
Postes  : 77 baris
Pasangan: 77 mahasiswa
Status  : valid
```

## 4. Statistik deskriptif

Untuk setiap kombinasi kelompok dan fase dihitung:

- jumlah data (`n`);
- rerata;
- simpangan baku;
- median;
- kuartil pertama dan ketiga;
- minimum dan maksimum.

*Gain* individual dihitung sebagai:

```text
gain_i = postes_i − pretes_i
```

Ringkasan skor total terbaru:

| Kelompok | Pretes, mean ± SD | Postes, mean ± SD | Gain, mean ± SD |
|---|---:|---:|---:|
| Eksperimen | 70,84 ± 5,55 | 82,70 ± 5,18 | 11,86 ± 6,71 |
| Kontrol | 71,21 ± 1,84 | 79,46 ± 3,41 | 8,25 ± 3,69 |

## 5. Catatan reliabilitas rubrik

Untuk naskah utama, bukti reliabilitas rubrik keterampilan berbicara harus tetap mengacu pada **kesepakatan antarpenilai** (*inter-rater reliability*) sebagaimana tertulis di BAB III.

Perhitungan Alpha Cronbach di bawah ini hanya diperlakukan sebagai **audit tambahan internal** pada level butir rubrik, bukan sebagai dasar utama pelaporan reliabilitas dalam naskah.

Rumus audit tambahannya:

```text
α = k/(k−1) × [1 − (Σ varians butir / varians skor total)]
```

Dengan `k = 15` butir.

| Kelompok | Pretes | Postes |
|---|---:|---:|
| Eksperimen | 0,781 | 0,684 |
| Kontrol | −0,083 | 0,439 |

Alpha kelompok kontrol yang rendah menunjukkan bahwa konsistensi internal pada level butir masih perlu dicermati. Namun, pada naskah utama, bagian ini sebaiknya dibaca sebagai catatan teknis pendukung, bukan bukti reliabilitas utama.

## 6. Pemeriksaan distribusi dan varians

### 6.1 Normalitas

Normalitas diperiksa menggunakan skewness, excess kurtosis, dan Jarque-Bera:

```text
JB = n/6 × [skewness² + excess_kurtosis²/4]
```

Hipotesis nol Jarque-Bera adalah data mengikuti distribusi normal. Nilai `p < 0,05` menunjukkan penyimpangan yang terdeteksi dari normalitas.

Normalitas utama diperiksa pada:

- pretes eksperimen dan kontrol;
- postes eksperimen dan kontrol;
- gain eksperimen dan kontrol.

Hasil utama:

- postes eksperimen tidak menolak normalitas (`p = 0,785`);
- beberapa distribusi lain masih menunjukkan penyimpangan pada taraf 0,05.

Untuk pelaporan dalam naskah utama, hasil ini cukup diringkas sebagai uji normalitas. Tidak perlu seluruh detail teknis ditarik ke BAB IV kecuali benar-benar dibutuhkan.

### 6.2 Homogenitas varians

Homogenitas varians diperiksa dengan Brown-Forsythe melalui langkah berikut:

1. menghitung median setiap kelompok;
2. menghitung deviasi absolut setiap skor dari median kelompok;
3. membandingkan rerata deviasi absolut antarkelompok.

Hasil utama:

| Variabel | p Brown-Forsythe | Keputusan |
|---|---:|---|
| Pretes | 0,0025 | Varians berbeda |
| Gain | 0,0003 | Varians berbeda |

Karena varians *gain* tidak homogen, perbandingan peningkatan antarkelompok lebih aman dibaca dengan pendekatan Welch sebagai penyesuaian dari *independent sample t-test*.

### 6.3 Kesetaraan skor awal

Skor pretes dibandingkan menggunakan pendekatan Welch:

```text
selisih eksperimen − kontrol = −0,37
95% CI = [−2,24; 1,50]
p = 0,692
Hedges g = −0,087
```

Tidak ditemukan perbedaan rerata awal yang berarti. Ini mendukung pembacaan bahwa kedua kelompok relatif sebanding pada titik awal, meskipun varians awalnya tidak sepenuhnya homogen.

## 7. Perubahan dalam setiap kelompok

*Paired sample t-test* bekerja pada skor selisih individual:

```text
d_i = postes_i − pretes_i
t = mean(d) / [SD(d)/√n]
df = n − 1
```

*Effect size* dalam kelompok dihitung dengan Cohen's `dz`:

```text
dz = mean(d) / SD(d)
```

Hasil:

| Kelompok | Mean gain | 95% CI | p | Cohen's dz |
|---|---:|---:|---:|---:|
| Eksperimen | 11,86 | [9,72; 14,01] | <0,001 | 1,768 |
| Kontrol | 8,25 | [7,02; 9,48] | <0,001 | 2,235 |

Kedua kelompok meningkat. Namun, signifikansi dalam kelompok tidak cukup untuk membuktikan efektivitas intervensi karena kelompok kontrol juga mengalami peningkatan.

## 8. Perbandingan peningkatan antarkelompok

Pada level konsep metodologis, bagian ini dibaca sebagai **independent sample t-test terhadap skor peningkatan**. Pada implementasi teknis, karena varians *gain* tidak homogen, dipakai pendekatan **Welch**.

Estimasi utama perbedaan perubahan dihitung dengan:

```text
selisih gain = mean(gain eksperimen) − mean(gain kontrol)
```

Standard error Welch:

```text
SE = √(s²_eksperimen/n_eksperimen + s²_kontrol/n_kontrol)
```

Derajat kebebasan menggunakan pendekatan Welch-Satterthwaite.

Hasil:

```text
selisih gain = 3,61 poin
95% CI       = [1,17; 6,06]
t(61,56)     = 2,955
p            = 0,00443
Hedges g     = 0,653
```

Hedges `g` menggunakan simpangan baku gabungan yang dikoreksi terhadap bias sampel kecil. Nilai 0,653 menunjukkan perbedaan peningkatan berukuran sedang.

## 9. N-gain sebagai pembacaan tambahan peningkatan

Selain *gain* mentah, peningkatan juga dapat dibaca melalui *N-gain*:

```text
N-gain = (postes − pretes) / (100 − pretes)
```

Ringkasan *N-gain* rata-rata:

| Kelompok | Mean N-gain | Kategori umum |
|---|---:|---|
| Eksperimen | 0,375 | Sedang |
| Kontrol | 0,284 | Rendah |
| Gabungan | 0,331 | Sedang |

*N-gain* membantu memperjelas arah peningkatan, tetapi tidak boleh dijadikan satu-satunya dasar pengambilan keputusan efektivitas. Pada naskah utama, *N-gain* sebaiknya dibaca bersama uji beda dan *effect size*.

## 10. Analisis lima aspek

Analisis aspek bersifat sekunder terhadap skor total. Untuk menjaga konsistensi dengan BAB III, aspek kelima pada tabel hasil sebaiknya ditulis sebagai **dampak penyampaian**.

Perbandingan peningkatan per aspek dihitung dengan pendekatan yang sama terhadap skor *gain* tiap aspek. Pada implementasi teknis yang lebih rinci, p-value dapat disesuaikan menggunakan prosedur Holm. Namun, untuk naskah utama, cukup ditekankan aspek mana yang menunjukkan perbedaan paling jelas.

Hasil teknis terbaru:

| Aspek | Selisih gain | 95% CI | p Holm | Hedges g |
|---|---:|---:|---:|---:|
| Pengorganisasian | 1,048 | [0,389; 1,707] | 0,007 | 0,702 |
| Kejelasan | −0,178 | [−0,840; 0,485] | 1,000 | −0,119 |
| Ketepatan | 1,006 | [0,423; 1,589] | 0,004 | 0,770 |
| Strategi | −0,218 | [−0,861; 0,426] | 1,000 | −0,151 |
| Dampak penyampaian* | 1,052 | [0,488; 1,616] | 0,002 | 0,830 |

`*` Pada file mentah saat ini, aspek ini masih berlabel `metakognitif`. Untuk pelaporan naskah utama, label tersebut harus diselaraskan agar tidak bertentangan dengan konstruk metakognitif yang diukur melalui angket terpisah.

Secara umum, perbedaan peningkatan paling tampak pada pengorganisasian, ketepatan, dan aspek kelima rubrik berbicara.

## 11. Analisis tambahan opsional

Bagian ini tidak wajib dijadikan analisis utama dalam naskah, tetapi boleh dipakai sebagai uji ketahanan bila diperlukan.

### 11.1 ANCOVA-HC3

ANCOVA mengestimasi perbedaan postes setelah mengendalikan skor awal:

```text
postes_i = β0 + β1(kelompok_i) + β2(pretes_i) + ε_i
```

Dengan pengkodean:

```text
eksperimen = 1
kontrol    = 0
```

Hasil teknis tambahan:

```text
efek kelompok tersesuaikan = 3,32 poin
95% CI                     = [1,29; 5,34]
t(74)                      = 3,258
p                          = 0,00169
partial eta²               = 0,125
```

### Catatan penting tentang ANCOVA

- ANCOVA berguna sebagai pemeriksaan tambahan ketika peneliti ingin melihat apakah perbedaan postes tetap muncul setelah skor awal diperhitungkan.
- Namun, untuk naskah utama penelitian ini, ANCOVA tidak harus dijadikan analisis pokok.
- Jika hasil utama dari *paired t-test*, perbandingan *gain* antarkelompok, *N-gain*, dan *effect size* sudah konsisten, ANCOVA cukup disebut sebagai analisis pendukung atau bahkan cukup disimpan pada catatan teknis dan notebook.

### 11.2 Uji permutasi

Uji permutasi digunakan sebagai pemeriksaan ketahanan karena distribusi *gain* tidak sepenuhnya normal dan variansnya berbeda.

Hasil teknis tambahan:

```text
selisih gain aktual = 3,612
permutasi           = 50.000
p dua arah          = 0,00448
```

Hasil permutasi konsisten dengan pembacaan menggunakan Welch.

## 12. Urutan pengambilan keputusan

Urutan yang disarankan untuk naskah utama:

```text
Validasi data
    ↓
Deskriptif dan audit nilai ekstrem
    ↓
Uji normalitas
    ↓
Uji homogenitas
    ↓
Paired sample t-test
    ↓
Independent sample t-test atau Welch pada skor gain
    ↓
N-gain
    ↓
Effect size
    ↓
Analisis aspek secara sekunder
    ↓
Kesimpulan utama
```

Jika diperlukan untuk catatan teknis atau penguat internal, jalur tambahan boleh dilampirkan sebagai berikut:

```text
ANCOVA-HC3
    ↓
Uji permutasi
    ↓
Pemeriksaan konsistensi hasil
```

Dengan demikian, kesimpulan utama tidak bergantung pada satu p-value atau satu model statistik yang terlalu kompleks.

## 13. Kesimpulan perhitungan

Setelah koreksi input E33 berdasarkan sumber asli, kelompok eksperimen memperoleh peningkatan rata-rata 3,61 poin lebih tinggi daripada kelompok kontrol. Hasil ini didukung oleh:

- peningkatan signifikan dalam masing-masing kelompok;
- perbedaan *gain* antarkelompok yang signifikan melalui pendekatan Welch (`p = 0,00443`);
- *N-gain* kelompok eksperimen yang lebih tinggi daripada kelompok kontrol;
- *effect size* antarkelompok pada kategori sedang (`Hedges g = 0,653`).

Dengan posisi metodologis saat ini, empat unsur di atas sudah cukup menjadi dasar utama pembacaan efektivitas pada naskah. ANCOVA-HC3 dan uji permutasi dapat dipertahankan sebagai analisis tambahan bila dibutuhkan untuk menunjukkan kehati-hatian analisis, tetapi tidak wajib dinaikkan menjadi pusat narasi hasil.

Interpretasi kausal tetap harus dibatasi oleh desain kuasi-eksperimen, kemungkinan efek kelas atau dosen, serta persoalan penamaan konstruk pada aspek kelima rubrik yang masih perlu diselaraskan pada tahap pelaporan akhir.
