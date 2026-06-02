# Pemetaan CSV Angket Metakognitif → Konstruk Penilaian

File CSV `angket-metakognitif.csv` merekam respons peserta terhadap angket kesadaran metakognitif berbicara. Angket ini mengukur aspek-aspek konstruk **Kognitif-Metakognitif (Goh & Liu, 2023)** yang tidak dapat diobservasi langsung dari video ujian, serta kemampuan penyederhanaan konsep berbasis **Teknik Feynman**.

---

## Posisi dalam Alur Uji

Angket metakognitif diisi peserta pada **Step 5** (pasca ujian bicara), sehingga mencerminkan refleksi diri setelah pengalaman berbicara berlangsung. Ini melengkapi data rubrik pengajar yang hanya mengukur aspek yang terobservasi dari video.

---

## Struktur Umum CSV

| Kolom | Keterangan |
|---|---|
| NIM | Nomor Induk Mahasiswa dari LMS |
| Nama | Nama lengkap peserta |
| Kode Player | Username di platform Bicaranta |
| Sesi | `Uji Awal` (pre-tes) atau `Ujian Akhir Semester` (pos-tes) |
| P1–P4 | Skor per butir aspek Planning (q1–q4) |
| M5–M8 | Skor per butir aspek Monitoring (q5–q8) |
| E9–E12 | Skor per butir aspek Evaluation (q9–q12) |
| I13–I16 | Skor per butir aspek Integratif/Feynman (q13–q16) |
| Planning (/16) | Total skor aspek Planning |
| Monitoring (/16) | Total skor aspek Monitoring |
| Evaluation (/16) | Total skor aspek Evaluation |
| Integratif (/16) | Total skor aspek Integratif |
| Total Skor (/64) | Jumlah seluruh butir |
| Persentase (%) | `(Total / 64) × 100` |
| Kategori | Sangat Rendah / Rendah / Sedang / Tinggi / Sangat Tinggi |
| Tanggal Isi | Waktu pengisian angket |

**Skala jawaban:** 1 = Sangat Tidak Setuju, 2 = Tidak Setuju, 3 = Setuju, 4 = Sangat Setuju

---

## Pemetaan Butir → Konstruk

### Aspek 1: Planning (Perencanaan) → Konstruk **Conceptualisation**

> *Conceptualisation: Perencanaan ide dan tujuan komunikasi sebelum berbicara (Goh & Liu, 2023)*

Dalam rubrik pengajar, Conceptualisation tidak masuk karena terjadi sebelum berbicara dan tidak terobservasi dari video. Angket ini mengukurnya secara langsung melalui laporan diri peserta.

| Kode | No. Butir | Pernyataan |
|---|---|---|
| P1 | 1 | Sebelum berbicara, saya selalu merencanakan poin-poin utama yang akan saya sampaikan |
| P2 | 2 | Saya menyiapkan struktur pembicaraan yang logis sebelum mulai berbicara |
| P3 | 3 | Saya menentukan tujuan yang jelas sebelum memulai pembicaraan atau presentasi |
| P4 | 4 | Saya mempertimbangkan siapa audiens saya sebelum merencanakan cara berbicara |

**Skor maks: 16** | Butir negatif: tidak ada

---

### Aspek 2: Monitoring (Pemantauan) → Konstruk **Self-monitoring**

> *Self-monitoring: Pemantauan diri selama proses berbicara berlangsung (Goh & Liu, 2023)*

Self-monitoring adalah proses internal yang tidak dapat diobservasi dari video. Angket ini menangkapnya sebagai kesadaran peserta terhadap proses berpikirnya sendiri saat berbicara.

| Kode | No. Butir | Pernyataan |
|---|---|---|
| M5 | 5 | Saat berbicara, saya memperhatikan apakah penyampaian saya sudah jelas atau belum |
| M6 | 6 | Saya dapat merasakan apakah audiens memahami apa yang saya sampaikan |
| M7 | 7 | Ketika menghadapi kesulitan saat berbicara, saya mengubah strategi penyampaian |
| M8 | 8 | Saya menyesuaikan kecepatan bicara saya berdasarkan respons audiens |

**Skor maks: 16** | Butir negatif: tidak ada

---

### Aspek 3: Evaluation (Evaluasi) → Konstruk **Self-evaluation**

> *Self-evaluation: Evaluasi diri setelah selesai berbicara (Goh & Liu, 2023)*

Self-evaluation mencerminkan kemampuan peserta menilai pencapaian tujuan komunikasinya sendiri dan mengidentifikasi kekuatan serta kelemahan pasca berbicara.

| Kode | No. Butir | Pernyataan |
|---|---|---|
| E9 | 9 | Setelah selesai berbicara, saya menilai apakah tujuan komunikasi saya tercapai |
| E10 | 10 | Saya merefleksikan keberhasilan penyampaian pesan setelah selesai berbicara |
| E11 | 11 | Saya dapat mengidentifikasi aspek keterampilan berbicara yang menjadi kekuatan saya |
| E12 | 12 | Saya menyadari kelemahan-kelemahan dalam keterampilan berbicara saya |

**Skor maks: 16** | Butir negatif: tidak ada

---

### Aspek 4: Integratif → **Teknik Feynman** (kemampuan menyederhanakan)

> *Aspek tambahan yang mengukur kemampuan menjelaskan ulang konsep dengan bahasa sederhana — merefleksikan kedalaman pemahaman dan keterampilan komunikasi peserta.*

Aspek ini bersifat integratif: mengukur seberapa jauh peserta mampu menyederhanakan dan mengartikulasikan ulang pemahaman mereka, yang berkaitan dengan kualitas Formulation dan kemampuan kognitif-metakognitif secara keseluruhan.

| Kode | No. Butir | Pernyataan |
|---|---|---|
| I13 | 13 | Saya mampu menjelaskan konsep kompleks dengan bahasa yang sederhana |
| I14 | 14 | Saya menggunakan analogi atau contoh untuk memperjelas penjelasan saya |
| I15 | 15 | Saya dapat mengidentifikasi bagian materi yang sulit saya jelaskan |
| I16 | 16 | Saya merefleksikan pemahaman saya setelah menjelaskan sesuatu kepada orang lain |

**Skor maks: 16** | Butir negatif: tidak ada

---

## Skema Penilaian & Kategorisasi

```
Total Skor  = Planning + Monitoring + Evaluation + Integratif
            = maks 16 + 16 + 16 + 16 = 64 poin

Persentase  = (Total Skor / 64) × 100
```

| Persentase | Kategori |
|---|---|
| ≥ 81% | Sangat Tinggi |
| 61–80% | Tinggi |
| 41–60% | Sedang |
| 21–40% | Rendah |
| < 21% | Sangat Rendah |

---

## Ringkasan: Relasi Angket Metakognitif ↔ Konstruk Utama & Rubrik

| Aspek Angket | Konstruk (Goh & Liu, 2023) | Ada di Rubrik Pengajar? | Keterangan |
|---|---|---|---|
| Planning | Conceptualisation | ✗ | Diukur via angket (proses pra-bicara) |
| Monitoring | Self-monitoring | ✗ | Diukur via angket (proses internal saat bicara) |
| Evaluation | Self-evaluation | ✗ | Diukur via angket (refleksi pasca-bicara) |
| Integratif | — (Feynman) | ✗ | Pelengkap: kedalaman pemahaman & artikulasi konsep |

Keempat aspek ini **tidak ada di rubrik pengajar** karena tidak dapat diobservasi dari video. Angket metakognitif adalah satu-satunya instrumen yang mengukurnya secara langsung dalam sistem Bicaranta.

---

## Catatan Penggunaan

- **Analisis pre→pos-tes**: bandingkan `Persentase` atau `Total Skor` peserta yang sama antara `Sesi = Uji Awal` dan `Sesi = Ujian Akhir Semester`.
- **Profil metakognitif**: gunakan kolom aspek total (`/16`) untuk melihat dimensi mana yang paling lemah/kuat per peserta.
- **Triangulasi dengan rubrik**: korelasikan skor Monitoring/Evaluation di angket ini dengan skor Dampak atau Penyesuaian di rubrik pengajar untuk melihat konsistensi antara penilaian diri dan penilaian eksternal.
