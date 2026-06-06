# Pemetaan CSV Angket Media → Konstruk Penilaian

File CSV `angket-media.csv` merekam respons peserta terhadap angket persepsi media pembelajaran. Angket ini mengukur efektivitas platform **Bicaranta** sebagai media web microlearning berbasis Teknik Feynman dalam mendukung pengembangan keterampilan berbicara dan kesadaran metakognitif peserta.

---

## Posisi dalam Alur Uji

Angket media diisi peserta bersamaan dengan angket metakognitif pada **Step 5** (pasca ujian bicara). Berbeda dengan angket metakognitif yang mengukur kondisi diri peserta, angket media mengukur **persepsi peserta terhadap platform** sebagai instrumen pembelajaran.

---

## Struktur Umum CSV

| Kolom | Keterangan |
|---|---|
| NIM | Nomor Induk Mahasiswa dari LMS |
| Nama | Nama lengkap peserta |
| Kode Player | Username di platform Bicaranta |
| Sesi | `Uji Awal` (pre-tes) atau `Ujian Akhir Semester` (pos-tes) |
| K1–K2 | Skor per butir: Kualitas Konten |
| Kem3*–Kem4 | Skor per butir: Kemudahan Penggunaan (`*` = butir negatif) |
| Ket5–Ket6 | Skor per butir: Keterlibatan |
| D7*–D8 | Skor per butir: Dampak Berbicara (`*` = butir negatif) |
| DM9–DM10 | Skor per butir: Dukungan Metakognisi |
| ML11*–ML12 | Skor per butir: Microlearning (`*` = butir negatif) |
| Ak13–Ak14* | Skor per butir: Aksesibilitas (`*` = butir negatif) |
| F15–F16 | Skor per butir: Teknik Feynman |
| R17*–R18 | Skor per butir: Refleksi Diri (`*` = butir negatif) |
| Kualitas Konten (/8) … Refleksi Diri (/8) | Total skor per aspek (9 aspek) |
| Total Skor (/72) | Jumlah seluruh butir |
| Persentase (%) | `(Total / 72) × 100` |
| Kategori | Sangat Rendah / Rendah / Sedang / Tinggi / Sangat Tinggi |
| Tanggal Isi | Waktu pengisian angket |

**Skala jawaban:** 1 = Sangat Tidak Setuju, 2 = Tidak Setuju, 3 = Setuju, 4 = Sangat Setuju

**Butir negatif** (reverse-scored dalam interpretasi): Kem3, D7, ML11, Ak14, R17 — skor tinggi pada butir ini mencerminkan persepsi *negatif* terhadap aspek tersebut.

---

## Pemetaan Butir → Dimensi Media

### Aspek 1: Kualitas Konten

> *Mengukur persepsi peserta terhadap ketepatan dan relevansi materi pembelajaran di platform.*

| Kode | No. Butir | Pernyataan |
|---|---|---|
| K1 | 1 | Materi dalam media pembelajaran ini mudah dipahami dan sesuai dengan tingkat kemampuan saya |
| K2 | 2 | Isi materi dalam media ini relevan dengan kebutuhan belajar keterampilan berbicara saya |

**Skor maks: 8** | Butir negatif: tidak ada

---

### Aspek 2: Kemudahan Penggunaan

> *Mengukur persepsi terhadap aksesibilitas teknis dan kemudahan navigasi platform.*

| Kode | No. Butir | Jenis | Pernyataan |
|---|---|---|---|
| Kem3* | 3 | Negatif | Media pembelajaran web microlearning ini sulit diakses dan digunakan |
| Kem4 | 4 | Positif | Navigasi dan fitur-fitur dalam media ini mudah dipahami dan dioperasikan |

**Skor maks: 8** | Butir negatif: Kem3

---

### Aspek 3: Keterlibatan

> *Mengukur sejauh mana platform mendorong partisipasi aktif dan engagement peserta dalam pembelajaran.*

| Kode | No. Butir | Pernyataan |
|---|---|---|
| Ket5 | 5 | Media ini mendorong saya untuk terlibat aktif dalam proses pembelajaran |
| Ket6 | 6 | Fitur-fitur interaktif dalam media ini membuat pembelajaran menjadi lebih menarik |

**Skor maks: 8** | Butir negatif: tidak ada

---

### Aspek 4: Dampak Berbicara

> *Mengukur persepsi peserta terhadap dampak penggunaan platform terhadap peningkatan keterampilan berbicara — berkaitan langsung dengan tujuan utama konstruk penilaian.*

| Kode | No. Butir | Jenis | Pernyataan |
|---|---|---|---|
| D7* | 7 | Negatif | Media ini kurang membantu meningkatkan kemampuan saya dalam berbicara |
| D8 | 8 | Positif | Setelah menggunakan media ini, saya merasa lebih percaya diri saat berbicara |

**Skor maks: 8** | Butir negatif: D7

---

### Aspek 5: Dukungan Metakognisi

> *Mengukur persepsi peserta terhadap kemampuan platform dalam mendorong kesadaran metakognitif — terhubung langsung ke konstruk Self-monitoring dan Self-evaluation (Goh & Liu, 2023).*

| Kode | No. Butir | Pernyataan |
|---|---|---|
| DM9 | 9 | Media ini membantu saya lebih sadar akan proses berpikir dan belajar saya sendiri |
| DM10 | 10 | Media ini meningkatkan kemampuan saya untuk merencanakan, memantau, dan mengevaluasi pembelajaran saya |

**Skor maks: 8** | Butir negatif: tidak ada

---

### Aspek 6: Microlearning

> *Mengukur persepsi terhadap pendekatan segmentasi konten dalam format microlearning — desain inti platform Bicaranta.*

| Kode | No. Butir | Jenis | Pernyataan |
|---|---|---|---|
| ML11* | 11 | Negatif | Durasi setiap modul pembelajaran tidak sesuai dan ada yang terlalu panjang/pendek |
| ML12 | 12 | Positif | Materi tersegmentasi (terbagi) dengan baik dalam unit-unit kecil yang mudah dipelajari |

**Skor maks: 8** | Butir negatif: ML11

---

### Aspek 7: Aksesibilitas

> *Mengukur fleksibilitas akses platform lintas waktu dan tempat, termasuk dampak format microlearning terhadap fokus belajar.*

| Kode | No. Butir | Jenis | Pernyataan |
|---|---|---|---|
| Ak13 | 13 | Positif | Saya dapat mengakses media ini kapan saja dan di mana saja dengan mudah |
| Ak14* | 14 | Negatif | Format microlearning membantu membuat saya tidak fokus pada materi yang sedang dipelajari |

**Skor maks: 8** | Butir negatif: Ak14

---

### Aspek 8: Teknik Feynman

> *Mengukur persepsi peserta terhadap efektivitas implementasi Teknik Feynman dalam platform — kemampuan menyederhanakan dan menjelaskan ulang konsep dengan bahasa sendiri.*

| Kode | No. Butir | Pernyataan |
|---|---|---|
| F15 | 15 | Media ini membantu saya menjelaskan konsep dengan bahasa yang lebih sederhana |
| F16 | 16 | Teknik "penyederhanaan & ulangi" dalam media ini meningkatkan pemahaman saya terhadap materi |

**Skor maks: 8** | Butir negatif: tidak ada

---

### Aspek 9: Refleksi Diri

> *Mengukur sejauh mana platform mendorong peserta untuk mengenali kesenjangan pemahaman dan melakukan refleksi mandiri — terhubung ke konstruk Self-evaluation.*

| Kode | No. Butir | Jenis | Pernyataan |
|---|---|---|---|
| R17* | 17 | Negatif | Saya tidak bisa mengidentifikasi bagian materi yang belum saya pahami dengan baik |
| R18 | 18 | Positif | Media ini mendorong saya untuk merefleksikan pemahaman saya sendiri |

**Skor maks: 8** | Butir negatif: R17

---

## Skema Penilaian & Kategorisasi

```
Total Skor  = Konten + Kemudahan + Keterlibatan + Dampak + Metakognisi
            + Microlearning + Aksesibilitas + Feynman + Refleksi
            = 9 aspek × 8 poin = maks 72 poin

Persentase  = (Total Skor / 72) × 100
```

| Persentase | Kategori |
|---|---|
| ≥ 81% | Sangat Tinggi |
| 61–80% | Tinggi |
| 41–60% | Sedang |
| 21–40% | Rendah |
| < 21% | Sangat Rendah |

---

## Relasi Angket Media ↔ Konstruk Penilaian & Angket Metakognitif

| Aspek Media | Relasi ke Konstruk Utama | Relasi ke Angket Metakognitif |
|---|---|---|
| Kualitas Konten | Mendukung Formulation (kosakata & retorika) | — |
| Kemudahan Penggunaan | — (aspek teknis platform) | — |
| Keterlibatan | Mendukung motivasi berbicara secara umum | — |
| Dampak Berbicara | Berkaitan dengan semua dimensi rubrik pengajar | Validasi eksternal atas perubahan Planning & Evaluation |
| Dukungan Metakognisi | Self-monitoring & Self-evaluation (Goh & Liu, 2023) | Cermin persepsi terhadap platform vs. laporan diri (Monitoring, Evaluation) |
| Microlearning | — (desain instruksional platform) | — |
| Aksesibilitas | — (aspek teknis platform) | — |
| Teknik Feynman | Mendukung Formulation & Articulation | Cermin persepsi vs. kemampuan Integratif di angket metakognitif |
| Refleksi Diri | Self-evaluation (Goh & Liu, 2023) | Cermin persepsi vs. skor Evaluation di angket metakognitif |

---

## Catatan Penggunaan

- **Butir negatif** (Kem3, D7, ML11, Ak14, R17): skor rendah = persepsi *positif* terhadap aspek tersebut. Perhatikan ini saat membaca data mentah per butir — total skor aspek sudah memperhitungkan arah butir secara akumulatif.
- **Analisis per aspek**: gunakan kolom `Aspek (/8)` untuk membandingkan persepsi terhadap tiap dimensi platform antar sesi (Uji Awal vs. UAS).
- **Triangulasi**: bandingkan `Dukungan Metakognisi` dan `Refleksi Diri` di angket media dengan skor `Monitoring` dan `Evaluation` di angket metakognitif untuk melihat konsistensi antara persepsi terhadap platform dan kondisi metakognitif aktual peserta.
- **Dampak Berbicara** adalah aspek yang paling langsung merepresentasikan tujuan akhir platform — gunakan sebagai indikator utama dalam evaluasi efektivitas media.
