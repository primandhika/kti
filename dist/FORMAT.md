# Spesifikasi Format Disertasi

Dokumen ini adalah kontrak format untuk compiler. Acuan pengukuran:
`main/_origins/original-dist.pdf` (191 halaman, keluaran Microsoft Word).

Jika PDF sumber tidak konsisten, aturan final memakai pola yang dominan.
Pengecualian eksplisit: seluruh heading bab ditetapkan 14 pt.

## 1. Token global

```yaml
page:
  size: A4
  width: 595.28pt
  height: 841.89pt
  margin_top: 4cm
  margin_right: 2cm
  margin_bottom: 3cm
  margin_left: 4cm

font:
  family: "Times New Roman"
  fallback: ["Liberation Serif", serif]
  body_size: 12pt
  color: "#000000"

paragraph:
  alignment: justify
  line_spacing: double
  measured_baseline_step: 27.5pt
  first_line_indent: 0.5in
  space_before: 0pt
  space_after: 0pt
  widow_lines: 2
  orphan_lines: 2
```

`line_spacing: double` adalah aturan utama. Nilai 27,5 pt adalah hasil ukur
keluaran Word untuk Times New Roman 12 pt; gunakan hanya jika backend meminta
tinggi baris absolut.

Dengan margin canonical di atas, area isi berukuran sekitar 425,2 pt × 643,5 pt.
Tepi kiri teks berada pada x = 113,4 pt dan tepi kanan pada x = 538,6 pt.

## 2. Hierarki heading

| Level | Contoh | Font | Bentuk | Perataan dan indent | Jarak |
|---|---|---|---|---|---|
| Bab (`h1`) | `BAB II` + `KAJIAN TEORI` | TNR 14 pt bold | Kapital, dua baris | Tengah; halaman baru | Antarbaris 1,5; sesudah blok 27,5 pt |
| Subbab (`h2`) | `A. Deskripsi Teori` | TNR 12 pt bold | Title case | Kiri; indent 0 | Sebelum 12 pt; sesudah 0 pt |
| Sub-subbab (`h3`) | `1. Keterampilan Berbicara` | TNR 12 pt bold | Title case | Kiri; indent 0,25 in | Sebelum 12 pt; sesudah 0 pt |
| Tingkat 4 (`h4`) | `a. Bagi Mahasiswa` | TNR 12 pt bold | Sentence/title case | Hanging indent; label 0,5 in, teks 0,75 in | 0 pt |
| Tingkat 5 (`h5`) | Butir tanpa judul mandiri | TNR 12 pt regular | Sentence case | Hanging indent bertambah 0,25 in | Mengikuti isi |

Heading bab harus dikompilasi sebagai satu elemen:

```yaml
chapter_heading:
  break_before: page
  keep_with_next: true
  line_1: "BAB <ROMAN>"
  line_2: "<JUDUL BAB>"
  font_family: "Times New Roman"
  font_size: 14pt
  font_weight: bold
  text_transform: uppercase
  text_align: center
  line_height: 1.5
```

Nomor dan judul bab tidak boleh memiliki ukuran atau weight berbeda. Heading
tidak boleh menjadi baris terakhir halaman; pertahankan minimal dua baris isi
bersama heading.

## 3. Paragraf dan daftar

- Isi utama: Times New Roman 12 pt, regular, rata kiri-kanan, spasi ganda.
- Baris pertama menjorok 0,5 in. Jangan membuat indent dengan spasi atau tab.
- Paragraf pertama setelah heading tetap memakai indent 0,5 in.
- Tidak ada spasi tambahan antarapagraf; pemisah berasal dari spasi ganda dan
  indent baris pertama.
- Istilah asing memakai italic, bukan font lain.
- Bold dalam paragraf hanya untuk label atau judul lokal.
- Daftar memakai hanging indent. Teks dimulai 0,25 in setelah posisi label.
- Jarak baris dalam dan antarbutir tetap ganda.

## 4. Tabel, gambar, dan persamaan

### Tabel

- Caption di atas tabel, TNR 12 pt, rata tengah.
- Label `Tabel <bab>.<nomor>` bold; teks judul regular.
- Isi tabel TNR 12 pt, spasi tunggal. Boleh turun hingga 10 pt hanya jika tabel
  tetap tidak muat setelah lebar kolom dioptimalkan.
- Header kolom bold dan rata tengah. Teks rata kiri; angka rata kanan/desimal.
- Tabel tidak boleh melewati area isi. Ulangi header pada halaman lanjutan.

### Gambar

- Gambar rata tengah dan maksimal selebar area isi.
- Caption di bawah gambar, TNR 12 pt, rata tengah.
- Label `Gambar <bab>.<nomor>` bold; teks judul regular.
- Pertahankan gambar dan caption pada halaman yang sama bila memungkinkan.

### Persamaan

- Persamaan rata tengah; nomor persamaan rata kanan.
- Simbol matematika boleh memakai Cambria Math. Teks tetap TNR 12 pt.

## 5. Nomor halaman dan section break

```yaml
pagination:
  front_matter:
    style: lower-roman
    position: bottom-center
  body:
    style: decimal
    default_position: top-right
    chapter_opening_position: bottom-center
  print_number_on_cover: false
```

- Setiap bab dimulai pada halaman baru.
- Bagian awal memakai Romawi kecil; isi utama dimulai lagi dari angka 1.
- Nomor halaman TNR 12 pt tanpa dekorasi.
- Nomor kanan-atas berjarak sekitar 1,27 cm dari tepi atas; nomor bawah-tengah
  berjarak sekitar 1,27 cm dari tepi bawah.

## 6. Daftar pustaka

```yaml
bibliography:
  heading: "DAFTAR PUSTAKA"
  heading_font: "Times New Roman Bold"
  heading_size: 12pt
  heading_alignment: center
  entry_font_size: 12pt
  entry_line_height: 15pt
  hanging_indent: 24pt
  space_after: 8pt
  alignment: left
```

Entri daftar pustaka memakai hanging indent sekitar 24 pt (0,85 cm), spasi
tunggal terukur sekitar 15 pt, dan jarak antarentri sekitar 8 pt.

## 7. Bagian awal dan lampiran

- Judul bagian awal (`KATA PENGANTAR`, `DAFTAR ISI`, dan sejenisnya): TNR 12 pt
  bold, kapital, rata tengah.
- Daftar isi: TNR 12 pt; judul bab bold; leader titik; nomor halaman rata kanan.
- Halaman pemisah lampiran: `LAMPIRAN <HURUF>` TNR 24 pt bold, kapital, rata
  tengah. Subjudul lampiran TNR 12 pt bold.
- Instrumen/formulir dan tabel lampiran boleh memakai spasi tunggal. Prosa
  naratif tetap TNR 12 pt dan spasi ganda.

## 8. Resolusi inkonsistensi sumber

Compiler hanya menghasilkan satu aturan final per elemen:

1. Heading bab selalu 14 pt bold, termasuk baris `BAB ...` dan judulnya.
2. Subbab dan level di bawahnya memakai 12 pt bold.
3. Varian bold italic pada beberapa heading dinormalisasi menjadi bold biasa.
4. Indent baris pertama dinormalisasi menjadi 0,5 in (36 pt).
5. Spasi paragraf tambahan yang muncul sporadis dihapus (`space_after: 0pt`).
6. Nomor halaman pembuka bab selalu bawah-tengah.

## 9. Pemetaan elemen ke compiler

| Pola sumber | Elemen compiler |
|---|---|
| Metadata bab atau `# BAB I — PENDAHULUAN` | `chapter` / `h1` dua baris |
| `## A. Latar Belakang` | `h2` |
| `### 1. Keterampilan Berbicara` | `h3` |
| `#### a. Bagi Mahasiswa` | `h4` |
| Paragraf biasa | `p` |
| Daftar `1.`, `2.` | `ol` dengan hanging indent |
| `Tabel X.Y ...` | `table` + caption di atas |
| `Gambar X.Y ...` | `figure` + caption di bawah |

Nomor bab, subbab, tabel, dan gambar harus dibentuk dari struktur dokumen,
bukan dipercaya dari teks manual. Validasi gagal jika level heading meloncat,
nomor bab tidak berurutan, atau caption tidak memiliki nomor bab.

## 10. Toleransi regression test

```yaml
render_tolerance:
  font_size: 0.25pt
  baseline_position: 1pt
  margin_position: 2pt
  line_step: 1pt
  page_count: informational
```

Jumlah halaman bukan assertion utama karena perubahan teks dapat menggeser
paginasi. Assertion utama: font, hierarki, margin, baseline, dan section break.
