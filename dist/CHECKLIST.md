# CHECKLIST: Kesesuaian BAB III, Instrumen, dan Data

Dokumen ini merangkum hasil pemeriksaan kesesuaian antara BAB III (Metode Penelitian), instrumen penelitian (`/instruments/`), dan data penelitian (`/data/`).

**Terakhir diperbarui:** 2 Juni 2026

---

## RINGKASAN TEMUAN

| Kategori | Status | Keterangan |
|----------|--------|------------|
| Instrumen vs BAB III | ⚠️ Ada ketidaksesuaian | Terminologi metakognitif & jumlah aspek rubrik |
| Data vs Instrumen | ⚠️ Ada ketidaksesuaian | Dimensi metakognitif berbeda |
| Kelengkapan Data (Berbicara) | ✅ Eksperimen lengkap | 35/38 punya pre+post |
| Kelengkapan Data (Metakognitif) | ⚠️ Pre-test terbatas | Hanya 7 pre-test dari 60+ |
| Jumlah Subjek | ⚠️ Tidak sesuai | BAB III: 72, Aktual: ~59 dengan data lengkap |

---

## A. KETIDAKSESUAIAN KRITIS - PERLU DIPERBAIKI

### A1. Terminologi Metakognitif (BAB III vs Instrumen) ✅ TERJAWAB

| Sumber | Dimensi yang Disebutkan |
|--------|-------------------------|
| **BAB I-II-III** (teoretis) | *awareness*, *evaluation*, *regulation* |
| **Instrumen** (operasional) | Perencanaan (Planning), Pemantauan (Monitoring), Evaluasi (Evaluation) |
| **Instrumen** (tambahan) | + Aspek Integratif (Teknik Feynman) |

**Status:** ✅ Tidak ada masalah. BAB II sudah menjelaskan pemetaan:

| Konstruk Teoretis (Magiera & Zawojewski) | Indikator Operasional |
|------------------------------------------|----------------------|
| *Metacognitive Awareness* | Perencanaan (Planning) |
| *Metacognitive Regulation* | Pemantauan (Monitoring) |
| *Metacognitive Evaluation* | Evaluasi (Evaluation) |

**Catatan:** Aspek Integratif (Teknik Feynman) di instrumen adalah tambahan khusus untuk penelitian ini, bukan bagian dari konstruk Magiera & Zawojewski.

- [x] Terminologi sudah konsisten dengan definisi operasional BAB I-II

---

### A2. Jumlah Dimensi Rubrik Keterampilan Berbicara (BAB III vs Instrumen)

| Sumber | Jumlah Dimensi | Dimensi |
|--------|----------------|---------|
| **BAB III (hal. E.3)** | 4 dimensi | Pengorganisasian ide, Kejelasan penyampaian, Ketepatan bahasa, Strategi komunikasi |
| **Rubrik Instrumen** | 5 dimensi | + Penerapan Metakognitif (20%) |
| **Data CSV** | 5 kolom | pre/post_pengorganisasian, _kejelasan, _ketepatan, _strategi, _metakognitif |

**Masalah:** BAB III menyebut 4 dimensi, tetapi instrumen dan data menggunakan 5 dimensi.

**Catatan BAB III:** "skor metakognitif **tidak digabungkan** ke dalam skor keterampilan berbicara" - tetapi rubrik dan data justru memasukkannya sebagai dimensi ke-5.

- [ ] **Perlu keputusan:** Apakah dimensi metakognitif (E) termasuk dalam skor keterampilan berbicara atau tidak?
- [ ] Jika termasuk: Revisi BAB III agar menyebut 5 dimensi
- [ ] Jika tidak termasuk: Pisahkan kolom `*_metakognitif` dari analisis keterampilan berbicara

---

### A3. Aspek Angket Respons Mahasiswa (BAB III vs Instrumen)

| Sumber | Jumlah Aspek | Aspek |
|--------|--------------|-------|
| **BAB III (hal. E.5)** | 5 aspek | Kualitas isi, Kemudahan penggunaan, Interaktivitas, Dampak keterampilan berbicara, Dampak metakognitif |
| **Angket Instrumen** | 7 aspek | + Karakteristik Microlearning (4 item), + Penerapan Teknik Feynman (4 item) |

- [ ] Revisi BAB III agar menyebut 7 aspek, atau
- [ ] Jelaskan bahwa 2 aspek tambahan merupakan sub-aspek

---

### A4. Lembar Validasi Ahli Bahasa - Tidak Ada

**BAB III (Tabel 3.3):** Menyebut 3 ahli bahasa sebagai validator.

**Instrumen yang ada:**
- ✅ lembar_validasi_materi.md
- ✅ lembar_validasi_media.md
- ❌ **lembar_validasi_bahasa.md - TIDAK ADA**

**Data:**
- ✅ validasi_materi.csv
- ✅ validasi_media.csv
- ✅ validasi_bahasa.csv (ada datanya, tetapi instrumennya tidak ada)

- [ ] **Buat instrumen** `instruments/md/lembar_validasi_bahasa.md`
- [ ] Atau jelaskan bahwa validasi bahasa menggunakan instrumen yang sama dengan materi

---

## B. STATUS DATA

### B1. Data Pre-test Keterampilan Berbicara ✅

**KOREKSI (2 Juni 2026):** Data pre-test SUDAH ADA di `[SoT] rekap-pretes-postes.csv` dan sudah diproses ke file `[v]`.

| Kelompok | Punya Pre-test | Persentase | Status |
|----------|----------------|------------|--------|
| Eksperimen | **35 dari 38** | 92% | ✅ Cukup untuk paired t-test |
| Kontrol | **6 dari ~21** | 29% | ⚠️ Terbatas |

**Catatan Metode Penilaian:**
- 28 pre-test menggunakan **Rubrik** (5 dimensi lengkap)
- 13 pre-test menggunakan **Langsung** (hanya nilai akhir, tanpa breakdown dimensi)
- Untuk analisis per-dimensi, hanya yang metode Rubrik yang bisa digunakan

**Implikasi Analisis:**
- [x] Data eksperimen cukup lengkap (35 pre+post)
- [ ] ❓ **Kontrol terbatas** (hanya 6 pre+post) - pertimbangkan strategi analisis alternatif
- [ ] Opsi: independent t-test pada post-test, atau ANCOVA dengan pre-test sebagai covariate untuk subset lengkap

---

### B2. Data Pre-test Metakognitif ✅ TERJAWAB

**Konfirmasi dari peneliti (2 Juni 2026):**
- Angket metakognitif di platform **hanya diberikan setelah menggunakan media** (post-test)
- Sebagian pre-test metakognitif dilakukan **di luar platform** (paper-based)

**Data di platform:**
- Pre-test (Uji Awal): 7 mahasiswa
- Post-test (Ujian Akhir): 63 mahasiswa

**Implikasi untuk Analisis:**
- [x] `QN_angket_penggunaan_media.csv` **BUKAN** pre-test metakognitif
- [ ] ❓ **Perlu dicari:** Data pre-test metakognitif paper-based (jika ada)
- [ ] Jika data paper-based tidak tersedia digital, analisis metakognitif:
  - Deskriptif post-test
  - Perbandingan kelompok eksperimen vs kontrol (independent t-test)
  - Paired t-test hanya untuk 7 mahasiswa yang ada di platform

---

### B3. Data Uji Coba (Small Group & Large Group) ❌

| File | Status |
|------|--------|
| `uji_coba/small_group_prepost.csv` | ❌ Template kosong (UCS01-UCS10) |
| `uji_coba/small_group_respons.csv` | ❌ Template kosong |
| `uji_coba/large_group_prepost.csv` | ❌ Template kosong (UCL01-UCL25) |
| `uji_coba/large_group_respons.csv` | ❌ Template kosong |

**BAB III (Tabel 3.3):** Uji coba terbatas = 10 orang, Uji coba lebih luas = 25 orang.

- [ ] Isi data uji coba terbatas (10 mahasiswa)
- [ ] Isi data uji coba lebih luas (25 mahasiswa)

---

### B4. Data Validasi 🔲

| File | Status | Catatan |
|------|--------|---------|
| `validasi/validasi_materi.csv` | ✅ Terisi | 15 butir x 3 validator |
| `validasi/validasi_media.csv` | 🔲 Perlu cek | |
| `validasi/validasi_bahasa.csv` | 🔲 Perlu cek | |
| `validasi/validasi_praktisi.csv` | 🔲 Perlu cek | |
| `validasi/revisi_produk.csv` | 🔲 Perlu cek | Masukan & revisi |

- [ ] Verifikasi kelengkapan semua file validasi
- [ ] Isi identitas validator (nama, gelar, afiliasi)

---

### B5. Data Kualitatif 🔲

| File | Status | Catatan |
|------|--------|---------|
| `kualitatif/wawancara.csv` | 🔲 Belum terisi | Perlu 12 kutipan, 5 tema |
| `kualitatif/observasi.csv` | 🔲 Belum terisi | Perlu 8 indikator |

- [ ] Isi kutipan wawancara verbatim
- [ ] Isi hasil observasi per indikator
- [ ] ❓ Konfirmasi: Apakah subjek wawancara = E03, E05, E07, E08, E09, E11, E12, E14, E16, E19, E22, E25?

---

## C. JUMLAH SUBJEK TIDAK SESUAI

### C1. Field Test

| Keterangan | BAB III (Tabel 3.3) | Data Aktual |
|------------|---------------------|-------------|
| Kelompok Eksperimen | 36 | 38 (E01-E38) |
| Kelompok Kontrol | 36 | 21 dengan data post |
| **Total** | **72** | **~59** |

- [ ] ❓ **Konfirmasi:** Mengapa jumlah kontrol hanya 21? Apakah ada dropout?
- [ ] Revisi BAB III jika jumlah aktual berbeda

### C2. Validator

| Peran | BAB III | Perlu Konfirmasi |
|-------|---------|------------------|
| Ahli Materi | 3 | ❓ Siapa nama & afiliasinya? |
| Ahli Media | 3 | ❓ Siapa nama & afiliasinya? |
| Ahli Bahasa | 3 | ❓ Siapa nama & afiliasinya? |
| Praktisi | 2 | ❓ Siapa nama & afiliasinya? |

- [ ] Isi identitas semua validator

---

## D. INSTRUMEN YANG ADA vs YANG DISEBUTKAN DI BAB III

| Instrumen di BAB III | File Instrumen | Status |
|----------------------|----------------|--------|
| Lembar observasi | `lembar_observasi.md` | ✅ Ada |
| Pedoman wawancara | `pedoman_wawancara.md` | ✅ Ada |
| Rubrik keterampilan berbicara | `rubrik_penilaian_bicara.md` | ✅ Ada |
| Angket metakognitif | `angket_metakognitif.md` | ✅ Ada |
| Angket respons mahasiswa | `angket_respons_mahasiswa.md` | ✅ Ada |
| Lembar validasi ahli materi | `lembar_validasi_materi.md` | ✅ Ada |
| Lembar validasi ahli media | `lembar_validasi_media.md` | ✅ Ada |
| Lembar validasi ahli bahasa | - | ❌ **TIDAK ADA** |
| Lembar validasi praktisi | - | ⚠️ Belum jelas (mungkin sama dengan materi?) |

**Instrumen tambahan yang ada tapi tidak disebutkan di BAB III:**

| File | Keterangan |
|------|------------|
| `uji_kepraktisan.md` | Lembar uji kepraktisan untuk mahasiswa |

- [ ] Tambahkan penjelasan uji kepraktisan di BAB III, atau
- [ ] Gabungkan dengan angket respons mahasiswa

---

## E. PRIORITAS PENGERJAAN

### 🔴 Prioritas 1: Keputusan Konseptual
1. [x] ~~**Terminologi metakognitif:**~~ Gunakan awareness-evaluation-regulation (teoretis), dipetakan ke planning-monitoring-evaluation (operasional) - sudah konsisten dengan BAB II
2. [ ] **Dimensi rubrik:** Apakah metakognitif termasuk dalam skor keterampilan berbicara?
3. [x] ~~**Pre-test metakognitif:**~~ `QN_angket_penggunaan_media.csv` BUKAN pre-test. Pre-test sebagian dilakukan paper-based.

### 🟠 Prioritas 2: Data Kritis
4. [x] ~~Konfirmasi keberadaan data pre-test keterampilan berbicara~~ (sudah ada, 35 eksperimen + 6 kontrol)
5. [ ] Isi data uji coba terbatas & lebih luas
6. [ ] Lengkapi data validasi + identitas validator
7. [ ] **Cari data pre-test metakognitif paper-based** (jika ada)

### 🟡 Prioritas 3: Data Pendukung
7. [ ] Isi data wawancara (kutipan verbatim)
8. [ ] Isi data observasi
9. [ ] Lengkapi respons mahasiswa (E35, E40)

### 🟢 Prioritas 4: Revisi Dokumen
10. [ ] Revisi BAB III sesuai keputusan di atas
11. [ ] Buat instrumen validasi bahasa (jika perlu)
12. [ ] Update jumlah subjek di BAB III

---

## F. CATATAN TEKNIS

### Format Data CSV
- **Skala validasi:** 1-4 (1=Sangat Kurang, 2=Kurang, 3=Baik, 4=Sangat Baik)
- **Skala respons:** 1-4 (STS-TS-S-SS)
- **Skala metakognitif:** 1-4 (STS-TS-S-SS), total per dimensi /16
- **Skala keterampilan berbicara:** /15 per aspek (5 aspek), total /75, dinormalisasi ke 0-100

### Kriteria Kelayakan
| Persentase | Kategori |
|------------|----------|
| 81-100% | Sangat Valid / Sangat Baik |
| 61-80% | Valid / Baik |
| 41-60% | Cukup Valid |
| 21-40% | Kurang Valid |
| 0-20% | Tidak Valid |

---

*Checklist ini dibuat berdasarkan pemeriksaan pada 2 Juni 2026.*
*Dikoreksi setelah verifikasi ulang file SoT.*
*Gunakan checklist ini sebagai panduan untuk melengkapi data dan merevisi BAB III.*
