#!/usr/bin/env python3
"""
Build a condensed 12-slide version of presentasi_hasil.html
Keeps slide 3 (Identifikasi Masalah with Primandhika refs) intact.
Focuses on results (hasil).
"""

import re

def read_file(path):
    with open(path, 'r', encoding='utf-8') as f:
        return f.read()

def write_file(path, content):
    with open(path, 'w', encoding='utf-8') as f:
        f.write(content)

def extract_lines(content, start, end):
    """Extract lines start to end (1-indexed, inclusive)"""
    lines = content.split('\n')
    return '\n'.join(lines[start-1:end])

def main():
    original = read_file('presentasi_hasil.html')
    
    # Extract sections
    header = extract_lines(original, 1, 352)  # CSS, head, etc.
    
    # Slide 1: Cover (lines 353-373) - KEEP
    slide_cover = extract_lines(original, 353, 373)
    
    # Slide 2: Identifikasi Masalah (lines 393-492) - MUST KEEP (hal. 3 original)
    slide_masalah = extract_lines(original, 393, 492)
    
    # Slide 3: Produk Bicaranta - condensed version
    slide_produk = """<section>
<div class="slide-content">
<h1><i class="fa-solid fa-laptop-code"></i> Produk: Platform <strong>Bicaranta</strong></h1>
<p>Web <em>microlearning</em> berbasis teknik Feynman yang mengintegrasikan <strong>tiga komponen</strong>: web microlearning, teknik Feynman, dan prinsip CTML. Dikembangkan dengan model <strong>ASSURE</strong> dalam 6 tahap.</p>
<div style="display:flex; gap:16px; margin-top:14px;">
<div class="card-panel" style="flex:1; text-align:left;">
<h3><i class="fa-solid fa-list-check"></i> Fitur Utama</h3>
<ul>
<li><strong>Modul Microlearning</strong> — 90 detik – 2,5 menit per topik</li>
<li><strong>Video Pembelajaran</strong> — contoh penerapan teknik Feynman</li>
<li><strong>Perekaman Berbicara</strong> — rekam, unggah, dan putar ulang</li>
<li><strong>Refleksi Metakognitif</strong> — lembar evaluasi diri</li>
<li><strong>Kuis Interaktif</strong> — umpan balik otomatis</li>
</ul>
</div>
<div class="card-panel" style="flex:1; text-align:left;">
<h3><i class="fa-solid fa-brain"></i> Lima Dimensi Metakognitif</h3>
<ul>
<li><em>Conceptualisation</em> — tujuan dan struktur isi</li>
<li><em>Formulation</em> — diksi dan strategi retorika</li>
<li><em>Articulation</em> — intonasi, kelancaran, kejelasan</li>
<li><em>Self-monitoring</em> — pemantauan koherensi</li>
<li><em>Self-evaluation</em> — refleksi sistematis</li>
</ul>
</div>
</div>
</div>
</section>"""

    # Slide 4: Validasi & Uji Coba (gabungan)
    slide_validasi_ujicoba = """<section>
<div class="slide-content">
<h1><i class="fa-solid fa-check-double"></i> Validasi & Uji Coba Produk</h1>
<h3>Rekapitulasi Validasi (Tabel 4.6)</h3>
<table><thead>
<tr><th>Jenis Validasi</th><th>Skor</th><th>Persentase</th><th>Aiken's V</th><th>Kriteria</th></tr>
</thead><tbody>
<tr><td>Ahli Materi</td><td>351/360</td><td><strong>97,50%</strong></td><td><strong>0,967</strong></td><td>Sangat Valid</td></tr>
<tr><td>Ahli Media</td><td>166/180</td><td><strong>92,22%</strong></td><td><strong>0,896</strong></td><td>Sangat Valid</td></tr>
<tr><td><strong>Total Tertimbang</strong></td><td><strong>517/540</strong></td><td><strong>95,74%</strong></td><td><strong>0,943</strong></td><td><strong>Layak diujicobakan</strong></td></tr>
</tbody></table>
<hr/>
<h3>Progres Uji Coba</h3>
<table><thead>
<tr><th>Tahap</th><th>N</th><th>Rerata Respons</th><th>Pretest → Posttest</th><th>N-gain</th></tr>
</thead><tbody>
<tr><td>Uji Coba Terbatas</td><td>6</td><td>79,63% (Baik)</td><td>62,22 → 69,32</td><td>—</td></tr>
<tr><td>Uji Coba Lebih Luas (Eks.)</td><td>5</td><td>96,25% (Sangat Baik)</td><td>72,54 → 89,86</td><td>0,594</td></tr>
<tr><td>Uji Coba Lebih Luas (Kntrl.)</td><td>5</td><td>—</td><td>70,67 → 85,08</td><td>0,491</td></tr>
</tbody></table>
<blockquote><p>Skor kepraktisan meningkat dari <strong>79,63%</strong> (Baik) ke <strong>96,25%</strong> (Sangat Baik) setelah revisi produk.</p></blockquote>
</div>
</section>"""

    # Slide 5: Desain Uji Lapangan (lines 657-680)
    slide_uji_lapangan = extract_lines(original, 657, 680)

    # Slide 6: Hasil Keterampilan Berbicara - nested with boxplot chart
    # Keep the stat table (682-702) and boxplot (703-713) as nested
    slide_statistik = extract_lines(original, 682, 719)

    # Slide 7: Hasil Uji Inferensial + Gain per Aspek (gabungan)
    slide_inferensial_gain = """<section>
<section>
<div class="slide-content">
<h1><i class="fa-solid fa-chart-line"></i> Hasil Uji Inferensial (Tabel 4.18)</h1>
<table><thead>
<tr><th>Uji</th><th>Statistik</th><th>Hasil</th><th>Keputusan</th></tr>
</thead><tbody>
<tr><td>Shapiro-Wilk</td><td><em>p</em></td><td>Eksperimen: 0,087; Kontrol: 0,052</td><td>Normal (p > 0,05)</td></tr>
<tr><td>Levene</td><td>F</td><td>F = 2,10; p = 0,151</td><td>Homogen</td></tr>
<tr><td><strong>Independent t-test</strong></td><td>t</td><td>t(75) = 2,962; <strong>p = 0,004</strong></td><td><strong>Signifikan (p < 0,05)</strong></td></tr>
<tr><td>Cohen's d</td><td>d</td><td><strong>0,676</strong></td><td>Efek <strong>sedang</strong></td></tr>
</tbody></table>
<blockquote><p><strong>Kesimpulan:</strong> Terdapat perbedaan signifikan antara gain kelompok eksperimen dan kontrol dengan ukuran efek sedang (<em>d</em> = 0,676).</p></blockquote>
</div>
</section>
<section>
<div class="slide-content">
<h1><i class="fa-solid fa-layer-group"></i> Perbedaan Gain per Aspek Berbicara (Tabel 4.19)</h1>
<table><thead>
<tr><th>Aspek</th><th>Gain Eks.</th><th>Gain Kntrl.</th><th>Selisih</th><th>Signifikansi</th></tr>
</thead><tbody>
<tr><td><strong>Isi/gagasan</strong></td><td>14.50</td><td>11.08</td><td><strong>+3.42</strong></td><td>p = 0.013</td></tr>
<tr><td><strong>Organisasi</strong></td><td>12.50</td><td>8.11</td><td><strong>+4.39</strong></td><td>p = 0.005</td></tr>
<tr><td>Bahasa</td><td>10.00</td><td>6.76</td><td>+3.24</td><td>p = 0.018</td></tr>
<tr><td>Pelafalan</td><td>9.00</td><td>7.03</td><td>+1.97</td><td>p = 0.068</td></tr>
<tr><td>Kelancaran</td><td>13.25</td><td>8.38</td><td><strong>+4.87</strong></td><td>p = 0.001</td></tr>
</tbody></table>
<blockquote><p>Aspek <strong>kelancaran</strong> dan <strong>organisasi</strong> menunjukkan perbedaan gain tertinggi yang signifikan secara statistik.</p></blockquote>
</div>
</section>
</section>"""

    # Slide 8: Kemampuan Metakognitif gabungan (kuantitatif + kualitatif)
    slide_metakognitif = """<section>
<div class="slide-content">
<h1><i class="fa-solid fa-brain"></i> Kemampuan Metakognitif — Temuan Utama</h1>
<h3><i class="fa-solid fa-chart-bar"></i> Data Kuantitatif</h3>
<table><thead>
<tr><th>Dimensi</th><th>Pretes (%)</th><th>Postes (%)</th><th>Perubahan</th></tr>
</thead><tbody>
<tr><td><em>Conceptualisation</em></td><td>55,36</td><td>75,83</td><td><strong>+20,47</strong></td></tr>
<tr><td><em>Formulation</em></td><td>51,43</td><td>72,50</td><td><strong>+21,07</strong></td></tr>
<tr><td><em>Articulation</em></td><td>48,57</td><td>71,67</td><td><strong>+23,10</strong></td></tr>
<tr><td><em>Self-monitoring</em></td><td>53,57</td><td>76,67</td><td><strong>+23,10</strong></td></tr>
<tr><td><em>Self-evaluation</em></td><td>50,00</td><td>73,33</td><td><strong>+23,33</strong></td></tr>
</tbody></table>
<hr/>
<h3><i class="fa-solid fa-magnifying-glass"></i> Perspektif Kualitatif</h3>
<ul>
<li>Mahasiswa mulai <strong>merencanakan</strong> isi pembicaraan sebelum tampil</li>
<li>Proses <strong>penyederhanaan bahasa</strong> (teknik Feynman) mendorong penguasaan konsep lebih dalam</li>
<li>Fitur perekaman memfasilitasi <strong>evaluasi diri</strong> yang lebih objektif</li>
<li>Keterampilan <strong>self-monitoring</strong> dan <strong>self-evaluation</strong> menunjukkan peningkatan tertinggi</li>
</ul>
<blockquote><p><strong>Catatan:</strong> Data <em>pretest</em> metakognitif terbatas (N=7). Temuan kualitatif memperkuat tren positif pada kelima dimensi.</p></blockquote>
</div>
</section>"""

    # Slide 9: Respons Dosen (lines 815-832)
    slide_dosen = extract_lines(original, 815, 832)
    
    # Slide 10: Temuan Kunci & Implikasi (gabungan lines 858-905)
    slide_temuan_implikasi = """<section>
<div class="slide-content">
<h1><i class="fa-solid fa-magnifying-glass-chart"></i> Temuan Kunci & Implikasi</h1>
<div style="display:flex; gap:20px; margin-top:12px;">
<div class="card-panel" style="flex:1; text-align:left;">
<h3><i class="fa-solid fa-check-circle"></i> Temuan Kunci</h3>
<ul>
<li>Produk pada kategori <strong>Sangat Valid (95,74%)</strong></li>
<li>Kepraktisan meningkat dari 79,63% ke <strong>96,25%</strong></li>
<li>Keterampilan berbicara: perbedaan signifikan (<strong>p = 0,004</strong>, <em>d</em> = 0,676)</li>
<li>Kelima dimensi metakognitif meningkat secara konsisten</li>
<li>Dosen menilai produk mendukung <strong>pembelajaran mandiri</strong></li>
</ul>
</div>
<div class="card-panel" style="flex:1; text-align:left;">
<h3><i class="fa-solid fa-arrow-trend-up"></i> Implikasi & Rekomendasi</h3>
<ul>
<li><strong>Teoretis:</strong> Integrasi microlearning + teknik Feynman efektif untuk metakognitif</li>
<li><strong>Praktis:</strong> Dapat diadopsi pada berbagai mata kuliah keterampilan</li>
<li><strong>Teknologi:</strong> Platform web responsif memudahkan akses lintas perangkat</li>
<li><strong>Lanjutan:</strong> Perlu uji skala besar dan longitudinal</li>
</ul>
</div>
</div>
</div>
</section>"""

    # Slide 11: Alur Analisis (chart) - nested (lines 907-924)
    slide_alur = extract_lines(original, 907, 924)

    # Slide 12: Simpulan (lines 926-943)
    slide_simpulan = extract_lines(original, 926, 943)

    # Footer (lines 944-end)
    footer = extract_lines(original, 944, len(original.split('\n')))

    # Assemble (header already contains <body><div class="reveal"><div class="slides">)
    parts = [
        header,
        '',
        slide_cover,
        '',
        slide_masalah,
        '',
        slide_produk,
        '',
        slide_validasi_ujicoba,
        '',
        slide_uji_lapangan,
        '',
        slide_statistik,
        '',
        slide_inferensial_gain,
        '',
        slide_metakognitif,
        '',
        slide_dosen,
        '',
        slide_temuan_implikasi,
        '',
        slide_alur,
        '',
        slide_simpulan,
        '',
        footer
    ]
    
    result = '\n'.join(parts)
    write_file('presentasi_hasil.html', result)
    print("Condensed presentation built successfully!")
    print(f"Output file size: {len(result)} bytes")

if __name__ == '__main__':
    main()
