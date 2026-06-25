import re

l1_path = "/home/primandhika/artikel/dist/main/02_Lampiran_01_Instrumen_Penelitian.md"
with open(l1_path, 'r', encoding='utf-8') as f:
    content = f.read()

# Angket Respons Mahasiswa
respons_kisi = """
### KISI-KISI ANGKET

| No | Aspek | Indikator | Nomor Item |
|:---:|---|---|:---:|
| 1 | Kualitas Isi | Materi mudah dipahami dan sesuai kebutuhan belajar | 1, 2 |
| 2 | Kemudahan Penggunaan | Media mudah diakses dan digunakan | 3, 4 |
| 3 | Interaktivitas | Media mendorong keterlibatan aktif mahasiswa | 5, 6 |
| 4 | Dampak terhadap Keterampilan Berbicara | Media membantu meningkatkan keterampilan berbicara | 7, 8 |
| 5 | Dampak terhadap Metakognitif | Media membantu meningkatkan kemampuan metakognitif | 9, 10 |

### PERNYATAAN ANGKET

| No. | Pernyataan | STS | TS | S | SS |
|:---:|---|:---:|:---:|:---:|:---:|
| 1 | Materi dalam media pembelajaran ini mudah dipahami dan sesuai dengan tingkat kemampuan saya | | | | |
| 2 | Isi materi dalam media ini relevan dengan kebutuhan belajar keterampilan berbicara saya | | | | |
| 3 | Media pembelajaran web microlearning ini sulit diakses dan digunakan | | | | |
| 4 | Navigasi dan fitur-fitur dalam media ini mudah dipahami dan dioperasikan | | | | |
| 5 | Media ini mendorong saya untuk terlibat aktif dalam proses pembelajaran | | | | |
| 6 | Fitur-fitur interaktif dalam media ini membuat pembelajaran menjadi lebih menarik | | | | |
| 7 | Media ini kurang membantu meningkatkan kemampuan saya dalam berbicara | | | | |
| 8 | Setelah menggunakan media ini, saya merasa lebih percaya diri saat berbicara | | | | |
| 9 | Media ini membantu saya lebih sadar akan proses berpikir dan belajar saya sendiri | | | | |
| 10 | Media ini meningkatkan kemampuan saya untuk merencanakan, memantau, dan mengevaluasi pembelajaran saya | | | | |
| 11 | Durasi setiap modul pembelajaran tidak sesuai dan ada yang terlalu panjang/pendek | | | | |
| 12 | Materi tersegmentasi (terbagi) dengan baik dalam unit-unit kecil yang mudah dipelajari | | | | |
| 13 | Saya dapat mengakses media ini kapan saja dan di mana saja dengan mudah | | | | |
| 14 | Format microlearning membantu membuat saya fokus pada materi yang sedang dipelajari | | | | |
| 15 | Media ini membantu saya menjelaskan konsep dengan bahasa yang lebih sederhana | | | | |
| 16 | Teknik "penyederhanaan & ulangi" dalam media ini meningkatkan pemahaman saya terhadap materi | | | | |
| 17 | Saya bisa mengidentifikasi bagian materi yang belum saya pahami dengan baik | | | | |
| 18 | Media ini mendorong saya untuk merefleksikan pemahaman saya sendiri | | | | |

### REKAPITULASI SKOR

| Aspek | Nomor | Perolehan | Persentase |
|---|:---:|:---:|:---:|
| Kualitas Isi | 1, 2 | ___/8 | ___% |
| Kemudahan Penggunaan | 3, 4 | ___/8 | ___% |
| Interaktivitas | 5, 6 | ___/8 | ___% |
| Dampak Keterampilan Berbicara | 7, 8 | ___/8 | ___% |
| Dampak Metakognitif | 9, 10 | ___/8 | ___% |
| Karakteristik Microlearning | 11-14 | ___/16 | ___% |
| Penerapan Teknik Feynman | 15-18 | ___/16 | ___% |
| **TOTAL KESELURUHAN** | 1-18 | ___/72 | ___% |
"""

observasi_kisi = """
### KISI-KISI OBSERVASI

| No | Aspek Observasi | Indikator | 1 | 2 | 3 | 4 | Ket. |
|:---:|---|---|:---:|:---:|:---:|:---:|---|
| **A** | **DURASI DAN INTENSITAS PENGGUNAAN MEDIA** | | | | | | |
| 1 | Ketepatan Waktu Akses | Mahasiswa mengakses media sesuai jadwal | | | | | |
| 2 | Durasi Belajar Efektif | Mahasiswa belajar sesuai durasi microlearning | | | | | |
| 3 | Konsistensi Penggunaan | Mahasiswa menyelesaikan seluruh tahapan modul | | | | | |
| **B** | **PARTISIPASI DAN KETERLIBATAN** | | | | | | |
| 4 | Keterlibatan dalam Modul | Mahasiswa mengikuti semua komponen modul | | | | | |
| 5 | Interaksi dengan Konten | Mahasiswa menyimak materi dan video | | | | | |
| 6 | Interaksi dengan Teman | Mahasiswa berdiskusi saat mengalami kesulitan | | | | | |
| **C** | **PENERAPAN TEKNIK FEYNMAN** | | | | | | |
| 7 | Penyederhanaan Konsep | Mahasiswa mampu menyederhanakan bahasa materi | | | | | |
| 8 | Kemampuan Mengidentifikasi Celah | Mahasiswa sadar bagian mana yang ia belum paham | | | | | |
| 9 | Pengulangan dan Perbaikan | Mahasiswa merevisi rekaman setelah evaluasi diri | | | | | |
"""

validasi_materi_kisi = """
### BUTIR PENILAIAN

| No | Indikator | 1 | 2 | 3 | 4 |
|:---:|---|:---:|:---:|:---:|:---:|
| **A** | **Kelayakan Isi Materi** | | | | |
| 1 | Kesesuaian materi dengan kompetensi keterampilan berbicara | | | | |
| 2 | Keakuratan konsep dan teori keterampilan berbicara yang disajikan | | | | |
| 3 | Kedalaman dan kelengkapan materi keterampilan berbicara | | | | |
| 4 | Kesesuaian materi dengan kurikulum yang berlaku | | | | |
| 5 | Relevansi materi dengan kebutuhan pembelajaran mahasiswa | | | | |
| **B** | **Desain Pembelajaran Microlearning** | | | | |
| 6 | Kesesuaian segmentasi materi dalam format bite-sized | | | | |
| 7 | Sistematika penyajian materi yang runtut dan logis | | | | |
| 8 | Kemudahan materi untuk dipahami mahasiswa | | | | |
| 9 | Keterpaduan antar modul dalam satu kesatuan pembelajaran | | | | |
| 10 | Fleksibilitas akses materi sesuai kebutuhan belajar mandiri | | | | |
| **C** | **Kesesuaian dengan Teknik Feynman** | | | | |
| 11 | Kesesuaian materi dengan prinsip teknik Feynman | | | | |
| 12 | Dukungan materi terhadap kemampuan menjelaskan konsep sederhana | | | | |
| 13 | Kesesuaian aktivitas pembelajaran dengan filosofi *learning by teaching* | | | | |
| 14 | Dukungan materi untuk identifikasi kesenjangan pemahaman | | | | |
| **D** | **Pengembangan Kemampuan Metakognitif** | | | | |
| 15 | Dukungan materi untuk pengembangan perencanaan (*planning*) | | | | |
| 16 | Dukungan materi untuk pengembangan pemantauan (*monitoring*) | | | | |
| 17 | Dukungan materi untuk pengembangan evaluasi (*evaluation*) | | | | |
"""

validasi_media_kisi = """
### BUTIR PENILAIAN

| No | Indikator | 1 | 2 | 3 | 4 |
|:---:|---|:---:|:---:|:---:|:---:|
| **A** | **Kualitas Tampilan (User Interface)** | | | | |
| 1 | Kemenarikan desain antarmuka aplikasi secara keseluruhan | | | | |
| 2 | Kesesuaian tipografi (jenis, ukuran, dan warna font) | | | | |
| 3 | Komposisi warna antarmuka yang harmonis dan nyaman | | | | |
| 4 | Kualitas grafis dan visualisasi pendukung materi | | | | |
| 5 | Konsistensi *layout* pada setiap halaman modul | | | | |
| **B** | **Kualitas Pengalaman Pengguna (User Experience)** | | | | |
| 6 | Kemudahan navigasi dan perpindahan antar modul | | | | |
| 7 | Kejelasan tombol, ikon, dan tautan (intuitif) | | | | |
| 8 | Kecepatan respons (*loading time*) saat mengakses materi/video | | | | |
| 9 | Aksesibilitas media pada berbagai ukuran layar (*responsive*) | | | | |
| 10 | Ketiadaan eror pada sistem autentikasi dan pendaftaran | | | | |
| **C** | **Fungsionalitas Fitur Pembelajaran** | | | | |
| 11 | Kestabilan fitur perekaman suara/video langsung | | | | |
| 12 | Keandalan sistem penyimpanan progres belajar (*log*) | | | | |
| 13 | Fungsionalitas pemutaran ulang rekaman dan pemberian komentar | | | | |
| 14 | Integrasi video materi pembelajaran ke dalam sistem (*embedded*) | | | | |
| 15 | Fungsionalitas sistem penilaian dan refleksi mandiri | | | | |
"""

wawancara_kisi = """
### KISI-KISI WAWANCARA MAHASISWA

| No | Aspek | Indikator | Pertanyaan |
|:---:|---|---|:---:|
| 1 | Pengalaman Penggunaan Media | Kemudahan/kesulitan menggunakan media | P1, P2 |
| 2 | Persepsi Kualitas Materi | Kejelasan dan relevansi isi materi | P3 |
| 3 | Dampak pada Keterampilan Berbicara | Perubahan kemampuan berbicara | P4 |
| 4 | Dampak pada Metakognitif | Pengaruh terhadap perencanaan, pemantauan, evaluasi | P5 |
| 5 | Saran Pengembangan | Masukan untuk perbaikan media | P6 |

### PERTANYAAN WAWANCARA MAHASISWA

**1. PENGALAMAN PENGGUNAAN MEDIA**
- **P1:** Bagaimana pengalaman Anda secara keseluruhan saat menggunakan media pembelajaran web *microlearning* ini?
- **P2:** Kesulitan apa saja yang Anda hadapi saat menggunakan media ini? Bagian mana yang paling mudah atau sulit diakses?
  - *Pertanyaan pengembangan: Apakah navigasinya mudah dipahami? Kecepatan loading? Ada kendala teknis?*

**2. PERSEPSI KUALITAS MATERI**
- **P3:** Menurut Anda, bagaimana kualitas materi yang disajikan dalam media ini? Apakah materinya mudah dipahami dan relevan?
  - *Pertanyaan pengembangan: Durasi modul pas? Contoh yang diberikan jelas?*

**3. DAMPAK PADA KETERAMPILAN BERBICARA**
- **P4:** Setelah menggunakan media ini, perubahan apa yang Anda rasakan dalam keterampilan berbicara Anda?
  - *Pertanyaan pengembangan: Apakah lebih percaya diri? Mampu menjelaskan dengan bahasa sendiri? Teknik Feynman membantu?*

**4. DAMPAK PADA METAKOGNITIF**
- **P5:** Bagaimana media ini memengaruhi cara Anda merencanakan, memantau, dan mengevaluasi persiapan presentasi Anda?
  - *Pertanyaan pengembangan: Apakah sekarang lebih sadar akan kesalahan bicara? Membantu persiapan lebih matang?*

**5. SARAN PENGEMBANGAN**
- **P6:** Masukan atau saran apa yang bisa Anda berikan untuk menyempurnakan media ini?
"""

# Replace in content using regex logic
import re

# Replace Angket Respons
content = re.sub(r'### KISI-KISI ANGKET.*?### EVALUASI KUALITATIF', respons_kisi + "\n### EVALUASI KUALITATIF", content, flags=re.DOTALL)

# Replace Observasi
content = re.sub(r'### KISI-KISI OBSERVASI.*?### CATATAN LAPANGAN', observasi_kisi + "\n### CATATAN LAPANGAN", content, flags=re.DOTALL)

# Replace Validasi Materi
content = re.sub(r'### KISI-KISI VALIDASI MATERI.*?### PENILAIAN UMUM', validasi_materi_kisi + "\n### PENILAIAN UMUM", content, flags=re.DOTALL)

# Replace Validasi Media
content = re.sub(r'### KISI-KISI VALIDASI MEDIA.*?### PENILAIAN UMUM', validasi_media_kisi + "\n### PENILAIAN UMUM", content, flags=re.DOTALL)

# Replace Wawancara
content = re.sub(r'### KISI-KISI WAWANCARA MAHASISWA.*?B\. PEDOMAN WAWANCARA DOSEN', wawancara_kisi + "\n\n## B. PEDOMAN WAWANCARA DOSEN", content, flags=re.DOTALL)

# Write back
with open(l1_path, 'w', encoding='utf-8') as f:
    f.write(content)

print("Berhasil merapikan tabel-tabel instrumen di Lampiran 01")
