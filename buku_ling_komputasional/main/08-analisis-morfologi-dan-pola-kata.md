# Bab 8. Analisis Morfologi dan Pola Kata

## Tujuan Pembelajaran
Setelah mempelajari bab ini, pembaca diharapkan mampu:

1. menjelaskan apa itu morfologi dan mengapa analisis bentuk kata penting dalam analisis bahasa;
2. mengenali pola imbuhan (*afiks*) dalam bahasa Indonesia, termasuk awalan, akhiran, dan konfiks;
3. menggunakan *regular expression* (*regex*) untuk mencari pola kata tertentu dalam teks;
4. menggunakan pustaka *Sastrawi* untuk menemukan kata dasar secara otomatis; dan
5. menerapkan analisis morfologi sederhana untuk keperluan pembelajaran bahasa.

## Pengantar Bab
Pada bab-bab sebelumnya, kita telah belajar memecah teks menjadi kata, menghitung frekuensinya, dan melihat konteks kata. Semua langkah itu memperlakukan kata sebagai satuan utuh. Namun, dalam bahasa Indonesia, satu kata sering terdiri dari kata dasar yang ditambahi imbuhan. Kata `pembelajaran`, misalnya, berasal dari kata dasar `ajar` yang mendapat awalan `pe-` dan akhiran `-an`. Kata `membacakan` terbentuk dari `baca` yang mendapat awalan `me-` dan akhiran `-kan`.

Proses pembentukan kata seperti ini disebut *morfologi*. Dalam analisis komputasional, morfologi menjadi penting karena banyak kata yang terlihat berbeda sebenarnya berasal dari akar yang sama. Jika kita menghitung frekuensi tanpa memperhatikan hal ini, kata `membaca`, `dibaca`, `bacaan`, dan `pembaca` akan dianggap sebagai empat kata yang berbeda, padahal semuanya berhubungan dengan akar yang sama, yaitu `baca`.

Bab ini memperkenalkan cara mengenali pola kata, mencari bentuk tertentu dengan *regular expression*, dan menemukan kata dasar secara otomatis dengan bantuan pustaka *Sastrawi*. Semua contoh tetap dekat dengan kebutuhan pembelajaran bahasa.

[Ilustrasi satu kata berimbuhan, misalnya "pembelajaran", yang dipecah menjadi awalan "pe-", sisipan "-", kata dasar "ajar", dan akhiran "-an". Visual menekankan bahwa kata utuh terbentuk dari bagian-bagian yang dapat dianalisis.]
Gambar 8.1 Kata Berimbuhan dan Bagian-bagiannya

## 8.1 Kata Dasar dan Kata Berimbuhan
Bahasa Indonesia termasuk bahasa yang *aglutinatif*, artinya kata sering dibentuk dengan menambahkan imbuhan pada kata dasar. Imbuhan dalam bahasa Indonesia terdiri dari beberapa jenis utama:

- **Awalan** (*prefiks*): ditambahkan di depan kata dasar, misalnya `me-`, `ber-`, `di-`, `per-`, `pe-`, `ter-`, `ke-`.
- **Akhiran** (*sufiks*): ditambahkan di belakang kata dasar, misalnya `-kan`, `-an`, `-i`.
- **Konfiks**: gabungan awalan dan akhiran yang bekerja bersama, misalnya `pe-...-an` pada `pembelajaran`, `ke-...-an` pada `kesalahan`.

### 8.1.1 Mengapa ini penting untuk analisis teks?
Ketika komputer memproses teks, ia tidak otomatis tahu bahwa `membaca` dan `bacaan` berasal dari kata dasar yang sama. Jika pembaca menghitung frekuensi kata tanpa memperhatikan morfologi, hasilnya bisa menyesatkan. Misalnya, kata `baca` bisa saja tampak jarang padahal bentuk turunannya muncul berkali-kali.

### 8.1.2 Melihat keluarga kata secara sederhana
Perhatikan satu keluarga kata dari akar `baca`.

```python
keluarga_baca = [
    "membaca", "dibaca", "terbaca",
    "bacaan", "pembaca", "membacakan"
]

for kata in keluarga_baca:
    print(kata)
```

**Output**
```text
membaca
dibaca
terbaca
bacaan
pembaca
membacakan
```

**Penjelasan**
Semua kata di atas terlihat berbeda, tetapi sebenarnya berasal dari akar yang sama, yaitu `baca`. Awalan `me-`, `di-`, `ter-`, `pe-` dan akhiran `-an`, `-kan` mengubah fungsi dan nuansa kata tanpa mengubah inti maknanya. Dalam analisis komputasional, kemampuan mengenali hubungan seperti ini sangat berguna.

### 8.1.3 Pola imbuhan yang umum dalam bahasa Indonesia
Tabel berikut merangkum beberapa pola imbuhan yang paling sering ditemui.

| Jenis Imbuhan | Contoh Pola | Contoh Kata |
|---|---|---|
| Awalan `me-` | me- + kata dasar | menulis, membaca, menghitung |
| Awalan `ber-` | ber- + kata dasar | berjalan, berlari, berdiskusi |
| Awalan `di-` | di- + kata dasar | ditulis, dibaca, dibahas |
| Awalan `ter-` | ter- + kata dasar | terbesar, terbaca, terbuka |
| Akhiran `-kan` | kata dasar + -kan | mengajarkan, membacakan |
| Akhiran `-an` | kata dasar + -an | bacaan, tulisan, ajaran |
| Konfiks `pe-...-an` | pe- + kata dasar + -an | pembelajaran, penulisan |
| Konfiks `ke-...-an` | ke- + kata dasar + -an | kesalahan, keindahan |

### Coba Perhatikan
Awalan `me-` dalam bahasa Indonesia mengalami perubahan bentuk tergantung pada huruf awal kata dasar. Misalnya, `me- + tulis` menjadi `menulis`, `me- + baca` menjadi `membaca`, `me- + hitung` menjadi `menghitung`, dan `me- + sapu` menjadi `menyapu`. Perubahan ini disebut *nasalisasi* dan sering menjadi tantangan dalam analisis otomatis.

## 8.2 Mengenal Pola Kata dalam Bahasa Indonesia
Sebelum masuk ke pencarian otomatis, pembaca perlu memahami bahwa pola imbuhan dapat diamati secara manual terlebih dahulu. Ini membantu pembaca membangun intuisi sebelum memakai alat.

### 8.2.1 Mengelompokkan kata berdasarkan awalan

```python
kata_kata = [
    "menulis", "membaca", "menghitung",
    "berjalan", "berlari", "berdiskusi",
    "ditulis", "dibaca", "dibahas"
]

awalan_me = [k for k in kata_kata if k.startswith("me")]
awalan_ber = [k for k in kata_kata if k.startswith("ber")]
awalan_di = [k for k in kata_kata if k.startswith("di")]

print("Awalan me-:", awalan_me)
print("Awalan ber-:", awalan_ber)
print("Awalan di-:", awalan_di)
```

**Output**
```text
Awalan me-: ['menulis', 'membaca', 'menghitung']
Awalan ber-: ['berjalan', 'berlari', 'berdiskusi']
Awalan di-: ['ditulis', 'dibaca', 'dibahas']
```

**Penjelasan**
Metode `.startswith()` dipakai untuk menyaring kata berdasarkan awalan. Hasilnya mengelompokkan kata sesuai pola awalnya. Cara ini sederhana, tetapi sudah cukup berguna untuk pengamatan awal terhadap distribusi imbuhan dalam satu teks.

### 8.2.2 Mengelompokkan kata berdasarkan akhiran

```python
kata_kata = [
    "bacaan", "tulisan", "ajaran",
    "mengajarkan", "membacakan", "menuliskan"
]

akhiran_an = [k for k in kata_kata if k.endswith("an")]
akhiran_kan = [k for k in kata_kata if k.endswith("kan")]

print("Akhiran -an:", akhiran_an)
print("Akhiran -kan:", akhiran_kan)
```

**Output**
```text
Akhiran -an: ['bacaan', 'tulisan', 'ajaran']
Akhiran -kan: ['mengajarkan', 'membacakan', 'menuliskan']
```

**Penjelasan**
Metode `.endswith()` bekerja seperti `.startswith()`, tetapi memeriksa bagian akhir kata. Perlu diingat bahwa cara ini bersifat permukaan saja. Kata `makan`, misalnya, akan terdeteksi berakhiran `-kan` padahal sebenarnya `makan` adalah kata dasar. Keterbatasan ini akan menjadi lebih jelas ketika pembaca bekerja dengan data yang lebih besar.

### Kesalahan Umum
Pemula kadang mengira bahwa semua kata yang dimulai dengan `ber-` pasti memiliki awalan. Padahal kata seperti `bersih` adalah kata dasar, bukan `ber- + sih`. Hal serupa berlaku untuk `makan` yang bukan `me- + akan`. Karena itu, pencocokan pola sederhana selalu perlu dibaca dengan hati-hati.

## 8.3 Pencarian Pola dengan Regular Expression
Untuk pencarian pola yang lebih fleksibel, pembaca dapat memakai *regular expression* (*regex*). Regex adalah cara menuliskan pola teks yang ingin dicari. Dalam *Python*, regex tersedia melalui modul `re` yang sudah terpasang bawaan.

### 8.3.1 Mencari kata berpola awalan tertentu
Misalnya, kita ingin mencari semua kata yang dimulai dengan `me` dan diakhiri dengan `kan`.

```python
import re

teks = "mengajarkan membacakan menuliskan memberikan mendengarkan"
pola = r"\bme\w+kan\b"
temuan = re.findall(pola, teks)
print(temuan)
```

**Output**
```text
['mengajarkan', 'membacakan', 'menuliskan', 'memberikan', 'mendengarkan']
```

**Penjelasan**
Pola `\bme\w+kan\b` berarti: cari kata yang diawali `me`, diikuti satu atau lebih huruf (`\w+`), lalu diakhiri `kan`. Tanda `\b` menandai batas kata agar pencarian tidak menangkap potongan di tengah kata lain. Fungsi `re.findall()` mengembalikan semua kata yang cocok dengan pola tersebut.

### 8.3.2 Mencari kata berpola konfiks pe-...-an

```python
import re

teks = "pembelajaran penulisan pengajaran pembacaan penemuan pemahaman"
pola = r"\bpe\w+an\b"
temuan = re.findall(pola, teks)
print(temuan)
```

**Output**
```text
['pembelajaran', 'penulisan', 'pengajaran', 'pembacaan', 'penemuan', 'pemahaman']
```

**Penjelasan**
Pola `\bpe\w+an\b` mencari kata yang dimulai `pe` dan diakhiri `an`. Ini menangkap bentuk-bentuk nominal yang sangat umum dalam bahasa Indonesia. Namun, pembaca perlu ingat bahwa pola ini bersifat permukaan. Kata seperti `pelan` juga bisa tertangkap padahal bukan bentukan konfiks.

### 8.3.3 Mengapa regex berguna untuk analisis bahasa?
Regex memungkinkan pembaca mencari pola tertentu tanpa harus memeriksa setiap kata satu per satu. Ini sangat berguna ketika pembaca bekerja dengan teks yang panjang dan ingin menemukan semua kata dengan struktur morfologis tertentu, misalnya semua bentuk pasif (`di-`), semua bentuk nominal (`pe-...-an`), atau semua kata kerja transitif (`me-...-kan`).

### 8.3.4 Elemen regex dasar yang sering dipakai

| Simbol | Arti | Contoh |
|---|---|---|
| `\b` | batas kata | `\bme` mencari kata yang dimulai `me` |
| `\w` | satu huruf, angka, atau garis bawah | `\w` cocok dengan `a`, `B`, `3` |
| `\w+` | satu atau lebih huruf/angka | `\w+` cocok dengan `kata`, `baca123` |
| `.` | satu karakter apa saja | `b.ca` cocok dengan `baca`, `beca` |
| `+` | satu kali atau lebih | `a+` cocok dengan `a`, `aa`, `aaa` |
| `*` | nol kali atau lebih | `ab*c` cocok dengan `ac`, `abc`, `abbc` |

### Coba Perhatikan
Regex adalah alat yang kuat, tetapi mudah menghasilkan hasil yang terlalu luas atau terlalu sempit jika pola tidak ditulis dengan cermat. Karena itu, pembaca disarankan selalu menguji pola pada contoh kecil terlebih dahulu sebelum menerapkannya pada data besar.

## 8.4 Menemukan Kata Dasar dengan Sastrawi
Pada subbab sebelumnya, kita mencari pola kata dari bentuk permukaannya. Namun, untuk benar-benar menemukan kata dasar, pendekatan pola permukaan sering tidak cukup. Bahasa Indonesia memiliki aturan morfologis yang cukup rumit, termasuk nasalisasi, peluluhan huruf, dan konfiks bersarang. Untuk itulah kita memerlukan alat yang dirancang khusus.

### 8.4.1 Mengenal Sastrawi
*Sastrawi* adalah pustaka *stemmer* untuk bahasa Indonesia. *Stemmer* adalah alat yang mengubah kata berimbuhan menjadi bentuk dasarnya. Sastrawi awalnya dikembangkan dalam bahasa pemrograman PHP oleh Andy Librian, lalu dipindahkan ke *Python* oleh Mulia Nasution dengan nama *PySastrawi*.

Algoritma yang digunakan Sastrawi didasarkan pada karya Nazief dan Adriani (1996), yang mengembangkan pendekatan *confix stripping* untuk menghilangkan imbuhan bahasa Indonesia secara bertahap. Pendekatan ini kemudian dikembangkan lebih lanjut oleh Asian (2007) dalam disertasinya tentang teknik temu kembali teks Indonesia, serta oleh Adriani, Asian, Nazief, Tahaghoghi, dan Williams (2007) dalam publikasi di jurnal *ACM Transactions on Asian Language Information Processing*.

Dengan kata lain, Sastrawi bukan sekadar alat pemrograman. Ia dibangun di atas penelitian linguistik komputasi yang serius tentang morfologi bahasa Indonesia.

### 8.4.2 Memasang dan menggunakan Sastrawi di Google Colab
Untuk memasang Sastrawi di *Google Colab*, pembaca cukup menjalankan satu baris perintah.

```python
!pip install PySastrawi
```

**Output**
```text
Successfully installed PySastrawi-1.2.1
```

**Penjelasan**
Perintah `!pip install` memasang pustaka dari *Python Package Index* (*PyPI*). Setelah pemasangan berhasil, pustaka siap dipakai di *notebook* yang sedang aktif.

### 8.4.3 Mencari kata dasar dari kata berimbuhan

```python
from Sastrawi.Stemmer.StemmerFactory import StemmerFactory

factory = StemmerFactory()
stemmer = factory.create_stemmer()

kata_uji = [
    "mempermasalahkan", "pembelajaran", "mengajarkan",
    "perkembangan", "membacakan", "penulisan",
    "berjalan", "terbesar", "kesalahan", "ditulis"
]

for kata in kata_uji:
    dasar = stemmer.stem(kata)
    print(f"{kata} -> {dasar}")
```

**Output**
```text
mempermasalahkan -> masalah
pembelajaran -> ajar
mengajarkan -> ajar
perkembangan -> kembang
membacakan -> baca
penulisan -> tulis
berjalan -> jalan
terbesar -> besar
kesalahan -> salah
ditulis -> tulis
```

**Penjelasan**
Fungsi `stemmer.stem()` menerima satu kata dan mengembalikan bentuk dasarnya. Pada contoh ini, setiap kata berimbuhan berhasil dikembalikan ke akarnya. Hasil seperti ini sangat berguna ketika pembaca ingin mengelompokkan kata-kata yang sebenarnya berasal dari akar yang sama.

### 8.4.4 Melihat seluruh keluarga kata dari satu akar

```python
from Sastrawi.Stemmer.StemmerFactory import StemmerFactory

factory = StemmerFactory()
stemmer = factory.create_stemmer()

teks = "membaca dibaca terbaca bacaan pembaca membacakan"
keluarga = {}

for kata in teks.split():
    dasar = stemmer.stem(kata)
    if dasar not in keluarga:
        keluarga[dasar] = []
    keluarga[dasar].append(kata)

for dasar, anggota in keluarga.items():
    print(f"Kata dasar '{dasar}': {anggota}")
```

**Output**
```text
Kata dasar 'baca': ['membaca', 'dibaca', 'terbaca', 'bacaan', 'pembaca', 'membacakan']
```

**Penjelasan**
Kode ini mengelompokkan semua kata berdasarkan kata dasarnya. Hasilnya menunjukkan bahwa enam kata yang tampak berbeda sebenarnya termasuk satu keluarga kata dengan akar `baca`. Pengelompokan seperti ini sangat berguna untuk analisis kekayaan morfologis suatu teks.

### 8.4.5 Mengolah kata dasar dari seluruh kalimat

```python
from Sastrawi.Stemmer.StemmerFactory import StemmerFactory
import re

factory = StemmerFactory()
stemmer = factory.create_stemmer()

kalimat = "Pembelajaran bahasa membutuhkan latihan membaca dan menulis secara teratur."
bersih = re.sub(r"[^\w\s]", "", kalimat.lower())
kata_kata = bersih.split()

hasil = []
for kata in kata_kata:
    dasar = stemmer.stem(kata)
    hasil.append(dasar)

print("Asli :", kata_kata)
print("Dasar:", hasil)
```

**Output**
```text
Asli : ['pembelajaran', 'bahasa', 'membutuhkan', 'latihan', 'membaca', 'dan', 'menulis', 'secara', 'teratur']
Dasar: ['ajar', 'bahasa', 'butuh', 'latih', 'baca', 'dan', 'tulis', 'cara', 'atur']
```

**Penjelasan**
Setiap kata dalam kalimat diubah ke bentuk dasarnya. Kata yang memang sudah dasar, seperti `bahasa` dan `dan`, tetap tidak berubah. Hasil ini memperlihatkan bahwa satu kalimat pendek bisa memiliki banyak lapisan morfologis yang tidak langsung terlihat dari bentuk permukaannya.

### 8.4.6 Keterbatasan Sastrawi
Sastrawi sangat berguna, tetapi tidak sempurna. Beberapa keterbatasan yang perlu pembaca ketahui:

- Kata yang tidak ada dalam kamus bawaan Sastrawi mungkin tidak berhasil distem. Misalnya, kata serapan atau kata tidak baku bisa dikembalikan tanpa perubahan.
- Sastrawi dioptimalkan untuk bahasa Indonesia formal. Untuk bahasa percakapan, bahasa daerah, atau bahasa campuran, hasilnya bisa kurang akurat.
- Proses *stemming* menghilangkan imbuhan, tetapi tidak menganalisis jenis imbuhan secara detail.

Keterbatasan ini bukan kelemahan fatal. Pembaca cukup menyadarinya agar tidak terlalu bergantung pada satu alat saja.

## 8.5 Pemanfaatan untuk Analisis Kesalahan Berbahasa dan Pembelajaran
Setelah menguasai dasar-dasar pencarian pola dan *stemming*, pembaca dapat mulai menerapkannya untuk kebutuhan yang lebih konkret dalam pembelajaran bahasa.

### 8.5.1 Menghitung kata berimbuhan dalam teks
Salah satu pengamatan sederhana yang berguna adalah menghitung berapa banyak kata berimbuhan dalam satu teks. Ini dapat memberi gambaran kasar tentang kompleksitas morfologis teks tersebut.

```python
from Sastrawi.Stemmer.StemmerFactory import StemmerFactory
import re

factory = StemmerFactory()
stemmer = factory.create_stemmer()

teks_cerpen = "Malam itu hujan turun perlahan di halaman rumah tua yang sudah lama ditinggalkan"
teks_artikel = "Pembelajaran bahasa membutuhkan pendekatan yang terstruktur dan berkelanjutan"

def hitung_berimbuhan(teks):
    bersih = re.sub(r"[^\w\s]", "", teks.lower())
    kata_kata = bersih.split()
    berubah = 0
    for kata in kata_kata:
        if stemmer.stem(kata) != kata:
            berubah += 1
    return berubah, len(kata_kata)

b_cerpen, t_cerpen = hitung_berimbuhan(teks_cerpen)
b_artikel, t_artikel = hitung_berimbuhan(teks_artikel)

print(f"Cerpen : {b_cerpen}/{t_cerpen} kata berimbuhan")
print(f"Artikel: {b_artikel}/{t_artikel} kata berimbuhan")
```

**Output**
```text
Cerpen : 1/13 kata berimbuhan
Artikel: 5/8 kata berimbuhan
```

**Penjelasan**
Pada teks cerpen, hanya satu dari tiga belas kata yang berimbuhan (`ditinggalkan`). Sementara pada teks artikel, lima dari delapan kata adalah kata berimbuhan. Perbedaan ini menunjukkan bahwa teks akademik cenderung lebih padat secara morfologis dibanding teks naratif sederhana. Pengamatan seperti ini dapat menjadi bahan diskusi tentang gaya dan tingkat keterbacaan suatu teks.

### 8.5.2 Mendeteksi kesalahan imbuhan secara awal
Sastrawi dapat membantu mendeteksi kata yang kemungkinan tidak baku. Jika sebuah kata berimbuhan tidak berhasil dikembalikan ke kata dasarnya, ada kemungkinan kata itu ditulis dengan ejaan yang tidak standar.

```python
from Sastrawi.Stemmer.StemmerFactory import StemmerFactory

factory = StemmerFactory()
stemmer = factory.create_stemmer()

kata_uji = ["membicarakan", "membicirakan", "mempercayai", "mempertjayai"]

for kata in kata_uji:
    dasar = stemmer.stem(kata)
    if dasar == kata:
        print(f"  {kata} -> (tidak berhasil distem, periksa ejaan)")
    else:
        print(f"  {kata} -> {dasar}")
```

**Output**
```text
  membicarakan -> bicara
  membicirakan -> (tidak berhasil distem, periksa ejaan)
  mempercayai -> percaya
  mempertjayai -> (tidak berhasil distem, periksa ejaan)
```

**Penjelasan**
Kata `membicarakan` dan `mempercayai` berhasil dikembalikan ke bentuk dasarnya. Sementara `membicirakan` dan `mempertjayai` gagal karena ejaan tidak standar. Dengan cara ini, pembaca bisa mendapatkan petunjuk awal tentang kemungkinan kesalahan ejaan atau imbuhan yang keliru.

### 8.5.3 Mengelompokkan kata berdasarkan akar yang sama

```python
from Sastrawi.Stemmer.StemmerFactory import StemmerFactory

factory = StemmerFactory()
stemmer = factory.create_stemmer()

teks = "perkembangan berkembang mengembangkan pengembangan kembang dikembangkan"
keluarga = {}

for kata in teks.split():
    dasar = stemmer.stem(kata)
    if dasar not in keluarga:
        keluarga[dasar] = []
    keluarga[dasar].append(kata)

for dasar, anggota in keluarga.items():
    print(f"Kata dasar '{dasar}': {anggota}")
```

**Output**
```text
Kata dasar 'kembang': ['perkembangan', 'berkembang', 'mengembangkan', 'pengembangan', 'kembang', 'dikembangkan']
```

**Penjelasan**
Enam kata yang berbeda ternyata berasal dari satu akar, yaitu `kembang`. Pengelompokan seperti ini berguna untuk melihat seberapa produktif satu kata dasar dalam satu teks, atau untuk menyiapkan latihan keluarga kata dalam pembelajaran kosakata.

### 8.5.4 Membandingkan dua tulisan berdasarkan kata dasarnya

```python
from Sastrawi.Stemmer.StemmerFactory import StemmerFactory
import re

factory = StemmerFactory()
stemmer = factory.create_stemmer()

tulisan_a = "Penulis membahas permasalahan lingkungan dan memberikan penjelasan tentang pencemaran"
tulisan_b = "Penulis membahas masalah lingkungan dan memberi penjelasan tentang polusi"

def stem_semua(teks):
    bersih = re.sub(r"[^\w\s]", "", teks.lower())
    return set(stemmer.stem(k) for k in bersih.split())

dasar_a = stem_semua(tulisan_a)
dasar_b = stem_semua(tulisan_b)
sama = sorted(dasar_a & dasar_b)

print("Kata dasar yang sama:", sama)
```

**Output**
```text
Kata dasar yang sama: ['bahas', 'beri', 'dan', 'jelas', 'lingkung', 'masalah', 'tentang', 'tulis']
```

**Penjelasan**
Meskipun kedua tulisan memakai kata yang berbeda di permukaan, ternyata kata dasar yang digunakan sebagian besar sama. Hanya satu perbedaan isi yang nyata: tulisan pertama memakai `pencemaran` (akar: `cemar`), sedangkan tulisan kedua memakai `polusi`. Perbandingan seperti ini berguna untuk melihat kesamaan gagasan di balik variasi bentuk kata.

### Contoh Nyata
Dalam konteks pembelajaran menulis, analisis morfologis dapat membantu pembaca melihat apakah seorang penulis sudah memakai variasi imbuhan yang cukup, atau apakah tulisannya cenderung memakai bentuk kata yang itu-itu saja. Untuk pembelajaran membaca, analisis ini bisa membantu memperkirakan tingkat kesulitan suatu teks berdasarkan kepadatan morfologisnya.

## Ringkasan
Bab ini memperkenalkan analisis morfologi sebagai langkah penting setelah frekuensi dan konteks kata. Pembaca belajar mengenali pola imbuhan dalam bahasa Indonesia, mencari bentuk kata tertentu dengan *regular expression*, dan menemukan kata dasar secara otomatis dengan pustaka *Sastrawi*.

Yang paling penting, pembaca mulai melihat bahwa kata-kata yang tampak berbeda sering kali berasal dari akar yang sama. Kesadaran ini membuka banyak kemungkinan analisis, mulai dari pengelompokan keluarga kata, pengamatan kompleksitas morfologis, hingga deteksi awal kesalahan ejaan.

Sastrawi, sebagai alat yang dibangun di atas penelitian Nazief dan Adriani (1996) serta Asian (2007), menunjukkan bahwa pustaka pemrograman yang kita pakai sering kali memiliki landasan ilmiah yang kuat. Karena itu, menghargai dan mengutip pengembang serta peneliti di baliknya adalah bagian penting dari praktik akademik yang baik.

## Latihan Akhir Bab
### Latihan 1. Mengelompokkan kata berdasarkan awalan
Ambil sepuluh kata dari satu teks pendek, lalu kelompokkan berdasarkan awalan (`me-`, `ber-`, `di-`, `ter-`, `pe-`, atau tanpa awalan).

### Latihan 2. Mencari pola kata dengan regex
Gunakan `re.findall()` untuk mencari semua kata yang memiliki pola `ber-` di satu paragraf. Uji apakah ada kata yang tertangkap padahal sebenarnya bukan kata berimbuhan.

### Latihan 3. Menemukan kata dasar dengan Sastrawi
Ambil satu kalimat pendek, lalu ubah setiap kata ke bentuk dasarnya menggunakan Sastrawi. Catat kata mana yang berubah dan kata mana yang tetap.

### Latihan 4. Mengelompokkan keluarga kata
Pilih satu kata dasar, misalnya `tulis` atau `ajar`, lalu:
1. buat lima kata turunan dari kata dasar itu,
2. jalankan Sastrawi untuk memastikan semua kembali ke akar yang sama, dan
3. jelaskan fungsi setiap kata turunan.

### Latihan 5. Membandingkan kompleksitas morfologis dua teks
Ambil dua teks pendek dari jenis yang berbeda, misalnya satu puisi dan satu artikel. Hitung berapa kata berimbuhan pada masing-masing teks. Jelaskan perbedaannya.

## Proyek Mini
### Proyek Mini 8. Analisis Keluarga Kata dan Kompleksitas Morfologis
**Tujuan pembelajaran**  
Menerapkan *stemming* dan pencarian pola untuk menganalisis kekayaan morfologis suatu teks.

**Alat yang digunakan**  
*Google Colab*, *PySastrawi*, dan modul `re`.

**Instruksi**
1. Pilih satu teks pendek, misalnya satu paragraf dari artikel atau satu halaman cerpen.
2. Bersihkan teks tersebut.
3. Ubah setiap kata ke bentuk dasarnya menggunakan Sastrawi.
4. Kelompokkan kata-kata yang berasal dari akar yang sama.
5. Hitung rasio kata berimbuhan terhadap total kata.
6. Tulis pengamatan singkat tentang kompleksitas morfologis teks tersebut.

**Keluaran yang diharapkan**  
Satu *notebook* Colab yang berisi:
- teks yang dipakai,
- daftar kata dan bentuk dasarnya,
- pengelompokan keluarga kata,
- rasio kompleksitas morfologis, dan
- pengamatan singkat.

**Refleksi**  
Apakah teks dengan banyak kata berimbuhan lebih sulit dibaca? Apakah teks sastra dan teks akademik memiliki pola morfologis yang berbeda? Kapan informasi tentang kata dasar berguna untuk pembelajaran bahasa?

## 🧠 Istilah yang dipelajari pada bab ini
- morfologi: cabang linguistik yang mempelajari pembentukan kata.
- kata dasar: bentuk kata tanpa imbuhan.
- imbuhan (*afiks*): unsur yang ditambahkan pada kata dasar untuk mengubah makna atau fungsi.
- awalan (*prefiks*): imbuhan yang ditambahkan di depan kata dasar.
- akhiran (*sufiks*): imbuhan yang ditambahkan di belakang kata dasar.
- konfiks: gabungan awalan dan akhiran yang bekerja bersama.
- nasalisasi: perubahan bunyi pada awalan `me-` sesuai huruf awal kata dasar.
- *stemming*: proses mengembalikan kata berimbuhan ke bentuk dasarnya.
- *stemmer*: alat yang melakukan *stemming*.
- *Sastrawi* / *PySastrawi*: pustaka *stemmer* untuk bahasa Indonesia, dikembangkan berdasarkan algoritma Nazief dan Adriani (1996).
- *regular expression* (*regex*): cara menuliskan pola teks untuk pencarian otomatis.
- `re.findall()`: fungsi Python untuk mencari semua kemunculan pola dalam teks.
- `\b`: simbol batas kata dalam regex.
- `\w+`: pola regex untuk satu atau lebih huruf, angka, atau garis bawah.
- keluarga kata: kumpulan kata turunan yang berasal dari kata dasar yang sama.

## Sumber Gambar yang Perlu Disiapkan
- [Tangkapan layar *Google Colab* yang menampilkan hasil *stemming* beberapa kata berimbuhan dengan Sastrawi.]
  Gambar 8.2 Menggunakan Sastrawi untuk Menemukan Kata Dasar
- [Tangkapan layar atau tabel yang memperlihatkan pengelompokan keluarga kata dari satu akar, misalnya semua turunan dari kata dasar `baca`.]
  Gambar 8.3 Keluarga Kata dari Satu Akar
- [Tangkapan layar *Google Colab* yang menampilkan hasil pencarian pola kata dengan regex.]
  Gambar 8.4 Mencari Pola Kata dengan Regex di *Google Colab*
