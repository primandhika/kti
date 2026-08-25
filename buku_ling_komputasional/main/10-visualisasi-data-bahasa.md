# Bab 10. Visualisasi Data Bahasa

## Tujuan Pembelajaran
Setelah mempelajari bab ini, pembaca diharapkan mampu:

1. menjelaskan mengapa visualisasi penting dalam analisis data bahasa;
2. membuat tabel frekuensi kata secara terformat;
3. membuat diagram batang sederhana untuk menampilkan frekuensi kata;
4. membuat *word cloud* (*awan kata*) dan menafsirkannya secara kritis; dan
5. menyajikan hasil analisis bahasa secara visual untuk kebutuhan kelas atau laporan.

## Pengantar Bab
Pada bab-bab sebelumnya, kita telah mengolah teks dengan berbagai cara: menghitung frekuensi kata, mengamati konteks, menganalisis morfologi, dan mengukur panjang kalimat. Semua hasil itu tersaji dalam bentuk angka dan daftar teks. Angka dan daftar tentu berguna, tetapi kadang belum cukup untuk menyampaikan temuan secara cepat dan jelas, terutama jika hasilnya ingin dibagikan kepada orang lain, ditampilkan di kelas, atau dimasukkan ke dalam laporan.

Di sinilah visualisasi berperan. Visualisasi mengubah angka menjadi bentuk yang lebih mudah ditangkap mata, seperti diagram batang, tabel terformat, atau awan kata. Dengan visualisasi, pola yang tersembunyi di balik angka bisa langsung terlihat. Kata mana yang paling dominan? Bagaimana perbandingan dua teks? Kalimat mana yang paling panjang? Semua itu lebih cepat dipahami ketika disajikan secara visual.

Bab ini memperkenalkan beberapa teknik visualisasi sederhana yang dapat dilakukan dengan *Python* di *Google Colab* atau *Jupyter Notebook*. Fokusnya bukan pada keindahan desain, melainkan pada kejelasan penyampaian hasil analisis bahasa.

[Ilustrasi diagram batang sederhana yang menampilkan frekuensi beberapa kata, di sampingnya awan kata dari teks yang sama. Visual menekankan bahwa angka dan gambar saling melengkapi.]
Gambar 10.1 Angka dan Visual Saling Melengkapi

## 10.1 Mengapa Visualisasi Penting?
Visualisasi bukan sekadar hiasan. Dalam analisis data, visualisasi memiliki beberapa fungsi penting.

### 10.1.1 Membantu melihat pola dengan cepat
Bayangkan pembaca memiliki daftar 50 kata beserta frekuensinya. Membaca daftar itu satu per satu tentu bisa dilakukan, tetapi melihat diagram batang yang langsung menunjukkan kata mana yang paling tinggi jauh lebih cepat dan efisien.

### 10.1.2 Membantu membandingkan
Ketika dua teks dibandingkan, perbandingan angka mentah bisa membingungkan. Namun, jika kedua frekuensi ditampilkan dalam diagram yang berdampingan, perbedaannya langsung terlihat.

### 10.1.3 Membantu menyampaikan temuan
Dalam konteks kelas, laporan tugas, atau presentasi, visualisasi membantu pembaca menyampaikan hasil analisis secara lebih menarik dan lebih mudah dipahami oleh orang lain.

### Coba Perhatikan
Visualisasi yang baik bukan yang paling ramai, melainkan yang paling jelas. Satu diagram sederhana yang tepat sasaran sering lebih berguna daripada lima grafik mewah yang justru membingungkan.

## 10.2 Tabel dan Diagram Frekuensi Kata
Langkah pertama dalam visualisasi data bahasa biasanya adalah menampilkan frekuensi kata, baik dalam bentuk tabel maupun diagram.

### 10.2.1 Membuat tabel frekuensi terformat
Sebelum membuat grafik, kadang kita perlu menampilkan tabel yang rapi di layar.

```python
from collections import Counter

teks = """Pembelajaran bahasa membutuhkan data yang nyata. Data bahasa dapat diperoleh
dari bacaan, tulisan, atau percakapan. Analisis data membantu pembaca memahami
pola kosakata. Kosakata yang sering muncul memberi petunjuk tentang topik utama.
Bahasa yang baik membantu pembaca memahami gagasan dengan lebih jernih."""

stopwords = {"yang", "dan", "di", "ke", "dari", "atau", "dapat", "dengan", "lebih", "membantu"}
bersih = teks.lower().replace("\n", " ").replace(".", "")
kata_list = [k for k in bersih.split() if k not in stopwords]
frek = Counter(kata_list)
top7 = frek.most_common(7)

print(f"{'Kata':<15} {'Frekuensi':>9}")
print("-" * 25)
for kata, jumlah in top7:
    print(f"{kata:<15} {jumlah:>9}")
```

**Output**
```text
Kata             Frekuensi
-------------------------
bahasa                  3
data                    3
pembaca                 2
memahami                2
kosakata                2
pembelajaran            1
membutuhkan             1
```

**Penjelasan**
Kode ini menghitung frekuensi kata setelah *stopwords* disaring, lalu menampilkan hasilnya dalam tabel sederhana. Format `{kata:<15}` berarti teks rata kiri dengan lebar 15 karakter, sedangkan `{jumlah:>9}` berarti angka rata kanan dengan lebar 9 karakter. Hasilnya rapi dan mudah dibaca.

### 10.2.2 Membuat diagram batang horizontal dengan matplotlib
Untuk membuat grafik, kita memakai pustaka *matplotlib*. Pustaka ini sudah tersedia secara bawaan di *Google Colab*. Jika pembaca menggunakan *Jupyter Notebook* lokal dan *matplotlib* belum terpasang, jalankan `pip install matplotlib` terlebih dahulu.

```python
import matplotlib.pyplot as plt
from collections import Counter

teks = """Pembelajaran bahasa membutuhkan data yang nyata. Data bahasa dapat diperoleh
dari bacaan, tulisan, atau percakapan. Analisis data membantu pembaca memahami
pola kosakata. Kosakata yang sering muncul memberi petunjuk tentang topik utama.
Bahasa yang baik membantu pembaca memahami gagasan dengan lebih jernih."""

stopwords = {"yang", "dan", "di", "ke", "dari", "atau", "dapat", "dengan", "lebih", "membantu"}
bersih = teks.lower().replace("\n", " ").replace(".", "")
kata_list = [k for k in bersih.split() if k not in stopwords]
frek = Counter(kata_list)
top7 = frek.most_common(7)

kata_label = [item[0] for item in reversed(top7)]
jumlah_label = [item[1] for item in reversed(top7)]

plt.figure(figsize=(8, 4))
plt.barh(kata_label, jumlah_label, color="steelblue")
plt.xlabel("Frekuensi")
plt.title("7 Kata Paling Sering (Tanpa Stopwords)")
plt.tight_layout()
plt.show()
```

**Output**
Diagram batang horizontal muncul di layar. Kata dengan frekuensi tertinggi (`bahasa` dan `data`, masing-masing 3) tampil dengan batang paling panjang di bagian atas. Kata dengan frekuensi lebih rendah tampil di bawahnya.

**Penjelasan**
Fungsi `plt.barh()` membuat diagram batang horizontal. Daftar kata dan frekuensinya dibalik urutannya (`reversed`) agar kata dengan frekuensi tertinggi tampil di atas. Parameter `color="steelblue"` memberi warna biru tua pada batang. Fungsi `plt.show()` menampilkan grafik di *notebook*.

### 10.2.3 Menyimpan grafik sebagai file gambar
Jika pembaca ingin menyimpan grafik untuk laporan atau presentasi, cukup tambahkan satu baris sebelum `plt.show()`.

```python
plt.savefig("frekuensi_kata.png", dpi=150)
```

**Output**
```text
File frekuensi_kata.png tersimpan di folder kerja.
```

**Penjelasan**
Fungsi `plt.savefig()` menyimpan grafik ke file gambar. Parameter `dpi=150` mengatur ketajaman gambar. File ini dapat diunduh dari *Google Colab* atau langsung dipakai dari folder lokal jika pembaca menggunakan *Jupyter Notebook*.

### Kesalahan Umum
Pemula kadang memanggil `plt.show()` sebelum `plt.savefig()`. Akibatnya, file yang tersimpan bisa kosong karena grafik sudah "ditampilkan" dan dianggap selesai. Pastikan `plt.savefig()` dipanggil sebelum `plt.show()`.

## 10.3 Word Cloud dan Interpretasinya
*Word cloud* (*awan kata*) adalah visualisasi yang menampilkan kata-kata dengan ukuran sebanding frekuensinya. Kata yang lebih sering muncul ditampilkan lebih besar. Tampilan ini sering dipakai karena menarik secara visual, tetapi perlu ditafsirkan dengan hati-hati.

### 10.3.1 Memasang dan menggunakan pustaka wordcloud
Pustaka *wordcloud* perlu dipasang terlebih dahulu.

```python
!pip install wordcloud
```

**Output**
```text
Successfully installed wordcloud-1.9.6
```

**Penjelasan**
Perintah ini mengunduh dan memasang pustaka *wordcloud* dari *PyPI*. Setelah terpasang, pustaka siap dipakai di *notebook* yang sedang aktif. Di *Google Colab*, pemasangan ini perlu dilakukan setiap kali sesi baru dimulai.

### 10.3.2 Membuat word cloud dari teks

```python
from wordcloud import WordCloud
import matplotlib.pyplot as plt

teks = """bahasa indonesia pembelajaran data analisis teks kosakata
bahasa membaca menulis bahasa data kelas bahasa puisi bahasa
pembelajaran kosakata analisis bahasa data teks membaca bahasa"""

wc = WordCloud(width=600, height=300, background_color="white").generate(teks)

plt.figure(figsize=(8, 4))
plt.imshow(wc, interpolation="bilinear")
plt.axis("off")
plt.title("Word Cloud dari Teks Pendek")
plt.tight_layout()
plt.show()
```

**Output**
Gambar awan kata muncul di layar. Kata `bahasa` tampil paling besar karena frekuensinya paling tinggi. Kata lain seperti `data`, `pembelajaran`, `analisis`, dan `kosakata` tampil lebih kecil sesuai frekuensinya.

**Penjelasan**
Objek `WordCloud()` menerima teks mentah dan menghitung frekuensi kata secara otomatis. Parameter `width` dan `height` mengatur ukuran gambar. `background_color="white"` memberi latar putih agar lebih mudah dibaca. Fungsi `plt.imshow()` menampilkan gambar, dan `plt.axis("off")` menyembunyikan sumbu agar tampilan lebih bersih.

### 10.3.3 Menafsirkan word cloud dengan kritis
*Word cloud* memang menarik, tetapi pembaca perlu berhati-hati dalam menafsirkannya.

- Ukuran kata hanya mencerminkan frekuensi. Kata yang besar belum tentu kata yang paling penting secara makna.
- Posisi kata dalam *word cloud* bersifat acak dan tidak membawa informasi.
- Kata-kata umum yang belum disaring bisa mendominasi tampilan dan menutupi kata-kata yang lebih bermakna.

Karena itu, *word cloud* sebaiknya diperlakukan sebagai alat eksplorasi awal, bukan sebagai kesimpulan akhir. Jika pembaca ingin analisis yang lebih terukur, tabel frekuensi atau diagram batang biasanya lebih informatif.

### Coba Perhatikan
Sebelum membuat *word cloud*, sebaiknya *stopwords* sudah disaring terlebih dahulu. Tanpa penyaringan, kata seperti `yang`, `dan`, atau `di` akan mendominasi tampilan dan mengaburkan kata-kata isi yang sebenarnya lebih menarik.

## 10.4 Visualisasi Sederhana di Python dan Colab
Setelah memahami tabel, diagram batang, dan *word cloud*, pembaca dapat mulai menggabungkan teknik-teknik ini untuk kebutuhan yang lebih kontekstual.

### 10.4.1 Membandingkan frekuensi kata pada dua teks
Salah satu visualisasi yang sangat berguna adalah membandingkan dua teks secara berdampingan.

```python
import matplotlib.pyplot as plt
from collections import Counter

teks_cerpen = "malam hujan sunyi perlahan malam gelap hujan turun malam sunyi"
teks_artikel = "data analisis bahasa pembelajaran data analisis metode data bahasa"

frek_c = Counter(teks_cerpen.split()).most_common(5)
frek_a = Counter(teks_artikel.split()).most_common(5)

fig, (ax1, ax2) = plt.subplots(1, 2, figsize=(12, 4))

kata_c = [x[0] for x in reversed(frek_c)]
jml_c = [x[1] for x in reversed(frek_c)]
ax1.barh(kata_c, jml_c, color="salmon")
ax1.set_title("Cerpen")
ax1.set_xlabel("Frekuensi")

kata_a = [x[0] for x in reversed(frek_a)]
jml_a = [x[1] for x in reversed(frek_a)]
ax2.barh(kata_a, jml_a, color="steelblue")
ax2.set_title("Artikel")
ax2.set_xlabel("Frekuensi")

plt.tight_layout()
plt.show()
```

**Output**
Dua diagram batang muncul berdampingan. Diagram kiri menampilkan kata dominan dari teks cerpen (`malam`, `hujan`, `sunyi`). Diagram kanan menampilkan kata dominan dari teks artikel (`data`, `analisis`, `bahasa`). Perbedaan kosakata kedua teks langsung terlihat.

**Penjelasan**
Fungsi `plt.subplots(1, 2)` membuat dua area grafik dalam satu baris. Masing-masing area (`ax1` dan `ax2`) diisi dengan diagram batang dari teks yang berbeda. Warna yang berbeda membantu membedakan kedua teks secara visual.

### 10.4.2 Membuat diagram batang distribusi panjang kalimat
Pada Bab 9, kita sudah menghitung panjang kalimat. Sekarang hasilnya dapat divisualisasikan.

```python
import matplotlib.pyplot as plt

kalimat_label = ["K1", "K2", "K3", "K4", "K5", "K6"]
panjang = [3, 5, 8, 2, 2, 15]

warna = []
for p in panjang:
    if p <= 4:
        warna.append("lightgreen")
    elif p <= 8:
        warna.append("gold")
    else:
        warna.append("salmon")

plt.figure(figsize=(8, 4))
plt.bar(kalimat_label, panjang, color=warna)
plt.xlabel("Kalimat")
plt.ylabel("Jumlah Kata")
plt.title("Distribusi Panjang Kalimat")
plt.tight_layout()
plt.show()
```

**Output**
Diagram batang vertikal muncul di layar. Setiap batang mewakili satu kalimat. Warna hijau muda menandai kalimat pendek (1 sampai 4 kata), kuning menandai kalimat sedang (5 sampai 8 kata), dan merah muda menandai kalimat panjang (9 kata atau lebih). Kalimat ke-6 langsung menonjol karena batangnya paling tinggi dan berwarna berbeda.

**Penjelasan**
Dengan memberi warna berbeda berdasarkan panjang, pembaca langsung melihat komposisi kalimat dalam satu teks. Teknik ini berguna untuk memeriksa variasi panjang kalimat dalam bahan ajar atau tulisan yang sedang dianalisis.

### 10.4.3 Menampilkan tabel perbandingan sederhana di layar
Kadang pembaca hanya perlu tabel terformat tanpa grafik, misalnya untuk membandingkan dua profil teks.

```python
label = ["Cerpen", "Artikel"]
kata_per_kalimat = [3.8, 16.0]
panjang_kata = [4.6, 7.8]

print(f"{'Teks':<12} {'Kata/Kalimat':>13} {'Panjang Kata':>13}")
print("-" * 40)
for i in range(len(label)):
    print(f"{label[i]:<12} {kata_per_kalimat[i]:>13.1f} {panjang_kata[i]:>13.1f}")
```

**Output**
```text
Teks          Kata/Kalimat  Panjang Kata
----------------------------------------
Cerpen                 3.8           4.6
Artikel               16.0           7.8
```

**Penjelasan**
Tabel ini menampilkan perbandingan dua indikator keterbacaan tanpa grafik. Kadang cara ini lebih cocok ketika hasilnya hanya sedikit dan tidak memerlukan diagram.

## 10.5 Menyajikan Hasil Analisis untuk Kelas
Setelah pembaca dapat membuat tabel dan grafik, pertanyaan terakhir adalah: bagaimana menyajikan semua itu dengan baik?

### 10.5.1 Prinsip penyajian yang baik
Beberapa prinsip sederhana yang dapat membantu:

- **Beri judul yang jelas pada setiap grafik.** Pembaca harus langsung tahu apa yang sedang ditampilkan.
- **Beri label pada sumbu.** Tanpa label, angka di sumbu tidak bermakna.
- **Gunakan warna secara bermakna.** Warna sebaiknya membantu membedakan kategori, bukan sekadar menghias.
- **Jangan terlalu ramai.** Satu grafik yang fokus lebih baik daripada satu grafik yang mencoba menampilkan segalanya.
- **Selalu sertakan penjelasan.** Grafik tanpa penjelasan bisa ditafsirkan secara keliru.

### 10.5.2 Urutan penyajian yang disarankan
Untuk laporan atau presentasi kelas, urutan berikut biasanya bekerja dengan baik:

1. Jelaskan teks yang dianalisis (sumber, panjang, jenis teks).
2. Tampilkan tabel frekuensi kata sebagai data dasar.
3. Tampilkan diagram batang untuk menyoroti kata atau pola yang menonjol.
4. Jika relevan, tampilkan *word cloud* sebagai gambaran umum.
5. Tutup dengan pengamatan atau pertanyaan diskusi.

### 10.5.3 Menyimpan semua hasil ke satu notebook
Salah satu kelebihan *Google Colab* dan *Jupyter Notebook* adalah semua kode, hasil, dan grafik dapat disimpan dalam satu *notebook*. Pembaca tidak perlu membuat dokumen terpisah untuk setiap langkah. Cukup jalankan semua sel secara berurutan, lalu *notebook* itu sendiri sudah menjadi laporan yang lengkap.

### Contoh Nyata
Jika pembaca diminta mempresentasikan hasil analisis satu bacaan, pembaca bisa menampilkan: tabel 10 kata paling sering, diagram batang frekuensi, dan satu *word cloud*. Lalu tutup dengan satu paragraf pengamatan tentang apa yang menarik dari data itu. Cara ini sudah cukup untuk presentasi kelas yang solid.

### Kesalahan Umum
Pemula kadang terlalu fokus pada tampilan visual sehingga lupa memberi konteks. Grafik yang indah tetapi tanpa penjelasan tentang dari mana datanya dan apa artinya justru bisa menyesatkan. Ingat: visualisasi adalah alat bantu, bukan pengganti analisis.

## Ringkasan
Bab ini memperkenalkan visualisasi sebagai langkah penting setelah analisis data bahasa. Pembaca belajar membuat tabel frekuensi terformat, diagram batang horizontal dan vertikal dengan *matplotlib*, serta *word cloud* dengan pustaka *wordcloud*. Selain itu, bab ini juga menunjukkan cara membandingkan dua teks secara visual dan memvisualisasikan distribusi panjang kalimat.

Yang paling penting, visualisasi harus selalu disertai konteks dan penjelasan. Grafik tanpa konteks bisa menyesatkan. Grafik dengan penjelasan yang tepat bisa menjadi alat komunikasi yang sangat kuat, baik untuk laporan, presentasi kelas, maupun diskusi.

## Latihan Akhir Bab
### Latihan 1. Membuat tabel frekuensi terformat
Ambil satu teks pendek, hitung frekuensi katanya, lalu tampilkan hasilnya dalam tabel terformat di layar.

### Latihan 2. Membuat diagram batang frekuensi kata
Dari teks yang sama, buat diagram batang horizontal yang menampilkan tujuh kata paling sering. Beri judul dan label sumbu yang jelas.

### Latihan 3. Membuat word cloud
Buat *word cloud* dari satu teks pendek. Jelaskan kata mana yang paling besar dan mengapa. Jelaskan juga apakah tampilan itu sudah cukup informatif atau masih memerlukan penyaringan tambahan.

### Latihan 4. Membandingkan dua teks secara visual
Ambil dua teks pendek dari jenis yang berbeda, misalnya satu cerpen mini dan satu paragraf artikel. Buat diagram batang berdampingan untuk membandingkan frekuensi katanya. Jelaskan perbedaan yang terlihat.

### Latihan 5. Menyajikan hasil analisis secara lengkap
Pilih satu teks, lalu sajikan hasilnya secara lengkap: tabel frekuensi, diagram batang, *word cloud*, dan satu paragraf pengamatan.

## Proyek Mini
### Proyek Mini 10. Poster Analisis Bahasa dari Satu Teks
**Tujuan pembelajaran**  
Menggabungkan beberapa teknik visualisasi untuk menyajikan hasil analisis satu teks secara ringkas dan jelas.

**Alat yang digunakan**  
*Google Colab* atau *Jupyter Notebook*, *matplotlib*, dan *wordcloud*.

**Instruksi**
1. Pilih satu teks pendek, misalnya satu halaman cerpen, satu artikel pendek, atau satu puisi.
2. Bersihkan teks tersebut dan saring *stopwords* jika perlu.
3. Buat tabel frekuensi 10 kata teratas.
4. Buat diagram batang frekuensi.
5. Buat *word cloud*.
6. Tulis satu paragraf pengamatan singkat.
7. Simpan semua hasil dalam satu *notebook*.

**Keluaran yang diharapkan**  
Satu *notebook* yang berisi:
- teks yang dipakai,
- tabel frekuensi,
- diagram batang,
- *word cloud*,
- pengamatan singkat, dan
- semua grafik sudah diberi judul dan label yang jelas.

**Refleksi**  
Visualisasi mana yang menurut pembaca paling membantu untuk memahami isi teks? Apakah *word cloud* memberi informasi yang sama dengan diagram batang, atau ada perbedaan yang perlu diperhatikan?

## 🧠 Istilah yang dipelajari pada bab ini
- visualisasi: penyajian data dalam bentuk visual seperti grafik, tabel, atau gambar.
- diagram batang: grafik yang menampilkan nilai menggunakan batang dengan panjang sebanding nilainya.
- *word cloud* (*awan kata*): visualisasi yang menampilkan kata dengan ukuran sebanding frekuensinya.
- *matplotlib*: pustaka Python untuk membuat grafik dan visualisasi data. Dikembangkan sebagai proyek *open source* oleh komunitas luas.
- *wordcloud*: pustaka Python untuk membuat awan kata.
- `plt.barh()`: fungsi *matplotlib* untuk membuat diagram batang horizontal.
- `plt.bar()`: fungsi *matplotlib* untuk membuat diagram batang vertikal.
- `plt.savefig()`: fungsi untuk menyimpan grafik ke file gambar.
- `plt.show()`: fungsi untuk menampilkan grafik di layar.
- `plt.subplots()`: fungsi untuk membuat beberapa area grafik dalam satu gambar.
- `plt.imshow()`: fungsi untuk menampilkan gambar, termasuk *word cloud*.
- `plt.axis("off")`: perintah untuk menyembunyikan sumbu pada grafik.
- `dpi`: satuan ketajaman gambar (*dots per inch*).

## Sumber Gambar yang Perlu Disiapkan
- [Tangkapan layar *Google Colab* yang menampilkan diagram batang horizontal frekuensi kata.]
  Gambar 10.2 Diagram Batang Frekuensi Kata di *Google Colab*
- [Tangkapan layar *Google Colab* yang menampilkan *word cloud* dari satu teks pendek.]
  Gambar 10.3 Word Cloud dari Teks Pendek
- [Tangkapan layar *Google Colab* yang menampilkan dua diagram batang berdampingan untuk perbandingan dua teks.]
  Gambar 10.4 Perbandingan Frekuensi Kata pada Dua Teks
- [Tangkapan layar *Google Colab* yang menampilkan diagram batang distribusi panjang kalimat dengan warna berbeda untuk pendek, sedang, dan panjang.]
  Gambar 10.5 Distribusi Panjang Kalimat dengan Kode Warna
