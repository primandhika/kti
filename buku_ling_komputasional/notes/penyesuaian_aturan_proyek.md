# Penyesuaian Aturan Proyek

## Proyek Aktif
**Linguistik Komputasi dalam Pembelajaran Bahasa: Pengantar Praktis dengan Python dan Google Colab**

## Observasi Awal
Dokumen `ATURAN_BUKU.md` memuat aturan kerja yang sebagian besar tetap relevan untuk proyek ini, walaupun judul dokumennya masih merujuk pada buku lain, yaitu *Mahir Presentasi Ilmiah*. Untuk kelancaran proyek saat ini, aturan tersebut saya tafsirkan sebagai **pedoman umum penulisan buku**, lalu saya sesuaikan konteksnya ke buku linguistik komputasi untuk mahasiswa bahasa, linguistik, pendidikan bahasa, dan sastra.

---

## Aturan Kerja yang Akan Dipakai untuk Proyek Ini

### 1. Struktur kerja naskah
- Naskah akan dikerjakan **bab per bab**, bukan lompat-lompat, kecuali ada arahan lain.
- Sebelum merevisi bab atau bagian tertentu, file target harus dibaca dulu.
- Jika revisi besar dilakukan pada file aktif, perubahan penting akan dicatat dalam berkas Markdown pada folder `_rev` yang sesuai.
- File arsip, cadangan, hasil konversi, atau bahan mentah tidak akan otomatis dianggap sebagai naskah utama.

### 2. Posisi naskah utama
Artefak kerja utama di folder `main` saat ini adalah:
- `00-hal-awal.md`
- `00-daftar-isi.md`
- `01-mengapa-linguistik-komputasi-penting-untuk-pembelajaran-bahasa.md`
- `02-bahasa-sebagai-data.md`
- `03-python-untuk-pemula-dalam-analisis-bahasa.md`
- `99-daftar-pustaka.md`

Naskah isi bab berikutnya harus mengikuti pola penamaan numerik yang konsisten sekaligus memuat judul bab secara ringkas, misalnya:
- `04-google-colab-sebagai-laboratorium-linguistik-komputasi.md`
- `05-membaca-dan-membersihkan-teks.md`
- dan seterusnya.

### 3. Gaya bahasa
- Bahasa Indonesia harus mengikuti kaidah yang baik, benar, wajar, dan sesuai EYD Edisi V.
- Saya akan **menghindari em dash**.
- Gaya penulisan akan dibuat **ramah pemula**, tidak terlalu teknis, tetapi tetap akademik.
- Istilah teknis seperti *tokenisasi*, *korpus*, *NLP*, *string*, atau *Google Colab* akan dijelaskan saat pertama kali muncul.
- Istilah asing, istilah teknis, atau nama fungsi penting akan dipertahankan bila memang lebih tepat dalam bentuk aslinya, tetapi harus diberi penjelasan atau padanan bahasa Indonesia saat kemunculan awal.
- Sudut pandang pembaca harus inklusif. Narasi tidak boleh mengandaikan pembaca pasti dosen atau mahasiswa, kecuali konteks bagian memang sedang membahas peran tersebut secara khusus.
- Contoh akan diprioritaskan pada konteks pembelajaran bahasa, sastra, dan pendidikan tinggi di Indonesia.

### 4. Fokus isi akademik
- Buku ini akan diposisikan sebagai **buku ajar praktis**, bukan buku teori murni dan bukan pula buku pemrograman tingkat lanjut.
- Klaim akademik, pedagogis, atau evaluatif akan didukung sitasi jika bagian itu memang sedang dikembangkan dengan rujukan.
- Untuk topik mutakhir seperti AI, LLM, platform digital, dan praktik terkini, sumber akan diutamakan dari **2022 ke atas**.
- Teori dasar boleh memakai sumber klasik yang masih relevan.
- Daftar pustaka akan dibuat **kolektif di akhir buku**, bukan per bab.
- Format bibliografi yang dipakai adalah **APA 7th edition**.

### 5. Materi praktis dan latihan
Karena buku ini bersifat langkah demi langkah, setiap bab praktik sebisa mungkin memuat:
- tujuan pembelajaran,
- alat yang digunakan,
- langkah kerja yang jelas,
- keluaran yang diharapkan,
- refleksi atau evaluasi,
- latihan akhir bab,
- dan bila cocok, proyek mini.

Untuk bagian yang memakai Python, Google Colab, atau alat AI, penjelasan akan menekankan:
- apa fungsi alatnya,
- bagaimana langkah memakainya,
- batasannya,
- dan tanggung jawab pengguna terhadap hasilnya.

Khusus untuk bab-bab praktik, contoh tidak boleh berhenti pada kode yang abstrak. Contoh harus:
- kontekstual dengan linguistik dan pembelajaran bahasa,
- menarik untuk dicoba mahasiswa,
- cukup sederhana untuk diikuti langkah demi langkah,
- dan memperlihatkan hubungan jelas antara kode, data teks, dan tujuan analisis.

### 6. Visual dan elemen pembaca
- Setiap bab idealnya memuat **2 sampai 3 visual utama** yang memang membantu pemahaman.
- Jika visual belum tersedia, akan digunakan penanda tempat seperti:
  `[Tangkapan layar antarmuka Google Colab dengan contoh notebook sederhana]`
  
  `Gambar 4.1 Antarmuka Google Colab untuk Praktik Analisis Teks`
- Visual tidak akan dimasukkan hanya sebagai hiasan.
- Elemen seperti **Trivia**, **Contoh Nyata**, **Kesalahan Umum**, atau **Latihan Singkat** dapat dipakai bila membantu pembaca.

### 7. Penyesuaian khusus untuk buku ini
Beberapa butir dalam `ATURAN_BUKU.md` perlu ditafsir ulang agar cocok dengan proyek sekarang:
- Rujukan yang sebelumnya diarahkan ke konteks *presentasi ilmiah* akan dialihkan ke konteks **pembelajaran bahasa, linguistik, sastra, dan literasi komputasional**.
- Contoh dan ilustrasi tidak akan berfokus pada presentasi ilmiah, tetapi pada:
  - analisis bacaan,
  - kosakata,
  - tulisan siswa,
  - tata bahasa,
  - teks sastra,
  - dan penggunaan Python atau Colab dalam kegiatan belajar.
- Bagian etika digital akan diperluas ke isu:
  - privasi data siswa,
  - penggunaan AI secara bertanggung jawab,
  - hak cipta bahan ajar,
  - dan keterbatasan alat otomatis dalam analisis bahasa.

---

## Implikasi Langsung untuk Proyek Saat Ini
Mulai tahap berikutnya, saya akan bekerja dengan aturan operasional berikut:
1. Menjadikan `daftar_isi_final.md` sebagai dasar struktur isi buku.
2. Mengembangkan naskah **bab per bab**.
3. Menulis dengan pola ajar yang konsisten dan ramah mahasiswa pemula.
4. Menjaga agar contoh selalu relevan dengan pembelajaran bahasa.
5. Pada bab Python, Google Colab, dan praktik analisis teks, memperbanyak contoh yang kontekstual dengan linguistik, menarik untuk dicoba, dan tidak terlalu abstrak.
6. Tidak menaruh daftar pustaka di akhir tiap bab.
7. Menyisipkan kebutuhan visual sebagai penanda tempat bila gambar final belum ada.
8. Menyiapkan latihan akhir bab sebagai komponen tetap.
9. Menambahkan bagian peristilahan di akhir setiap bab dengan label `🧠 Istilah yang dipelajari pada bab ini` sebagai bahan baku glosarium buku.

---

## Rekomendasi Operasional Berikutnya
Agar proyek lebih lancar, urutan kerja yang paling aman adalah:
1. finalisasi daftar isi,
2. buat template baku isi bab,
3. tulis Bab 1 sampai selesai,
4. cek gaya, struktur, dan arah pedagogisnya,
5. baru lanjut ke bab berikutnya.

Dengan begitu, konsistensi buku akan lebih mudah dijaga sejak awal.
