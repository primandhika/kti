# Aturan Konversi Dokumen UAS ke PDF

Gunakan aturan berikut setiap kali membuat atau mengonversi dokumen UAS agar tata letaknya konsisten.

## Halaman dan margin

- Ukuran kertas: A4.
- Margin isi dokumen: 2,5 cm pada semua sisi.
- Margin atas halaman pertama khusus kop: 0,8 cm (Different First Page).
- Margin atas dan bawah halaman berikutnya: 2,5 cm.
- Kop tidak boleh mengikuti margin horizontal isi dokumen. Kop dan garis bawahnya harus memakai lebar penuh halaman A4 (210 mm).
- Isi utama setelah kop tetap berada dalam margin kiri dan kanan 2,5 cm.

Implementasi HTML/CSS yang digunakan:

```css
@page { size: A4; margin: 25mm 0; }
@page:first { size: A4; margin: 8mm 0 25mm 0; }

body { padding: 0 25mm; }

.kop {
  width: 210mm;
  margin-left: -25mm;
  margin-right: -25mm;
}
```

Jangan memakai `@page { margin: 25mm; }` lalu memperlebar kop dengan margin negatif. Chromium akan memotong bagian kop pada batas area cetak.

## Kop institusi

- Font kop: Cambria atau Caladea. Caladea harus tertanam dalam PDF apabila Cambria tidak tersedia.
- Akreditasi: **AKREDITASI INSTITUSI “UNGGUL”**.
- Email: **info@ikipsiliwangi.ac.id**.
- Logo harus tampil utuh dan tidak terpotong.
- Garis bawah kop harus membentang dari tepi kiri sampai tepi kanan halaman.

## Identitas dokumen

- UAS Media Pembelajaran Bahasa Berbasis ICT: kelas **A1 Reguler 2025**.
- UAS Pembelajaran Bahasa Indonesia SD: kelas **A3 Reguler 2025**.
- UAS Pembelajaran Bahasa Indonesia SD harus memuat daftar rujukan nyata, bukan sekadar contoh kategori sumber.

## Pemeriksaan setelah konversi

- Render halaman pertama PDF menjadi gambar dan periksa secara visual.
- Pastikan logo tidak terpotong dan kop tidak masuk ke margin isi.
- Pastikan isi dokumen tetap memiliki margin kiri dan kanan 2,5 cm.
- Periksa font PDF dengan `pdffonts`; Caladea harus tercantum dan tertanam.
- Periksa teks dengan `pdftotext`, khususnya akreditasi, email, kelas, dan referensi.
