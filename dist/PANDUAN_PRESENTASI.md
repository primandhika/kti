# Panduan Menjalankan Presentasi Hasil Penelitian (RISE)

Dokumen ini berisi panduan untuk menjalankan presentasi interaktif hasil penelitian Bab IV menggunakan **Jupyter Notebook** dengan ekstensi **RISE**.

## 1. Persiapan Lingkungan

Pastikan Anda berada di direktori proyek `artikel/dist`. Gunakan virtual environment yang sudah ada (`.venv`) agar dependensi tidak berantakan.

```bash
# Masuk ke direktori proyek
cd /home/primandhika/artikel/dist

# Aktifkan virtual environment
source .venv/bin/activate
```

## 2. Instalasi Dependensi yang Dibutuhkan

Jika Anda belum menginstal Jupyter Notebook dan ekstensi RISE, jalankan perintah berikut:

```bash
# Pastikan pip sudah yang terbaru
pip install --upgrade pip

# Instal Jupyter Notebook, RISE, dan matplotlib (untuk visualisasi data)
pip install notebook jupyterlab_rise matplotlib numpy
```

## 3. Menjalankan Presentasi

Setelah semua dependensi terinstal, Anda dapat langsung menjalankan presentasi menggunakan perintah berikut di terminal:

```bash
# Menjalankan jupyter notebook dan langsung membukanya dalam mode slideshow RISE
jupyter notebook output/presentation_hasil.ipynb
```

Setelah browser terbuka:
1. Jalankan semua *cell* terlebih dahulu agar grafik (chart) muncul. Caranya: pada menu atas, klik **Cell** > **Run All**.
2. Klik tombol **"Enter/Exit RISE Slideshow"** (ikon grafik batang kecil seperti layar presentasi) di toolbar Jupyter Notebook (biasanya di sebelah kanan ikon *Command Palette*).
3. Atau, Anda dapat menggunakan pintasan keyboard: **`Alt + R`** (di beberapa sistem **`Option + R`**).

## 4. Navigasi Slideshow RISE

Saat slideshow berjalan, gunakan panduan navigasi berikut:
- **Spasi / Panah Kanan** : Pindah ke slide berikutnya.
- **Shift + Spasi / Panah Kiri** : Kembali ke slide sebelumnya.
- **Panah Bawah** : Pindah ke *sub-slide* (misalnya untuk melihat grafik setelah slide utama).
- **Panah Atas** : Kembali ke *slide* utama dari *sub-slide*.
- **W** : Menampilkan papan tulis (Chalkboard) untuk mencoret-coret selama presentasi.
- **T / Esc** : Keluar dari mode slideshow.

## 5. Menutup Presentasi

Setelah selesai:
1. Tekan `Esc` atau `T` untuk keluar dari mode slideshow.
2. Di terminal tempat Jupyter berjalan, tekan `Ctrl + C` dua kali untuk mematikan server Jupyter.
3. Menonaktifkan virtual environment dengan mengetik: `deactivate`

---
Catatan: Pastikan Anda menjalankan Jupyter Notebook melalui terminal yang berada di `/home/primandhika/artikel/dist` agar path untuk penyimpanan gambar grafik (ke folder `fig/`) dapat berfungsi dengan baik.
