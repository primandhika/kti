import re

l1_path = "/home/primandhika/artikel/dist/main/02_Lampiran_01_Instrumen_Penelitian.md"
with open(l1_path, "r", encoding='utf-8') as f:
    content = f.read()

# Generate the detailed 15-item rubric markdown
rubric_md = """## C. Rubrik Penilaian Keterampilan Berbicara

Tes keterampilan berbicara diukur menggunakan rubrik asesmen unjuk kerja berskala 1–5 untuk mengevaluasi 15 butir indikator spesifik yang terkelompok ke dalam 5 aspek utama. Penilaian dilakukan oleh 2 orang *rater* independen untuk menjaga objektivitas. 

| Aspek | Indikator (15 Butir) | Skor 1 (Sangat Kurang) | Skor 2 (Kurang) | Skor 3 (Cukup) | Skor 4 (Baik) | Skor 5 (Sangat Baik) |
|---|---|---|---|---|---|---|
| **1. Pengorganisasian Ide** | **1. Urutan** (Sistematika gagasan) | Sangat tidak terstruktur | Kurang terstruktur | Cukup runtut | Runtut dan terarah | Sangat sistematis dan logis |
| | **2. Transisi** (Peralihan antar-ide) | Tidak ada transisi | Transisi membingungkan | Cukup jelas | Transisi mulus | Sangat mulus dan kohesif |
| | **3. Simpulan** (Penarikan kesimpulan) | Tidak ada simpulan | Simpulan tidak relevan | Ada simpulan standar | Simpulan kuat | Simpulan sangat mengikat dan jelas |
| **2. Kejelasan Penyampaian** | **4. Artikulasi** (Kejelasan pelafalan) | Sangat buram/bergumam | Sering kurang jelas | Cukup jelas | Jelas dan tegas | Sangat jelas, artikulasi presisi |
| | **5. Intonasi** (Dinamika nada) | Sangat monoton | Kurang variasi | Cukup bervariasi | Intonasi tepat konteks | Sangat dinamis dan ekspresif |
| | **6. Kecepatan** (Tempo berbicara) | Terlalu cepat/lambat | Sering tidak stabil | Cukup terkendali | Tempo proporsional | Tempo sangat pas dan adaptif |
| **3. Ketepatan Bahasa** | **7. Diksi** (Pemilihan kosakata) | Sangat miskin kosakata | Banyak diksi tidak tepat | Diksi cukup sesuai | Diksi variatif | Diksi sangat kaya dan elegan |
| | **8. Gramatikal** (Tata bahasa) | Banyak kaidah yang salah | Sering terjadi kesalahan | Cukup sesuai kaidah | Hampir tanpa salah | Sempurna tanpa kesalahan gramatikal |
| | **9. Formalitas** (Ragam bahasa akademik) | Sangat santai/slang | Kurang formal | Cukup formal | Sesuai ragam akademik | Sangat profesional dan akademik |
| **4. Strategi Komunikasi** | **10. Bahasa Tubuh** (Gestur/kinesik) | Tidak ada gestur/kaku | Gestur tidak relevan | Gestur cukup mendukung | Gestur aktif mendukung | Gestur sangat natural & menghidupkan |
| | **11. Perhatian** (Kontak mata) | Menunduk/membaca teks | Jarang menatap audiens | Kontak mata cukup | Menjaga kontak mata | Interaksi mata sangat intens & merata |
| | **12. Retorika** (Gaya persuasi) | Tidak meyakinkan | Kurang daya tarik | Cukup menarik | Menguasai audiens | Gaya retorika sangat karismatik |
| **5. Dampak Penyampaian** | **13. Contoh** (Analogi ala Feynman) | Tanpa contoh/analogi | Contoh membingungkan | Contoh cukup relevan | Analogi jelas & tepat | Analogi sangat cerdas dan memahamkan |
| | **14. Persuasi** (Daya pengaruh) | Tidak ada pengaruh | Kurang berdampak | Cukup berdampak | Pesan tersampaikan kuat | Pesan sangat membekas di audiens |
| | **15. Inspirasi** (Dampak pemahaman) | Audiens sama sekali gagal paham | Audiens kebingungan | Pemahaman standar tercapai | Pemahaman utuh tercapai | Pemahaman mendalam tingkat tinggi |
"""

# Replace the section C in Lampiran 01
parts = re.split(r'## C\. Rubrik Penilaian Keterampilan Berbicara.*?---', content, flags=re.DOTALL)
if len(parts) == 2:
    new_content = parts[0] + rubric_md + "\n---\n" + parts[1]
    with open(l1_path, "w", encoding='utf-8') as f:
        f.write(new_content)
    print("Berhasil memperbarui Lampiran 01 dengan Rubrik 15 butir skala 1-5.")
else:
    print("Gagal menemukan bagian C untuk diganti di Lampiran 01.")

# Update BAB III
b3_path = "/home/primandhika/artikel/dist/main/01_BAB_III_METODOLOGI_PENELITIAN.md"
with open(b3_path, "r", encoding='utf-8') as f:
    b3_content = f.read()

# Replace "berskala 1–4 yang memuat lima aspek performa berbicara, yaitu:"
b3_content = b3_content.replace(
    "berskala 1–4 yang memuat lima aspek performa berbicara, yaitu:",
    "berskala 1–5 yang memuat 15 butir indikator spesifik yang terkelompok ke dalam lima aspek performa berbicara, yaitu:"
)
# Update the examples in BAB III
b3_content = b3_content.replace("skor 4:", "skor 5:")
with open(b3_path, "w", encoding='utf-8') as f:
    f.write(b3_content)
    
print("Berhasil memperbaiki deskripsi rubrik di BAB III.")

# Create Revision Note
rev_path = "/home/primandhika/artikel/dist/_rev/Lampiran_01_Rubrik_rev.md"
rev_note = """# Catatan Revisi Lampiran 01 (Rubrik Keterampilan Berbicara)

**Tanggal:** 22 Juni 2026
**File yang Direvisi:** `main/02_Lampiran_01_Instrumen_Penelitian.md` & `main/01_BAB_III_METODOLOGI_PENELITIAN.md`

## Rincian Perubahan:
1. Memperbaiki matriks **Rubrik Penilaian Keterampilan Berbicara** yang awalnya disederhanakan menjadi skala 1-4 dengan 5 butir, dikembalikan menjadi **skala 1-5 dengan 15 butir indikator spesifik** (seperti urutan, transisi, artikulasi, diksi, gramatikal, bahasa tubuh, contoh/analogi Feynman, dll.) agar sinkron 100% dengan pangkalan data uji lapangan (`keterampilan_berbicara_pretes.csv`).
2. Menyesuaikan narasi penjelasan rubrik di BAB III agar secara definitif menyebutkan 15 indikator dengan skala 1-5, tidak lagi skala 1-4.
"""
with open(rev_path, "w", encoding='utf-8') as f:
    f.write(rev_note)
print("Berhasil membuat catatan revisi.")
