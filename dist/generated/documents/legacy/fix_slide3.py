#!/usr/bin/env python3
"""
Revisi slide 3:
- Ganti 1 kalimat ringkasan dengan 5 poin studi pendahuluan dari BAB I
- Pisahkan tabel 5 dimensi metakognitif ke slide baru (slide 3b)
- Tambahkan sumber referensi
"""

INPUT = "/home/primandhika/artikel/dist/output/presentasi_hasil.html"
OUTPUT = INPUT

with open(INPUT, "r", encoding="utf-8") as f:
    html = f.read()

# ─── Old slide 3 content ───
OLD_SLIDE_3 = """<section>
<div class="slide-content">
<h1><i class="fa-solid fa-triangle-exclamation"></i> Mengapa Penelitian Ini Penting?</h1>
<h3><i class="fa-solid fa-triangle-exclamation"></i> Masalah yang Ditemukan di Lapangan</h3>
<pre><code class="">
Mahasiswa sering kali mampu menunjukkan penguasaan materi melalui tugas tertulis,
tetapi mengalami kesulitan ketika harus memberikan penjelasan lisan yang runtut.
</code></pre>
<p><strong>Lima Dimensi Kelemahan Metakognitif dalam Berbicara:</strong></p>
<table><thead>
<tr><th>Dimensi</th><th>Kelemahan</th></tr>
</thead><tbody>
<tr><td><em>Conceptualisation</em></td><td>Berbicara tanpa tujuan dan struktur isi yang jelas</td></tr>
<tr><td><em>Formulation</em></td><td>Pemilihan diksi dan strategi retorika belum sesuai konteks</td></tr>
<tr><td><em>Articulation</em></td><td>Intonasi, kelancaran, dan kejelasan belum stabil</td></tr>
<tr><td><em>Self-monitoring</em></td><td>Belum terbiasa memantau koherensi argumen saat berbicara</td></tr>
<tr><td><em>Self-evaluation</em></td><td>Kurangnya refleksi sistematis setelah performa lisan selesai</td></tr>
</tbody></table>
</div>
</section>"""

# ─── New slide 3: 5 poin studi pendahuluan ───
# ─── New slide 3b: tabel 5 dimensi metakognitif ───
NEW_SLIDES = """<section>
<div class="slide-content">
<h1><i class="fa-solid fa-triangle-exclamation"></i> Mengapa Penelitian Ini Penting?</h1>
<h3>Temuan Studi Pendahuluan</h3>
<p>Studi pendahuluan (Primandhika et al., 2025a, 2025b, 2026) mengungkap lima permasalahan kritis:</p>
<ol>
<li>Mahasiswa mampu menunjukkan penguasaan materi melalui tugas tertulis, tetapi <strong>mengalami kesulitan ketika harus memberikan penjelasan lisan</strong> yang runtut dan argumentatif.</li>
<li>Penyampaian lisan mahasiswa cenderung <strong>bersifat deskriptif dan repetitif</strong> tanpa disertai elaborasi makna yang memadai.</li>
<li>Kemampuan <strong>menyesuaikan strategi komunikasi</strong> terhadap konteks audiens akademik masih lemah.</li>
<li>Faktor seperti <strong>keterbatasan kosakata, kurangnya persiapan, dan rendahnya kepercayaan diri</strong> menjadi penghambat signifikan.</li>
<li>Terdapat <strong>kesenjangan antara pemahaman kognitif</strong> yang dimiliki mahasiswa dan kemampuannya mengomunikasikan pemahaman itu secara lisan di ruang akademik.</li>
</ol>
<blockquote>
<p><strong>Sumber:</strong><br/>
Primandhika, R. B., Hikmat, A., Safii, I., &amp; Yani, A. S. (2025a). <em>The Impact of Learning Technology on Cognitive Abilities...</em> KEMBARA, 11(1).<br/>
Primandhika, R. B., Yani, A. S., Solihati, N., &amp; Zulaiha, S. (2025b). <em>Analisis Potensi Microlearning berbasis Infografis...</em> Proceeding SIPBI, 1(1), 47–61.<br/>
Primandhika, R. B., Solihati, N., &amp; Zulaiha, S. (2026). <em>AI's Impact on Students' Oral Presentation Skills...</em> Edukasi, 13(1), 274–293.</p>
</blockquote>
</div>
</section>

<section>
<div class="slide-content">
<h1><i class="fa-solid fa-brain"></i> Lima Dimensi Kelemahan Metakognitif</h1>
<p>Analisis lebih lanjut menunjukkan bahwa kelemahan keterampilan berbicara mahasiswa berkaitan erat dengan <strong>lima dimensi metakognitif</strong>:</p>
<table><thead>
<tr><th>No</th><th>Dimensi</th><th>Kelemahan yang Teridentifikasi</th></tr>
</thead><tbody>
<tr><td>1</td><td><em>Conceptualisation</em></td><td>Berbicara tanpa tujuan dan struktur isi yang jelas</td></tr>
<tr><td>2</td><td><em>Formulation</em></td><td>Pemilihan diksi dan strategi retorika belum sesuai konteks</td></tr>
<tr><td>3</td><td><em>Articulation</em></td><td>Intonasi, kelancaran, dan kejelasan belum stabil</td></tr>
<tr><td>4</td><td><em>Self-monitoring</em></td><td>Belum terbiasa memantau koherensi argumen saat berbicara</td></tr>
<tr><td>5</td><td><em>Self-evaluation</em></td><td>Kurangnya refleksi sistematis setelah performa lisan selesai</td></tr>
</tbody></table>
<blockquote>
<p>Kelima dimensi ini menjadi dasar desain web <em>microlearning</em> berbasis teknik Feynman yang dikembangkan dalam penelitian ini.</p>
</blockquote>
</div>
</section>"""

if OLD_SLIDE_3 in html:
    html = html.replace(OLD_SLIDE_3, NEW_SLIDES)
    print("✅ Slide 3 replaced with 5 poin studi pendahuluan + sumber")
    print("✅ Slide 3b (5 dimensi metakognitif) created as separate slide")
else:
    print("❌ Could not find old slide 3 content")
    # Debug: try to find parts of it
    parts = [
        "Mengapa Penelitian Ini Penting?",
        "Masalah yang Ditemukan di Lapangan",
        "Lima Dimensi Kelemahan Metakognitif",
    ]
    for p in parts:
        if p in html:
            print(f"  ✓ Found: '{p}'")
        else:
            print(f"  ✗ NOT found: '{p}'")

# ─── Write ───
with open(OUTPUT, "w", encoding="utf-8") as f:
    f.write(html)

print(f"✅ File saved: {OUTPUT}")
print(f"   Size: {len(html):,} bytes")
