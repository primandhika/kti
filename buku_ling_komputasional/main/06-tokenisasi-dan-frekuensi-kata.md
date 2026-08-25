# Bab 6. Tokenisasi dan Frekuensi Kata

## Tujuan Pembelajaran
Setelah mempelajari bab ini, pembaca diharapkan mampu:

1. menjelaskan apa itu *tokenization* (*tokenisasi*) dan mengapa langkah ini penting dalam analisis bahasa;
2. memecah teks sederhana menjadi kata dan kalimat dengan pendekatan awal di *Python*;
3. menghitung jumlah kata dan jumlah kata unik dalam satu teks;
4. mengamati frekuensi kata untuk menemukan kosakata dominan; dan
5. membandingkan frekuensi kata pada dua teks sederhana untuk tujuan pembelajaran.

## Pengantar Bab
Pada bab sebelumnya, kita telah belajar membaca dan membersihkan teks. Tahap itu penting karena komputer tidak otomatis memahami bahwa huruf besar, spasi ganda, atau tanda baca yang menempel bisa mengganggu analisis. Setelah teks mulai rapi, pertanyaan berikutnya muncul: bagaimana teks itu kita pecah agar dapat dihitung, dibandingkan, dan diamati polanya?

Di sinilah *tokenization* (*tokenisasi*) menjadi penting. Secara sederhana, tokenisasi adalah proses memecah teks menjadi unit-unit yang dapat diolah, misalnya kata atau kalimat. Dalam praktik awal, langkah ini sering terasa sederhana. Namun, bagi pembelajar bahasa, tokenisasi justru membuka pintu ke banyak analisis penting, seperti menghitung kata dominan, mencari kosakata kunci, membandingkan dua bacaan, atau melihat pengulangan yang terlalu sering muncul dalam tulisan.

Bab ini melanjutkan alur dari Bab 5. Jika sebelumnya kita menyiapkan teks agar lebih bersih, sekarang kita mulai mengubah teks itu menjadi bahan analisis yang lebih terstruktur. Kita akan memecah teks menjadi kata dan kalimat, menghitung jumlah kata, membedakan kata total dan kata unik, mengamati frekuensi, lalu mencoba membandingkan dua teks secara sederhana. Semua contoh akan tetap dekat dengan pembelajaran bahasa, sastra, dan literasi kelas.

[Ilustrasi satu paragraf pendek yang dipecah menjadi potongan kata-kata dan kalimat dalam kotak-kotak terpisah. Visual menekankan perubahan dari teks utuh menjadi unit analisis yang dapat dihitung.]
Gambar 6.1 Dari Teks Utuh ke Kata dan Kalimat

## 6.1 Memecah Teks menjadi Kata dan Kalimat
Dalam analisis komputasional, teks sering perlu dipecah menjadi unit yang lebih kecil. Untuk tahap awal, dua unit yang paling berguna adalah kata dan kalimat. Dalam kajian korpus, pengamatan terhadap kata dan distribusinya memang sering menjadi langkah awal sebelum pembaca masuk ke konteks yang lebih luas (Meyer, 2023; McEnery & Hardie, 2012).

### 6.1.1 Memecah teks menjadi kata
Cara paling sederhana untuk memecah teks menjadi kata adalah memakai `.split()`. Metode ini memisahkan teks berdasarkan spasi.

```python
teks = "Bahasa yang baik membantu pembaca memahami gagasan dengan lebih jernih."
kata_kata = teks.split()
print(kata_kata)
```

**Output**
```text
['Bahasa', 'yang', 'baik', 'membantu', 'pembaca', 'memahami', 'gagasan', 'dengan', 'lebih', 'jernih.']
```

**Penjelasan**
Baris pertama menyimpan satu kalimat ke variabel `teks`. Baris kedua memakai `.split()` untuk memecah teks berdasarkan spasi. Hasilnya berupa *list*, yaitu daftar kata-kata yang dapat diproses lebih lanjut. Perhatikan bahwa tanda titik pada kata `jernih.` masih menempel. Ini mengingatkan kita bahwa tokenisasi sederhana sering perlu didahului atau diikuti pembersihan teks.

### 6.1.2 Tokenisasi sederhana untuk kalimat
Selain kata, pembaca juga sering perlu memecah teks menjadi kalimat. Untuk latihan awal, kita bisa memakai pendekatan sederhana dengan mengganti tanda seru dan tanda tanya menjadi titik, lalu memecah teks berdasarkan titik.

```python
teks = "Puisi ini singkat. Namun, suasananya pekat! Apakah pembaca merasakannya?"
sementara = teks.replace("!", ".").replace("?", ".")
kalimat = [k.strip() for k in sementara.split(".") if k.strip()]
print(kalimat)
```

**Output**
```text
['Puisi ini singkat', 'Namun, suasananya pekat', 'Apakah pembaca merasakannya']
```

**Penjelasan**
Kode ini belum sempurna, tetapi cukup untuk latihan awal. Tanda seru dan tanda tanya disamakan dulu menjadi titik. Setelah itu, teks dipecah berdasarkan titik. Hasil akhirnya adalah daftar kalimat sederhana. Pendekatan ini berguna untuk memahami logika dasar pemecahan kalimat sebelum pembaca memakai alat yang lebih canggih.

### 6.1.3 Tokenisasi sederhana tidak selalu cukup
Bagian ini penting. Dalam data nyata, kata bisa terhubung dengan tanda baca, singkatan, angka, atau bentuk campuran bahasa. Kalimat juga tidak selalu mudah dipisahkan hanya dengan titik. Namun, untuk pembelajaran awal, pendekatan sederhana seperti ini tetap berguna karena memperlihatkan logika dasarnya dengan jelas.

### Contoh Nyata
Bayangkan pembaca ingin memeriksa bahan bacaan untuk pembelajar BIPA. Dengan tokenisasi awal, pembaca bisa melihat apakah satu paragraf terlalu padat, apakah kalimatnya terlalu panjang, atau apakah ada banyak kata yang berulang. Langkah ini sederhana, tetapi langsung berkaitan dengan keputusan pedagogis.

## 6.2 Menghitung Jumlah Kata dan Kata Unik
Setelah teks berhasil dipecah menjadi kata, kita dapat mulai menghitung. Ada dua hitungan dasar yang sangat penting:
1. **jumlah kata total**, yaitu semua kata yang muncul dalam teks; dan
2. **jumlah kata unik**, yaitu bentuk kata yang berbeda.

Perbedaan ini penting. Sebuah teks bisa memiliki jumlah kata total yang besar, tetapi kata uniknya sedikit jika banyak pengulangan.

### 6.2.1 Menghitung jumlah kata total

```python
teks = "malam malam hujan turun di kelas bahasa"
kata_kata = teks.split()
print(len(kata_kata))
```

**Output**
```text
7
```

**Penjelasan**
`len(kata_kata)` menghitung berapa banyak item di dalam daftar kata. Pada contoh ini, jumlah kata total adalah tujuh. Kata `malam` dihitung dua kali karena memang muncul dua kali.

### 6.2.2 Menghitung jumlah kata unik
Untuk melihat variasi kosakata, kita dapat menghitung kata unik.

```python
teks = "malam malam hujan turun di kelas bahasa"
kata_unik = sorted(set(teks.split()))
print(kata_unik)
print("Jumlah kata unik:", len(kata_unik))
```

**Output**
```text
['bahasa', 'di', 'hujan', 'kelas', 'malam', 'turun']
Jumlah kata unik: 6
```

**Penjelasan**
`set(...)` dipakai untuk mengambil hanya bentuk kata yang berbeda. Setelah itu, `sorted(...)` dipakai agar hasilnya tampil berurutan dan lebih mudah dibaca. Dari tujuh kata total, ternyata hanya ada enam kata unik karena `malam` muncul dua kali.

### 6.2.3 Mengapa kata unik penting?
Dalam pembelajaran menulis atau membaca, kata unik dapat memberi petunjuk tentang variasi kosakata. Teks dengan banyak pengulangan belum tentu buruk, tetapi pengulangan yang terlalu dominan bisa memberi sinyal bahwa pilihan kata masih sempit. Dalam konteks pengajaran berbasis korpus, perhatian terhadap variasi leksikal seperti ini relevan untuk diskusi kosakata dan kualitas bahan ajar (O’Keeffe et al., 2007; Widodo et al., 2023).

### Coba Perhatikan
Jumlah kata total dan jumlah kata unik tidak menjawab semua pertanyaan. Namun, dua ukuran ini memberi pembaca titik mulai yang sangat berguna untuk melihat kepadatan dan variasi sebuah teks.

## 6.3 Frekuensi Kata dan Kosakata Dominan
Setelah mengetahui jumlah kata, pembaca biasanya ingin tahu kata mana yang paling sering muncul. Inilah yang disebut frekuensi kata. Dalam analisis korpus dasar, frekuensi sering menjadi langkah awal untuk melihat topik, fokus, atau kecenderungan kosakata dalam suatu teks (Meyer, 2023).

### 6.3.1 Menghitung frekuensi kata dengan kamus sederhana
Kita dapat membuat perhitungan frekuensi tanpa pustaka tambahan.

```python
teks = "bahasa data bahasa pembelajaran data bahasa"
frek = {}
for kata in teks.split():
    frek[kata] = frek.get(kata, 0) + 1

print(frek)
```

**Output**
```text
{'bahasa': 3, 'data': 2, 'pembelajaran': 1}
```

**Penjelasan**
Variabel `frek` adalah kamus kecil yang menyimpan pasangan kata dan jumlah kemunculannya. Jika sebuah kata belum pernah muncul, nilainya dimulai dari `0`. Lalu setiap kemunculan baru menambah hitungannya satu. Dengan cara ini, pembaca dapat melihat bahwa kata `bahasa` paling dominan pada teks contoh.

### 6.3.2 Menyusun kata dari yang paling sering muncul
Setelah frekuensi dihitung, hasilnya bisa diurutkan.

```python
teks = "bahasa data bahasa pembelajaran data bahasa"
frek = {}
for kata in teks.split():
    frek[kata] = frek.get(kata, 0) + 1

urutan = sorted(frek.items(), key=lambda item: item[1], reverse=True)
print(urutan)
```

**Output**
```text
[('bahasa', 3), ('data', 2), ('pembelajaran', 1)]
```

**Penjelasan**
`frek.items()` mengubah kamus menjadi daftar pasangan `(kata, jumlah)`. Lalu `sorted(..., reverse=True)` mengurutkannya dari frekuensi terbesar ke terkecil. Hasil seperti ini sangat berguna untuk menemukan kosakata dominan dalam bacaan atau tulisan.

### 6.3.3 Menafsirkan kata dominan dengan hati-hati
Kata yang sering muncul belum tentu otomatis menjadi kata paling penting. Bisa jadi kata itu dominan karena topiknya memang sering dibahas. Bisa juga karena teks kurang bervariasi. Karena itu, frekuensi perlu dibaca bersama konteks. Inilah sebabnya analisis frekuensi sebaiknya tidak berhenti pada hitungan mentah saja.

### Contoh Nyata
Jika pembaca sedang menyiapkan bahan diskusi dari satu artikel, daftar kata dominan dapat membantu memilih kosakata yang patut dibahas sebelum membaca. Namun, pembaca tetap perlu menilai apakah kata yang sering muncul benar-benar kata kunci, atau hanya kata umum yang kebetulan banyak dipakai.

[Diagram batang sederhana yang memperlihatkan tiga kata dengan frekuensi berbeda, misalnya `bahasa`, `data`, dan `pembelajaran`. Visual menekankan hubungan antara jumlah kemunculan dan interpretasi awal.]
Gambar 6.2 Contoh Frekuensi Kata dalam Teks Pendek

## 6.4 Stopwords dan Penyaringan Kata Umum
Ketika menghitung frekuensi kata, hasil teratas sering diisi kata yang sangat umum, seperti `yang`, `dan`, `di`, atau `ini`. Kata-kata seperti ini sering disebut *stopwords*, yaitu kata umum yang dalam beberapa jenis analisis bisa disaring agar perhatian pembaca lebih tertuju pada kata-kata isi.

### 6.4.1 Mengapa kata umum kadang perlu disaring?
Jika pembaca ingin mencari topik utama dalam bacaan, kata umum sering kurang membantu. Misalnya, jika daftar frekuensi didominasi oleh `yang`, `dan`, atau `di`, pembaca belum tentu mendapat gambaran yang jelas tentang isi teks. Dengan menyaring kata umum, kata-kata seperti `bacaan`, `tema`, `kunci`, atau `puisi` bisa terlihat lebih jelas.

### 6.4.2 Menyaring stopwords sederhana

```python
teks = "yang paling penting dari bacaan ini adalah kata kunci dan tema utama"
stopwords = {"yang", "dan", "di", "ke", "dari", "ini", "itu", "adalah"}
hasil = [kata for kata in teks.split() if kata not in stopwords]
print(hasil)
```

**Output**
```text
['paling', 'penting', 'bacaan', 'kata', 'kunci', 'tema', 'utama']
```

**Penjelasan**
Pada contoh ini, `stopwords` disimpan sebagai *set* agar pencariannya cepat. Daftar hasil lalu hanya berisi kata-kata yang tidak termasuk dalam kelompok kata umum tersebut. Dengan cara ini, pembaca dapat melihat kosakata isi dengan lebih jelas.

### 6.4.3 Stopwords tidak selalu harus dibuang
Bagian ini sangat penting. Dalam beberapa analisis, kata umum justru penting. Misalnya, jika pembaca ingin meneliti gaya bahasa, kohesi, atau struktur kalimat, kata seperti `dan`, `yang`, atau `di` bisa memberi informasi yang berguna. Karena itu, penyaringan *stopwords* harus selalu mengikuti tujuan analisis.

### Kesalahan Umum
Pemula kadang mengira bahwa daftar *stopwords* selalu sama untuk semua kebutuhan. Padahal daftar itu sangat bergantung pada bahasa, jenis teks, dan tujuan pembelajaran. Daftar untuk artikel berita belum tentu cocok untuk puisi, dialog, atau tulisan pembelajar.

## 6.5 Membandingkan Frekuensi pada Dua Teks
Salah satu latihan yang sangat berguna dalam pembelajaran bahasa adalah membandingkan dua teks. Dengan perbandingan sederhana, pembaca dapat melihat bahwa teks berbeda memiliki kosakata dominan yang berbeda pula.

### 6.5.1 Menyiapkan dua teks sederhana
Misalnya kita membandingkan satu teks yang bernuansa sastra dan satu teks yang bernuansa akademik.

```python
teks_a = "puisi ini memakai kata malam malam hujan dan sepi"
teks_b = "artikel ini memakai kata data analisis dan pembelajaran"

print(teks_a)
print(teks_b)
```

**Output**
```text
puisi ini memakai kata malam malam hujan dan sepi
artikel ini memakai kata data analisis dan pembelajaran
```

**Penjelasan**
Kedua teks ini sengaja dibuat pendek agar perbedaannya mudah diamati. Teks pertama lebih dekat ke suasana sastra, sedangkan teks kedua lebih dekat ke bahasa akademik.

### 6.5.2 Menghitung frekuensi masing-masing teks

```python
def hitung_frekuensi(teks):
    frek = {}
    for kata in teks.split():
        frek[kata] = frek.get(kata, 0) + 1
    return frek

frek_a = hitung_frekuensi(teks_a)
frek_b = hitung_frekuensi(teks_b)

print(frek_a)
print(frek_b)
```

**Output**
```text
{'puisi': 1, 'ini': 1, 'memakai': 1, 'kata': 1, 'malam': 2, 'hujan': 1, 'dan': 1, 'sepi': 1}
{'artikel': 1, 'ini': 1, 'memakai': 1, 'kata': 1, 'data': 1, 'analisis': 1, 'dan': 1, 'pembelajaran': 1}
```

**Penjelasan**
Fungsi `hitung_frekuensi()` membuat perhitungan yang sama untuk dua teks sekaligus. Hasilnya langsung memperlihatkan perbedaan: kata `malam` dominan pada teks pertama, sedangkan kata `data`, `analisis`, dan `pembelajaran` muncul pada teks kedua.

### 6.5.3 Membandingkan kata target tertentu
Agar lebih terarah, kita bisa membandingkan beberapa kata target saja.

```python
kata_target = ["malam", "hujan", "data", "pembelajaran"]
for kata in kata_target:
    print(kata, frek_a.get(kata, 0), frek_b.get(kata, 0))
```

**Output**
```text
malam 2 0
hujan 1 0
data 0 1
pembelajaran 0 1
```

**Penjelasan**
Setiap baris memperlihatkan satu kata target, lalu dua angka setelahnya. Angka pertama menunjukkan frekuensi pada `teks_a`, sedangkan angka kedua menunjukkan frekuensi pada `teks_b`. Dengan cara ini, pembaca dapat melihat perbedaan kosakata antarteks secara lebih terarah.

### Contoh Nyata
Dalam pembelajaran membaca, perbandingan seperti ini berguna untuk menunjukkan bahwa teks sastra dan teks akademik sering menonjolkan jenis kosakata yang berbeda. Dalam pembelajaran menulis, teknik yang sama bisa dipakai untuk membandingkan draf awal dan draf revisi agar pembaca melihat apakah variasi kosakata meningkat.

### Trivia
Perbandingan dua teks pendek saja sudah cukup untuk melatih pembaca melihat bahwa data bahasa tidak netral. Setiap teks membawa pilihan kata yang mencerminkan tujuan, topik, dan situasi pemakaiannya.

[Ilustrasi dua kolom berdampingan yang menampilkan daftar kata dominan dari dua teks berbeda, satu bernuansa sastra dan satu bernuansa akademik.]
Gambar 6.3 Membandingkan Kosakata Dominan pada Dua Teks

## Ringkasan
Bab ini membahas langkah penting setelah pembersihan teks, yaitu tokenisasi dan penghitungan frekuensi kata. Pembaca belajar memecah teks menjadi kata dan kalimat, lalu menggunakan hasil pemecahan itu untuk menghitung jumlah kata total, jumlah kata unik, dan frekuensi kemunculan kata.

Bab ini juga memperlihatkan bahwa frekuensi kata dapat membantu menemukan kosakata dominan, tetapi hasil hitungan tetap harus dibaca bersama konteks. Selain itu, pembaca diperkenalkan pada *stopwords* dan penyaringan kata umum agar analisis lebih terarah ketika tujuan utamanya adalah menemukan kata isi.

Di bagian akhir, bab ini menunjukkan bahwa dua teks sederhana dapat dibandingkan untuk melihat perbedaan kosakata. Dengan begitu, pembaca tidak hanya belajar menghitung, tetapi juga mulai melihat bagaimana data bahasa dapat dipakai untuk membaca perbedaan topik, gaya, dan tujuan teks.

## Latihan Akhir Bab
### Latihan 1. Memecah teks menjadi kata
Gunakan satu paragraf pendek dari bahan bacaan, lalu:
1. simpan paragraf itu dalam variabel `teks`,
2. pecah menjadi daftar kata dengan `.split()`, dan
3. tampilkan hasilnya.

### Latihan 2. Memecah teks menjadi kalimat
Gunakan satu teks yang memiliki minimal tiga kalimat, lalu:
1. ganti tanda seru dan tanda tanya menjadi titik,
2. pecah teks menjadi daftar kalimat sederhana, dan
3. hitung jumlah kalimat yang terbaca.

### Latihan 3. Menghitung kata total dan kata unik
Ambil satu teks pendek, lalu:
1. hitung jumlah kata total,
2. hitung jumlah kata unik, dan
3. jelaskan apakah teks itu tampak banyak pengulangan atau cukup bervariasi.

### Latihan 4. Menghitung frekuensi kata
Gunakan satu paragraf pendek yang sudah dibersihkan, lalu:
1. hitung frekuensi setiap kata,
2. urutkan hasilnya dari yang paling sering muncul, dan
3. pilih tiga kata yang menurut pembaca paling penting untuk dibahas.

### Latihan 5. Menyaring kata umum
Gunakan satu kalimat atau paragraf yang memuat banyak kata umum, lalu:
1. buat daftar *stopwords* sederhana,
2. saring teks itu, dan
3. bandingkan hasil sebelum dan sesudah penyaringan.

### Latihan 6. Membandingkan dua teks
Pilih dua teks pendek dengan topik atau genre berbeda, lalu:
1. hitung frekuensi masing-masing,
2. pilih minimal empat kata target, dan
3. bandingkan kemunculannya pada kedua teks.

## Proyek Mini
### Proyek Mini 6. Menemukan Kosakata Dominan dalam Bacaan
**Tujuan pembelajaran**  
Menerapkan tokenisasi, penghitungan kata, penyaringan sederhana, dan perbandingan frekuensi untuk membaca kecenderungan kosakata dalam satu atau dua teks.

**Alat yang digunakan**  
*Google Colab* dan satu atau dua teks latihan yang sudah cukup bersih.

**Instruksi**
1. Pilih satu bacaan utama, atau dua bacaan pendek jika ingin membuat perbandingan.
2. Bersihkan teks secara dasar bila masih perlu.
3. Pecah teks menjadi daftar kata.
4. Hitung jumlah kata total dan jumlah kata unik.
5. Hitung frekuensi kata.
6. Jika perlu, saring beberapa kata umum dengan daftar *stopwords* sederhana.
7. Tampilkan lima kata yang paling dominan.
8. Jika memakai dua teks, bandingkan minimal empat kata target pada kedua teks.
9. Tuliskan satu paragraf interpretasi tentang apa yang dapat dibaca dari hasil itu.

**Keluaran yang diharapkan**  
Satu *notebook* Colab yang berisi:
- teks yang dianalisis,
- daftar kata hasil tokenisasi,
- jumlah kata total,
- jumlah kata unik,
- frekuensi kata,
- hasil penyaringan sederhana bila dipakai, dan
- interpretasi singkat atas kosakata dominan.

**Refleksi**  
Apakah kata yang paling sering muncul otomatis menjadi kata yang paling penting? Dalam konteks pembelajaran bahasa, kapan hasil frekuensi kata membantu, dan kapan pembaca tetap perlu kembali membaca konteks kalimat atau paragrafnya?

## 🧠 Istilah yang dipelajari pada bab ini
- *tokenization* (*tokenisasi*): proses memecah teks menjadi unit-unit yang lebih kecil, misalnya kata atau kalimat.
- token: satu unit hasil tokenisasi, misalnya satu kata atau satu simbol tertentu.
- kata unik: bentuk kata yang berbeda, tanpa menghitung pengulangan bentuk yang sama.
- frekuensi kata: jumlah kemunculan suatu kata dalam teks.
- kosakata dominan: kata yang muncul paling sering atau paling menonjol dalam suatu data.
- *stopwords*: kata-kata umum yang dalam beberapa analisis dapat disaring agar kata isi terlihat lebih jelas.
- penyaringan kata umum: proses membuang kata yang dianggap terlalu umum untuk tujuan analisis tertentu.
- `set()`: struktur data yang berguna untuk mengambil unsur unik.
- `sorted()`: fungsi untuk mengurutkan data.
- `dict.get()`: cara mengambil nilai dari kamus dengan nilai awal tertentu jika kunci belum ada.

## Sumber Gambar yang Perlu Disiapkan
- [Tangkapan layar *Google Colab* yang menampilkan satu paragraf diubah menjadi daftar kata melalui `.split()`, dengan keluaran list terlihat jelas di bawah sel kode.]
  Gambar 6.4 Tokenisasi Kata Sederhana di *Google Colab*
- [Tangkapan layar keluaran perhitungan jumlah kata total dan jumlah kata unik dari satu teks pendek, dengan penanda visual yang membedakan keduanya.]
  Gambar 6.5 Membandingkan Kata Total dan Kata Unik
- [Tangkapan layar atau diagram sederhana yang menampilkan hasil perbandingan frekuensi kata pada dua teks berbeda, misalnya teks sastra dan teks akademik.]
  Gambar 6.6 Perbandingan Frekuensi Kata pada Dua Teks
