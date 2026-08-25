# Bab 4. Google Colab sebagai Laboratorium Linguistik Komputasi

## Tujuan Pembelajaran
Setelah mempelajari bab ini, pembaca diharapkan mampu:

1. menjelaskan apa itu *Google Colab* dan mengapa platform ini berguna untuk pembelajaran linguistik komputasi;
2. membuat, menamai, dan menjalankan *notebook* sederhana di *Google Colab*;
3. menulis serta menjalankan kode *Python* dasar dalam sel kode;
4. mengunggah, membaca, dan menyimpan file sederhana untuk latihan analisis bahasa; dan
5. membagikan *notebook* secara lebih aman untuk tugas, praktik, atau kerja kolaboratif.

## Pengantar Bab
Pada bab sebelumnya, kita sudah berkenalan dengan *Python* sebagai alat kerja dasar untuk mengolah data bahasa. Masalahnya, tidak semua pembelajar siap memasang *Python* di komputer pada hari pertama. Ada yang memakai perangkat bersama, ada yang memorinya terbatas, ada pula yang sekadar ingin mulai secepat mungkin tanpa berurusan dengan instalasi.

Di sinilah *Google Colab* menjadi sangat berguna. *Google Colab* adalah layanan *hosted Jupyter Notebook*, yaitu layanan *notebook Jupyter* yang sudah disiapkan secara daring sehingga pembaca tidak perlu mengatur lingkungan kerja dari nol. Dalam penjelasan resminya, Colab dapat dipakai tanpa penyiapan awal dan sangat cocok untuk pendidikan, analisis data, dan kerja komputasional berbasis *Python* (Google, 2026a; Google, 2026b).

Dalam konteks buku ini, kita dapat membayangkan Colab sebagai laboratorium praktik. Ia bukan sekadar tempat mengetik kode, tetapi ruang kerja tempat kita membaca teks, mencoba langkah analisis, menyimpan catatan, melihat hasil, lalu membagikannya kembali. Bagi pembelajar bahasa, pendekatan seperti ini sangat membantu karena proses belajar tidak berhenti pada teori. Kita dapat langsung mencoba hal kecil, misalnya menghitung jumlah kata, membaca file teks, atau menyusun catatan analisis dalam satu dokumen kerja yang sama.

Bab ini sengaja dibuat praktis. Kita tidak akan membahas semua menu di Colab. Fokus kita hanya pada hal-hal yang benar-benar berguna untuk langkah awal: membuat *notebook*, menjalankan sel, menulis kode sederhana, mengunggah file, menyimpan hasil, dan membagikan *notebook* untuk pembelajaran.

[Ilustrasi pembelajar membuka *Google Colab* di peramban, dengan satu notebook kosong yang menampilkan area judul, tombol jalankan, dan satu sel kode aktif. Visual menekankan kesan laboratorium praktik yang siap dipakai tanpa instalasi rumit.]
Gambar 4.1 *Google Colab* sebagai Laboratorium Praktik Analisis Bahasa

## 4.1 Mengenal Google Colab
Secara ringkas, *Google Colab* adalah layanan daring yang memungkinkan kita menulis dan menjalankan *Python* di dalam *notebook* melalui peramban. Menurut FAQ resminya, Colab dibangun di atas proyek *Jupyter* dan memungkinkan pengguna memakai serta membagikan *notebook* tanpa perlu mengunduh, memasang, atau menjalankan lingkungan sendiri di komputer lokal (Google, 2026b).

Bagi pembelajar pemula, ada beberapa alasan mengapa Colab sangat membantu.

1. **Tidak perlu instalasi rumit.**  
   Pembaca bisa langsung mulai dari peramban.

2. **Cocok untuk belajar bertahap.**  
   Kode, catatan, dan keluaran bisa diletakkan dalam satu tempat.

3. **Mudah dibagikan.**  
   *Notebook* Colab dapat dibagikan seperti berbagi dokumen di Google Drive (Google, 2026b).

4. **Mudah dipakai untuk praktik kelas.**  
   Satu file dapat berisi penjelasan, instruksi, kode, dan hasil latihan.

### 4.1.1 Apa itu notebook?
*Notebook* dapat dipahami sebagai buku kerja digital interaktif. Di dalamnya, kita dapat menaruh dua hal utama:
- **sel teks**, untuk menulis penjelasan, instruksi, atau catatan refleksi; dan
- **sel kode**, untuk menulis serta menjalankan kode *Python*.

Karena itu, *notebook* sangat cocok untuk pembelajaran. Pembaca tidak perlu memisahkan catatan teori dan praktik ke banyak file. Semua bisa diletakkan dalam satu alur kerja.

### 4.1.2 Colab bukan komputer pribadi
Meski Colab terasa seperti ruang kerja pribadi, ada satu hal penting yang perlu dipahami sejak awal. Kode pada Colab dijalankan di *virtual machine* (mesin virtual) yang bersifat privat untuk akun pengguna. Mesin virtual ini tidak selalu aktif terus-menerus. Jika terlalu lama tidak dipakai, atau jika masa pakainya selesai, lingkungan kerja itu bisa terputus atau dihapus oleh layanan Colab (Google, 2026b).

Artinya, pembaca perlu membedakan tiga hal berikut.

| Yang dikerjakan | Disimpan di mana | Catatan penting |
|---|---|---|
| isi *notebook* | biasanya di Google Drive | judul, teks, kode, dan keluaran *notebook* dapat disimpan dan dibagikan |
| kode yang sedang dijalankan | di mesin virtual Colab | keadaan kerja dapat terputus jika sesi habis atau terlalu lama diam |
| file tambahan yang diunggah saat sesi berjalan | di lingkungan kerja sementara, kecuali disimpan lagi | file bisa hilang jika sesi berakhir |

Tabel ini sangat penting bagi pembelajar bahasa. Banyak pemula mengira bahwa semua yang muncul di layar otomatis aman tersimpan selamanya. Padahal, *notebook* dan file kerja tidak selalu memiliki nasib yang sama.

### Contoh Nyata
Bayangkan kita sedang menganalisis 10 paragraf bacaan di Colab. Jika paragraf itu hanya ditempel ke sel lalu dihitung, hasilnya memang muncul. Namun, jika kita juga mengunggah file teks tambahan dan tidak menyimpannya lagi, file itu bisa hilang ketika sesi berakhir. Karena itu, disiplin menyimpan kerja menjadi bagian dari literasi digital, bukan sekadar urusan teknis.

### Coba Perhatikan
Colab paling nyaman dipakai sebagai laboratorium latihan dan eksplorasi. Ia sangat cocok untuk memulai, mencoba, dan berbagi. Namun, pembaca tetap perlu sadar bahwa lingkungan kerjanya bersifat dinamis dan tidak sama dengan menyimpan semua berkas secara permanen di komputer sendiri.

## 4.2 Membuat dan Menjalankan Notebook
Langkah pertama untuk memakai Colab sangat sederhana.

1. Buka `https://colab.research.google.com/`.
2. Masuk dengan akun Google.
3. Pilih **New Notebook** untuk membuat *notebook* baru.
4. Ubah judul *notebook* agar mudah dikenali, misalnya `latihan-analisis-teks-bab-4`.

Setelah itu, pembaca akan melihat area kerja utama. Umumnya ada judul di bagian atas, menu, tombol jalankan, dan satu sel kode kosong.

### 4.2.1 Menjalankan sel pertama
Cobalah ketik kode berikut pada satu sel.

```python
print("Halo dari Google Colab!")
```

**Output**
```text
Halo dari Google Colab!
```

**Penjelasan**
Perintah `print()` menampilkan teks ke layar. Jika kalimat itu muncul, berarti sel berhasil dijalankan dan lingkungan kerja Colab sudah siap dipakai.

### 4.2.2 Menamai notebook dengan jelas
Pemula sering mengabaikan judul file. Padahal dalam praktik pembelajaran, nama *notebook* yang jelas sangat membantu. Bandingkan dua nama berikut.

- `Untitled0`
- `latihan-kosakata-cerpen-bab-4`

Nama kedua jauh lebih informatif. Ketika file mulai banyak, nama yang jelas akan menghemat waktu dan mencegah kekeliruan.

### 4.2.3 Menambahkan sel baru
Di Colab, pembaca bisa menambah sel kode atau sel teks. Untuk pembelajaran bahasa, sel teks berguna untuk:
- menulis tujuan latihan,
- mencatat sumber data,
- menulis refleksi singkat, dan
- menandai hasil pengamatan.

Sementara itu, sel kode dipakai untuk menjalankan langkah teknis, seperti menghitung jumlah kata atau membaca file.

### Latihan Singkat
Buat satu *notebook* baru, lalu isi dengan tiga bagian berikut.
1. judul *notebook* yang jelas,
2. satu sel teks berisi tujuan latihan, dan
3. satu sel kode berisi `print("Saya siap belajar di Colab")`.

### Kesalahan Umum
Sebagian pembaca langsung menumpuk semua langkah ke satu sel yang sangat panjang. Cara ini membuat *notebook* sulit dibaca. Lebih baik pisahkan pekerjaan menjadi beberapa sel kecil sesuai alur berpikir.

[Cuplikan tampilan *Google Colab* yang memperlihatkan judul notebook, tombol jalankan di sisi kiri sel, serta dua jenis sel: sel teks dan sel kode.]
Gambar 4.2 Bagian Dasar Notebook di *Google Colab*

## 4.3 Menulis dan Menjalankan Kode Python
Setelah *notebook* siap, kita dapat mulai memakai Colab untuk pekerjaan yang benar-benar dekat dengan linguistik komputasi.

### 4.3.1 Menulis kode sederhana untuk data bahasa
Misalnya kita ingin menyimpan satu kalimat, lalu menghitung jumlah katanya.

```python
kalimat = "Pembelajar bahasa dapat memakai Colab untuk mencoba analisis teks sederhana."
kata_kata = kalimat.split()
print("Daftar kata:", kata_kata)
print("Jumlah kata:", len(kata_kata))
```

**Output**
```text
Daftar kata: ['Pembelajar', 'bahasa', 'dapat', 'memakai', 'Colab', 'untuk', 'mencoba', 'analisis', 'teks', 'sederhana.']
Jumlah kata: 10
```

**Penjelasan**
Baris pertama menyimpan satu kalimat ke variabel `kalimat`. Baris kedua memakai `.split()` untuk memecah kalimat menjadi daftar kata. Baris ketiga menampilkan daftar kata, sedangkan baris keempat menghitung jumlah item di dalam daftar itu.

### 4.3.2 Mengapa Colab nyaman untuk pembelajar?
Salah satu kelebihan Colab adalah hasil dapat langsung terlihat tepat di bawah sel yang dijalankan. Ini sangat membantu pembelajar pemula karena hubungan antara kode dan hasil menjadi lebih jelas. Kita menulis satu langkah, menjalankannya, lalu langsung melihat akibatnya.

### 4.3.3 Mengubah dan menjalankan ulang
Jika kita mengubah isi kalimat, hasilnya juga berubah. Perhatikan contoh berikut.

```python
kalimat = "Kita dapat membandingkan dua paragraf untuk melihat pengulangan kata."
kata_kata = kalimat.split()
print("Jumlah kata:", len(kata_kata))
```

**Output**
```text
Jumlah kata: 9
```

**Penjelasan**
Contoh ini menunjukkan bahwa Colab cocok untuk eksplorasi. Pembaca bisa mengganti isi teks, menjalankan ulang sel, lalu langsung membandingkan hasilnya tanpa harus menutup program atau membuat file baru.

### 4.3.4 Contoh kontekstual: menyiapkan data kosakata target
Bayangkan kita sedang menyiapkan bacaan untuk pembelajaran membaca. Kita ingin tahu kata apa saja yang muncul dalam satu paragraf latihan.

```python
paragraf = "Puisi ini memakai citra malam, sepi, dan hujan untuk membangun suasana yang tenang."
kata_kata = paragraf.lower().split()
print(kata_kata)
```

**Output**
```text
['puisi', 'ini', 'memakai', 'citra', 'malam,', 'sepi,', 'dan', 'hujan', 'untuk', 'membangun', 'suasana', 'yang', 'tenang.']
```

**Penjelasan**
Teks diubah ke huruf kecil dengan `.lower()`, lalu dipecah menjadi daftar kata. Hasil ini belum bersih sepenuhnya karena tanda baca masih menempel. Namun, untuk latihan awal, keluaran ini sudah membantu pembaca melihat bahan kosakata yang muncul.

### Contoh Nyata
Jika pembaca sedang menyiapkan bahan diskusi sastra, Colab dapat dipakai untuk memeriksa kata-kata yang menandai suasana, misalnya *malam*, *sepi*, atau *hujan*. Dengan begitu, diskusi kelas tidak hanya berbasis kesan, tetapi juga berbasis bukti yang tampak di data.

## 4.4 Mengunggah, Membaca, dan Menyimpan File
Setelah nyaman dengan teks pendek di dalam sel, langkah berikutnya adalah bekerja dengan file. Bagian ini sangat penting karena analisis bahasa nyata jarang berhenti pada satu kalimat yang diketik manual.

### 4.4.1 Mengunggah file ke Colab
Salah satu cara termudah mengunggah file adalah memakai modul bawaan Colab berikut.

```python
from google.colab import files
unggahan = files.upload()
print(list(unggahan.keys()))
```

**Output**
```text
['korpus_mini.txt', 'tagger_multibahasa.csv']
```

**Penjelasan**
Saat sel dijalankan, Colab akan membuka pemilih file. Setelah pembaca memilih satu atau beberapa file, nama file yang berhasil masuk akan tampil sebagai keluaran. Pada contoh ini, pembaca mengunggah dua file sekaligus, yaitu `korpus_mini.txt` dan `tagger_multibahasa.csv`. Jika file yang dipilih berbeda, nama yang muncul juga akan berbeda.

### 4.4.2 Mengapa latihan awal sebaiknya memakai `.txt` atau `.csv`?
Pada tahap awal, dua jenis file yang paling ramah untuk pembelajaran adalah file `.txt` dan *CSV* (*comma-separated values*, yaitu data berbentuk kolom yang dipisahkan koma).

| Jenis file | Cocok untuk | Mengapa berguna pada tahap awal |
|---|---|---|
| `.txt` | korpus sederhana, satu teks utuh, kumpulan paragraf, atau satu dokumen per file | isinya berupa *plain text* (teks polos), mudah dibaca, mudah diperiksa, dan tidak membawa format tersembunyi seperti warna, tabel rumit, atau tata letak halaman |
| `.csv` | data berbaris dan berkolom, misalnya token, bahasa, label kelas kata, atau metadata korpus | strukturnya rapi, mudah diproses sebagai tabel, dan sangat cocok untuk data tagger, anotasi, atau korpus multilingual |

Dengan kata lain, `.txt` membantu ketika fokus kita ada pada isi teksnya, sedangkan `.csv` membantu ketika fokus kita ada pada struktur datanya.

### 4.4.3 Contoh `.txt` untuk korpus sederhana
Bayangkan kita punya satu file bernama `korpus_mini.txt`. Isinya tiga baris teks pendek, masing-masing mewakili satu dokumen latihan dalam korpus kecil.

```python
with open("korpus_mini.txt", "r", encoding="utf-8") as file:
    isi = file.read()

print(isi)
```

**Output**
```text
cerpen pertama memakai banyak kata malam dan hujan.
artikel kedua menekankan pentingnya membaca kritis.
esai ketiga membahas variasi kosakata dalam tulisan pembelajar.
```

**Penjelasan**
Blok `with open(...)` membuka file teks bernama `korpus_mini.txt`, lalu seluruh isinya disimpan ke variabel `isi`. Bentuk `.txt` cocok untuk korpus sederhana karena pembaca dapat langsung melihat isi teks tanpa gangguan format lain.

Sekarang kita olah file itu sebagai korpus mini.

```python
with open("korpus_mini.txt", "r", encoding="utf-8") as file:
    isi = file.read()

dokumen = isi.strip().split("\n")
semua_kata = isi.split()

print("Jumlah baris teks:", len(dokumen))
print("Jumlah kata:", len(semua_kata))
print("Baris pertama:", dokumen[0])
```

**Output**
```text
Jumlah baris teks: 3
Jumlah kata: 22
Baris pertama: cerpen pertama memakai banyak kata malam dan hujan.
```

**Penjelasan**
Pada contoh ini, setiap baris diperlakukan sebagai satu dokumen kecil. Variabel `dokumen` menyimpan tiga baris teks, sedangkan `semua_kata` menyimpan semua kata yang terbaca dari seluruh file. Pola seperti ini berguna untuk latihan awal korpus, misalnya ketika pembaca ingin menghitung jumlah dokumen, jumlah kata, atau mulai membandingkan isi antarbaris.

### 4.4.4 Contoh `.csv` untuk data tagger atau korpus multilingual
Sekarang bayangkan kita punya file `tagger_multibahasa.csv` dengan tiga kolom berikut:
- `token`, yaitu kata yang diamati,
- `bahasa`, yaitu kode bahasa, misalnya `id` atau `en`, dan
- `label`, yaitu label seperti `NOUN`, `VERB`, atau `ADJ`.

Struktur seperti ini sangat cocok untuk data tagger atau korpus multilingual karena setiap baris mewakili satu unit data yang jelas.

```python
import csv

with open("tagger_multibahasa.csv", "r", encoding="utf-8") as file:
    pembaca = csv.DictReader(file)
    data = list(pembaca)

print("Jumlah baris data:", len(data))
print("Tiga baris pertama:", data[:3])
```

**Output**
```text
Jumlah baris data: 5
Tiga baris pertama: [{'token': 'rumah', 'bahasa': 'id', 'label': 'NOUN'}, {'token': 'beautiful', 'bahasa': 'en', 'label': 'ADJ'}, {'token': 'makan', 'bahasa': 'id', 'label': 'VERB'}]
```

**Penjelasan**
Modul `csv` membaca file berkolom itu lalu mengubah setiap baris menjadi kamus kecil dengan nama kolom sebagai kunci. Bentuk seperti ini memudahkan pembaca memproses data teranotasi tanpa harus menebak-nebak urutan kolom secara manual.

Setelah file berhasil dibaca, kita dapat memprosesnya lebih lanjut.

```python
jumlah_id = sum(1 for baris in data if baris["bahasa"] == "id")
jumlah_noun = sum(1 for baris in data if baris["label"] == "NOUN")

print("Baris bahasa Indonesia:", jumlah_id)
print("Label NOUN:", jumlah_noun)
```

**Output**
```text
Baris bahasa Indonesia: 3
Label NOUN: 2
```

**Penjelasan**
Contoh ini menunjukkan mengapa `.csv` penting untuk data tagger atau korpus multilingual. Karena setiap unsur sudah dipisahkan ke dalam kolom, kita bisa langsung menghitung berapa banyak token bahasa Indonesia atau berapa banyak label `NOUN`. Pada data yang lebih besar, pola ini sangat membantu untuk inspeksi awal sebelum analisis yang lebih jauh.

### 4.4.5 Menyimpan hasil kerja ke file baru
Hasil pengolahan juga dapat disimpan kembali.

```python
teks_bersih = "bahasa yang baik membantu pembaca memahami gagasan dengan lebih jernih"

with open("hasil_bersih.txt", "w", encoding="utf-8") as file:
    file.write(teks_bersih)

print("File hasil_bersih.txt berhasil disimpan")
```

**Output**
```text
File hasil_bersih.txt berhasil disimpan
```

**Penjelasan**
Kode ini membuat file baru bernama `hasil_bersih.txt`, lalu menuliskan isi `teks_bersih` ke dalamnya. Pola kerja seperti ini sangat berguna ketika pembaca mulai membersihkan data, menyimpan daftar kata, atau menyiapkan hasil analisis sederhana.

### 4.4.6 Hal penting tentang file di Colab
Bagian ini sering membingungkan pemula. Perlu diingat:
- file yang diunggah untuk satu sesi belum tentu tetap ada pada sesi berikutnya,
- file hasil pengolahan sebaiknya segera diunduh atau disimpan lagi dengan sadar, dan
- *notebook* yang dibagikan tidak otomatis membawa semua file tambahan serta pustaka yang dipasang selama sesi berjalan (Google, 2026b).

Karena itu, untuk tugas kuliah atau kerja kelas, sebaiknya pembaca menuliskan dengan jelas:
1. file apa yang dibutuhkan,
2. bagaimana cara mengunggahnya,
3. apakah file itu `.txt` atau `.csv`, dan
4. hasil apa yang seharusnya muncul.

### Coba Perhatikan
Pada tahap awal, file `.txt` dan `.csv` adalah dua pilihan yang paling aman untuk latihan. File `.txt` cocok ketika pembaca ingin fokus pada isi teks atau korpus sederhana. File `.csv` cocok ketika pembaca ingin fokus pada data berkolom, misalnya token, bahasa, label, atau metadata.

[Cuplikan tampilan panel file di sisi kiri *Google Colab* yang menunjukkan satu file teks berhasil diunggah, lalu satu file hasil baru muncul setelah kode dijalankan.]
Gambar 4.3 Mengunggah dan Menyimpan File di *Google Colab*

## 4.5 Berbagi Notebook untuk Tugas dan Praktik Kelas
Salah satu kelebihan paling praktis dari Colab adalah kemudahan berbagi. Menurut penjelasan resmi Google, *notebook* Colab dapat disimpan di Google Drive dan dibagikan seperti berbagi Google Docs atau Google Sheets (Google, 2026b).

### 4.5.1 Apa yang sebenarnya dibagikan?
Ini pertanyaan yang sangat penting. Ketika pembaca membagikan *notebook*, yang dibagikan adalah isi penuh *notebook* itu, termasuk:
- teks,
- kode,
- keluaran, dan
- komentar di dalam *notebook* (Google, 2026b).

Namun, mesin virtual yang sedang dipakai, file sementara, dan pustaka tambahan yang dipasang selama sesi belum tentu ikut terbawa saat *notebook* dibagikan (Google, 2026b). Inilah sebabnya mengapa *notebook* yang tampak berjalan baik di akun kita belum tentu langsung berjalan mulus di akun orang lain.

### 4.5.2 Checklist sebelum berbagi notebook
Sebelum menekan tombol **Share**, periksa hal-hal berikut.

| Yang perlu dicek | Mengapa penting |
|---|---|
| judul *notebook* jelas | penerima langsung tahu fungsi file |
| ada penjelasan singkat di awal | penerima paham tujuan latihan |
| urutan sel rapi | penerima tidak bingung langkah mana dijalankan lebih dulu |
| file yang dibutuhkan disebutkan | penerima tahu apa yang perlu diunggah |
| tidak ada data sensitif | pembaca lain tidak melihat data yang seharusnya privat |
| keluaran dibersihkan bila perlu | tampilan *notebook* lebih rapi dan aman dibagikan |

Jika pembaca tidak ingin keluaran sel ikut tersimpan, Colab menyediakan pengaturan untuk mengabaikan keluaran saat menyimpan *notebook* melalui menu pengaturan *notebook* (Google, 2026b).

### 4.5.3 Contoh kontekstual untuk pembelajaran bahasa
Bayangkan pembaca ingin memberi tugas sederhana kepada teman sekelompok. Tugasnya adalah membaca satu file teks, menghitung jumlah kata, lalu menandai tiga kata yang paling penting untuk diskusi. Dalam situasi seperti ini, *notebook* Colab sangat berguna karena pembaca bisa membagikan:
- instruksi tugas,
- contoh file,
- contoh kode awal, dan
- ruang kosong untuk modifikasi.

Dengan begitu, penerima tidak memulai dari halaman kosong. Mereka tinggal membuka *notebook*, membaca instruksi, lalu mencoba menjalankan atau mengubah kode yang sudah tersedia.

### 4.5.4 Catatan etis saat berbagi
Karena Colab mudah dibagikan, pembaca juga perlu lebih berhati-hati. Jangan unggah atau bagikan:
- tulisan pembelajar yang masih memuat identitas pribadi,
- transkrip percakapan tanpa izin,
- bahan ujian rahasia,
- atau materi berhak cipta yang tidak memiliki izin penggunaan.

Jika pembaca memakai fitur AI di Colab, kehati-hatian ini menjadi lebih penting. Google menjelaskan bahwa prompt, potongan kode terkait, dan keluaran fitur AI dapat dikumpulkan untuk penyediaan serta pengembangan layanan. Karena itu, jangan masukkan data sensitif atau informasi pribadi ke fitur tersebut (Google, 2026b).

### Contoh Nyata
Untuk kerja kelompok analisis bahasa, pola yang aman adalah ini: bagikan *notebook* kosong terstruktur, gunakan data latihan yang aman, dan minta setiap anggota menyalin hasil akhir ke file atau folder masing-masing. Pola ini lebih tertib daripada semua anggota mengedit satu *notebook* dengan data campur aduk.

## Ringkasan
Bab ini menunjukkan bahwa *Google Colab* dapat dipakai sebagai laboratorium praktik yang ringan, mudah diakses, dan sangat cocok untuk pembelajaran linguistik komputasi. Colab memungkinkan pembaca menulis dan menjalankan *Python* melalui peramban tanpa instalasi rumit, sambil menggabungkan catatan, kode, dan keluaran dalam satu *notebook*.

Bab ini juga menegaskan beberapa hal praktis yang sangat penting. Pertama, *notebook* berbeda dari file sementara dan berbeda pula dari mesin virtual tempat kode dijalankan. Kedua, Colab sangat cocok untuk mencoba analisis bahasa sederhana, baik dengan file `.txt` untuk korpus kecil maupun file `.csv` untuk data berkolom seperti tagger atau korpus multilingual. Ketiga, kemudahan berbagi *notebook* harus diimbangi dengan ketelitian dalam menata langkah, menyebut kebutuhan file, dan menjaga etika data.

Dengan bekal ini, pembaca siap memakai Colab bukan hanya sebagai tempat mengetik kode, tetapi sebagai ruang kerja belajar yang rapi, terarah, dan relevan untuk analisis bahasa.

## Latihan Akhir Bab
### Latihan 1. Membuka notebook pertama
Buat satu *notebook* baru di Colab, ubah judulnya, lalu jalankan satu sel kode yang menampilkan salam pembuka.

### Latihan 2. Menulis kode sederhana
Ketik satu kalimat pendek tentang pembelajaran bahasa ke dalam variabel `kalimat`, lalu:
1. tampilkan kalimat itu,
2. pecah menjadi daftar kata, dan
3. hitung jumlah katanya.

### Latihan 3. Mengunggah file teks
Siapkan satu file `.txt` pendek, lalu:
1. unggah file itu ke Colab,
2. baca isinya,
3. tampilkan isi file, dan
4. hitung jumlah katanya.

### Latihan 4. Mengolah file CSV
Siapkan satu file `.csv` kecil dengan kolom `token`, `bahasa`, dan `label`, lalu:
1. unggah file itu ke Colab,
2. baca isinya,
3. tampilkan tiga baris pertama, dan
4. hitung berapa banyak baris yang memiliki kode bahasa `id`.

### Latihan 5. Menyimpan hasil
Buat satu file baru bernama `hasil_latihan.txt` yang berisi versi huruf kecil dari teks yang tadi dibaca.

## Proyek Mini
### Proyek Mini 4. Menyiapkan Notebook Analisis Bacaan
**Tujuan pembelajaran**  
Membuat *notebook* Colab sederhana yang siap dipakai untuk membaca satu file `.txt` atau `.csv`, melakukan pengolahan awal, dan menyimpan hasil analisis dasar.

**Alat yang digunakan**  
*Google Colab* dan satu file `.txt` atau `.csv` yang aman dipakai untuk latihan.

**Instruksi**
1. Buka *Google Colab* dan buat *notebook* baru.
2. Beri judul *notebook* yang jelas.
3. Tambahkan satu sel teks berisi tujuan latihan.
4. Unggah satu file latihan, bisa berupa `.txt` atau `.csv`.
5. Jika file berupa `.txt`, baca isi file lalu hitung jumlah katanya.
6. Jika file berupa `.csv`, baca isinya lalu tampilkan beberapa baris pertama.
7. Lakukan satu pengolahan awal yang sesuai dengan jenis file.
8. Simpan satu versi hasil sederhana ke file baru, misalnya `hasil_ringkas.txt`.
9. Rapikan *notebook* agar orang lain dapat memahaminya.

**Keluaran yang diharapkan**  
Satu *notebook* Colab yang berisi:
- judul yang jelas,
- penjelasan singkat tujuan,
- kode untuk membaca file,
- keluaran isi file `.txt` atau beberapa baris awal file `.csv`,
- satu hasil pengolahan awal, dan
- satu file hasil sederhana yang disimpan dari proses tersebut.

**Refleksi**  
Bagian mana yang terasa paling mudah? Bagian mana yang masih membingungkan? Menurut pembaca, apakah Colab lebih cocok dipakai untuk latihan awal, kerja kelompok, atau eksplorasi mandiri? Jelaskan alasannya.

## 🧠 Istilah yang dipelajari pada bab ini
- *Google Colab*: layanan *notebook* daring dari Google untuk menulis dan menjalankan *Python* melalui peramban.
- *notebook Jupyter* (*buku catatan komputasional Jupyter*): dokumen interaktif yang dapat memadukan teks, kode, dan keluaran.
- *hosted service* (layanan yang sudah disiapkan daring): layanan yang tidak perlu dipasang sendiri di komputer pengguna.
- *virtual machine* (*mesin virtual*): lingkungan komputasi tempat kode dijalankan selama sesi berlangsung.
- sel kode: bagian *notebook* yang dipakai untuk menulis dan menjalankan kode.
- sel teks: bagian *notebook* yang dipakai untuk menulis penjelasan atau catatan.
- *CSV* (*comma-separated values*): format data berkolom yang cocok untuk token, label, bahasa, atau metadata lain.
- unggah file: proses memasukkan file dari perangkat pengguna ke lingkungan kerja Colab.
- berbagi *notebook*: proses memberikan akses *notebook* kepada orang lain untuk dilihat atau disunting.
- keluaran (*output*): hasil yang muncul setelah kode dijalankan.
- sesi: periode kerja ketika *notebook* terhubung dengan lingkungan eksekusi Colab.

## Sumber Gambar yang Perlu Disiapkan
- [Tangkapan layar halaman awal *Google Colab* setelah pengguna membuat notebook baru, dengan area judul dan satu sel kode kosong terlihat jelas.]
  Gambar 4.4 Halaman Awal Notebook di *Google Colab*
- [Tangkapan layar satu notebook Colab yang menampilkan hasil *print*, daftar kata, dan jumlah kata di bawah sel kode sebagai contoh hubungan kode dan keluaran.]
  Gambar 4.5 Contoh Keluaran Kode di *Google Colab*
- [Tangkapan layar dialog berbagi notebook di *Google Colab* atau Google Drive yang menunjukkan opsi berbagi aman untuk pembelajaran.]
  Gambar 4.6 Berbagi Notebook untuk Tugas dan Praktik Kelas
