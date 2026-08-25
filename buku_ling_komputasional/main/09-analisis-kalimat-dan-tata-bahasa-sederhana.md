# Bab 9. Analisis Kalimat dan Tata Bahasa Sederhana

## Tujuan Pembelajaran
Setelah mempelajari bab ini, pembaca diharapkan mampu:

1. menjelaskan mengapa kalimat penting sebagai satuan analisis, bukan hanya kata;
2. memecah teks menjadi kalimat dan menghitung panjang setiap kalimat;
3. menemukan kalimat yang terlalu panjang atau terlalu pendek dalam satu teks;
4. mengenali pola tata bahasa sederhana secara komputasional, termasuk kecenderungan kalimat aktif dan pasif; dan
5. mengukur keterbacaan teks secara awal untuk keperluan pembelajaran bahasa.

## Pengantar Bab
Pada bab-bab sebelumnya, perhatian kita lebih banyak tertuju pada kata: bagaimana kata dihitung, bagaimana konteksnya diamati, dan bagaimana bentuk dasarnya ditemukan. Semua itu penting, tetapi bahasa tidak hanya hidup di tataran kata. Kalimat adalah satuan di mana gagasan disampaikan secara utuh. Satu kata saja jarang cukup untuk menyampaikan maksud. Justru dalam kalimatlah kata-kata itu bekerja bersama, membentuk makna yang lebih lengkap.

Dalam pembelajaran bahasa, kalimat sering menjadi perhatian utama. Pembaca ingin tahu apakah kalimat dalam suatu teks terlalu panjang atau terlalu pendek, apakah strukturnya bervariasi, dan apakah teks itu mudah dipahami. Pertanyaan-pertanyaan seperti ini dapat dijawab secara lebih terukur dengan bantuan komputasi.

Bab ini memperkenalkan cara menganalisis kalimat secara sederhana: memecah teks menjadi kalimat, menghitung panjangnya, menemukan kalimat yang menonjol, mengenali pola tata bahasa dasar, dan mengukur keterbacaan teks. Semua contoh tetap dekat dengan kebutuhan pembelajaran bahasa.

[Ilustrasi satu paragraf pendek yang dipecah menjadi beberapa kalimat dengan panjang berbeda. Kalimat pendek, sedang, dan panjang ditandai dengan warna atau ketebalan garis yang berbeda.]
Gambar 9.1 Kalimat Pendek, Sedang, dan Panjang dalam Satu Paragraf

## 9.1 Kalimat sebagai Objek Analisis
Dalam linguistik, kalimat adalah satuan bahasa yang mengandung gagasan lengkap. Dalam analisis komputasional, kalimat menjadi penting karena banyak pengamatan yang baru bermakna ketika dilakukan di tataran kalimat, bukan hanya di tataran kata.

### 9.1.1 Mengapa kalimat perlu dianalisis secara terpisah?
Beberapa alasan utama:

- **Panjang kalimat** memberi petunjuk tentang kompleksitas teks. Teks dengan kalimat rata-rata sangat panjang cenderung lebih sulit dipahami.
- **Variasi panjang kalimat** memberi petunjuk tentang gaya penulisan. Teks yang seluruh kalimatnya sama panjang bisa terasa monoton.
- **Struktur kalimat** memberi petunjuk tentang pola tata bahasa. Misalnya, apakah teks lebih banyak menggunakan kalimat aktif atau pasif.

### 9.1.2 Memecah teks menjadi kalimat
Pada Bab 6, kita sudah mencoba memecah teks menjadi kalimat secara sederhana. Pendekatan yang sama dapat dipakai di sini.

```python
teks = """Pembelajaran bahasa membutuhkan latihan yang teratur. Tanpa latihan, pemahaman akan sulit berkembang! Apakah kita sudah cukup berlatih? Menulis dan membaca adalah dua keterampilan utama."""

sementara = teks.replace("!", ".").replace("?", ".")
kalimat_list = [k.strip() for k in sementara.split(".") if k.strip()]

for i, k in enumerate(kalimat_list, 1):
    print(f"Kalimat {i}: {k}")
```

**Output**
```text
Kalimat 1: Pembelajaran bahasa membutuhkan latihan yang teratur
Kalimat 2: Tanpa latihan, pemahaman akan sulit berkembang
Kalimat 3: Apakah kita sudah cukup berlatih
Kalimat 4: Menulis dan membaca adalah dua keterampilan utama
```

**Penjelasan**
Tanda seru dan tanda tanya disamakan dulu menjadi titik, lalu teks dipecah berdasarkan titik. Setiap potongan yang tidak kosong dianggap sebagai satu kalimat. Pendekatan ini belum sempurna untuk semua kasus, tetapi cukup memadai untuk latihan awal.

### 9.1.3 Keterbatasan pemecahan kalimat sederhana
Perlu diingat bahwa pendekatan ini bisa keliru pada beberapa situasi, misalnya:

- singkatan yang mengandung titik, seperti `Dr.`, `Prof.`, atau `dsb.`;
- angka desimal seperti `3.14`; dan
- kutipan langsung yang mengandung tanda baca di dalamnya.

Pada tahap ini, pembaca cukup menyadari keterbatasan itu. Untuk analisis yang lebih canggih, pustaka seperti *Stanza* atau *spaCy* menyediakan pemecah kalimat yang lebih cermat.

## 9.2 Menghitung Panjang Kalimat
Setelah teks berhasil dipecah menjadi kalimat, langkah berikutnya adalah menghitung panjang setiap kalimat.

### 9.2.1 Panjang kalimat dalam jumlah kata

```python
teks = """Pembelajaran bahasa membutuhkan latihan yang teratur. Tanpa latihan, pemahaman akan sulit berkembang! Apakah kita sudah cukup berlatih? Menulis dan membaca adalah dua keterampilan utama."""

sementara = teks.replace("!", ".").replace("?", ".")
kalimat_list = [k.strip() for k in sementara.split(".") if k.strip()]

for i, k in enumerate(kalimat_list, 1):
    jumlah = len(k.split())
    print(f"Kalimat {i}: {jumlah} kata")
```

**Output**
```text
Kalimat 1: 6 kata
Kalimat 2: 6 kata
Kalimat 3: 5 kata
Kalimat 4: 7 kata
```

**Penjelasan**
Setiap kalimat dipecah menjadi kata dengan `.split()`, lalu jumlah katanya dihitung dengan `len()`. Dari sini pembaca sudah bisa melihat bahwa kalimat-kalimat dalam teks contoh ini relatif seimbang panjangnya.

### 9.2.2 Menghitung rata-rata panjang kalimat

```python
teks = """Pembelajaran bahasa membutuhkan latihan yang teratur. Tanpa latihan, pemahaman akan sulit berkembang! Apakah kita sudah cukup berlatih? Menulis dan membaca adalah dua keterampilan utama."""

sementara = teks.replace("!", ".").replace("?", ".")
kalimat_list = [k.strip() for k in sementara.split(".") if k.strip()]

total_kata = sum(len(k.split()) for k in kalimat_list)
rata_rata = total_kata / len(kalimat_list)

print(f"Jumlah kalimat : {len(kalimat_list)}")
print(f"Total kata     : {total_kata}")
print(f"Rata-rata      : {rata_rata:.1f} kata per kalimat")
```

**Output**
```text
Jumlah kalimat : 4
Total kata     : 24
Rata-rata      : 6.0 kata per kalimat
```

**Penjelasan**
Rata-rata panjang kalimat dihitung dengan membagi total kata dengan jumlah kalimat. Angka `6.0` menunjukkan bahwa teks ini cukup ringkas. Untuk teks akademik, rata-rata biasanya lebih tinggi. Ukuran ini berguna sebagai indikator awal keterbacaan.

### Coba Perhatikan
Rata-rata panjang kalimat bukan ukuran yang sempurna. Sebuah teks bisa memiliki rata-rata yang wajar, tetapi satu kalimat sangat panjang sementara yang lain sangat pendek. Karena itu, melihat distribusi panjang kalimat sering lebih berguna daripada hanya melihat rata-ratanya.

## 9.3 Menemukan Kalimat yang Rumit atau Terlalu Panjang
Dalam pembelajaran bahasa, kalimat yang terlalu panjang sering menjadi kendala pemahaman. Sebaliknya, kalimat yang selalu pendek bisa membuat teks terasa monoton. Mendeteksi keduanya secara komputasional dapat membantu pembaca menganalisis bahan ajar atau tulisan dengan lebih terukur.

### 9.3.1 Menandai kalimat berdasarkan panjangnya

```python
teks = """Puisi ini pendek. Namun maknanya dalam dan berlapis. Setiap kata dipilih dengan sangat hati-hati oleh penyairnya. Irama terasa. Bunyi menggema. Pembaca diajak merasakan kesunyian yang digambarkan melalui pilihan kata dan jeda antarlarik yang tidak biasa."""

sementara = teks.replace("!", ".").replace("?", ".")
kalimat_list = [k.strip() for k in sementara.split(".") if k.strip()]

for i, k in enumerate(kalimat_list, 1):
    jumlah = len(k.split())
    if jumlah <= 4:
        label = "pendek"
    elif jumlah <= 8:
        label = "sedang"
    else:
        label = "panjang"
    print(f"  [{label:7s}] ({jumlah} kata) {k}")
```

**Output**
```text
  [pendek ] (3 kata) Puisi ini pendek
  [sedang ] (5 kata) Namun maknanya dalam dan berlapis
  [sedang ] (8 kata) Setiap kata dipilih dengan sangat hati-hati oleh penyairnya
  [pendek ] (2 kata) Irama terasa
  [pendek ] (2 kata) Bunyi menggema
  [panjang] (15 kata) Pembaca diajak merasakan kesunyian yang digambarkan melalui pilihan kata dan jeda antarlarik yang tidak biasa
```

**Penjelasan**
Setiap kalimat diberi label berdasarkan jumlah katanya. Batas yang dipakai di sini bersifat sederhana: 1 sampai 4 kata dianggap pendek, 5 sampai 8 kata dianggap sedang, dan 9 kata atau lebih dianggap panjang. Dari hasil ini, pembaca langsung melihat bahwa teks tersebut memiliki variasi yang cukup menarik: tiga kalimat pendek, dua kalimat sedang, dan satu kalimat panjang.

### 9.3.2 Menghitung distribusi panjang kalimat

```python
teks = """Puisi ini pendek. Namun maknanya dalam dan berlapis. Setiap kata dipilih dengan sangat hati-hati oleh penyairnya. Irama terasa. Bunyi menggema. Pembaca diajak merasakan kesunyian yang digambarkan melalui pilihan kata dan jeda antarlarik yang tidak biasa."""

sementara = teks.replace("!", ".").replace("?", ".")
kalimat_list = [k.strip() for k in sementara.split(".") if k.strip()]

pendek = 0
sedang = 0
panjang = 0

for k in kalimat_list:
    jumlah = len(k.split())
    if jumlah <= 4:
        pendek += 1
    elif jumlah <= 8:
        sedang += 1
    else:
        panjang += 1

print(f"Kalimat pendek (1-4 kata) : {pendek}")
print(f"Kalimat sedang (5-8 kata) : {sedang}")
print(f"Kalimat panjang (9+ kata) : {panjang}")
```

**Output**
```text
Kalimat pendek (1-4 kata) : 3
Kalimat sedang (5-8 kata) : 2
Kalimat panjang (9+ kata) : 1
```

**Penjelasan**
Distribusi ini memberi gambaran ringkas tentang komposisi kalimat dalam teks. Teks dengan dominasi kalimat panjang mungkin perlu disederhanakan untuk bahan ajar pemula. Sebaliknya, teks yang seluruh kalimatnya pendek mungkin perlu diberi variasi agar lebih hidup.

### 9.3.3 Mengapa kalimat panjang bukan selalu buruk?
Kalimat panjang tidak otomatis berarti buruk. Dalam teks sastra, kalimat panjang sering digunakan secara sengaja untuk menciptakan efek tertentu, seperti membangun suasana atau menyampaikan alur pikiran yang mengalir. Yang menjadi masalah adalah ketika kalimat panjang muncul di teks yang seharusnya mudah dipahami, misalnya bahan bacaan untuk pembelajar pemula.

### Contoh Nyata
Jika pembaca sedang menyiapkan bahan bacaan untuk kelas, menghitung distribusi panjang kalimat dapat membantu memutuskan apakah teks itu perlu dipecah, disederhanakan, atau justru sudah cukup variatif.

## 9.4 Pola Tata Bahasa Sederhana dalam Teks
Selain panjang kalimat, pembaca juga dapat mengamati pola tata bahasa secara sederhana. Salah satu pola yang paling mudah dikenali secara komputasional dalam bahasa Indonesia adalah perbedaan antara kalimat aktif dan kalimat pasif.

### 9.4.1 Mengenali kecenderungan aktif dan pasif
Dalam bahasa Indonesia, kalimat pasif sering ditandai oleh kata kerja berawalan `di-`. Meskipun pendeteksian ini tidak sempurna, ia cukup berguna sebagai pengamatan awal.

```python
kalimat_list = [
    "Pembaca menganalisis teks dengan cermat.",
    "Teks itu dianalisis oleh pembaca.",
    "Kita membaca puisi bersama.",
    "Puisi itu dibaca di kelas.",
    "Penulis menulis cerpen baru.",
    "Cerpen itu ditulis oleh penulis muda."
]

for k in kalimat_list:
    kata = k.lower().replace(".", "").split()
    pasif = any(w.startswith("di") and len(w) > 3 for w in kata)
    if pasif:
        print(f"  [pasif]  {k}")
    else:
        print(f"  [aktif]  {k}")
```

**Output**
```text
  [aktif]  Pembaca menganalisis teks dengan cermat.
  [pasif]  Teks itu dianalisis oleh pembaca.
  [aktif]  Kita membaca puisi bersama.
  [pasif]  Puisi itu dibaca di kelas.
  [aktif]  Penulis menulis cerpen baru.
  [pasif]  Cerpen itu ditulis oleh penulis muda.
```

**Penjelasan**
Kode ini memeriksa apakah ada kata yang dimulai dengan `di` dan panjangnya lebih dari tiga huruf. Jika ada, kalimat diberi label `pasif`. Pendekatan ini memang sederhana dan bisa keliru pada beberapa kasus, tetapi untuk pengamatan awal sudah cukup berguna.

### 9.4.2 Membedakan preposisi `di` dan awalan `di-`
Satu tantangan yang perlu dipahami: kata `di` sebagai preposisi (misalnya `di kelas`, `di rumah`) berbeda dari `di-` sebagai awalan pasif (misalnya `dibaca`, `ditulis`). Dalam penulisan yang benar menurut EYD, preposisi `di` ditulis terpisah dari kata yang mengikutinya, sedangkan awalan `di-` ditulis menyatu.

```python
import re

teks = "Buku itu dibaca oleh banyak orang. Penulisnya dikenal luas. Ceritanya ditulis dengan bahasa yang indah."
pola = r"\bdi\w{3,}\b"
temuan = re.findall(pola, teks.lower())
print("Kata berpola di- (kemungkinan pasif):", temuan)
```

**Output**
```text
Kata berpola di- (kemungkinan pasif): ['dibaca', 'dikenal', 'ditulis']
```

**Penjelasan**
Pola regex `\bdi\w{3,}\b` mencari kata yang dimulai dengan `di` dan diikuti minimal tiga huruf lagi. Ini membantu menyaring kata seperti `dia` atau `din` yang bukan bentuk pasif. Namun, pembaca tetap perlu memeriksa hasilnya secara manual karena tidak semua kata yang cocok dengan pola ini pasti kata kerja pasif.

### 9.4.3 Mengapa pengamatan pola tata bahasa berguna?
Dalam pembelajaran bahasa, distribusi kalimat aktif dan pasif dapat memberi informasi tentang gaya penulisan. Teks berita, misalnya, sering lebih banyak menggunakan kalimat pasif. Teks naratif sering lebih banyak menggunakan kalimat aktif. Dengan pengamatan sederhana seperti ini, pembaca mulai terbiasa melihat pola tata bahasa sebagai fenomena yang dapat diamati dan dihitung, bukan hanya dihafal dari buku tata bahasa.

### Kesalahan Umum
Pemula kadang mengira bahwa setiap kata yang dimulai dengan `di` pasti bentuk pasif. Padahal kata seperti `dingin`, `dinding`, atau `dinosaurus` jelas bukan bentuk pasif. Karena itu, hasil deteksi otomatis selalu perlu dibaca ulang secara kritis.

### Contoh Nyata
Jika pembaca sedang memeriksa tulisan sendiri atau tulisan teman, menghitung proporsi kalimat pasif bisa membantu menemukan gaya yang terlalu monoton. Misalnya, jika sebagian besar kalimat dalam satu esai ternyata pasif, itu bisa menjadi bahan refleksi untuk memvariasikan struktur kalimat.

## 9.5 Keterbacaan Teks untuk Pembelajaran
Setelah memahami panjang kalimat dan pola tata bahasa, pembaca dapat mulai mengukur keterbacaan teks secara lebih terstruktur. Keterbacaan (*readability*) adalah perkiraan seberapa mudah sebuah teks dipahami oleh pembacanya.

### 9.5.1 Apa saja yang memengaruhi keterbacaan?
Beberapa faktor utama:

- **rata-rata panjang kalimat**: kalimat yang lebih panjang cenderung lebih sulit dipahami;
- **rata-rata panjang kata**: kata yang lebih panjang sering menandakan kosakata yang lebih teknis atau formal; dan
- **variasi struktur kalimat**: teks yang terlalu seragam bisa terasa membosankan, tetapi teks yang terlalu bervariasi tanpa pola bisa membingungkan.

### 9.5.2 Membuat fungsi pengukur keterbacaan sederhana

```python
import re

def ukur_keterbacaan(teks):
    sementara = teks.replace("!", ".").replace("?", ".")
    kalimat = [k.strip() for k in sementara.split(".") if k.strip()]
    jumlah_kalimat = len(kalimat)
    kata = re.sub(r"[^\w\s]", "", teks.lower()).split()
    jumlah_kata = len(kata)
    rata_panjang_kata = sum(len(k) for k in kata) / jumlah_kata if jumlah_kata else 0
    rata_kata_per_kalimat = jumlah_kata / jumlah_kalimat if jumlah_kalimat else 0
    return {
        "jumlah_kalimat": jumlah_kalimat,
        "jumlah_kata": jumlah_kata,
        "rata_panjang_kata": round(rata_panjang_kata, 1),
        "rata_kata_per_kalimat": round(rata_kata_per_kalimat, 1)
    }

teks_mudah = "Ini buku. Buku ini bagus. Kita baca buku ini."
teks_sulit = "Pembelajaran bahasa membutuhkan pendekatan interdisipliner yang mengintegrasikan perspektif linguistik komputasional dengan metodologi pengajaran kontekstual."

print("Teks mudah:", ukur_keterbacaan(teks_mudah))
print("Teks sulit:", ukur_keterbacaan(teks_sulit))
```

**Output**
```text
Teks mudah: {'jumlah_kalimat': 3, 'jumlah_kata': 9, 'rata_panjang_kata': 3.8, 'rata_kata_per_kalimat': 3.0}
Teks sulit: {'jumlah_kalimat': 1, 'jumlah_kata': 14, 'rata_panjang_kata': 10.3, 'rata_kata_per_kalimat': 14.0}
```

**Penjelasan**
Fungsi `ukur_keterbacaan()` menghitung empat indikator dasar. Teks mudah memiliki rata-rata 3.0 kata per kalimat dan panjang kata rata-rata 3.8 huruf. Teks sulit memiliki 14.0 kata per kalimat dan panjang kata rata-rata 10.3 huruf. Perbedaan ini langsung terlihat dan dapat menjadi dasar diskusi tentang tingkat kesulitan bahan ajar.

### 9.5.3 Membandingkan keterbacaan dua jenis teks

```python
import re

def ukur_keterbacaan(teks):
    sementara = teks.replace("!", ".").replace("?", ".")
    kalimat = [k.strip() for k in sementara.split(".") if k.strip()]
    jumlah_kalimat = len(kalimat)
    kata = re.sub(r"[^\w\s]", "", teks.lower()).split()
    jumlah_kata = len(kata)
    rata_panjang_kata = sum(len(k) for k in kata) / jumlah_kata if jumlah_kata else 0
    rata_kata_per_kalimat = jumlah_kata / jumlah_kalimat if jumlah_kalimat else 0
    return {
        "jumlah_kalimat": jumlah_kalimat,
        "jumlah_kata": jumlah_kata,
        "rata_panjang_kata": round(rata_panjang_kata, 1),
        "rata_kata_per_kalimat": round(rata_kata_per_kalimat, 1)
    }

teks_cerpen = "Malam itu sunyi. Hujan turun perlahan. Anak itu duduk di teras. Ia membaca buku cerita."
teks_artikel = "Analisis komputasional terhadap data kebahasaan memerlukan pemahaman mendalam tentang struktur morfologis dan sintaktis bahasa yang diteliti."

r_cerpen = ukur_keterbacaan(teks_cerpen)
r_artikel = ukur_keterbacaan(teks_artikel)

print(f"Cerpen  : {r_cerpen['rata_kata_per_kalimat']} kata/kalimat, panjang kata rata-rata {r_cerpen['rata_panjang_kata']} huruf")
print(f"Artikel : {r_artikel['rata_kata_per_kalimat']} kata/kalimat, panjang kata rata-rata {r_artikel['rata_panjang_kata']} huruf")
```

**Output**
```text
Cerpen  : 3.8 kata/kalimat, panjang kata rata-rata 4.6 huruf
Artikel : 16.0 kata/kalimat, panjang kata rata-rata 7.8 huruf
```

**Penjelasan**
Perbandingan ini menegaskan perbedaan yang sudah terasa secara intuitif. Teks cerpen memiliki kalimat yang jauh lebih pendek dan kata yang lebih sederhana. Teks artikel memiliki kalimat yang lebih panjang dan kata yang lebih panjang pula. Angka-angka ini dapat membantu pembaca memilih atau menyesuaikan bahan ajar sesuai tingkat kemampuan pembelajar.

### 9.5.4 Keterbatasan ukuran keterbacaan sederhana
Ukuran yang kita buat di sini bersifat dasar. Keterbacaan teks sebenarnya dipengaruhi oleh banyak faktor lain yang tidak tertangkap oleh hitungan panjang kata dan kalimat saja, misalnya:

- kejelasan struktur paragraf;
- familiaritas pembaca dengan topik;
- kehadiran istilah teknis yang tidak dijelaskan; dan
- koherensi antarkalimat.

Karena itu, fungsi `ukur_keterbacaan()` sebaiknya diperlakukan sebagai alat bantu awal, bukan pengganti penilaian manusia.

### Contoh Nyata
Jika pembaca sedang menyiapkan bahan bacaan untuk kelas bahasa Indonesia bagi penutur asing, mengukur rata-rata panjang kalimat dan panjang kata bisa menjadi langkah awal yang cepat untuk menyaring teks mana yang mungkin terlalu sulit dan mana yang sudah sesuai.

### Trivia
Dalam tradisi pengukuran keterbacaan bahasa Inggris, terdapat rumus seperti *Flesch Reading Ease* dan *Gunning Fog Index* yang menggabungkan panjang kalimat dengan jumlah suku kata. Untuk bahasa Indonesia, formula standar yang diterima luas belum ada, tetapi prinsip dasarnya serupa: kalimat dan kata yang lebih pendek cenderung lebih mudah dibaca.

## Ringkasan
Bab ini memperkenalkan kalimat sebagai satuan analisis yang penting. Setelah bab-bab sebelumnya berfokus pada kata, bab ini membantu pembaca bergerak ke tataran yang lebih tinggi. Pembaca belajar memecah teks menjadi kalimat, menghitung panjang kalimat, menemukan kalimat yang terlalu panjang atau terlalu pendek, mengenali pola aktif dan pasif secara sederhana, dan mengukur keterbacaan teks.

Yang paling penting, semua pengamatan ini bukan pengganti penilaian manusia. Angka dan pola yang ditemukan memberi petunjuk awal, tetapi penafsiran akhir tetap memerlukan pertimbangan konteks, tujuan teks, dan latar belakang pembaca.

## Latihan Akhir Bab
### Latihan 1. Memecah teks menjadi kalimat
Ambil satu paragraf pendek, lalu pecah menjadi kalimat. Hitung berapa kalimat yang dihasilkan.

### Latihan 2. Menghitung panjang kalimat
Dari paragraf yang sama, hitung panjang setiap kalimat dan rata-ratanya.

### Latihan 3. Menandai kalimat pendek, sedang, dan panjang
Tentukan batas yang menurut pembaca wajar untuk kategori pendek, sedang, dan panjang. Lalu tandai setiap kalimat sesuai kategorinya. Jelaskan alasan pemilihan batas tersebut.

### Latihan 4. Mengenali pola aktif dan pasif
Ambil lima kalimat dari satu teks, lalu deteksi secara sederhana mana yang aktif dan mana yang pasif. Periksa apakah ada hasil yang keliru, dan jelaskan mengapa.

### Latihan 5. Mengukur keterbacaan dua teks
Ambil dua teks dari sumber yang berbeda, misalnya satu dari berita dan satu dari buku pelajaran. Ukur keterbacaannya dengan fungsi `ukur_keterbacaan()`. Jelaskan perbedaan yang ditemukan.

## Proyek Mini
### Proyek Mini 9. Profil Keterbacaan Satu Teks
**Tujuan pembelajaran**  
Menganalisis satu teks secara lebih lengkap: dari panjang kalimat, distribusi panjang, pola aktif-pasif, hingga ukuran keterbacaan sederhana.

**Alat yang digunakan**  
*Google Colab* atau *Jupyter Notebook* dan modul `re`.

**Instruksi**
1. Pilih satu teks pendek, misalnya satu halaman artikel, satu cerpen mini, atau satu bagian bahan ajar.
2. Pecah teks menjadi kalimat.
3. Hitung panjang setiap kalimat dan rata-ratanya.
4. Tandai distribusi kalimat pendek, sedang, dan panjang.
5. Deteksi kecenderungan aktif dan pasif secara sederhana.
6. Ukur keterbacaan teks dengan fungsi `ukur_keterbacaan()`.
7. Tulis pengamatan singkat tentang profil keterbacaan teks tersebut.

**Keluaran yang diharapkan**  
Satu *notebook* yang berisi:
- teks yang dipakai,
- daftar kalimat beserta panjangnya,
- distribusi kalimat pendek, sedang, dan panjang,
- daftar kalimat aktif dan pasif,
- hasil ukuran keterbacaan, dan
- pengamatan singkat.

**Refleksi**  
Apakah hasil pengukuran sesuai dengan kesan pembaca ketika membaca teks itu secara langsung? Dalam situasi apa pengukuran ini paling berguna, misalnya untuk menyiapkan bahan ajar, menyunting tulisan, atau membandingkan tingkat kesulitan dua teks?

## 🧠 Istilah yang dipelajari pada bab ini
- kalimat: satuan bahasa yang mengandung gagasan lengkap.
- panjang kalimat: jumlah kata dalam satu kalimat.
- rata-rata panjang kalimat: total kata dibagi jumlah kalimat.
- distribusi panjang kalimat: sebaran kalimat berdasarkan panjangnya.
- kalimat aktif: kalimat yang subjeknya melakukan tindakan.
- kalimat pasif: kalimat yang subjeknya dikenai tindakan, sering ditandai awalan `di-` dalam bahasa Indonesia.
- keterbacaan (*readability*): perkiraan seberapa mudah teks dipahami oleh pembaca.
- preposisi: kata depan, misalnya `di`, `ke`, `dari`.
- `enumerate()`: fungsi Python untuk membaca item sambil mengetahui urutannya.
- `any()`: fungsi Python yang mengembalikan `True` jika ada item yang memenuhi syarat.
- `re.findall()`: fungsi untuk mencari semua kemunculan pola dalam teks.
- `re.sub()`: fungsi untuk mengganti bagian teks berdasarkan pola.

## Sumber Gambar yang Perlu Disiapkan
- [Tangkapan layar *Google Colab* yang menampilkan distribusi panjang kalimat dari satu teks, dengan label pendek, sedang, dan panjang.]
  Gambar 9.2 Distribusi Panjang Kalimat dari Satu Teks
- [Tangkapan layar *Google Colab* yang menampilkan hasil deteksi kalimat aktif dan pasif secara sederhana.]
  Gambar 9.3 Mengenali Kecenderungan Aktif dan Pasif
- [Tangkapan layar atau tabel perbandingan keterbacaan dua teks dengan angka rata-rata panjang kalimat dan rata-rata panjang kata.]
  Gambar 9.4 Perbandingan Keterbacaan Dua Teks
