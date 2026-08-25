# Bab 7. Kolokasi, Konkordansi, dan Konteks Kata

## Tujuan Pembelajaran
Setelah mempelajari bab ini, pembaca diharapkan mampu:

1. menjelaskan mengapa kata perlu dibaca dalam konteks, bukan hanya sebagai bentuk lepas;
2. membuat *concordance* (*konkordansi*) sederhana untuk menampilkan kata target dalam lingkungannya;
3. mengenali *collocation* (*kolokasi*) sebagai kecenderungan kata muncul bersama kata lain;
4. menafsirkan makna awal dari lingkungan kata secara lebih hati-hati; dan
5. menerapkan pengamatan konteks kata untuk pembelajaran kosakata dan membaca.

## Pengantar Bab
Pada bab sebelumnya, kita telah memecah teks menjadi kata dan menghitung frekuensinya. Langkah itu penting karena pembaca mulai bisa melihat kata mana yang sering muncul, kata mana yang unik, dan kata mana yang tampak dominan. Namun, frekuensi saja belum cukup. Sebuah kata bisa sering muncul, tetapi maknanya baru menjadi jelas ketika kita melihat kata itu berdampingan dengan kata lain, muncul dalam kalimat tertentu, atau berulang dalam pola konteks yang khas.

Di sinilah konsep konteks kata menjadi penting. Dalam linguistik korpus, kata tidak hanya diamati sebagai satuan lepas, tetapi juga sebagai unsur yang hidup di dalam lingkungan pemakaian nyata. Pendekatan seperti ini membantu pembaca bergerak dari pertanyaan “kata apa yang sering muncul?” ke pertanyaan yang lebih tajam, seperti “kata ini biasanya muncul bersama apa?”, “nuansa apa yang dibangun oleh kata ini?”, atau “bagaimana kata yang sama bekerja berbeda pada dua teks?” (McEnery & Hardie, 2012; O’Keeffe et al., 2007).

Bab ini memperkenalkan tiga gagasan utama: **kata dalam konteks**, **konkordansi**, dan **kolokasi**. Semua akan dibahas dengan contoh sederhana yang dapat dijalankan di *Python* atau *Google Colab*. Tujuannya bukan untuk membuat pembaca langsung menjadi analis korpus tingkat lanjut, melainkan agar pembaca mulai terbiasa melihat bahwa makna kata sering kali terletak pada tetangganya.

[Ilustrasi satu kata target di tengah, misalnya “malam”, lalu di sekelilingnya tampak kata-kata yang sering muncul bersamanya seperti “sunyi”, “hujan”, “gelap”, dan “tenang”. Visual menekankan bahwa kata mendapat nuansa dari lingkungan katanya.]
Gambar 7.1 Kata Mendapat Makna dari Lingkungan Pemakaiannya

## 7.1 Kata dalam Konteks
Salah satu kesalahan paling umum dalam analisis awal adalah memperlakukan kata sebagai benda yang berdiri sendiri. Padahal dalam pemakaian nyata, kata hampir selalu muncul bersama kata lain, berada di dalam kalimat tertentu, dan membawa nuansa sesuai konteksnya.

### 7.1.1 Melihat kata sebagai bagian dari aliran teks
Perhatikan contoh berikut.

```python
teks = "Pembelajar bahasa perlu membaca data bahasa dalam konteks kalimat yang utuh."
kata_kata = teks.lower().replace(".", "").split()
print(kata_kata)
```

**Output**
```text
['pembelajar', 'bahasa', 'perlu', 'membaca', 'data', 'bahasa', 'dalam', 'konteks', 'kalimat', 'yang', 'utuh']
```

**Penjelasan**
Setelah teks diubah menjadi huruf kecil dan titik dihapus, kita memperoleh daftar kata. Dari sini pembaca dapat melihat bahwa kata `bahasa` muncul dua kali. Namun, hitungan itu belum menjelaskan bagaimana kata tersebut bekerja di dalam kalimat. Untuk itu, pembaca perlu melihat kata-kata di sekitarnya.

### 7.1.2 Konteks membantu memperjelas fungsi kata
Kata yang sama dapat memunculkan fungsi atau nuansa berbeda pada kalimat yang berbeda. Itulah sebabnya analisis konteks penting dalam pembelajaran bahasa. Dalam pedagogi berbasis korpus, pembaca diajak melihat kata bukan hanya melalui definisi kamus, tetapi juga melalui pola pemakaian autentiknya (O’Keeffe et al., 2007).

### Contoh Nyata
Jika pembaca sedang menyiapkan bahan kosakata untuk pembelajar BIPA, kata `bahasa` mungkin terlalu umum jika dilihat sendirian. Namun, ketika ia muncul dalam frasa seperti `bahasa Indonesia`, `pembelajaran bahasa`, atau `data bahasa`, pembaca mulai melihat fungsi dan ranah pemakaiannya dengan lebih jelas.

## 7.2 Konkordansi Sederhana
*Concordance* (*konkordansi*) adalah tampilan kata target bersama konteks sekitarnya. Dalam studi korpus, konkordansi sangat berguna karena membantu pembaca melihat pola pemakaian kata secara langsung, bukan hanya melalui definisi atau intuisi (McEnery & Hardie, 2012; Meyer, 2023).

### 7.2.1 Menampilkan kalimat yang memuat kata target
Pendekatan paling sederhana adalah mencari kalimat yang mengandung kata tertentu.

```python
kalimat_list = [
    "Bahasa yang baik membantu pembaca memahami isi.",
    "Pembelajaran bahasa membutuhkan latihan yang teratur.",
    "Analisis bahasa memberi sudut pandang baru."
]

for kalimat in kalimat_list:
    bersih = kalimat.lower().replace(".", "")
    if "bahasa" in bersih.split():
        print(kalimat)
```

**Output**
```text
Bahasa yang baik membantu pembaca memahami isi.
Pembelajaran bahasa membutuhkan latihan yang teratur.
Analisis bahasa memberi sudut pandang baru.
```

**Penjelasan**
Kode ini menelusuri setiap kalimat, lalu menampilkan hanya kalimat yang mengandung kata `bahasa`. Hasilnya sederhana, tetapi sudah memberi gambaran awal tentang bagaimana satu kata bekerja pada beberapa lingkungan yang berbeda.

### 7.2.2 Membuat jendela konteks di sekitar kata target
Agar lebih mirip tampilan konkordansi, kita dapat mengambil beberapa kata di kiri dan kanan kata target.

```python
teks = "bahasa indonesia dipakai dalam kelas bahasa indonesia untuk analisis bahasa"
kata = teks.split()
target = "bahasa"
window = 2

for i, k in enumerate(kata):
    if k == target:
        kiri = " ".join(kata[max(0, i - window):i])
        kanan = " ".join(kata[i + 1:i + 1 + window])
        print(f"{kiri} [{k}] {kanan}")
```

**Output**
```text
 [bahasa] indonesia dipakai
dalam kelas [bahasa] indonesia untuk
untuk analisis [bahasa] 
```

**Penjelasan**
Variabel `window = 2` berarti pembaca mengambil dua kata di kiri dan dua kata di kanan kata target jika tersedia. Tanda kurung siku dipakai untuk menandai posisi kata target. Dengan tampilan ini, pembaca langsung melihat bahwa `bahasa` muncul dalam beberapa lingkungan yang berbeda.

### 7.2.3 Apa manfaat konkordansi bagi pembelajar?
Konkordansi membantu pembaca melihat pola nyata. Daripada hanya membaca bahwa satu kata “berarti” sesuatu, pembaca bisa melihat sendiri bagaimana kata itu hidup dalam kalimat. Ini sangat berguna untuk pembelajaran kosakata, pemahaman bacaan, dan analisis gaya.

### Coba Perhatikan
Konkordansi bukan sekadar daftar contoh. Ia adalah alat untuk melatih kepekaan terhadap pola pemakaian kata. Kadang-kadang justru dari daftar konteks sederhana itulah pembaca mulai melihat bahwa satu kata lebih dekat ke satu topik, suasana, atau jenis teks tertentu.

## 7.3 Kolokasi dan Pasangan Kata yang Sering Muncul
Jika konkordansi menampilkan kata target dalam konteksnya, *collocation* (*kolokasi*) membantu pembaca melihat pasangan kata yang cenderung muncul bersama. Dalam pembelajaran bahasa, kolokasi penting karena penutur tidak memilih kata secara acak. Banyak kata terasa “alami” karena memang sering berdampingan dalam pemakaian nyata (O’Keeffe et al., 2007).

### 7.3.1 Mengambil pasangan kata berurutan
Untuk latihan awal, kita dapat membuat pasangan kata berurutan atau *bigram* sederhana.

```python
from collections import Counter

teks = "bahasa indonesia dipakai dalam kelas bahasa indonesia dan pembelajaran bahasa indonesia"
kata = teks.split()
pasangan = []

for i in range(len(kata) - 1):
    pasangan.append((kata[i], kata[i + 1]))

print(pasangan)
```

**Output**
```text
[('bahasa', 'indonesia'), ('indonesia', 'dipakai'), ('dipakai', 'dalam'), ('dalam', 'kelas'), ('kelas', 'bahasa'), ('bahasa', 'indonesia'), ('indonesia', 'dan'), ('dan', 'pembelajaran'), ('pembelajaran', 'bahasa'), ('bahasa', 'indonesia')]
```

**Penjelasan**
Setiap pasangan mewakili dua kata yang muncul berurutan. Hasil ini masih berupa daftar mentah. Namun, dari sini pembaca sudah bisa melihat bahwa pasangan `bahasa indonesia` muncul berulang.

### 7.3.2 Menghitung kolokasi yang paling sering muncul
Setelah pasangan dibuat, pembaca bisa menghitung frekuensinya.

```python
from collections import Counter

teks = "bahasa indonesia dipakai dalam kelas bahasa indonesia dan pembelajaran bahasa indonesia"
kata = teks.split()
pasangan = []

for i in range(len(kata) - 1):
    pasangan.append((kata[i], kata[i + 1]))

frek_pasangan = Counter(pasangan)
print(frek_pasangan.most_common())
```

**Output**
```text
[(('bahasa', 'indonesia'), 3), (('indonesia', 'dipakai'), 1), (('dipakai', 'dalam'), 1), (('dalam', 'kelas'), 1), (('kelas', 'bahasa'), 1), (('indonesia', 'dan'), 1), (('dan', 'pembelajaran'), 1), (('pembelajaran', 'bahasa'), 1)]
```

**Penjelasan**
`Counter` membantu menghitung seberapa sering setiap pasangan muncul. Pada contoh ini, pasangan `bahasa indonesia` tampil tiga kali sehingga tampak sebagai kolokasi paling menonjol.

### 7.3.3 Kolokasi tidak selalu berarti makna tetap
Kolokasi membantu pembaca melihat kecenderungan, tetapi tidak berarti bahwa dua kata selalu harus bersama. Ia hanya menunjukkan bahwa dalam data tertentu, ada pasangan kata yang lebih sering muncul dibanding pasangan lain. Karena itu, kolokasi perlu dibaca sebagai pola, bukan aturan mutlak.

### Contoh Nyata
Dalam pembelajaran kosakata, kolokasi sangat berguna. Misalnya, pembaca tidak hanya belajar kata `analisis`, tetapi juga melihat pasangan yang wajar seperti `analisis data` atau `analisis bahasa`. Dengan begitu, pembelajaran bergerak dari hafalan kata tunggal ke penggunaan kata dalam gabungan yang lebih alami.

[Ilustrasi tabel dua kolom yang memuat pasangan kata dan frekuensinya, dengan `bahasa indonesia` ditandai sebagai pasangan yang paling dominan.]
Gambar 7.2 Contoh Kolokasi dari Teks Pendek

## 7.4 Menafsirkan Makna dari Lingkungan Kata
Setelah pembaca melihat konkordansi dan kolokasi, langkah berikutnya adalah menafsirkan hasilnya. Bagian ini penting karena analisis konteks kata tidak berhenti pada hitungan. Tujuannya adalah membaca pola dengan lebih hati-hati.

### 7.4.1 Melihat kata target pada dua ranah yang berbeda
Perhatikan dua kumpulan konteks berikut.

```python
konteks_puisi = [
    "malam sunyi turun perlahan",
    "hujan malam membuat suasana tenang",
    "langit malam terasa gelap"
]

konteks_artikel = [
    "data bahasa dikumpulkan dari bacaan",
    "analisis data membantu pembaca",
    "visualisasi data memperjelas temuan"
]

for baris in konteks_puisi:
    if "malam" in baris.split():
        print("PUISI:", baris)

for baris in konteks_artikel:
    if "data" in baris.split():
        print("ARTIKEL:", baris)
```

**Output**
```text
PUISI: malam sunyi turun perlahan
PUISI: hujan malam membuat suasana tenang
PUISI: langit malam terasa gelap
ARTIKEL: data bahasa dikumpulkan dari bacaan
ARTIKEL: analisis data membantu pembaca
ARTIKEL: visualisasi data memperjelas temuan
```

**Penjelasan**
Kata `malam` pada kumpulan konteks pertama muncul bersama kata-kata yang membangun suasana, seperti `sunyi`, `hujan`, dan `gelap`. Sebaliknya, kata `data` pada kumpulan konteks kedua muncul bersama kata-kata yang lebih dekat ke ranah akademik, seperti `analisis`, `bahasa`, dan `visualisasi`. Dari sini pembaca mulai melihat bahwa konteks membantu membedakan nuansa kata.

### 7.4.2 Membaca tetangga kiri dan kanan kata target
Pendekatan lain adalah melihat kata yang paling dekat di kiri dan kanan kata target.

```python
teks = "puisi itu memakai kata malam yang tenang dan malam yang panjang"
kata = teks.split()
target = "malam"
kiri = []
kanan = []

for i, k in enumerate(kata):
    if k == target:
        if i > 0:
            kiri.append(kata[i - 1])
        if i < len(kata) - 1:
            kanan.append(kata[i + 1])

print("Kata di kiri:", kiri)
print("Kata di kanan:", kanan)
```

**Output**
```text
Kata di kiri: ['kata', 'dan']
Kata di kanan: ['yang', 'yang']
```

**Penjelasan**
Dari hasil ini, pembaca dapat melihat bahwa posisi sekitar kata target juga membawa informasi. Dalam contoh ini, kata yang paling dekat di kanan `malam` selalu `yang`, sehingga ada petunjuk bahwa `malam` sering diikuti keterangan lanjutan pada pola kalimat tersebut.

### 7.4.3 Interpretasi harus tetap rendah hati
Hasil konkordansi dan kolokasi memberi petunjuk, bukan keputusan final. Jika data terlalu kecil, pola yang tampak bisa berubah ketika data diperluas. Karena itu, pembaca perlu bersikap hati-hati. Pola konteks bagus untuk memulai pembacaan, tetapi tidak boleh langsung diperlakukan sebagai kebenaran mutlak.

### Kesalahan Umum
Pemula kadang melihat satu kolokasi lalu langsung menyimpulkan bahwa itulah makna utama suatu kata. Padahal makna kata tetap bergantung pada keseluruhan teks, tujuan penulis, dan jenis data yang diamati.

## 7.5 Aplikasi untuk Pembelajaran Kosakata
Setelah memahami konteks kata, pertanyaan terakhir adalah: bagaimana semua ini berguna dalam pembelajaran?

Jawabannya sangat praktis. Pendekatan konteks kata membantu pembaca melihat bahwa mempelajari kosakata tidak cukup hanya dengan menghafal definisi. Pembelajar juga perlu melihat bagaimana kata itu muncul, dengan kata apa ia sering berdampingan, dan dalam situasi apa ia dipakai.

### 7.5.1 Mencari lingkungan kata target untuk bahan ajar
Bayangkan pembaca ingin mengajar atau mempelajari kata `membaca`. Kita bisa melihat beberapa konteks sederhananya.

```python
kalimat_list = [
    "Pembelajar membaca cerpen di kelas.",
    "Pembelajar membaca artikel untuk diskusi.",
    "Pembelajar membaca puisi dengan suara pelan."
]

for kalimat in kalimat_list:
    kata = kalimat.lower().replace(".", "").split()
    i = kata.index("membaca")
    kiri = kata[i - 1] if i > 0 else "-"
    kanan = kata[i + 1] if i < len(kata) - 1 else "-"
    print(kiri, "[membaca]", kanan)
```

**Output**
```text
pembelajar [membaca] cerpen
pembelajar [membaca] artikel
pembelajar [membaca] puisi
```

**Penjelasan**
Kata `membaca` muncul dengan tetangga kanan yang berbeda, yaitu `cerpen`, `artikel`, dan `puisi`. Hasil ini dapat dipakai untuk menyiapkan diskusi kosakata, latihan kolokasi, atau perbandingan jenis bacaan.

### 7.5.2 Manfaat untuk pembelajaran membaca dan menulis
Dalam pembelajaran membaca, konteks kata dapat membantu pembaca memilih kosakata yang memang penting untuk memahami isi teks. Dalam pembelajaran menulis, konkordansi sederhana dapat membantu melihat apakah satu kata dipakai terlalu berulang atau apakah ada pasangan kata yang terasa janggal.

### 7.5.3 Manfaat untuk sastra dan analisis wacana awal
Untuk teks sastra, konteks kata membantu pembaca menangkap suasana, citraan, atau pilihan diksi yang menonjol. Untuk teks nonsastra, konteks kata membantu pembaca melihat ranah topik dan hubungan antargagasan. Dengan kata lain, analisis konteks menjadi jembatan antara hitungan sederhana dan pembacaan makna yang lebih kaya.

### Contoh Nyata
Jika pembaca sedang menyiapkan latihan kosakata dari satu cerpen, pembaca bisa memilih beberapa kata target, lalu menunjukkan lingkungan katanya kepada kelompok belajar. Cara ini sering lebih hidup daripada hanya memberi daftar arti kata, karena pembelajar melihat kata itu bekerja langsung dalam kalimat.

### Trivia
Dalam pembelajaran berbasis korpus, pembelajar sering justru lebih cepat memahami nuansa kata ketika mereka melihat banyak contoh pemakaian nyata daripada ketika mereka hanya membaca satu definisi kamus.

[Ilustrasi lembar kerja kosakata yang menampilkan satu kata target di tengah, lalu beberapa contoh konteks dan pasangan kata yang sering muncul di sekelilingnya.]
Gambar 7.3 Lingkungan Kata sebagai Bahan Pembelajaran Kosakata

## Ringkasan
Bab ini menegaskan bahwa kata perlu dibaca dalam konteks. Setelah pembaca mengetahui frekuensi kata, langkah berikutnya adalah melihat lingkungan pemakaiannya melalui konkordansi dan kolokasi. Dengan cara ini, pembaca dapat bergerak dari hitungan sederhana ke pembacaan pola yang lebih bermakna.

Bab ini juga menunjukkan bahwa konkordansi membantu menampilkan kata target dalam kalimat atau jendela konteks, sedangkan kolokasi membantu melihat pasangan kata yang sering muncul bersama. Keduanya berguna untuk pembelajaran kosakata, membaca, menulis, dan analisis sastra awal.

Yang paling penting, hasil konteks kata tetap perlu ditafsirkan dengan hati-hati. Pola yang terlihat memberi petunjuk, tetapi makna akhir tetap harus dibaca bersama tujuan teks, jenis data, dan konteks yang lebih luas.

## Latihan Akhir Bab
### Latihan 1. Melihat kata dalam konteks
Pilih satu paragraf pendek, lalu:
1. pilih satu kata target,
2. tampilkan daftar kata dari paragraf itu, dan
3. jelaskan kata-kata di sekitar target yang menurut pembaca paling membantu memahami maknanya.

### Latihan 2. Membuat konkordansi sederhana
Gunakan minimal tiga kalimat yang memuat kata target yang sama, lalu:
1. tampilkan hanya kalimat yang mengandung kata target,
2. tandai kata target di dalam keluaran, dan
3. jelaskan pola yang mulai terlihat.

### Latihan 3. Menghitung kolokasi sederhana
Ambil satu teks pendek, lalu:
1. buat pasangan kata berurutan,
2. hitung frekuensi setiap pasangan, dan
3. pilih dua pasangan yang menurut pembaca paling menarik.

### Latihan 4. Menafsirkan lingkungan kata
Bandingkan dua kata target dari dua teks berbeda, misalnya satu dari puisi dan satu dari artikel. Tuliskan perbedaan nuansa yang tampak dari konteksnya.

### Latihan 5. Aplikasi untuk pembelajaran kosakata
Pilih satu kata kerja atau satu kata benda yang sering muncul dalam bahan ajar. Buat tiga contoh konteks sederhananya, lalu jelaskan bagaimana contoh itu dapat dipakai untuk latihan kosakata.

## Proyek Mini
### Proyek Mini 7. Membuat Lembar Kerja Konkordansi Sederhana
**Tujuan pembelajaran**  
Menggunakan konteks kata, konkordansi, dan kolokasi sederhana untuk menyusun bahan latihan kosakata yang lebih hidup.

**Alat yang digunakan**  
*Google Colab* dan satu teks pendek yang sudah dibersihkan.

**Instruksi**
1. Pilih satu teks pendek, misalnya cerpen mini, artikel populer, atau paragraf dari bahan ajar.
2. Tentukan dua atau tiga kata target yang ingin diamati.
3. Buat konkordansi sederhana untuk tiap kata target.
4. Catat pasangan kata yang sering muncul di sekitar kata target.
5. Tulis penjelasan singkat tentang nuansa atau fungsi kata target berdasarkan konteksnya.
6. Susun hasil itu menjadi lembar kerja mini untuk pembelajaran kosakata.

**Keluaran yang diharapkan**  
Satu *notebook* Colab yang berisi:
- teks yang dipakai,
- daftar kata target,
- hasil konkordansi sederhana,
- daftar kolokasi awal,
- penjelasan singkat konteks kata, dan
- usulan pemakaian hasilnya untuk latihan kosakata.

**Refleksi**  
Apakah melihat kata dalam konteks membantu pembaca lebih baik daripada hanya melihat daftar arti? Dalam situasi apa pendekatan ini paling berguna, misalnya untuk membaca, menulis, sastra, atau pembelajaran BIPA?

## 🧠 Istilah yang dipelajari pada bab ini
- konteks kata: lingkungan kata dalam kalimat atau teks yang membantu memperjelas maknanya.
- *concordance* (*konkordansi*): tampilan kata target beserta konteks sekitarnya.
- *collocation* (*kolokasi*): kecenderungan dua kata atau lebih muncul bersama dalam pemakaian nyata.
- jendela konteks: jumlah kata di kiri dan kanan kata target yang dipilih untuk ditampilkan.
- kata target: kata yang sengaja dipilih untuk diamati dalam analisis.
- *bigram*: pasangan dua kata yang muncul berurutan.
- lingkungan kata: kata-kata di sekitar suatu kata yang memberi petunjuk makna atau fungsi.
- `Counter`: alat dari Python untuk menghitung frekuensi unsur dalam data.
- `enumerate()`: fungsi Python untuk membaca item data sambil mengetahui posisinya.
- `most_common()`: metode untuk menampilkan unsur yang paling sering muncul lebih dahulu.

## Sumber Gambar yang Perlu Disiapkan
- [Tangkapan layar *Google Colab* yang menampilkan hasil konkordansi sederhana, dengan kata target ditandai di tengah konteks kiri dan kanan.]
  Gambar 7.4 Konkordansi Sederhana di *Google Colab*
- [Tangkapan layar atau diagram tabel pasangan kata berurutan beserta frekuensinya, dengan satu kolokasi dominan ditandai jelas.]
  Gambar 7.5 Menghitung Kolokasi dari Teks Pendek
- [Tangkapan layar lembar kerja kosakata yang memanfaatkan konteks kata dan kolokasi sebagai bahan diskusi atau latihan kelas.]
  Gambar 7.6 Konteks Kata sebagai Bahan Latihan Kosakata
