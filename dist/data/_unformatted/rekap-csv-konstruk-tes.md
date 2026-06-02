# Pemetaan CSV Rekap Pre-tes & Pos-tes → Konstruk Penilaian

File CSV `rekap-pretes-postes.csv` merupakan ekspor hasil penilaian keterampilan berbicara pada dua sesi ujian: **Uji Awal (pre-tes)** dan **Ujian Akhir Semester (pos-tes)**. Kolom-kolomnya diturunkan langsung dari konstruk penilaian yang didefinisikan dalam `konstruk-penilaian.md`.

---

## Struktur Umum CSV

| Kolom | Tipe | Keterangan |
|---|---|---|
| NIM | Identitas | Nomor Induk Mahasiswa dari sistem LMS |
| Nama | Identitas | Nama lengkap peserta |
| Kode Player | Identitas | Username peserta di platform Bicaranta |
| Sesi | Konteks | `Uji Awal` (pre-tes) atau `Ujian Akhir Semester` (pos-tes) |
| Topik | Konteks | Topik bicara yang dipilih peserta (terkait aspek Conceptualisation) |
| Nilai Akhir | Skor | Skor final 0–100 (hasil normalisasi atau penilaian langsung) |
| Metode Penilaian | Meta | `Rubrik` = skor dihitung dari rubrik; `Langsung` = penilai memberi skor langsung |
| Penilai | Meta | Nama pengajar/admin yang memberi nilai |
| Tanggal Nilai | Meta | Waktu penilaian diselesaikan |

---

## Pemetaan Kolom → Konstruk

### Dimensi Kognitif-Metakognitif (Goh & Liu, 2023)

#### Aspek: Conceptualisation
> *Perencanaan ide dan tujuan komunikasi — dilakukan sebelum berbicara.*

Tidak masuk rubrik pengajar karena tidak dapat diobservasi dari video. Tercermin secara tidak langsung melalui kolom **Topik** (pilihan topik dan subtopik peserta di awal sesi uji).

#### Aspek: Formulation → Dimensi **Kebahasaan**

| Kolom CSV | Sub-kriteria | Indikator |
|---|---|---|
| `Kebahasaan (/15)` | Total dimensi | Jumlah dari tiga sub di bawah (maks 15) |
| `Diksi` | Pilihan kata | Ketepatan dan variasi kosakata yang digunakan |
| `Gramatikal` | Tata bahasa | Ketepatan struktur kalimat dan tata bahasa |
| `Retorika` | Strategi retorika | Penggunaan strategi retorika yang sesuai konteks |

#### Aspek: Articulation → Dimensi **Artikulasi**

| Kolom CSV | Sub-kriteria | Indikator |
|---|---|---|
| `Artikulasi (/15)` | Total dimensi | Jumlah dari tiga sub di bawah (maks 15) |
| `Artikulasi (sub)` | Kejelasan lafal | Artikulasi yang jelas dan mudah dipahami |
| `Intonasi` | Variasi intonasi | Intonasi yang bervariasi dan ekspresif |
| `Kecepatan` | Tempo bicara | Kecepatan bicara yang tepat dan nyaman diikuti |

#### Aspek: Self-monitoring & Self-evaluation
> *Pemantauan dan evaluasi diri — proses internal, tidak dapat diobservasi dari video.*

Tidak masuk rubrik pengajar. Dinilai melalui angket metakognitif (Step 5 dalam alur uji), yang datanya tersimpan terpisah di tabel `questionnaire_responses`.

---

### Dimensi Performatif (Lucas & Stob, 2020)

#### Aspek: Pengorganisasian Ide → Dimensi **Struktur**

| Kolom CSV | Sub-kriteria | Indikator |
|---|---|---|
| `Struktur (/15)` | Total dimensi | Jumlah dari tiga sub di bawah (maks 15) |
| `Urutan` | Urutan logis | Penyampaian yang sistematis dan runtut |
| `Transisi` | Perpindahan ide | Transisi antar ide yang jelas |
| `Simpulan` | Penutup | Kesimpulan yang koheren dan merangkum pesan utamaa |

#### Aspek: Penyesuaian Pesan → Dimensi **Penyesuaian**

| Kolom CSV | Sub-kriteria | Indikator |
|---|---|---|
| `Penyesuaian (/15)` | Total dimensi | Jumlah dari tiga sub di bawah (maks 15) |
| `Bahasa` | Bahasa sesuai audiens | Penggunaan bahasa sesuai tingkat dan karakteristik audiens |
| `Contoh` | Relevansi contoh | Pemilihan contoh atau analogi yang relevan dan tepat |
| `Formalitas` | Tingkat formalitas | Penyesuaian tingkat formalitas sesuai konteks |

#### Aspek: Penyampaian Berdampak → Dimensi **Dampak**

| Kolom CSV | Sub-kriteria | Indikator |
|---|---|---|
| `Dampak (/15)` | Total dimensi | Jumlah dari tiga sub di bawah (maks 15) |
| `Persuasi` | Teknik persuasi | Penggunaan teknik persuasi yang efektif |
| `Perhatian` | Menarik perhatian | Penyampaian yang menarik dan mempertahankan perhatian audiens |
| `Inspirasi` | Pesan inspiratif | Pesan yang menginspirasi atau memotivasi audiens |

#### Aspek: Adaptasi Umpan Balik
> *Responsivitas terhadap reaksi audiens — memerlukan interaksi real-time.*

Tidak masuk rubrik pengajar karena format ujian berbasis video monolog. Dicatat melalui komentar review video (Step 3 dalam alur uji).

---

## Skema Penilaian & Perhitungan Nilai

```
Skor Mentah = Kebahasaan + Artikulasi + Struktur + Penyesuaian + Dampak
            = maks 15 + 15 + 15 + 15 + 15
            = maks 75 poin

Nilai Akhir = (Skor Mentah / 75) × 100
```

Jika kolom `Metode Penilaian` bernilai **Langsung**, penilai memberikan `Nilai Akhir` secara langsung tanpa mengisi rubrik per sub-kriteria — kolom-kolom dimensi dan sub-kriteria akan kosong untuk baris tersebut.

---

## Ringkasan: Aspek Konstruk vs Ketersediaan di CSV

| Aspek Konstruk | Dimensi Rubrik | Ada di CSV? | Keterangan |
|---|---|---|---|
| Formulation | Kebahasaan | ✓ | Kolom `Kebahasaan (/15)` + `Diksi`, `Gramatikal`, `Retorika` |
| Articulation | Artikulasi | ✓ | Kolom `Artikulasi (/15)` + `Artikulasi (sub)`, `Intonasi`, `Kecepatan` |
| Pengorganisasian | Struktur | ✓ | Kolom `Struktur (/15)` + `Urutan`, `Transisi`, `Simpulan` |
| Penyesuaian Pesan | Penyesuaian | ✓ | Kolom `Penyesuaian (/15)` + `Bahasa`, `Contoh`, `Formalitas` |
| Penyampaian Berdampak | Dampak | ✓ | Kolom `Dampak (/15)` + `Persuasi`, `Perhatian`, `Inspirasi` |
| Conceptualisation | — | Parsial | Tercermin di kolom `Topik` |
| Self-monitoring | — | ✗ | Data di `questionnaire_responses`, tidak diekspor di CSV ini |
| Self-evaluation | — | ✗ | Data di `questionnaire_responses`, tidak diekspor di CSV ini |
| Adaptasi Umpan Balik | — | ✗ | Dicatat sebagai komentar review video, tidak diekspor di CSV ini |

---

## Catatan Penggunaan

- **Analisis gain skor** (peningkatan pre→pos-tes): bandingkan `Nilai Akhir` peserta yang sama antara baris `Sesi = Uji Awal` dan `Sesi = Ujian Akhir Semester`.
- **Analisis per dimensi**: gunakan kolom total dimensi (`/15`) untuk melihat profil kekuatan/kelemahan per aspek konstruk.
- **Analisis per sub-kriteria**: kolom sub-kriteria (mis. `Diksi`, `Intonasi`) memungkinkan analisis yang lebih granular, namun hanya tersedia untuk baris dengan `Metode Penilaian = Rubrik`.
- **Data metakognitif** (Self-monitoring, Self-evaluation) perlu digabungkan dari tabel `questionnaire_responses` jika diperlukan untuk analisis lengkap.
