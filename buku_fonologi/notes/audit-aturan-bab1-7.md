# Audit Kepatuhan Bab 1–7 terhadap ATURAN_BUKU.md

Tanggal: 2026-09-16

## Ringkasan

Bab 1–7 merupakan draf hasil ekstraksi modul yang **belum direvisi** terhadap aturan buku. Ditemukan **12 kategori ketidaksesuaian** yang konsisten di hampir semua bab. Berikut rinciannya.

---

## 1. Tujuan Pembelajaran — ❌ KOSONG (semua bab)

Aturan: Setiap bab perlu tujuan yang dapat diperiksa melalui latihan.

| Bab | Status |
|-----|--------|
| 1–7 | Semua masih `<!-- TODO -->` |

**Tindakan:** Tulis 3–5 butir tujuan pembelajaran per bab yang terukur dan terkait langsung dengan latihan akhir bab.

---

## 2. Rangkuman — ❌ KOSONG (semua bab)

Aturan: Tidak eksplisit wajib, tetapi sudah ada placeholder dan konsistensi menuntutnya.

| Bab | Status |
|-----|--------|
| 1–7 | Semua masih `<!-- TODO -->` |

**Tindakan:** Tulis rangkuman padat (3–5 paragraf pendek) setelah isi bab diperiksa.

---

## 3. 🧠 Istilah yang Dipelajari — ❌ KOSONG (semua bab)

Aturan: Setiap bab wajib diakhiri bagian `🧠 Istilah yang dipelajari pada bab ini` berisi daftar istilah penting dengan penjelasan ringkas.

| Bab | Status |
|-----|--------|
| 1–7 | Semua masih `<!-- TODO -->` |

**Tindakan:** Identifikasi 8–15 istilah kunci per bab, tulis definisi ringkas, lalu catat juga di `notes/glosarium-kerja.md`.

---

## 4. Visual / Gambar — ❌ NOL (semua bab)

Aturan: Setiap bab rata-rata minimal 2–3 gambar/visual utama. Jika belum tersedia, tulis penanda tempat `[deskripsi visual]` + caption `Gambar B.U`.

| Bab | Gambar/visual | Penanda tempat |
|-----|--------------|----------------|
| 1–7 | 0 | 0 |

**Tindakan:** Tambahkan minimal 2–3 penanda tempat visual per bab dengan deskripsi spesifik dan nomor gambar. Contoh prioritas:
- Bab 1: Diagram hubungan fonologi–fonetik–fonemik
- Bab 2: Diagram alat ucap manusia, alur produksi bunyi
- Bab 3: Bagan vokal (trapesium vokal), tabel konsonan IPA
- Bab 4: Ilustrasi kontur intonasi
- Bab 5: Diagram hubungan tanda baca–jeda–intonasi
- Bab 6: Diagram struktur silabel (onset–nukleus–koda)
- Bab 7: Peta variasi dialektal

---

## 5. Em Dash — ❌ Ditemukan di Bab 4

Aturan: **Jangan menggunakan em dash.** Gunakan tanda baca atau susunan kalimat lain.

Lokasi: `04-segmental-dan-suprasegmental.md` baris 11:
> `Kedua jenis bunyi ini—segmental dan suprasegmental—berperan penting`

**Tindakan:** Ganti em dash dengan koma, tanda kurung, atau kalimat terpisah.

---

## 6. Label "Fakta Terkait" — ❌ Harus "Trivia" (Bab 1, 2)

Aturan: Gunakan label `**Trivia**`, bukan "Trivia Bersumber" atau variasi lain. Label "Fakta Terkait" bukan label standar yang diatur.

| Bab | Label saat ini | Seharusnya |
|-----|----------------|------------|
| 1 | `## Fakta Terkait` | `## Trivia` |
| 2 | `## Fakta Terkait` | `## Trivia` |

Bab 3–7 tidak memiliki elemen pembaca sama sekali (Trivia, Contoh Nyata, Coba Perhatikan, Kesalahan Umum, Latihan Singkat).

Bab 4 memiliki `## Sekilas Mengenai Bunyi Suprasegmental` yang fungsinya mirip Trivia tapi labelnya tidak standar.

Bab 5 memiliki `## Jadi, Apa Pentingnya Pungtuasi?` yang fungsinya mirip rangkuman informal tapi bukan bagian standar.

**Tindakan:**
- Ganti label "Fakta Terkait" → "Trivia" di Bab 1 dan 2.
- Standarkan "Sekilas Mengenai..." di Bab 4 → label yang sesuai atau integrasikan ke narasi.
- Standarkan "Jadi, Apa Pentingnya..." di Bab 5 → integrasikan ke Rangkuman atau bagian standar lain.
- Tambahkan minimal 1–2 elemen pembaca (Trivia, Contoh Nyata, dsb.) di Bab 3–7.

---

## 7. Sudut Pandang — ⚠️ Inkonsisten

Aturan: Utamakan sudut pandang **pembelajar** (*kita*, *pembaca*, *pembelajar*). Hindari suara dosen. Jangan seolah-olah pembaca pasti mahasiswa/dosen.

Temuan:
- Bab 1: "Bab ini akan mengajak **Anda** memahami..." — penggunaan "Anda"
- Bab 2: "perlu **Anda** ketahui" — penggunaan "Anda"
- Bab 1 §1.5 poin 2: "Apakah **kamu** pernah mendengarkan..." — terlalu informal, inkonsisten
- Sebagian besar narasi sudah menggunakan "kita" (baik)

**Tindakan:** Ganti "Anda" → "kita" atau "pembaca", ganti "kamu" → "kita". Pertahankan konsistensi di semua bab.

---

## 8. Istilah Asing Pertama Kali — ⚠️ Tidak Konsisten

Aturan: Istilah asing/teknis pertama kali muncul harus **cetak miring** lalu beri arti/padanan bahasa Indonesia.

Temuan positif: Beberapa sudah dilakukan, misalnya *International Phonetic Alphabet* di Bab 1, *parole* di Bab 1.

Temuan negatif:
- Bab 1: "Fonetik Artikulatoris", "Fonetik Akustik", "Fonetik Auditori" — tidak cetak miring
- Bab 2: "Onset", "koda", "nuklus" di Bab 6 — tidak konsisten cetak miring
- Bab 4: *Stress*, *Tone* — sudah cetak miring, tapi "Durasi" tidak
- Bab 7: *Sound Shift* — sudah cetak miring (baik)

**Tindakan:** Audit istilah asing per bab dan pastikan format cetak miring + padanan pada kemunculan pertama.

---

## 9. Sitasi dan Rujukan — ⚠️ Minim dan Belum Diverifikasi

Aturan: Klaim akademik harus didukung sitasi memadai. Jangan membangun pembahasan penting hanya dari opini naratif.

Temuan:
- Bab 1: Ladefoged (2006) disebut, Harris (1951), Sapir (2004) — belum diverifikasi
- Bab 2: Zaman (2018) pada tabel asimilasi — belum diverifikasi
- Bab 6: Zainuddin (1994), Chaer (1994) — belum diverifikasi
- **Bab 3, 4, 5**: Tidak ada sitasi sama sekali dalam uraian
- Bab 7: Labov disebut tanpa tahun/publikasi spesifik; Bernstein, Sapir-Whorf disebut tanpa sitasi

**Tindakan:** Minimal setiap bab perlu 2–3 sitasi untuk klaim utama. Verifikasi semua entri yang ada terhadap sumber asli.

---

## 10. Trivia Faktual Tanpa Sumber — ❌ Bab 1, 2

Aturan: Trivia/fakta unik yang memuat klaim faktual harus memiliki sumber jelas.

| Bab | Trivia | Sumber |
|-----|--------|--------|
| 1 | Elipsis fonem /k/ akhir kata | Tidak ada sumber |
| 2 | Glottal stop /ʔ/ akhir kata | Tidak ada sumber |

**Tindakan:** Tambahkan sitasi untuk setiap klaim faktual dalam Trivia.

---

## 11. Gaya Narasi — ⚠️ Berulang dan Formulaik

Aturan: Variasikan panjang kalimat dan paragraf. Jangan membuat setiap paragraf berpola sama.

Temuan:
- Bab 2 §2.1: Subheading bernomor 1–4 dengan blockquote identik — terasa formulaik
- Bab 3: Setiap subbab dimulai dengan definisi → contoh → penjelasan, pola identik
- Bab 7: Pengulangan signifikan antara §7.2 dan §7.3 (keduanya membahas variasi dialek, elisi, asimilasi)
- Bab 5 §5.1–5.3: Format daftar bernomor yang monoton

**Tindakan:** Saat revisi, variasikan pembukaan paragraf, selingi narasi dengan tabel/contoh kasus/latihan singkat, hindari blockquote berlebihan.

---

## 12. Pengantar Bab 7 — ❌ KOSONG

Semua bab lain (1–6) sudah punya pengantar meskipun singkat. Bab 7 masih `<!-- TODO -->`.

**Tindakan:** Tulis pengantar yang menghubungkan Bab 6 (Silabel) dengan topik variasi fonem dan meneruskan ke Bab 8 (Realisasi Fonem).

---

## 13. Isu Konten Spesifik (dari catatan-editorial.md + temuan baru)

| Bab | Isu | Prioritas |
|-----|-----|-----------|
| 1 | "Bita" sebagai pasangan minimal "Pita" — perlu verifikasi leksikal | Tinggi |
| 1 | "patu" bukan kata bahasa Indonesia — contoh pasangan minimal cacat | Tinggi |
| 1 | Tabel 1.2 alofon aspirasi/pranasalisasi tidak dijelaskan distribusinya | Sedang |
| 2 | Tabel 2.1: θ bukan fonem bahasa Indonesia, tapi ada di tabel | Sedang |
| 3–4 | "JAlan"/"jaLAN" sebagai pembeda kelas kata — klaim tidak standar | Tinggi |
| 4 | Duplikasi topik dengan Bab 3 §3.3 (bunyi suprasegmental) | Sedang |
| 5 | Batas konsep pungtuasi vs prosodi vs tanda baca kurang tegas | Sedang |
| 6 | Contoh "arts" dan "korps" — kata Indonesia atau bukan? | Sedang |
| 6 | Penjelasan silabel ≈ copy-paste dengan parafrase minimal antarparagraf | Tinggi |
| 7 | Banyak contoh dari bahasa Inggris — terlalu dominan untuk buku BI | Sedang |
| 7 | Klaim dialek (Minang, Betawi, Jawa, dsb.) tanpa sumber | Tinggi |

---

## Matriks Ringkasan per Bab

| Komponen | B1 | B2 | B3 | B4 | B5 | B6 | B7 |
|----------|----|----|----|----|----|----|-----|
| Tujuan Pembelajaran | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| Pengantar Bab | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ |
| Rangkuman | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| 🧠 Istilah | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| Visual/Gambar | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| Trivia/elemen pembaca | ⚠️ | ⚠️ | ❌ | ⚠️ | ⚠️ | ❌ | ❌ |
| Sitasi memadai | ⚠️ | ⚠️ | ❌ | ❌ | ❌ | ⚠️ | ⚠️ |
| Sudut pandang konsisten | ⚠️ | ⚠️ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Istilah asing berformat | ⚠️ | ⚠️ | ⚠️ | ⚠️ | ⚠️ | ⚠️ | ⚠️ |
| Tanpa em dash | ✅ | ✅ | ✅ | ❌ | ✅ | ✅ | ✅ |
| Latihan Akhir Bab | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |

Keterangan: ✅ = sesuai, ⚠️ = sebagian/perlu perbaikan, ❌ = tidak ada/melanggar

---

## Prioritas Kerja yang Disarankan

**Gelombang 1 — Perbaikan struktural cepat (semua bab):**
1. Tulis Tujuan Pembelajaran
2. Tulis Rangkuman
3. Tulis 🧠 Istilah
4. Tambah penanda tempat visual (Gambar B.U)
5. Perbaiki label "Fakta Terkait" → "Trivia"
6. Hapus em dash di Bab 4
7. Tulis Pengantar Bab 7

**Gelombang 2 — Perbaikan narasi dan konten (per bab):**
1. Konsistensi sudut pandang (Anda/kamu → kita)
2. Format istilah asing
3. Tambah elemen pembaca (Trivia, Contoh Nyata, dsb.)
4. Perbaiki contoh bermasalah (pasangan minimal Bab 1, "JAlan" Bab 3–4, dll.)
5. Variasikan gaya narasi

**Gelombang 3 — Verifikasi dan pendalaman:**
1. Verifikasi semua sitasi
2. Tambah sitasi untuk bab yang minim
3. Periksa klaim dialektal
4. Tinjau tumpang tindih antar-bab (Bab 3–4, Bab 7 §7.2 vs §7.3)
