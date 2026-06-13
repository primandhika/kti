# Rancangan Uji Coba Terbatas

Dokumen ini merangkum fokus pelaksanaan uji coba terbatas sebelum produk digunakan pada tahap pengujian yang lebih luas.

Instrumen khusus:

- Tahap 1: `instruments/md/uji_coba_kelompok_kecil.md`
- Tahap 2: `instruments/md/uji_coba_terbatas_lanjutan.md`

## Tahap 1: Uji Coba Kelompok Kecil

Jumlah peserta: 6 mahasiswa.

Tahap ini merupakan uji awal kepada pengguna untuk memperoleh gambaran cepat tentang kelayakan tampilan, isi, dan alur penggunaan media. Fokus utama tahap ini meliputi:

- Kelayakan materi dan keterbacaan isi.
- Kejelasan instruksi pada setiap bagian media.
- Kemudahan navigasi antarhalaman atau antarkomponen.
- Respons awal pengguna terhadap media.
- Usability awal, terutama kemudahan penggunaan dan kenyamanan interaksi.
- Face validity pengguna, yaitu kesan awal pengguna terhadap kesesuaian media dengan tujuan pembelajaran.

Hasil dari tahap ini digunakan sebagai dasar revisi awal terhadap materi, instruksi, tampilan, navigasi, dan alur penggunaan media.

## Tahap 2: Uji Coba Terbatas Lanjutan

Jumlah peserta: 10 mahasiswa pada data seed saat ini, dengan rentang rancangan 10-15 mahasiswa.

Tahap ini dilaksanakan setelah revisi awal dari uji coba kelompok kecil. Fokusnya adalah memastikan produk lebih stabil dan siap digunakan pada skenario pembelajaran yang lebih lengkap. Fokus utama tahap ini meliputi:

- Stabilitas produk setelah revisi.
- Kepraktisan penggunaan media oleh mahasiswa.
- Keterlaksanaan skenario pembelajaran.
- Deteksi bug atau kekutu pada fitur, navigasi, maupun alur penggunaan.
- Kesiapan instrumen yang digunakan untuk mengumpulkan data.
- Simulasi alur pembelajaran dari awal sampai akhir.

Hasil dari tahap ini digunakan untuk menyempurnakan produk, memperbaiki gangguan teknis, dan memastikan instrumen serta alur pembelajaran siap digunakan pada tahap berikutnya.

Data peserta tahap ini disimpan pada:

- `data/uji_coba/large_group_prepost.csv`
- `data/uji_coba/large_group_respons.csv`

Peserta dipilih dari `data/field_test/keterampilan_berbicara.csv`, yaitu 5 mahasiswa kelompok eksperimen dan 5 mahasiswa kelompok kontrol dengan skor `post_total` tertinggi pada masing-masing kelompok.
