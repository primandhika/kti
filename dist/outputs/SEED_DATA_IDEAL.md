# Seed Data Ideal Berbasis Data Existing

Dokumen ini menjelaskan paket **seed data sintetis** yang dibuat dengan cara **melanjutkan struktur file existing** lalu **mengisi bagian yang kosong** sebagai contoh ideal. Paket ini **bukan data aktual penelitian** dan tidak boleh diperlakukan sebagai bukti empiris.

## Lokasi File

Semua file contoh disimpan di:

`outputs/seed_data_ideal/`

Strukturnya mengikuti folder lama:
- `field_test/`
- `uji_coba/`
- `kualitatif/`

## Prinsip Pengisian

1. **Header dipertahankan** agar kompatibel dengan file existing.
2. **Baris yang kosong diisi** dengan nilai sintetis yang masuk akal.
3. **Nilai dibuat konsisten dengan narasi penelitian** di `RESPONSE_CHECKLIST.md`:
   - eksperimen cenderung mengalami kenaikan lebih kuat dibanding kontrol
   - dimensi metakognitif termasuk dalam skor berbicara
   - angket respons terutama relevan untuk kelompok eksperimen
   - wawancara dan observasi mengikuti tema yang sudah Anda jelaskan
4. Untuk field test, seed copy disejajarkan ke **74 subjek ideal**:
   - 38 eksperimen (`E01-E38`)
   - 36 kontrol (`K01-K36`)
   - `E39`, `E40`, dan `K37` tidak diteruskan karena tidak sesuai hitungan final yang Anda jelaskan

## File yang Dibuat

### 1. Field Test
- `field_test/keterampilan_berbicara.csv`
- `field_test/metakognitif.csv`
- `field_test/respons_mahasiswa.csv`

### 2. Uji Coba
- `uji_coba/small_group_prepost.csv` → diisi untuk **5 peserta uji fitur terbatas**
- `uji_coba/small_group_respons.csv` → diisi untuk **10 peserta uji kepraktisan**
- `uji_coba/large_group_prepost.csv` → **opsional**, hanya dipakai jika BAB IV tetap mempertahankan uji coba lebih luas
- `uji_coba/large_group_respons.csv` → **opsional**, sama seperti di atas

### 3. Data Kualitatif
- `kualitatif/wawancara.csv` → 12 kutipan contoh terisi
- `kualitatif/observasi.csv` → 8 indikator observasi terisi

## Catatan Penting untuk Disertasi

### A. Mana yang paling aman dipakai sebagai “gambaran ideal”
Jika tujuan Anda adalah menyusun BAB IV tanpa mengaburkan data riil, maka yang paling aman adalah memakai file seed ini sebagai:
- **contoh format pengisian**
- **panduan narasi hasil**
- **alat untuk melihat data apa saja yang masih harus dikumpulkan**

### B. Mana yang sebaiknya tidak langsung dianggap data final
Jangan langsung memakai angka sintetis ini untuk:
- perhitungan statistik final
- tabel hasil uji hipotesis
- klaim efektivitas empiris

### C. Rekomendasi kerja berikutnya
1. Bandingkan file seed dengan file aktual Anda.
2. Ganti angka sintetis dengan data riil yang benar-benar tersedia.
3. Jika ada tahap penelitian yang ternyata tidak jadi dilakukan, lebih baik **hapus tahap itu dari BAB III/BAB IV** daripada mengisinya dengan data fiktif.

## Ringkasan Interpretasi Narasi

- **Uji fitur terbatas**: 5 orang (3 eksperimen, 2 kontrol)
- **Uji kepraktisan**: 10 orang (5 eksperimen, 5 kontrol)
- **Field test**: 74 orang total (38 eksperimen, 36 kontrol)
- **Respons mahasiswa field test**: terutama kelompok eksperimen sebagai pengguna utama Bicaranta

## Status Integritas

Semua file di folder ini adalah:
- **synthetic / seed only**
- **berbasis pola data existing**
- **tidak menggantikan data penelitian asli**
