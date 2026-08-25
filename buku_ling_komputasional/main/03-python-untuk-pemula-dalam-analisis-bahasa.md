# Bab 3. Python untuk Pemula dalam Analisis Bahasa

## Tujuan Pembelajaran
Setelah mempelajari bab ini, pembaca diharapkan mampu:

1. menjelaskan alasan *Python* sering dipakai dalam analisis bahasa;
2. menyiapkan ruang praktik awal, baik melalui instalasi *Python* di komputer maupun melalui *Google Colab*;
3. memahami penggunaan *variabel*, tipe data dasar, dan operasi sederhana dalam *Python*;
4. memakai *string*, *list*, perulangan, dan percabangan untuk tugas kebahasaan sederhana; dan
5. menulis *fungsi* sederhana untuk membantu analisis teks awal.

## Pengantar Bab
Bagi banyak pembelajar bahasa, bagian yang paling menegangkan dari buku seperti ini biasanya bukan konsep linguistiknya, melainkan kode. Kata *Python* sering terdengar seperti penanda bahwa pembahasan akan menjadi rumit, penuh simbol, dan hanya cocok untuk orang yang sudah lama belajar komputer. Kesan itu wajar, tetapi tidak selalu benar.

Dalam buku ini, Python diperkenalkan bukan sebagai tujuan akhir, melainkan sebagai alat kerja. Kita tidak sedang menyiapkan pembaca menjadi pengembang perangkat lunak profesional. Yang lebih penting ialah membuat pembaca cukup nyaman menggunakan beberapa perintah sederhana agar dapat membaca teks, membersihkannya, menghitung bentuk bahasa tertentu, dan menyiapkan analisis awal. Pada tahap ini, *Python* dipelajari sebagai alat bantu untuk berpikir lebih teratur dan bekerja lebih efisien.

Ada alasan praktis mengapa Python banyak dipakai dalam linguistik komputasi dan analisis data bahasa. Bahasa pemrograman ini relatif mudah dibaca, memiliki banyak dokumentasi resmi, dan didukung ekosistem pustaka yang luas untuk pengolahan teks, data, dan visualisasi (Python Software Foundation, 2026a; Python Software Foundation, 2026b). Bagi pembelajar pemula, kelebihan terbesarnya adalah sintaksnya cukup bersih sehingga perhatian dapat tetap diarahkan pada masalah yang sedang dipecahkan, bukan hanya pada bentuk kodenya.

Bab ini dirancang agar pembaca bisa mulai dari nol. Contoh-contoh tidak akan memakai data yang terlalu rumit. Sebaliknya, semua latihan diarahkan ke konteks yang dekat dengan pembelajaran bahasa, seperti menghitung jumlah kata dalam paragraf, memeriksa pengulangan kata, memisahkan kalimat, atau membuat fungsi kecil untuk membantu membaca data teks. Jika pada bab-bab sebelumnya kita sudah belajar melihat bahasa sebagai data, maka pada bab ini kita mulai belajar alat yang dapat membantu mengolah data itu.

[Ilustrasi pembelajar membuka laptop dengan tiga pilihan jalur praktik: *Google Colab* di peramban, *Jupyter Notebook* di komputer lokal, dan *Python* langsung dari terminal. Visual menekankan bahwa pembaca dapat memilih jalur yang paling nyaman lebih dahulu.]
Gambar 3.1 Tiga Jalur Praktik Awal

## 3.1 Apa itu Python dan Mengapa Relevan?
*Python* adalah bahasa pemrograman tingkat tinggi yang banyak digunakan untuk pembelajaran, analisis data, otomasi, pengembangan aplikasi, dan pengolahan bahasa. Dalam dokumentasi resminya, Python diperkenalkan sebagai bahasa yang menekankan keterbacaan kode dan memungkinkan penulisan program dengan lebih ringkas dibandingkan banyak bahasa lain (Python Software Foundation, 2026a).

Bagi pembelajar bahasa, *Python* relevan bukan karena semua orang harus menjadi *programmer*, melainkan karena banyak pekerjaan analisis teks dapat dilakukan dengan perintah yang cukup sederhana. Misalnya, kita dapat:

- menyimpan teks dalam variabel;
- mengubah huruf menjadi huruf kecil;
- memecah paragraf menjadi kata-kata;
- menghitung jumlah kata;
- mencari kata tertentu;
- membuat daftar kata unik; dan
- menulis fungsi kecil untuk tugas yang berulang.

Dengan kata lain, Python cocok sebagai jembatan awal antara bahasa dan komputasi. Kita dapat mulai dari pekerjaan yang kecil, lalu menambah tingkat kerumitan sedikit demi sedikit.

### Mengapa Python banyak dipakai dalam analisis bahasa?
Ada beberapa alasan utama.

1. **Sintaksnya relatif mudah dibaca.**  
   Kode Python sering lebih dekat ke logika berpikir manusia daripada ke bentuk teknis yang terlalu padat.

2. **Cocok untuk pengolahan teks.**  
   Python memiliki tipe data dan operasi bawaan yang membantu memproses teks, terutama melalui *string*, *list*, dan fungsi-fungsi dasar (Python Software Foundation, 2026b; Python Software Foundation, 2026c).

3. **Banyak contoh dan dokumentasi resmi.**  
   Ini memudahkan pemula untuk belajar mandiri.

4. **Mudah dipakai di *Google Colab*.**  
   Pembaca dapat berlatih tanpa instalasi rumit karena *Google Colab* menyediakan *notebook Jupyter* (*buku catatan komputasional Jupyter*) yang dihosting dan dapat dijalankan melalui peramban (Google, 2026a; Google, 2026b).

### Sebelum mulai: pilih jalur praktik yang paling nyaman
Untuk buku ini, ada tiga jalur praktik yang sama-sama sah.

| Opsi | Kelebihan | Kekurangan | Cocok untuk |
|---|---|---|---|
| *Google Colab* | tidak perlu instalasi; mudah dibagikan; cepat untuk mulai | bergantung pada akun Google dan internet | pembaca pemula dan praktik cepat |
| *Jupyter Notebook* di komputer | pengalaman *notebook* interaktif seperti Colab, tetapi berjalan di komputer sendiri; tidak bergantung pada internet setelah terpasang | perlu instalasi Python dan Jupyter | pembaca yang ingin *notebook* interaktif tanpa tergantung internet |
| *Python* langsung dari terminal | paling ringan; tidak perlu peramban; cocok untuk skrip otomatis | tampilan kurang interaktif untuk pemula | pembaca yang sudah terbiasa dengan terminal atau *command line* |

Bagi kebanyakan pembaca pemula, jalur paling aman biasanya adalah **memulai dari Google Colab lebih dahulu**. Setelah cukup nyaman, pembaca dapat mencoba **Jupyter Notebook di komputer sendiri** agar tidak selalu bergantung pada internet. Jalur ketiga, yaitu menjalankan Python langsung dari terminal, lebih cocok untuk tahap lanjut atau untuk skrip pendek yang tidak memerlukan tampilan interaktif.

### Langkah ringkas memasang Python di komputer
#### Windows
1. Buka situs resmi Python untuk Windows.
2. Unduh installer versi terbaru.
3. Jalankan installer.
4. Centang **Add Python to PATH**.
5. Klik **Install Now**.
6. Setelah selesai, cek lewat **Command Prompt**:

```bash
python --version
```

**Contoh output**
```text
Python 3.13.0
```

**Penjelasan**
Perintah ini dipakai untuk memeriksa apakah *Python* sudah terpasang dan versi apa yang sedang digunakan. Nomor versinya dapat berbeda pada komputer pembaca.

Jika tidak terbaca, coba:

```bash
py --version
```

**Contoh output**
```text
Python 3.13.0
```

**Penjelasan**
Pada sebagian komputer Windows, perintah `py` lebih mudah dikenali daripada `python`. Jika nomor versi muncul, berarti *Python launcher* atau *Python* sudah siap dipakai.

#### macOS
1. Buka situs resmi Python untuk macOS.
2. Unduh installer.
3. Jalankan proses instalasi.
4. Cek lewat Terminal:

```bash
python3 --version
```

**Contoh output**
```text
Python 3.13.0
```

**Penjelasan**
Perintah ini memeriksa versi *Python 3*. Pada macOS dan banyak distribusi Linux, nama perintah yang paling umum memang `python3`, bukan `python`.

#### Linux
Pada banyak distribusi Linux, Python biasanya sudah tersedia. Cek lewat Terminal:

```bash
python3 --version
```

**Contoh output**
```text
Python 3.13.0
```

**Penjelasan**
Perintah ini memeriksa versi *Python 3*. Pada macOS dan banyak distribusi Linux, nama perintah yang paling umum memang `python3`, bukan `python`.

### Langkah memasang dan memakai *Jupyter Notebook* di komputer
Jika pembaca sudah memasang *Python* dan ingin mendapatkan pengalaman *notebook* interaktif yang mirip *Google Colab* tetapi berjalan di komputer sendiri, pembaca dapat memasang *Jupyter Notebook*. *Jupyter* adalah proyek *open source* yang dikembangkan oleh komunitas luas dan dikelola oleh Project Jupyter (Kluyver et al., 2016). Nama *Jupyter* berasal dari tiga bahasa pemrograman utama yang didukungnya, yaitu Julia, Python, dan R.

#### Memasang Jupyter Notebook
Setelah Python terpasang, buka Terminal atau Command Prompt, lalu jalankan perintah berikut.

```bash
pip install notebook
```

**Contoh output**
```text
Successfully installed notebook-7.4.2
```

**Penjelasan**
Perintah `pip install notebook` mengunduh dan memasang paket *Jupyter Notebook* dari *PyPI*. Nomor versi dapat berbeda pada komputer pembaca. Jika pemasangan berhasil, pesan `Successfully installed` akan muncul.

#### Menjalankan Jupyter Notebook
Setelah terpasang, jalankan Jupyter Notebook dari Terminal atau Command Prompt.

```bash
jupyter notebook
```

**Contoh output**
```text
[I 2026-08-25 10:15:32.456 ServerApp] Jupyter Server 2.14.0 is running at:
[I 2026-08-25 10:15:32.456 ServerApp]     http://localhost:8888/tree
```

**Penjelasan**
Perintah ini menjalankan *server* Jupyter di komputer pembaca. Setelah itu, peramban biasanya akan terbuka secara otomatis dan menampilkan halaman awal Jupyter. Jika peramban tidak terbuka secara otomatis, pembaca dapat menyalin alamat `http://localhost:8888/tree` ke peramban secara manual.

#### Membuat notebook baru
1. Pada halaman awal Jupyter di peramban, klik **New**, lalu pilih **Python 3**.
2. Sebuah *notebook* baru akan terbuka. Tampilan dan cara kerjanya sangat mirip dengan *Google Colab*: ada sel kode tempat pembaca mengetik perintah Python, dan hasilnya langsung muncul di bawah sel setelah dijalankan.
3. Ketik kode berikut pada sel pertama, lalu tekan **Shift + Enter** untuk menjalankannya.

```python
print("Halo dari Jupyter Notebook!")
```

**Output**
```text
Halo dari Jupyter Notebook!
```

**Penjelasan**
Jika tulisan ini muncul, berarti Jupyter Notebook sudah berjalan dengan baik di komputer pembaca. Mulai dari sini, semua contoh kode dalam buku ini dapat dijalankan baik di *Google Colab* maupun di *Jupyter Notebook* lokal.

#### Kapan memakai Jupyter Notebook di lokal?
Jupyter Notebook di komputer sendiri sangat berguna dalam beberapa situasi:

- ketika internet tidak tersedia atau tidak stabil;
- ketika pembaca ingin menyimpan semua data dan *notebook* di komputer sendiri;
- ketika pembaca bekerja dengan data yang tidak boleh diunggah ke layanan daring; atau
- ketika pembaca ingin berlatih tanpa bergantung pada akun Google.

Semua contoh dan latihan dalam buku ini dirancang agar dapat dijalankan baik di *Google Colab* maupun di *Jupyter Notebook* lokal. Perbedaan utamanya hanya pada cara memulai: Colab diakses melalui peramban di `colab.research.google.com`, sedangkan Jupyter lokal diakses melalui peramban di `localhost:8888` setelah menjalankan perintah `jupyter notebook` dari terminal.

[Tangkapan layar halaman awal *Jupyter Notebook* di peramban lokal, menampilkan daftar berkas dan tombol New untuk membuat *notebook* baru.]
Gambar 3.6 Halaman Awal *Jupyter Notebook* di Komputer Lokal

### Langkah paling cepat memakai *Google Colab*
1. Buka `https://colab.research.google.com/`.
2. Masuk dengan akun Google.
3. Klik **New Notebook**.
4. Ketik kode berikut pada satu sel.

```python
print("Halo, Python!")
```

**Output**
```text
Halo, Python!
```

**Penjelasan**
Perintah `print()` menampilkan isi yang ada di dalam tanda kurung ke layar. Jika tulisan **Halo, Python!** muncul, berarti sel pertama sudah berhasil dijalankan.

5. Jalankan sel itu.
6. Jika hasilnya muncul, pembaca sudah siap praktik.

### Coba Perhatikan
Untuk banyak pembelajar pemula, jalur paling aman biasanya adalah **memulai dari Google Colab lebih dahulu**. Setelah terbiasa, pembaca dapat mencoba **Jupyter Notebook di komputer sendiri** agar tidak selalu bergantung pada internet. Menjalankan Python langsung dari terminal juga tetap berguna, terutama untuk skrip pendek atau tugas otomatis.

### Catatan Etis
Jika memakai Colab atau layanan daring lain, jangan memasukkan data pribadi, tugas rahasia, rekaman tanpa izin, atau materi berhak cipta yang tidak memiliki izin penggunaan. Pada tahap awal, gunakan teks latihan yang aman, singkat, dan tidak sensitif.

## 3.2 Mengenal Variabel, Tipe Data, dan Operasi Dasar
Setelah ruang praktik siap, langkah berikutnya adalah memahami bagaimana Python menyimpan informasi. Dalam Python, informasi biasanya disimpan dalam **variabel**. Variabel dapat dipahami sebagai nama yang kita berikan untuk menyimpan suatu nilai.

Contoh:

```python
teks = "Bahasa yang baik membantu pembaca memahami isi tulisan."
jumlah = 10
```

**Output**
```text
Tidak ada output.
```

**Penjelasan**
Kode ini baru menyimpan dua data ke dalam variabel. Variabel `teks` berisi *string* atau teks, sedangkan `jumlah` berisi *integer* atau bilangan bulat. Karena belum ada `print()`, belum ada hasil yang tampil di layar.

Pada contoh itu, `teks` menyimpan kalimat, sedangkan `jumlah` menyimpan angka.

### 3.2.1 Variabel
Pikirkan variabel sebagai label pada kotak. Di dalam kotak itu kita simpan sesuatu. Dalam analisis bahasa, sesuatu itu bisa berupa:
- satu kata,
- satu kalimat,
- satu paragraf,
- daftar kata,
- atau angka hasil hitung.

Contoh yang lebih kontekstual:

```python
judul_teks = "Pentingnya Membaca Kritis"
jumlah_kalimat = 5
rata_rata_kata = 14.2
```

**Output**
```text
Tidak ada output.
```

**Penjelasan**
Contoh ini menunjukkan bahwa satu program dapat menyimpan beberapa jenis data sekaligus. `judul_teks` menyimpan teks, `jumlah_kalimat` menyimpan bilangan bulat, dan `rata_rata_kata` menyimpan bilangan desimal.

### 3.2.2 Tipe data dasar
Beberapa tipe data dasar yang paling penting untuk tahap awal adalah:

| Tipe data | Fungsi | Contoh |
|---|---|---|
| `str` | menyimpan teks | `"bahasa"` |
| `int` | menyimpan bilangan bulat | `12` |
| `float` | menyimpan bilangan desimal | `12.5` |
| `bool` | menyimpan nilai benar atau salah | `True`, `False` |
| `list` | menyimpan kumpulan data | `["kata", "kalimat"]` |

Contoh memeriksa tipe data:

```python
teks = "Puisi ini memakai banyak kata alam."
angka = 7
print(type(teks))
print(type(angka))
```

**Output**
```text
<class 'str'>
<class 'int'>
```

**Penjelasan**
Baris pertama menunjukkan bahwa `teks` bertipe `str`, yaitu data teks. Baris kedua menunjukkan bahwa `angka` bertipe `int`, yaitu bilangan bulat.

### 3.2.3 Operasi dasar
*Python* juga dapat dipakai untuk operasi sederhana.

```python
jumlah_teks = 12
jumlah_baru = jumlah_teks + 3
print(jumlah_baru)
```

**Output**
```text
15
```

**Penjelasan**
Program ini menjumlahkan nilai `12` dengan `3`, lalu menyimpan hasilnya ke variabel `jumlah_baru`. Ketika dicetak, hasilnya adalah `15`.

Untuk teks, kita bisa menggabungkan *string* (data teks).

```python
kata1 = "linguistik"
kata2 = "komputasi"
frasa = kata1 + " " + kata2
print(frasa)
```

**Output**
```text
linguistik komputasi
```

**Penjelasan**
Tanda `+` pada contoh ini dipakai untuk menggabungkan dua *string*. Bagian `" "` menambahkan satu spasi di antara kedua kata.

### Contoh Nyata
Bayangkan kita ingin menyimpan satu paragraf bacaan sebagai data awal. Kita bisa menuliskannya seperti ini:

```python
paragraf = "Kita perlu mengenal data teks agar mampu melihat pola kebahasaan secara lebih sistematis."
print(paragraf)
```

**Output**
```text
Kita perlu mengenal data teks agar mampu melihat pola kebahasaan secara lebih sistematis.
```

**Penjelasan**
Di sini paragraf disimpan lebih dahulu ke dalam variabel `paragraf`, lalu ditampilkan kembali dengan `print()`. Langkah sederhana ini penting karena setelah teks masuk ke variabel, teks itu bisa diolah lebih lanjut.

Dengan satu langkah ini, paragraf yang semula hanya dibaca biasa sekarang sudah bisa diolah lebih lanjut.

### Latihan Singkat
Cobalah ketik kode berikut, lalu ganti isinya dengan kalimat sendiri.

```python
nama_mata_kuliah = "Linguistik Komputasi"
jumlah_pembelajar = 32
print(nama_mata_kuliah)
print(jumlah_pembelajar)
```

**Output**
```text
Linguistik Komputasi
32
```

**Penjelasan**
Baris pertama menampilkan teks nama mata kuliah. Baris kedua menampilkan angka jumlah pembelajar. Contoh ini membantu kita melihat perbedaan keluaran antara data teks dan data angka.

[Cuplikan visual notebook yang menampilkan sel kode sederhana berisi variabel teks dan angka, lalu keluaran hasil `print`.]
Gambar 3.2 Contoh Sel Kode Python Dasar untuk Menyimpan Teks dan Nilai

## 3.3 String, List, dan Pengolahan Teks Sederhana
Pada tahap awal analisis bahasa, dua tipe data yang paling sering dipakai adalah **string** dan **list**.

### 3.3.1 String
*String* adalah data teks. Dalam bahasa sederhana, *string* dapat dipahami sebagai teks yang disimpan di dalam program. Satu kata, satu frasa, atau satu paragraf dapat disimpan sebagai *string*.

```python
teks = "Pembelajaran bahasa dapat dibantu oleh analisis teks sederhana."
print(teks)
```

**Output**
```text
Pembelajaran bahasa dapat dibantu oleh analisis teks sederhana.
```

**Penjelasan**
Variabel `teks` menyimpan satu kalimat sebagai *string*. Ketika dicetak, Python menampilkan isi kalimat itu apa adanya.

Beberapa operasi *string* yang sangat berguna:

#### Mengubah huruf menjadi huruf kecil
```python
teks = "Bahasa Indonesia"
print(teks.lower())
```

**Output**
```text
bahasa indonesia
```

**Penjelasan**
Metode `.lower()` mengubah semua huruf menjadi huruf kecil. Langkah ini sering dipakai agar perhitungan kata lebih konsisten.

#### Mengubah huruf menjadi huruf besar
```python
teks = "Bahasa Indonesia"
print(teks.upper())
```

**Output**
```text
BAHASA INDONESIA
```

**Penjelasan**
Metode `.upper()` mengubah semua huruf menjadi huruf besar. Ini berguna, misalnya, saat pembaca ingin menonjolkan bentuk tertentu atau memeriksa konsistensi penulisan.

#### Mengganti bagian teks
```python
teks = "Saya suka analisa teks."
teks_baru = teks.replace("analisa", "analisis")
print(teks_baru)
```

**Output**
```text
Saya suka analisis teks.
```

**Penjelasan**
Metode `.replace()` mengganti bagian teks lama dengan teks baru. Pada contoh ini, kata `analisa` diganti menjadi `analisis`.

#### Menghitung jumlah karakter
```python
teks = "Bahasa"
print(len(teks))
```

**Output**
```text
6
```

**Penjelasan**
Fungsi `len()` menghitung jumlah karakter dalam *string*. Kata `Bahasa` terdiri atas enam karakter.

### 3.3.2 Memecah teks menjadi daftar kata
Salah satu langkah paling penting dalam analisis bahasa adalah memecah teks menjadi kata-kata. Untuk langkah awal, kita bisa memakai `.split()` (metode untuk memisahkan teks berdasarkan spasi).

```python
kalimat = "Kita belajar linguistik komputasi di kelas."
kata_kata = kalimat.split()
print(kata_kata)
```

**Output**
```text
['Kita', 'belajar', 'linguistik', 'komputasi', 'di', 'kelas.']
```

**Penjelasan**
Metode `.split()` memecah satu kalimat menjadi daftar bagian berdasarkan spasi. Karena itu hasilnya bukan lagi satu *string*, melainkan *list* yang berisi enam item.

Hasilnya bukan lagi satu *string*, melainkan **list**.

### 3.3.3 List
*List* adalah kumpulan item yang disimpan dalam satu variabel. Dalam bahasa sederhana, *list* dapat dipahami sebagai daftar data.

```python
kata_kata = ["kita", "belajar", "linguistik", "komputasi"]
print(kata_kata)
```

**Output**
```text
['kita', 'belajar', 'linguistik', 'komputasi']
```

**Penjelasan**
Daftar ini berisi empat item. Tanda kurung siku `[]` menunjukkan bahwa data tersebut adalah *list*.

Kita bisa mengambil item tertentu dengan indeks.

```python
print(kata_kata[0])
print(kata_kata[1])
```

**Output**
```text
kita
belajar
```

**Penjelasan**
Python membaca indeks mulai dari `0`, bukan dari `1`. Karena itu, `kata_kata[0]` berarti item pertama, sedangkan `kata_kata[1]` berarti item kedua.

### 3.3.4 Menghitung jumlah kata
Setelah teks dipecah menjadi *list*, kita bisa menghitung jumlah katanya.

```python
kalimat = "Kita belajar linguistik komputasi di kelas."
kata_kata = kalimat.split()
print(len(kata_kata))
```

**Output**
```text
6
```

**Penjelasan**
Setelah kalimat dipecah menjadi daftar kata, `len(kata_kata)` menghitung jumlah item di dalam daftar. Hasilnya `6` karena ada enam kata yang terbaca oleh `.split()`.

### 3.3.5 Contoh kontekstual untuk pembelajaran bahasa
Misalnya kita ingin menghitung jumlah kata dalam satu paragraf bacaan.

```python
paragraf = "Kita dapat memakai data teks untuk melihat kosakata dominan, pola kalimat, dan pengulangan kata."
kata_kata = paragraf.split()
print("Jumlah kata:", len(kata_kata))
print("Daftar kata:", kata_kata)
```

**Output**
```text
Jumlah kata: 14
Daftar kata: ['Kita', 'dapat', 'memakai', 'data', 'teks', 'untuk', 'melihat', 'kosakata', 'dominan,', 'pola', 'kalimat,', 'dan', 'pengulangan', 'kata.']
```

**Penjelasan**
Contoh ini memperlihatkan dua hasil sekaligus. Baris pertama menunjukkan jumlah kata yang terbaca. Baris kedua menunjukkan daftar katanya. Tanda baca seperti koma dan titik masih menempel pada beberapa kata, sehingga pada bab berikutnya teks perlu dibersihkan lebih lanjut.

### Kesalahan Umum
Pemula sering mengira bahwa `.split()` selalu menghasilkan token yang benar-benar bersih. Padahal tanda baca seperti koma dan titik masih bisa ikut menempel. Karena itu, pada bab-bab berikutnya kita akan belajar membersihkan teks dengan lebih rapi.

### Contoh Nyata
Jika kita ingin memeriksa apakah tulisan kita terlalu sering memakai kata “bahasa”, kita bisa mulai dengan memecah teks dan melihat daftar katanya. Ini belum analisis yang sempurna, tetapi cukup baik untuk latihan awal.

## 3.4 Perulangan dan Percabangan untuk Analisis Bahasa
Ketika data semakin banyak, kita tidak mungkin memeriksa tiap item satu per satu secara manual. Di sinilah **perulangan** dan **percabangan** menjadi berguna.

### 3.4.1 Perulangan `for`
Perulangan `for` dipakai untuk memproses item satu per satu dari sebuah urutan data, misalnya daftar kata (Python Software Foundation, 2026c).

```python
kata_kata = ["kita", "belajar", "bahasa"]
for kata in kata_kata:
    print(kata)
```

**Output**
```text
kita
belajar
bahasa
```

**Penjelasan**
Perulangan `for` membaca isi daftar satu per satu. Setiap item yang sedang dibaca disimpan sementara ke variabel `kata`, lalu langsung dicetak.

### 3.4.2 Percabangan `if`
Percabangan `if` membantu kita mengambil keputusan berdasarkan kondisi tertentu.

```python
kata = "linguistik"
if len(kata) > 8:
    print("Kata ini cukup panjang")
```

**Output**
```text
Kata ini cukup panjang
```

**Penjelasan**
Panjang kata `linguistik` adalah 10 karakter. Karena `10` lebih besar daripada `8`, kondisi pada `if` bernilai benar dan pesan di dalamnya ditampilkan.

### 3.4.3 Menggabungkan `for` dan `if`
Gabungan keduanya sangat berguna untuk analisis kebahasaan sederhana.

Contoh: menampilkan kata yang panjangnya lebih dari 6 huruf.

```python
kata_kata = ["kelas", "pembelajar", "bahasa", "komputasi", "teks"]
for kata in kata_kata:
    if len(kata) > 6:
        print(kata)
```

**Output**
```text
pembelajar
komputasi
```

**Penjelasan**
Program ini memeriksa setiap kata di dalam daftar. Hanya kata yang panjangnya lebih dari enam huruf yang ditampilkan. Karena itu, yang muncul hanya `pembelajar` dan `komputasi`.

### 3.4.4 Contoh kontekstual: mencari pengulangan kata target
```python
paragraf = "Bahasa yang baik membantu bahasa tulis menjadi lebih jelas bagi pembaca bahasa kedua."
kata_kata = paragraf.lower().split()
for kata in kata_kata:
    if kata == "bahasa":
        print("Ditemukan:", kata)
```

**Output**
```text
Ditemukan: bahasa
Ditemukan: bahasa
Ditemukan: bahasa
```

**Penjelasan**
Teks lebih dahulu diubah ke huruf kecil agar bentuk `Bahasa` dan `bahasa` diperlakukan sama. Setelah itu, program menelusuri setiap kata dan mencetak setiap kemunculan kata target `bahasa`.

### 3.4.5 Contoh kontekstual: mengelompokkan kalimat pendek dan panjang
```python
kalimat_list = [
    "Kita membaca puisi.",
    "Kita menganalisis pilihan kata dalam puisi modern.",
    "Kita membahas makna kontekstual kata."
]

for kalimat in kalimat_list:
    jumlah_kata = len(kalimat.split())
    if jumlah_kata <= 4:
        print("Kalimat pendek:", kalimat)
    else:
        print("Kalimat lebih panjang:", kalimat)
```

**Output**
```text
Kalimat pendek: Kita membaca puisi.
Kalimat lebih panjang: Kita menganalisis pilihan kata dalam puisi modern.
Kalimat lebih panjang: Kita membahas makna kontekstual kata.
```

**Penjelasan**
Setiap kalimat dihitung jumlah katanya. Jika jumlah katanya `4` atau kurang, kalimat diberi label `Kalimat pendek`. Jika lebih dari itu, kalimat masuk ke kelompok `Kalimat lebih panjang`.

### Coba Perhatikan
Pada tahap ini, kita belum mengejar program yang canggih. Yang lebih penting ialah memahami logika dasarnya: Python dapat membaca item satu per satu dan memberi respons sesuai syarat yang kita tentukan.

## 3.5 Fungsi Sederhana untuk Tugas Kebahasaan
Saat satu langkah perlu diulang berkali-kali, sebaiknya kita menaruhnya dalam **fungsi**. Fungsi adalah blok kode yang diberi nama agar dapat dipakai kembali.

### 3.5.1 Fungsi dasar
```python
def sapa():
    print("Halo, pembelajar bahasa!")

sapa()
```

**Output**
```text
Halo, pembelajar bahasa!
```

**Penjelasan**
Pada contoh ini, fungsi `sapa()` hanya berisi satu perintah, yaitu menampilkan salam. Output baru muncul ketika fungsi itu dipanggil dengan `sapa()`.

### 3.5.2 Fungsi dengan masukan
```python
def hitung_jumlah_kata(teks):
    kata_kata = teks.split()
    return len(kata_kata)

kalimat = "Kita belajar analisis teks sederhana."
print(hitung_jumlah_kata(kalimat))
```

**Output**
```text
5
```

**Penjelasan**
Fungsi `hitung_jumlah_kata()` menerima satu teks, memecahnya menjadi daftar kata, lalu mengembalikan jumlah itemnya. Untuk kalimat ini, hasilnya `5`.

### 3.5.3 Fungsi untuk tugas kebahasaan sederhana
Contoh: fungsi untuk menghitung berapa kali kata tertentu muncul.

```python
def hitung_kemunculan(teks, target):
    kata_kata = teks.lower().split()
    jumlah = 0
    for kata in kata_kata:
        if kata == target.lower():
            jumlah += 1
    return jumlah

paragraf = "Bahasa yang baik membuat bahasa tulis lebih jelas dan bahasa lisan lebih terarah."
print(hitung_kemunculan(paragraf, "bahasa"))
```

**Output**
```text
3
```

**Penjelasan**
Fungsi ini menghitung berapa kali kata target muncul di dalam teks. Pada paragraf contoh, kata `bahasa` muncul tiga kali sehingga hasil yang dicetak adalah `3`.

### 3.5.4 Fungsi untuk mengambil kata panjang
```python
def ambil_kata_panjang(teks, batas):
    hasil = []
    for kata in teks.split():
        if len(kata) >= batas:
            hasil.append(kata)
    return hasil

teks = "Linguistik komputasi membantu pembelajaran bahasa menjadi lebih sistematis."
print(ambil_kata_panjang(teks, 8))
```

**Output**
```text
['Linguistik', 'komputasi', 'membantu', 'pembelajaran', 'sistematis.']
```

**Penjelasan**
Fungsi ini mengumpulkan semua kata yang panjangnya minimal delapan karakter. Hasil akhirnya berupa *list* baru yang hanya berisi kata-kata panjang.

### 3.5.5 Mengapa fungsi penting?
Bagi pembelajar bahasa, *fungsi* berguna karena:
- membuat pekerjaan berulang menjadi lebih rapi;
- membantu mengurangi kesalahan penyalinan kode;
- memudahkan pembaca memahami langkah kerja; dan
- menjadi dasar bagi analisis yang lebih besar pada bab-bab berikutnya.

### Contoh Nyata
Bayangkan kita harus menghitung jumlah kata pada 15 paragraf bacaan. Tanpa fungsi, kita mungkin menulis langkah yang sama berulang kali. Dengan fungsi, kita cukup menulis rumusnya sekali, lalu memakainya berkali-kali.

[Diagram alur kecil yang menunjukkan data teks masuk ke fungsi, diproses, lalu menghasilkan keluaran seperti jumlah kata atau kata target.]
Gambar 3.3 Fungsi Sederhana untuk Memproses Data Teks

## Ringkasan
Bab ini memperkenalkan *Python* sebagai alat kerja dasar untuk analisis bahasa. Python penting bukan karena semua pembelajar harus menjadi *programmer*, melainkan karena bahasa pemrograman ini membantu pembaca menyimpan, mengolah, dan memeriksa data teks secara lebih teratur. Untuk memulai, pembaca dapat memilih tiga jalur: langsung memakai *Google Colab* untuk kemudahan awal, memasang *Jupyter Notebook* di komputer sendiri untuk pengalaman *notebook* interaktif tanpa tergantung internet, atau menjalankan *Python* langsung dari terminal untuk kebutuhan yang lebih ringkas.

Bab ini juga memperkenalkan beberapa konsep dasar, yaitu *variabel*, tipe data, operasi sederhana, *string*, *list*, perulangan, percabangan, dan *fungsi*. Semua konsep itu dijelaskan melalui contoh yang dekat dengan pembelajaran bahasa, seperti menghitung jumlah kata, memecah kalimat menjadi daftar kata, mencari kata target, dan mengelompokkan kalimat berdasarkan panjangnya.

Yang paling penting, pembaca tidak perlu mengejar kode yang rumit pada tahap ini. Fokus utama bab ini adalah membangun rasa akrab dengan logika Python agar pada bab-bab berikutnya pembaca siap memakai Python dan *Google Colab* untuk analisis teks yang lebih kontekstual.

## Latihan Akhir Bab
### Latihan 1. Variabel dan tipe data
Buatlah tiga variabel berikut.
1. satu variabel berisi judul bacaan,
2. satu variabel berisi jumlah pembelajar,
3. satu variabel berisi satu kalimat pendek.

Lalu cetak ketiganya.

### Latihan 2. String dan list
Gunakan satu kalimat berikut atau buat kalimat sendiri.

```python
kalimat = "Kita perlu mengenal data teks."
```

**Output awal**
```text
Tidak ada output.
```

**Penjelasan**
Potongan kode ini baru menyimpan satu kalimat ke variabel `kalimat`. Output akan muncul setelah pembaca menambahkan perintah seperti `print(kalimat)`, `.lower()`, `.split()`, atau `len()`.

Lakukan langkah berikut.
1. Ubah semua huruf menjadi huruf kecil.
2. Pecah kalimat menjadi daftar kata.
3. Hitung jumlah katanya.

### Latihan 3. Perulangan dan percabangan
Buat daftar kata berikut.

```python
kata_kata = ["puisi", "pembelajaran", "teks", "linguistik", "kelas"]
```

**Output awal**
```text
Tidak ada output.
```

**Penjelasan**
Potongan kode ini hanya menyiapkan daftar kata untuk latihan. Output baru akan muncul setelah pembaca menambahkan perulangan dan `print()` sesuai instruksi latihan.

Tampilkan hanya kata yang panjangnya lebih dari 5 huruf.

### Latihan 4. Fungsi sederhana
Buat fungsi bernama `hitung_jumlah_kata(teks)` yang menerima satu kalimat dan mengembalikan jumlah katanya. Uji fungsi itu dengan dua kalimat berbeda.

## Proyek Mini
### Proyek Mini 3. Membaca Satu Paragraf dengan Python
**Tujuan pembelajaran**  
Mempraktikkan langkah awal pengolahan teks menggunakan Python atau *Google Colab* dengan data yang dekat dengan pembelajaran bahasa.

**Alat yang digunakan**  
*Python* di komputer pribadi atau *Google Colab*.

**Instruksi**
1. Pilih satu paragraf pendek dari bahan ajar, tulisan sendiri, atau teks latihan yang aman.
2. Simpan paragraf itu dalam satu variabel Python bernama `paragraf`.
3. Tampilkan paragraf tersebut.
4. Ubah paragraf menjadi huruf kecil.
5. Pecah paragraf menjadi daftar kata menggunakan `.split()`.
6. Hitung jumlah kata.
7. Buat fungsi sederhana untuk menghitung berapa kali satu kata target muncul.
8. Uji fungsi itu dengan satu kata penting dari paragraf.

**Keluaran yang diharapkan**  
Kode sederhana yang dapat dijalankan dan menampilkan:
- paragraf asli,
- paragraf dalam huruf kecil,
- daftar kata,
- jumlah kata, dan
- jumlah kemunculan satu kata target.

**Refleksi**  
Bagian mana yang terasa paling mudah? Bagian mana yang masih membingungkan? Menurut pembaca, bagaimana langkah sederhana ini dapat membantu analisis bahasa pada bab-bab berikutnya?


## 🧠 Istilah yang dipelajari pada bab ini
- *Python*: bahasa pemrograman yang banyak dipakai untuk analisis data dan pengolahan teks.
- *Google Colab*: layanan daring untuk menjalankan notebook Python melalui peramban.
- *Jupyter Notebook*: ruang kerja interaktif yang memadukan kode, teks, dan keluaran hasil. Dapat dijalankan di komputer sendiri (lokal) atau melalui layanan daring seperti *Google Colab*. Dikembangkan oleh Project Jupyter (Kluyver et al., 2016).
- *variabel*: nama yang dipakai untuk menyimpan nilai atau data.
- *string*: tipe data teks dalam Python.
- *list*: tipe data berupa kumpulan item dalam satu urutan.
- *float*: tipe data bilangan desimal.
- *bool*: tipe data logika yang berisi nilai benar atau salah.
- `for`: perintah perulangan untuk membaca item satu per satu.
- `if`: perintah percabangan untuk mengambil keputusan berdasarkan syarat tertentu.
- `.split()`: fungsi/metode untuk memecah teks menjadi bagian-bagian yang lebih kecil.
- *fungsi*: blok kode yang diberi nama agar dapat dipakai kembali.

## Sumber Gambar yang Perlu Disiapkan
- [Tangkapan layar halaman awal *Google Colab* dengan tombol New Notebook yang terlihat jelas, agar pembelajar memahami titik mulai praktik daring.]
  Gambar 3.4 Halaman Awal *Google Colab* untuk Praktik Pemula
- [Tangkapan layar jendela Command Prompt atau Terminal yang menampilkan hasil `python --version` atau `python3 --version` sebagai contoh verifikasi instalasi.]
  Gambar 3.5 Memeriksa Instalasi Python di Komputer Pribadi
