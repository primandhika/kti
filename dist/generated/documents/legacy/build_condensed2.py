#!/usr/bin/env python3
import re

def read_file(path):
    with open(path, 'r', encoding='utf-8') as f:
        return f.read()

def write_file(path, content):
    with open(path, 'w', encoding='utf-8') as f:
        f.write(content)

def main():
    header = read_file('/tmp/header.html')
    footer = read_file('/tmp/footer.html')
    slide_cover = read_file('/tmp/slide_cover.html')
    slide_masalah = read_file('/tmp/slide_masalah.html')
    
    # 3. Produk Bicaranta
    slide_produk = """<section>
<div class="slide-content">
<h1><i class="fa-solid fa-laptop-code"></i> Produk: Platform <strong>Bicaranta</strong></h1>
<p>Web <em>microlearning</em> berbasis teknik Feynman yang mengintegrasikan <strong>tiga komponen</strong>: web microlearning, teknik Feynman, dan prinsip CTML. Dikembangkan dengan model <strong>ASSURE</strong> dalam 6 tahap.</p>
<div style="display:flex; gap:16px; margin-top:14px;">
<div class="card-panel" style="flex:1; text-align:left; padding:12px; border:1px solid #ddd; border-radius:6px; background:#f9f9f9;">
<h3 style="margin-top:0;"><i class="fa-solid fa-list-check"></i> Fitur Utama</h3>
<ul>
<li><strong>Modul Microlearning</strong> — 90 detik – 2,5 menit per topik</li>
<li><strong>Video Pembelajaran</strong> — contoh penerapan teknik Feynman</li>
<li><strong>Perekaman Berbicara</strong> — rekam, unggah, dan putar ulang</li>
<li><strong>Refleksi Metakognitif</strong> — lembar evaluasi diri</li>
<li><strong>Kuis Interaktif</strong> — umpan balik otomatis</li>
</ul>
</div>
<div class="card-panel" style="flex:1; text-align:left; padding:12px; border:1px solid #ddd; border-radius:6px; background:#f9f9f9;">
<h3 style="margin-top:0;"><i class="fa-solid fa-brain"></i> Lima Dimensi Metakognitif</h3>
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

    # 4. Validasi & Uji Coba (Gabungan)
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

    # 5. Uji Lapangan Desain
    slide_uji_lapangan = """<section>
<div class="slide-content">
<h1><i class="fa-solid fa-vial-circle-check"></i> Uji Lapangan - Desain Kuasi-Eksperimen</h1>
<h3>Partisipan</h3>
<table><thead>
<tr><th>Kelompok</th><th style="text-align:right;">N</th><th>Perlakuan</th></tr>
</thead><tbody>
<tr><td><strong>Eksperimen</strong></td><td style="text-align:right;">40</td><td>Menggunakan media web <em>microlearning</em> Bicaranta</td></tr>
<tr><td><strong>Kontrol</strong></td><td style="text-align:right;">37</td><td>Mengikuti pembelajaran reguler</td></tr>
<tr><td><strong>Total</strong></td><td style="text-align:right;"><strong>77</strong></td><td></td></tr>
</tbody></table>
<h3>Desain Pengukuran</h3>
<pre><code>Pretest -> [Perlakuan] -> Posttest
         |
         +-- Keterampilan Berbicara (Tersedia lengkap, N=77)
         +-- Kemampuan Metakognitif (Terbatas, N posttest=59)
         +-- Respons Mahasiswa (Eksperimen, N=40)</code></pre>
<blockquote><p>Catatan: Data <em>pretest</em> kemampuan metakognitif sangat terbatas (N=7). Oleh karena itu, uji efektivitas difokuskan pada keterampilan berbicara.</p></blockquote>
</div>
</section>"""

    # 6. Statistik Deskriptif Berbicara
    # This combines the table and the boxplot image
    boxplot_html = read_file('/tmp/slide_boxplot.html')
    # extract just the image part from boxplot_html
    import re
    img_match = re.search(r'<img[^>]+>', boxplot_html)
    img_tag = img_match.group(0) if img_match else ""

    slide_statistik = f"""<section>
<section>
<div class="slide-content">
<h1><i class="fa-solid fa-chart-bar"></i> Hasil Keterampilan Berbicara (Tabel 4.17)</h1>
<table><thead>
<tr><th>Statistik</th><th colspan="3">Eksperimen (n=40)</th><th colspan="3">Kontrol (n=37)</th></tr>
<tr><th></th><th>Pretes</th><th>Postes</th><th>Gain</th><th>Pretes</th><th>Postes</th><th>Gain</th></tr>
</thead><tbody>
<tr><td><strong>Mean</strong></td><td>70.84</td><td>82.70</td><td>11.86</td><td>71.21</td><td>79.46</td><td>8.25</td></tr>
<tr><td><strong>Median</strong></td><td>72.00</td><td>81.31</td><td>—</td><td>70.67</td><td>78.67</td><td>—</td></tr>
<tr><td><strong>SD</strong></td><td>5.48</td><td>5.11</td><td>6.63</td><td>1.82</td><td>3.36</td><td>3.64</td></tr>
<tr><td><strong>Min</strong></td><td>58.70</td><td>68.00</td><td>-10.70</td><td>69.30</td><td>66.70</td><td>-3.97</td></tr>
<tr><td><strong>Max</strong></td><td>90.70</td><td>92.00</td><td>24.00</td><td>77.30</td><td>86.70</td><td>16.03</td></tr>
<tr><td><strong>N-gain</strong></td><td>—</td><td>—</td><td><strong>0.375</strong></td><td>—</td><td>—</td><td><strong>0.284</strong></td></tr>
</tbody></table>
<blockquote><p>Selisih mean gain antarkelompok: <strong>3.61 poin</strong> — kedua kelompok meningkat, eksperimen lebih besar.</p></blockquote>
</div>
</section>
<section>
<div class="slide-content">
<h2><i class="fa-solid fa-chart-pie"></i> Distribusi Skor — Boxplot Pretes & Postes</h2>
<div style="text-align:center;">
{img_tag}
</div>
</div>
</section>
</section>"""

    # 7. Inferensial & Gain (Gabungan)
    gain_html = read_file('/tmp/slide_gain_chart.html')
    img_match2 = re.search(r'<img[^>]+>', gain_html)
    gain_img = img_match2.group(0) if img_match2 else ""

    slide_inferensial_gain = f"""<section>
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
<section>
<div class="slide-content">
<div style="text-align:center;">
{gain_img}
</div>
</div>
</section>
</section>"""

    # 8. Kemampuan Metakognitif (Gabungan Kuantitatif & Kualitatif)
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

    # 9. Respons Dosen
    slide_dosen = """<section>
<div class="slide-content">
<h1><i class="fa-solid fa-comments"></i> Temuan Kualitatif - Respons Dosen (Tabel 4.21)</h1>
<table><thead>
<tr><th>Dimensi Evaluasi</th><th>Kutipan (Subjek 1 - Dosen)</th></tr>
</thead><tbody>
<tr><td><strong>Kebermanfaatan</strong></td><td>"Sebagai media pembantu, Bicaranta <strong>cukup efektif untuk memandu mahasiswa</strong> dalam praktik berbicara."</td></tr>
<tr><td><strong>Penerapan Metode</strong></td><td>"Teknik Feynman... cocok karena <strong>memaksa mahasiswa menyederhanakan ide</strong> agar lebih mudah dipahami oleh pendengar."</td></tr>
<tr><td><strong>Evaluasi Berbasis Rekaman</strong></td><td>"Fasilitas rekam dan refleksi... <strong>sangat bermanfaat bagi mahasiswa</strong> yang sebelumnya malu tampil di depan kelas."</td></tr>
<tr><td><strong>Masukan &amp; Kritik</strong></td><td>"Sistem kuis dapat diperbanyak variasinya, mungkin bisa lebih ke arah studi kasus atau simulasi kecil."</td></tr>
</tbody></table>
<blockquote><p>Secara umum, dosen menilai integrasi web <em>microlearning</em> dan teknik Feynman bermanfaat untuk mendukung mahasiswa berlatih secara mandiri.</p></blockquote>
</div>
</section>"""

    # 10. Temuan Kunci & Implikasi
    slide_temuan_implikasi = """<section>
<div class="slide-content">
<h1><i class="fa-solid fa-magnifying-glass-chart"></i> Temuan Kunci & Implikasi</h1>
<div style="display:flex; gap:20px; margin-top:12px;">
<div class="card-panel" style="flex:1; text-align:left; padding:12px; border:1px solid #ddd; border-radius:6px; background:#f9f9f9;">
<h3 style="margin-top:0;"><i class="fa-solid fa-check-circle"></i> Temuan Kunci</h3>
<ul>
<li>Produk pada kategori <strong>Sangat Valid (95,74%)</strong></li>
<li>Kepraktisan meningkat dari 79,63% ke <strong>96,25%</strong></li>
<li>Keterampilan berbicara: perbedaan signifikan (<strong>p = 0,004</strong>, <em>d</em> = 0,676)</li>
<li>Kelima dimensi metakognitif meningkat secara konsisten</li>
<li>Dosen menilai produk mendukung <strong>pembelajaran mandiri</strong></li>
</ul>
</div>
<div class="card-panel" style="flex:1; text-align:left; padding:12px; border:1px solid #ddd; border-radius:6px; background:#f9f9f9;">
<h3 style="margin-top:0;"><i class="fa-solid fa-arrow-trend-up"></i> Implikasi & Rekomendasi</h3>
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

    # 11. Alur Analisis
    alur_html = read_file('/tmp/slide_alur_chart.html')
    alur_img_match = re.search(r'<img[^>]+>', alur_html)
    alur_img = alur_img_match.group(0) if alur_img_match else ""

    slide_alur = f"""<section>
<section>
<div class="slide-content">
<h1><i class="fa-solid fa-diagram-project"></i> Alur Analisis Data Penelitian</h1>
<div style="text-align:center;">
{alur_img}
</div>
</div>
</section>
</section>"""

    # 12. Simpulan
    slide_simpulan = """<section>
<div class="slide-content">
<h1><i class="fa-solid fa-flag-checkered"></i> Simpulan</h1>
<ol>
<li>
<p><strong>Pengembangan Produk:</strong><br/>
Telah berhasil dikembangkan produk web <em>microlearning</em> Bicaranta berbasis teknik Feynman yang memfasilitasi pelatihan keterampilan berbicara secara mandiri dan reflektif.</p>
</li>
<li>
<p><strong>Validitas dan Kepraktisan:</strong><br/>
Produk memenuhi kriteria <strong>sangat valid</strong> (95,74%) berdasarkan penilaian ahli materi dan media, serta <strong>sangat praktis</strong> (96,25%) menurut penilaian pengguna (mahasiswa) pada uji coba skala lebih luas.</p>
</li>
<li>
<p><strong>Efektivitas (Keterampilan Berbicara):</strong><br/>
Penerapan platform ini terbukti secara signifikan (<em>p</em> &lt; 0,05; <em>d</em> = 0,676) lebih unggul dalam meningkatkan keterampilan berbicara (khususnya aspek <strong>kelancaran</strong> dan <strong>organisasi</strong>) dibandingkan dengan pembelajaran reguler.</p>
</li>
<li>
<p><strong>Efektivitas (Kemampuan Metakognitif):</strong><br/>
Platform mendorong peningkatan pada lima dimensi metakognitif, dengan perubahan tertinggi pada <em>self-evaluation</em> (+23,33%) dan <em>self-monitoring</em> (+23,10%). Proses penyederhanaan bahasa dalam teknik Feynman menstimulasi mahasiswa untuk secara aktif merencanakan, memantau, dan mengevaluasi ujaran mereka.</p>
</li>
</ol>
</div>
</section>"""

    # Build everything
    html_content = (
        header + "\n" +
        slide_cover + "\n" +
        slide_masalah + "\n" +
        slide_produk + "\n" +
        slide_validasi_ujicoba + "\n" +
        slide_uji_lapangan + "\n" +
        slide_statistik + "\n" +
        slide_inferensial_gain + "\n" +
        slide_metakognitif + "\n" +
        slide_dosen + "\n" +
        slide_temuan_implikasi + "\n" +
        slide_alur + "\n" +
        slide_simpulan + "\n" +
        footer
    )

    write_file('/home/primandhika/artikel/dist/output/presentasi_hasil.html', html_content)

if __name__ == '__main__':
    main()
