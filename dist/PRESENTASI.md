# Panduan Pembuatan Presentasi

## Aturan Desain & Konten
- jika membuat atau merevisi presentasi (reveal.js / HTML slide), **selalu perhatikan safe area slide**. konten setiap slide harus muat dalam dimensi slide yang dikonfigurasi (width × height) dikurangi margin. jangan biarkan konten melewati batas bawah atau samping slide.
- **JANGAN PERNAH memenggal (truncate) atau menyingkat sumber referensi akademik**, kutipan, atau data esensial hanya demi menghemat ruang. Setiap referensi harus ditulis UTUH sesuai standar penulisan (mencakup nama semua penulis, tahun, judul karya, nama jurnal/prosiding/penerbit, dan halaman).
- jika konten terasa terlalu padat untuk *safe area* (terutama untuk referensi/daftar pustaka), solusinya adalah **memperkecil font size** khusus pada elemen tersebut (misalnya menjadi `0.4em` atau `0.45em`) atau memecahnya ke slide berikutnya. BUKAN dengan memotong/memenggal teksnya.
- sebelum memasukkan konten ke slide, perhitungkan jumlah elemen (heading, paragraf, tabel, gambar, list) agar tidak melebihi kapasitas tampilan. 
- presentasi harus dinamis, ringkas, dan visual (misal: manfaatkan UI *cards* dengan efek *hover*, ikon, dan desain yang modern). hindari teks paragraf utuh yang panjang atau *list* bernomor yang monoton.
- **perhatikan proporsi desain UI (padding dan line-height).** jangan boros *line-height* (misal: gunakan `1.1` atau `1.15` untuk *cards*) yang menyebabkan teks memakan terlalu banyak ruang vertikal secara sia-sia. berikan *padding* dalam (inner padding) yang cukup agar elemen tidak terlihat sesak dan lebih enak dilihat secara estetik.
- perhatikan bahwa logo atau gambar dengan latar non-transparan (seperti PNG berlatar putih) akan terlihat murahan di atas *background* gelap. solusinya: ubah *background* slide tersebut (misal slide judul) menjadi putih agar menyatu, atau beri bingkai putih yang estetik.
- slide judul/cover harus memiliki hierarki visual yang jelas: Judul utama harus paling besar (menggunakan tag `<h1>`) dan bersatu utuh dalam satu teks, tidak dipecah sembarangan ke *subtitle*.
