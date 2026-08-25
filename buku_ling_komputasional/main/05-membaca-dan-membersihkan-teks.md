# Bab 5. Membaca dan Membersihkan Teks

## Tujuan Pembelajaran
Setelah mempelajari bab ini, pembaca diharapkan mampu:

1. membaca data teks dari file `.txt` atau dari kolom teks pada file `.csv`;
2. menjelaskan fungsi *lowercasing* dan normalisasi dasar dalam pengolahan teks;
3. membersihkan tanda baca, angka, dan karakter yang tidak relevan sesuai tujuan analisis;
4. menangani spasi, baris, dan format teks yang berantakan; dan
5. menjelaskan mengapa pembersihan teks penting sebelum analisis bahasa dilakukan.

## Pengantar Bab
Pada bab sebelumnya, kita telah memakai *Google Colab* untuk mengunggah file, membaca isinya, dan menjalankan kode sederhana. Namun, begitu kita mulai bekerja dengan data bahasa yang lebih nyata, satu hal segera terlihat: teks jarang datang dalam keadaan rapi. Ada huruf besar dan kecil yang tidak konsisten, tanda baca yang menempel, spasi ganda, baris kosong, angka yang tidak diperlukan, atau hasil salin-tempel yang membuat format teks berantakan.

Karena itu, sebelum menghitung frekuensi kata, membandingkan kosakata, atau mencari pola tertentu, kita perlu membersihkan teks lebih dahulu. Dalam kerja linguistik korpus dan analisis data bahasa, tahap ini penting karena kualitas hasil sangat dipengaruhi oleh kualitas data yang masuk. Data yang kotor dapat membuat pola terlihat kabur, bahkan menyesatkan interpretasi (McEnery & Hardie, 2012; Meyer, 2023).

Bab ini membahas tahap awal yang sangat praktis, yaitu membaca teks dari file lalu membersihkannya sedikit demi sedikit. Kita tidak akan langsung memakai teknik yang rumit. Fokus kita adalah membangun kebiasaan kerja yang rapi: membaca file dengan benar, merapikan bentuk tulisan, menghapus unsur yang tidak diperlukan, dan menyiapkan teks agar siap dianalisis pada bab-bab berikutnya.

[Ilustrasi layar *Google Colab* yang menampilkan dua panel: sisi kiri berisi teks mentah dengan huruf campur, spasi ganda, dan tanda baca berlebih; sisi kanan berisi versi teks yang sudah rapi. Visual menekankan proses perubahan dari data mentah ke data siap analisis.]
Gambar 5.1 Dari Teks Mentah ke Teks Siap Analisis

## 5.1 Membaca Data Teks dari File
Langkah pertama dalam pengolahan teks adalah membaca data dengan benar. Pada tahap awal, sumber yang paling umum adalah file `.txt` dan file `.csv`. File `.txt` cocok untuk *plain text* (teks polos) seperti artikel, puisi, esai, atau transkrip sederhana. File `.csv` cocok untuk data berkolom, misalnya tabel yang memiliki kolom `teks`, `bahasa`, `label`, atau metadata lain.

### 5.1.1 Membaca file `.txt`
Bayangkan kita mempunyai file `korpus_puisi.txt` yang berisi dua baris teks.

```python
with open("korpus_puisi.txt", "r", encoding="utf-8") as file:
    isi = file.read()

print(isi)
```

**Output**
```text
Puisi ini memakai kata Malam, malam, dan hujan.
Baris kedua memuat kata Sunyi dan sunyi.
```

**Penjelasan**
Blok `with open(...)` membuka file teks bernama `korpus_puisi.txt`, lalu seluruh isinya disimpan ke variabel `isi`. Perintah `print(isi)` menampilkan teks itu ke layar. Pada tahap ini, kita belum membersihkan apa pun. Kita baru memastikan bahwa data berhasil dibaca.

### 5.1.2 Menghitung jumlah baris dan jumlah kata awal
Setelah file berhasil dibaca, kita bisa mulai mengamati bentuk kasarnya.

```python
with open("korpus_puisi.txt", "r", encoding="utf-8") as file:
    isi = file.read()

baris = isi.splitlines()
kata_kata = isi.split()

print("Jumlah baris:", len(baris))
print("Jumlah kata awal:", len(kata_kata))
```

**Output**
```text
Jumlah baris: 2
Jumlah kata awal: 15
```

**Penjelasan**
Metode `.splitlines()` memecah teks berdasarkan pergantian baris, sedangkan `.split()` memecah teks berdasarkan spasi. Hasil ini belum final, tetapi cukup untuk memberi gambaran awal tentang ukuran data yang sedang kita tangani.

### 5.1.3 Membaca kolom teks dari file `.csv`
Sekarang bayangkan kita mempunyai file `data_bacaan.csv` dengan kolom `id`, `teks`, dan `label`. Struktur seperti ini lazim ketika teks disimpan bersama metadata.

```python
import csv

with open("data_bacaan.csv", "r", encoding="utf-8") as file:
    pembaca = csv.DictReader(file)
    daftar_teks = [baris["teks"] for baris in pembaca]

print(daftar_teks)
print("Jumlah baris data:", len(daftar_teks))
```

**Output**
```text
['Malam datang perlahan.', 'Pembelajar menulis esai singkat.', 'Teman memberi umpan balik.']
Jumlah baris data: 3
```

**Penjelasan**
Modul `csv` membaca file berkolom dan `csv.DictReader` membuat setiap baris dapat diakses melalui nama kolomnya. Dengan cara ini, pembaca tidak harus menebak urutan kolom. Jika fokus analisis ada pada kolom `teks`, kita bisa mengambil kolom itu saja untuk diolah lebih lanjut.

### Contoh Nyata
Dalam pembelajaran bahasa, langkah ini sangat berguna. Misalnya, satu file `.txt` dapat dipakai sebagai korpus mini berisi puisi atau artikel pendek. Sementara itu, file `.csv` dapat dipakai untuk menyimpan tulisan pembelajar bersama kategori tugas, tingkat kelas, atau label topik. Keduanya sama-sama berisi data bahasa, tetapi bentuk penyimpanannya berbeda.

## 5.2 Lowercasing dan Normalisasi Dasar
Setelah data berhasil dibaca, langkah berikutnya adalah merapikan bentuk tulisannya. Salah satu tahap paling umum adalah *lowercasing* (mengubah teks menjadi huruf kecil). Tahap ini sering dilakukan agar kata yang sebenarnya sama tidak dihitung sebagai bentuk yang berbeda hanya karena huruf kapital.

Sebagai contoh, kata `Malam`, `malam`, dan `MALAM` secara makna mungkin ingin kita perlakukan sebagai satu bentuk yang sama. Jika tidak dirapikan lebih dahulu, ketiganya bisa terbaca sebagai tiga bentuk yang berbeda.

### 5.2.1 Mengubah teks menjadi huruf kecil

```python
teks = "Malam, malam, dan Hujan membuat suasana puisi terasa muram."
print(teks.lower())
```

**Output**
```text
malam, malam, dan hujan membuat suasana puisi terasa muram.
```

**Penjelasan**
Metode `.lower()` mengubah semua huruf menjadi huruf kecil. Langkah ini berguna ketika pembaca ingin menghitung kata atau membandingkan bentuk leksikal tanpa dipengaruhi variasi kapitalisasi.

### 5.2.2 Normalisasi dasar dengan `.strip()`
Selain huruf besar dan kecil, teks sering memiliki spasi yang tidak perlu di awal atau akhir. Untuk itu, kita dapat memakai `.strip()`.

```python
teks = "   Bahasa Indonesia dipakai dalam kelas BIPA.   "
teks_normal = teks.strip()
print("[" + teks_normal + "]")
```

**Output**
```text
[Bahasa Indonesia dipakai dalam kelas BIPA.]
```

**Penjelasan**
Tanda kurung siku di keluaran dipakai agar pembaca dapat melihat bahwa spasi di awal dan akhir sudah hilang. Ini contoh *normalization* (*normalisasi*) dasar, yaitu menyamakan bentuk tulisan agar lebih rapi dan konsisten sebelum analisis dilakukan.

### 5.2.3 Normalisasi tidak selalu berarti menghapus semua variasi
Tahap normalisasi perlu disesuaikan dengan tujuan. Jika pembaca sedang meneliti perbedaan nama diri, singkatan resmi, atau gaya penulisan judul, huruf kapital justru bisa penting. Karena itu, *lowercasing* bukan aturan mutlak. Ia adalah keputusan kerja yang harus disesuaikan dengan pertanyaan analisis.

### Coba Perhatikan
Pembersihan teks bukan kegiatan mekanis yang selalu sama. Teks untuk analisis kosakata mungkin perlu dirapikan dengan satu cara. Teks untuk analisis puisi atau gaya bahasa mungkin perlu dirapikan dengan cara lain.

## 5.3 Menghapus Tanda Baca dan Karakter Tidak Perlu
Setelah huruf dirapikan, pembaca sering perlu menghapus tanda baca atau karakter yang tidak relevan. Ini penting ketika tujuan analisis ada pada kata-kata inti, bukan pada tanda baca, angka, atau simbol lain.

### 5.3.1 Menghapus tanda baca
Salah satu cara sederhana adalah memakai modul `string`.

```python
import string

teks = "Analisis, teks! untuk: kelas?"
bersih = teks.translate(str.maketrans("", "", string.punctuation))
print(bersih)
```

**Output**
```text
Analisis teks untuk kelas
```

**Penjelasan**
`string.punctuation` berisi kumpulan tanda baca umum. Perintah `translate(...)` lalu menghapus tanda baca itu dari teks. Hasilnya adalah versi yang lebih bersih untuk analisis kata.

### 5.3.2 Menghapus angka dan simbol jika memang tidak dibutuhkan
Kadang-kadang angka juga perlu dihapus. Misalnya, pembaca hanya ingin fokus pada kosakata dalam pengumuman, bukan pada nomor urut atau jumlah angka.

```python
import string

teks = "Baca 3 puisi, lalu pilih 1 tema utama!"
hapus = string.punctuation + "0123456789"
bersih = teks.translate(str.maketrans("", "", hapus))
print(bersih)
```

**Output**
```text
Baca  puisi lalu pilih  tema utama
```

**Penjelasan**
Kode ini menghapus tanda baca sekaligus angka. Hasilnya memang lebih bersih dari sisi karakter, tetapi masih menyisakan spasi ganda. Ini normal. Dalam praktik pembersihan teks, kita sering bekerja bertahap. Satu tahap membersihkan karakter, tahap berikutnya merapikan spasi.

### 5.3.3 Tidak semua tanda baca harus dihapus
Bagian ini penting. Jika pembaca sedang menganalisis ekspresi emosi pada media sosial, dialog, atau puisi, tanda seru, tanda tanya, tanda petik, atau garis miring bisa memiliki fungsi makna. Dalam kasus seperti itu, menghapus semua tanda baca justru dapat membuang informasi penting.

### Kesalahan Umum
Pemula kadang terlalu cepat membersihkan teks sampai semua ciri khasnya hilang. Teks memang perlu dirapikan, tetapi jangan sampai data menjadi terlalu miskin untuk menjawab pertanyaan analisis.

[Diagram alur sederhana yang memperlihatkan urutan kerja: membaca file, mengubah huruf menjadi kecil, menghapus tanda baca, merapikan spasi, lalu menyiapkan teks untuk analisis.]
Gambar 5.2 Alur Dasar Pembersihan Teks

## 5.4 Menangani Spasi, Baris, dan Format Teks
Masalah lain yang sangat sering muncul adalah format teks yang berantakan. Ini bisa berasal dari hasil salin-tempel PDF, subtitle, transkrip, atau data yang diketik tidak rapi. Akibatnya, satu teks bisa memiliki spasi ganda, baris kosong, atau pemenggalan yang mengganggu.

### 5.4.1 Menghapus baris kosong dan merapikan baris

```python
teks = "Baris pertama.\n\nBaris kedua dengan   spasi berlebih.\nBaris ketiga."
baris_rapi = [b.strip() for b in teks.splitlines() if b.strip()]
teks_rapi = " ".join(" ".join(baris_rapi).split())

print(baris_rapi)
print(teks_rapi)
```

**Output**
```text
['Baris pertama.', 'Baris kedua dengan   spasi berlebih.', 'Baris ketiga.']
Baris pertama. Baris kedua dengan spasi berlebih. Baris ketiga.
```

**Penjelasan**
`splitlines()` memecah teks berdasarkan pergantian baris. Lalu setiap baris dirapikan dengan `.strip()` dan baris kosong dibuang. Pada tahap akhir, `" ".join(...split())` dipakai untuk merapikan spasi ganda menjadi satu spasi. Hasil akhirnya jauh lebih enak dibaca dan lebih siap untuk analisis lanjutan.

### 5.4.2 Menangani teks hasil salin-tempel
Teks hasil salin-tempel sering memecah kalimat secara aneh.

```python
teks = "Puisi   ini\nmemakai   kata-kata  yang\nterputus oleh salin-tempel."
rapi = " ".join(teks.split())
print(rapi)
```

**Output**
```text
Puisi ini memakai kata-kata yang terputus oleh salin-tempel.
```

**Penjelasan**
Metode `split()` tanpa argumen memecah teks berdasarkan spasi apa pun, termasuk spasi ganda dan pergantian baris. Ketika hasilnya disatukan lagi dengan `" ".join(...)`, teks menjadi lebih rapi.

### 5.4.3 Format teks bergantung pada tujuan
Jika pembaca sedang meneliti struktur bait puisi, pergantian baris mungkin justru penting dan tidak boleh dihapus. Sebaliknya, jika tujuan analisis adalah menghitung kata atau menyiapkan data masukan untuk model sederhana, penyatuan baris sering membantu. Lagi-lagi, tahap pembersihan selalu bergantung pada tujuan.

### Contoh Nyata
Dalam kerja kelas, pembaca kadang menyalin artikel dari PDF atau situs web ke Colab. Hasilnya bisa penuh spasi aneh dan pemenggalan baris yang tidak perlu. Jika teks seperti itu langsung dianalisis, hasil hitungan kata atau daftar token bisa menjadi kacau. Karena itu, merapikan format adalah langkah yang sangat praktis, bukan tambahan yang mewah.

## 5.5 Mengapa Pembersihan Data Penting?
Setelah melihat beberapa langkah teknis, pertanyaan utamanya adalah ini: mengapa pembersihan data perlu dilakukan sebelum analisis?

Jawabannya sederhana. Analisis bahasa bekerja di atas bentuk-bentuk yang dibaca komputer. Jika bentuk-bentuk itu tidak konsisten, hasil analisis juga mudah melenceng. Kata yang seharusnya sama bisa terbaca berbeda. Teks yang seharusnya satu kalimat bisa terbaca seperti beberapa potongan. Tanda baca yang menempel bisa membuat penghitungan kata menjadi tidak stabil.

Dalam kajian korpus dan pembelajaran berbasis data, ketelitian awal seperti ini sangat penting. Data yang lebih rapi membantu pembelajar melihat pola dengan lebih jernih dan menafsirkan hasil dengan lebih hati-hati (McEnery & Hardie, 2012; O’Keeffe et al., 2007; Widodo et al., 2023).

### 5.5.1 Contoh alur pembersihan sederhana
Berikut contoh kecil yang menggabungkan beberapa langkah sekaligus.

```python
import string

teks = "Malam, malam!  Hujan\n turun di kelas bahasa."
bersih = teks.lower()
bersih = bersih.translate(str.maketrans("", "", string.punctuation))
bersih = " ".join(bersih.split())

print("Teks awal:", teks)
print("Teks bersih:", bersih)
print("Daftar kata:", bersih.split())
```

**Output**
```text
Teks awal: Malam, malam!  Hujan
 turun di kelas bahasa.
Teks bersih: malam malam hujan turun di kelas bahasa
Daftar kata: ['malam', 'malam', 'hujan', 'turun', 'di', 'kelas', 'bahasa']
```

**Penjelasan**
Contoh ini menunjukkan perubahan dari teks mentah ke teks yang lebih siap dianalisis. Huruf besar diubah ke huruf kecil, tanda baca dihapus, lalu spasi dan pergantian baris dirapikan. Hasil akhirnya belum tentu sempurna untuk semua penelitian, tetapi cukup baik untuk banyak latihan awal.

### 5.5.2 Pembersihan yang baik itu cukup, bukan berlebihan
Pembersihan teks bukan perlombaan untuk membuat data menjadi polos sepenuhnya. Tujuannya adalah membuat data cukup rapi agar pertanyaan analisis dapat dijawab lebih baik. Jika pembaca menghapus terlalu banyak hal, informasi penting bisa ikut hilang.

### Trivia
Dalam praktik nyata, banyak kesalahan analisis awal bukan terjadi karena rumus yang salah, melainkan karena data yang belum dibereskan dengan baik.

[Cuplikan *notebook* yang memperlihatkan satu sel berisi teks mentah dan satu sel berikutnya berisi versi teks yang sudah dibersihkan, lengkap dengan keluaran daftar kata.]
Gambar 5.3 Perbandingan Teks Mentah dan Teks Bersih di *Google Colab*

## Ringkasan
Bab ini membahas tahap dasar yang sangat penting dalam analisis bahasa, yaitu membaca dan membersihkan teks. Pembaca mulai dari membaca file `.txt` dan `.csv`, lalu belajar melihat bahwa data mentah hampir selalu memerlukan perapian sebelum dipakai lebih jauh.

Bab ini juga memperkenalkan beberapa langkah praktis, yaitu *lowercasing*, normalisasi dasar, penghapusan tanda baca atau karakter tertentu, serta penanganan spasi dan baris. Semua langkah itu berguna karena komputer membaca teks berdasarkan bentuk yang ada, bukan berdasarkan niat penulisnya.

Yang paling penting, bab ini menekankan bahwa pembersihan data harus selalu disesuaikan dengan tujuan. Tidak semua huruf kapital perlu dihapus, tidak semua tanda baca harus dibuang, dan tidak semua pergantian baris wajib diratakan. Dengan sikap seperti ini, pembaca tidak hanya belajar membersihkan data, tetapi juga belajar berpikir kritis tentang apa yang sedang dilakukan pada teks.

## Latihan Akhir Bab
### Latihan 1. Membaca file teks
Siapkan satu file `.txt` pendek yang berisi dua atau tiga paragraf. Baca file itu di Colab, lalu tampilkan:
1. isi teks,
2. jumlah baris, dan
3. jumlah kata awal.

### Latihan 2. Lowercasing dan normalisasi dasar
Ambil satu paragraf pendek yang masih memiliki campuran huruf besar dan kecil. Lalu:
1. ubah seluruh teks menjadi huruf kecil,
2. hilangkan spasi berlebih di awal dan akhir, dan
3. bandingkan hasil sebelum dan sesudah dirapikan.

### Latihan 3. Menghapus karakter yang tidak diperlukan
Gunakan satu kalimat atau paragraf yang memuat angka, tanda baca, atau simbol. Bersihkan teks itu sesuai tujuan berikut:
1. fokus hanya pada kata-kata inti, dan
2. jelaskan karakter apa saja yang dihapus dan mengapa.

### Latihan 4. Merapikan spasi dan baris
Salin satu teks pendek dari PDF, subtitle, atau sumber lain yang memiliki banyak spasi atau pergantian baris. Rapikan teks itu hingga menjadi satu bentuk yang lebih stabil untuk dianalisis.

### Latihan 5. Membaca kolom teks dari file CSV
Siapkan satu file `.csv` kecil dengan kolom `teks` dan satu kolom metadata lain. Baca kolom `teks`, lalu tampilkan:
1. daftar teks,
2. jumlah baris data, dan
3. satu contoh teks yang ingin dibersihkan lebih lanjut.

## Proyek Mini
### Proyek Mini 5. Menyiapkan Korpus Mini yang Lebih Bersih
**Tujuan pembelajaran**  
Membaca satu kumpulan teks sederhana lalu membersihkannya agar siap dipakai untuk analisis kata pada bab berikutnya.

**Alat yang digunakan**  
*Google Colab* dan satu file `.txt` atau `.csv` yang berisi data teks latihan.

**Instruksi**
1. Pilih satu file latihan, bisa berupa `.txt` atau `.csv`.
2. Jika file berupa `.txt`, baca seluruh isinya ke dalam satu variabel.
3. Jika file berupa `.csv`, ambil satu kolom `teks` untuk diproses.
4. Terapkan minimal tiga langkah pembersihan, misalnya *lowercasing*, penghapusan tanda baca, dan perapian spasi.
5. Tampilkan contoh teks sebelum dan sesudah dibersihkan.
6. Simpan hasil bersih ke file baru, misalnya `korpus_bersih.txt`.
7. Tuliskan satu paragraf refleksi: langkah mana yang paling membantu dan apa yang masih terasa sulit.

**Keluaran yang diharapkan**  
Satu *notebook* Colab yang berisi:
- kode untuk membaca file,
- contoh teks mentah,
- contoh teks bersih,
- penjelasan singkat langkah pembersihan, dan
- satu file hasil akhir yang lebih rapi.

**Refleksi**  
Apakah semua bagian teks perlu dibersihkan dengan cara yang sama? Bagian mana yang perlu dipertahankan jika tujuan analisis berubah, misalnya dari hitung kata ke analisis puisi atau dialog?

## 🧠 Istilah yang dipelajari pada bab ini
- *plain text* (teks polos): teks tanpa format tampilan yang rumit, seperti warna, tabel, atau tata letak halaman.
- *lowercasing*: proses mengubah huruf menjadi huruf kecil.
- *normalization* (*normalisasi*): proses menyamakan bentuk tulisan agar lebih konsisten untuk pengolahan.
- tanda baca: simbol seperti koma, titik, tanda tanya, atau tanda seru yang dapat dipertahankan atau dihapus sesuai tujuan analisis.
- karakter tidak perlu: simbol, angka, atau unsur lain yang dianggap tidak relevan bagi pertanyaan analisis tertentu.
- spasi ganda: jarak kosong berlebih yang dapat mengganggu pemrosesan teks.
- pergantian baris: pemisahan teks ke baris baru yang kadang penting, kadang perlu dirapikan.
- metadata: informasi tambahan yang menyertai teks, misalnya label, topik, atau kategori.
- `splitlines()`: metode untuk memecah teks berdasarkan pergantian baris.
- `translate()`: metode untuk mengubah atau menghapus karakter tertentu dari teks.

## Sumber Gambar yang Perlu Disiapkan
- [Tangkapan layar notebook Colab yang menampilkan file `.txt` dibaca ke dalam satu sel dan hasil teks mentah muncul di bawahnya.]
  Gambar 5.4 Membaca File Teks Mentah di *Google Colab*
- [Tangkapan layar dua keluaran kode yang memperlihatkan perbedaan antara teks sebelum dan sesudah *lowercasing* serta pembersihan tanda baca.]
  Gambar 5.5 Perubahan Bentuk Teks Setelah Pembersihan Dasar
- [Tangkapan layar file `.csv` dengan kolom `teks` dan metadata, lalu keluaran Colab yang menampilkan beberapa baris awal hasil pembacaan data.]
  Gambar 5.6 Membaca Kolom Teks dari File CSV
