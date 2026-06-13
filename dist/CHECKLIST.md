# CHECKLIST: Kesesuaian BAB III, Instrumen, dan Data

Dokumen ini merangkum hasil pemeriksaan kesesuaian antara BAB III (Metode Penelitian), instrumen penelitian (`/instruments/`), dan data penelitian (`/data/`).

**Terakhir diperbarui:** 7 Juni 2026

---

## RINGKASAN TEMUAN

| Kategori | Status | Keterangan |
|----------|--------|------------|
| Instrumen vs BAB III | ✅ Konsisten | Terminologi metakognitif sudah dipetakan |
| Data vs Instrumen | ✅ Konsisten | Dimensi metakognitif termasuk dalam skor berbicara |
| Kelengkapan Data (Berbicara) | ✅ Eksperimen lengkap | 35/38 punya pre+post |
| Kelengkapan Data (Metakognitif) | ⚠️ Pre-test terbatas | Hanya 7 pre-test dari 60+ |
| Jumlah Subjek | ✅ Sesuai | 74 total (38 eksperimen + 36 kontrol) |

---

## A. STATUS ISU KONSEPTUAL - SUDAH TERJAWAB

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

**Catatan:** Aspek Integratif (Teknik Feynman) di instrumen adalah tambahan khusus untuk penelitian ini.

- [x] Terminologi sudah konsisten dengan definisi operasional BAB I-II

---

### A2. Terminologi Keterampilan Berbicara ✅ TERJAWAB

**Konstruk berdasarkan definisi operasional:**
- **5 aspek dari Goh & Liu:** Pengorganisasian ide, Kejelasan penyampaian, Ketepatan bahasa, Strategi komunikasi, + 1 aspek
- **3 aspek dari Lucas & Stob:** Speaking & Metacognitive (terintegrasi)

- [x] Terminologi sesuai konstruk definisi operasional

---

### A3. Dimensi Rubrik Keterampilan Berbicara ✅ TERJAWAB

| Sumber | Jumlah Dimensi | Dimensi |
|--------|----------------|---------|
| **BAB III (hal. E.3)** | 4 dimensi | Pengorganisasian ide, Kejelasan penyampaian, Ketepatan bahasa, Strategi komunikasi |
| **Rubrik Instrumen** | 5 dimensi | + Penerapan Metakognitif (20%) |
| **Data CSV** | 5 kolom | pre/post_pengorganisasian, _kejelasan, _ketepatan, _strategi, _metakognitif |

**Status:** ✅ TERJAWAB

**Keputusan:** Dimensi metakognitif **TERMASUK** dalam skor keterampilan berbicara.

**Penjelasan:**
- Ada **2x tes** dalam penelitian ini:
  1. Tes berbicara di depan umum (presentasi)
  2. Tes berbicara secara teruji di platform
- Data nilai berasal dari **rata-rata** kedua tes tersebut

- [x] Dimensi metakognitif termasuk dalam skor keterampilan berbicara
- [ ] Revisi BAB III agar menyebut 5 dimensi (jika belum)

---

### A4. Aspek Angket Respons Mahasiswa (BAB III vs Instrumen)

| Sumber | Jumlah Aspek | Aspek |
|--------|--------------|-------|
| **BAB III (hal. E.5)** | 5 aspek | Kualitas isi, Kemudahan penggunaan, Interaktivitas, Dampak keterampilan berbicara, Dampak metakognitif |
| **Angket Instrumen** | 7 aspek | + Karakteristik Microlearning (4 item), + Penerapan Teknik Feynman (4 item) |

- [ ] Revisi BAB III agar menyebut 7 aspek, atau
- [ ] Jelaskan bahwa 2 aspek tambahan merupakan sub-aspek

---

### A5. Lembar Validasi Ahli Bahasa ✅ TERJAWAB

**Status:** ✅ **Lembar validasi bahasa SUDAH ADA**

**Instrumen yang ada:**
- ✅ lembar_validasi_materi.md
- ✅ lembar_validasi_media.md
- ✅ lembar_validasi_bahasa.md

**Data:**
- ✅ validasi_materi.csv
- ✅ validasi_media.csv
- ✅ validasi_bahasa.csv

- [x] Instrumen validasi bahasa sudah tersedia

---

## B. STATUS DATA

### B1. Data Pre-test Keterampilan Berbicara ✅

**KOREKSI (2 Juni 2026):** Data pre-test SUDAH ADA di `[SoT] rekap-pretes-postes.csv` dan sudah diproses ke file `[v]`.

| Kelompok | Punya Pre-test | Persentase | Status |
|----------|----------------|------------|--------|
| Eksperimen | **35 dari 38** | 92% | ✅ Cukup untuk paired t-test |
| Kontrol | **6 dari ~36** | 17% | ⚠️ Terbatas |

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

### B3. Data Post-test ⚠️ BELUM MASUK

**Status (7 Juni 2026):** Data post-test **belum masuk sepenuhnya**.

- [ ] Lengkapi data post-test keterampilan berbicara
- [ ] Lengkapi data post-test metakognitif

---

### B4. Data Uji Coba ✅ TERJAWAB

**Struktur Uji Coba:**

| Tahap | Jumlah | Komposisi | Tujuan |
|-------|--------|-----------|--------|
| **Uji Coba Terbatas (Fitur)** | 5 orang | 3 eksperimen + 2 kontrol | Memastikan kelancaran fitur dari awal sampai akhir |
| **Uji Kepraktisan** | 10 orang | 5 eksperimen + 5 kontrol | Menguji aspek kepraktisan dan kognitif sesuai CTML |

**Hasil Uji Coba Terbatas:**
- Peserta adalah mahasiswa yang kesulitan memilih media pembelajaran dan punya beragam preferensi belajar (outliers dari analisis awal)
- **100% menjawab** fitur yang mereka butuhkan untuk belajar terakomodasi

**Catatan:** Uji kepraktisan dilakukan sekaligus saat uji coba terbatas.

| File | Status |
|------|--------|
| `uji_coba/small_group_prepost.csv` | ⚠️ Perlu isi skor (6 orang sudah seed) |
| `uji_coba/small_group_respons.csv` | ⚠️ Perlu isi respons (6 orang sudah seed) |
| `uji_coba/large_group_prepost.csv` | ⚠️ Perlu isi skor tahap 2 (10 orang sudah seed) |
| `uji_coba/large_group_respons.csv` | ⚠️ Perlu isi respons tahap 2 (10 orang sudah seed) |

- [ ] Isi data uji coba kelompok kecil (6 mahasiswa)
- [ ] Isi data uji coba terbatas lanjutan (10 mahasiswa)

---

### B5. Data Validasi ✅ TERJAWAB

| File | Status | Catatan |
|------|--------|---------|
| `validasi/validasi_materi.csv` | ✅ Terisi | 15 butir x 3 validator |
| `validasi/validasi_media.csv` | 🔲 Perlu cek | |
| `validasi/validasi_bahasa.csv` | ✅ Ada | |
| `validasi/validasi_praktisi.csv` | 🔲 Perlu cek | |
| `validasi/revisi_produk.csv` | 🔲 Perlu cek | Masukan & revisi |

**Identitas Validator (Terkonfirmasi):**

| Peran | Nama | Afiliasi |
|-------|------|----------|
| Ahli Materi | Dr. Teti Sobari, M.Pd. | IKIP Siliwangi |
| Ahli Media | Udi Samanhudi, Ph.D | Universitas Sultan Ageng Tirtayasa |
| Ahli Bahasa | ❓ | ❓ |
| Praktisi | ❓ | ❓ |

- [x] Identitas ahli materi sudah dikonfirmasi
- [x] Identitas ahli media sudah dikonfirmasi
- [ ] Konfirmasi identitas ahli bahasa
- [ ] Konfirmasi identitas praktisi

---

### B6. Data Kualitatif 🔲

| File | Status | Catatan |
|------|--------|---------|
| `kualitatif/wawancara.csv` | 🔲 Belum terisi | Perlu kutipan verbatim |
| `kualitatif/observasi.csv` | 🔲 Belum terisi | Perlu 8 indikator |

**Temuan Wawancara Awal dengan Pengampu:**

| Pengampu | Kelas | Temuan |
|----------|-------|--------|
| Via Nugraha | Kontrol | Mahasiswa agak abai terhadap kesantunan bahasa |
| Aditya Permana | Eksperimen | Mahasiswa cuek terhadap pemilihan kata formal dalam situasi akademis, kurang mampu mengontrol pilihan kata, tidak sanggup diberi modul panjang (hanya baca awal), ada yang belajar dari TikTok tanpa sumber valid |

- [ ] Isi kutipan wawancara verbatim
- [ ] Isi hasil observasi per indikator

---

## C. JUMLAH SUBJEK ✅ TERJAWAB

### C1. Field Test

| Keterangan | Data Aktual |
|------------|-------------|
| Kelompok Eksperimen | **38** (E01-E38) |
| Kelompok Kontrol | **36** |
| **Total** | **74** |

**Catatan:** Ikuti data aktual (38 eksperimen + 36 kontrol = 74 total).

- [x] Jumlah subjek field test sudah dikonfirmasi
- [ ] Revisi BAB III jika menyebut angka berbeda (72 → 74)

### C2. Pengampu Kelas

| Kelas | Pengampu |
|-------|----------|
| Eksperimen | Aditya Permana |
| Kontrol | Via Nugraha |

---

## D. DESKRIPSI PERLAKUAN

### D1. Kelas Eksperimen
- **Diwajibkan** menggunakan media **Bicaranta** sebagai sumber pembelajaran
- Membaca 2-3 bagian setiap pertemuan beserta latihannya
- Menyelesaikan setiap tahapan modul secara **sinkronus maupun asinkronus**
- Merasakan manfaat microlearning dibanding pembelajaran konvensional
- Latihan menjelaskan ulang dengan kata-kata sendiri (**Teknik Feynman**)
- Tes 1-2 orang per pertemuan: diberi topik yang juga disajikan dalam Uji Akhir

### D2. Kelas Kontrol
- **Tidak diwajibkan** mengakses materi
- Pembelajaran konvensional

---

## E. INSTRUMEN YANG ADA vs YANG DISEBUTKAN DI BAB III

| Instrumen di BAB III | File Instrumen | Status |
|----------------------|----------------|--------|
| Lembar observasi | `lembar_observasi.md` | ✅ Ada |
| Pedoman wawancara | `pedoman_wawancara.md` | ✅ Ada |
| Rubrik keterampilan berbicara | `rubrik_penilaian_bicara.md` | ✅ Ada |
| Angket metakognitif | `angket_metakognitif.md` | ✅ Ada |
| Angket respons mahasiswa | `angket_respons_mahasiswa.md` | ✅ Ada |
| Lembar validasi ahli materi | `lembar_validasi_materi.md` | ✅ Ada |
| Lembar validasi ahli media | `lembar_validasi_media.md` | ✅ Ada |
| Lembar validasi ahli bahasa | `lembar_validasi_bahasa.md` | ✅ Ada |
| Lembar validasi praktisi | - | ⚠️ Perlu cek |
| Lembar uji kepraktisan | `uji_kepraktisan.md` | ✅ Ada |

**Catatan Uji Kepraktisan:**
- Dilakukan sekaligus saat uji coba terbatas dengan **10 orang** (5 eksperimen + 5 kontrol)
- Menguji aspek: ease of use, kualitas konten, dan aspek kognitif sesuai CTML

---

## F. PRIORITAS PENGERJAAN

### 🔴 Prioritas 1: Data Kritis
1. [ ] Lengkapi data post-test (belum masuk)
2. [ ] Isi data uji coba terbatas (5 mahasiswa)
3. [ ] Isi data uji kepraktisan (10 mahasiswa)
4. [ ] Cari data pre-test metakognitif paper-based (jika ada)

### 🟠 Prioritas 2: Validasi & Identitas
5. [ ] Konfirmasi identitas ahli bahasa
6. [ ] Konfirmasi identitas praktisi
7. [ ] Verifikasi kelengkapan semua file validasi

### 🟡 Prioritas 3: Data Pendukung
8. [ ] Isi data wawancara (kutipan verbatim)
9. [ ] Isi data observasi
10. [ ] Lengkapi respons mahasiswa (E35, E40)

### 🟢 Prioritas 4: Revisi Dokumen
11. [ ] Revisi BAB III: jumlah subjek 72 → 74
12. [ ] Revisi BAB III: 4 dimensi → 5 dimensi (jika belum)
13. [ ] Revisi BAB III: jelaskan uji kepraktisan terintegrasi dengan uji coba terbatas

---

## G. CATATAN TEKNIS

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
*Dikoreksi berdasarkan respons peneliti pada 7 Juni 2026.*
*Gunakan checklist ini sebagai panduan untuk melengkapi data dan merevisi BAB III.*
