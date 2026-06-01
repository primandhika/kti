# Data Templates — Metacognitive Awareness Habituation Study

## Daftar File

| File | Isi | Sumber Data |
|------|-----|-------------|
| `01_rubric_scores.csv` | Skor rubrik metakognitif per siswa (pre & post) | Observation rubric |
| `02_session_behaviors.csv` | Perilaku metakognitif per siswa per sesi | Observasi kelas |
| `03_student_reflections.csv` | Teks refleksi siswa + koding tema | Written reflections |
| `04_teacher_notes.csv` | Catatan observasi guru per sesi | Teacher observation |

---

## Panduan Pengisian

### 01_rubric_scores.csv
- **N = 28 siswa** (S01–S28), sudah tersedia semua baris.
- Kolom skor: `planning_pre`, `monitoring_pre`, `reflection_pre`, `overall_pre` (dan `_post`).
- **Skala: 1–3** (1 = not observed, 2 = sometimes observed, 3 = consistently observed).
- `overall_*` bisa diisi manual atau dikosongkan (nanti dihitung rata-rata dari 3 indikator).
- → Menghasilkan **Table 2** di artikel.

### 02_session_behaviors.csv
- Per siswa × per sesi (8 sesi). Template contoh hanya S01–S05; **tambahkan S06–S28** dengan pola yang sama.
- Kolom: `pausing_to_plan`, `self_correction`, `voluntary_reflection`.
- **Isi: 1 (ya/teramati) atau 0 (tidak).**
- → Menghasilkan **Table 3** di artikel (persentase early vs final sessions).
- "Early sessions" = sesi 1–2, "Final sessions" = sesi 7–8 (bisa disesuaikan).

### 03_student_reflections.csv
- Satu baris per refleksi yang dikumpulkan. Template contoh refleksi di sesi 2, 4, 6, 8 — sesuaikan frekuensi sebenarnya.
- `reflection_text`: kutipan/teks refleksi siswa.
- `theme_code`: kode tema (PL = planning, MN = monitoring, RF = reflection).
- `theme_label`: label lengkap (planning_awareness, monitoring_awareness, reflective_evaluation).
- → Menghasilkan **Table 4** di artikel.

### 04_teacher_notes.csv
- Satu baris per sesi (8 sesi).
- `date`: tanggal pelaksanaan (format: YYYY-MM-DD).
- `observation_note`: catatan naratif guru.
- `metacognitive_focus`: fokus utama sesi (sudah diisi default, sesuaikan jika berbeda).
- `notable_students`: ID siswa yang menonjol di sesi itu (misal: S03, S15).

---

## Catatan
- Semua file UTF-8, separator koma.
- Jangan ubah nama kolom (header row).
- Boleh tambah kolom baru jika ada data tambahan, tapi jangan hapus kolom yang ada.
- Setelah diisi, bilang saja dan saya bantu analisis + update tabel di artikel.
