# Catatan Revisi: Draf Bab 8 Analisis Morfologi dan Pola Kata

## Berkas yang dibuat atau diubah
- `main/08-analisis-morfologi-dan-pola-kata.md` (baru)
- `main/99-daftar-pustaka.md` (ditambah 5 entri baru)

## Fokus isi
1. Memperkenalkan morfologi bahasa Indonesia sebagai kelanjutan dari frekuensi dan konteks kata.
2. Menjelaskan kata dasar, imbuhan (prefiks, sufiks, konfiks), dan nasalisasi.
3. Menunjukkan pengelompokan kata berdasarkan awalan dan akhiran.
4. Memperkenalkan *regular expression* (*regex*) untuk pencarian pola kata.
5. **Memperkenalkan pustaka Sastrawi / PySastrawi dengan atribusi penuh.**
6. Menerapkan *stemming* untuk analisis keluarga kata, kompleksitas morfologis, deteksi kesalahan ejaan, dan perbandingan dua tulisan.

## Atribusi library yang ditambahkan
- **Nazief, B., & Adriani, M. (1996)**: algoritma *confix stripping* yang menjadi dasar Sastrawi.
- **Asian, J. (2007)**: disertasi di RMIT University yang mengembangkan teknik temu kembali teks Indonesia, termasuk pengembangan lanjut algoritma Nazief-Adriani.
- **Adriani, M., Asian, J., Nazief, B., Tahaghoghi, S. M. M., & Williams, H. E. (2007)**: publikasi jurnal di *ACM Transactions on Asian Language Information Processing*.
- **Librian, A. (2013)**: pengembang pustaka Sastrawi versi PHP asli.
- **Nasution, M. (2016)**: pengembang PySastrawi, port Python dari Sastrawi.

## Catatan penting
- Semua contoh kode diverifikasi outputnya secara langsung pada PySastrawi 1.2.1.
- API yang benar adalah `factory.create_stemmer()` (snake_case), bukan `createStemmer()` (camelCase) yang tertulis di beberapa README lama.
- 13 blok kode Python, masing-masing dilengkapi output dan penjelasan.
- Format dijaga sederhana agar aman untuk konversi ke DOCX.
- Tidak ada em dash, tidak ada `Anda`, tidak ada `mahasiswa`/`dosen`/`guru`/`siswa` di luar konteks yang wajar.
