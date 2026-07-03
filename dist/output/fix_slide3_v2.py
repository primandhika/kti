#!/usr/bin/env python3
import re

INPUT = "/home/primandhika/artikel/dist/output/presentasi_hasil.html"

with open(INPUT, "r", encoding="utf-8") as f:
    html = f.read()

start_str = "<h1><i class=\"fa-solid fa-triangle-exclamation\"></i> Mengapa Penelitian Ini Penting?</h1>"
start_idx = html.find(start_str)

if start_idx != -1:
    section_start = html.rfind("<section>", 0, start_idx)
    section_end = html.find("</section>", start_idx) + len("</section>")
    
    old_slide = html[section_start:section_end]
    
    new_slide = """<section>
<div class="slide-content" style="padding: 10px 40px;">
<h2 style="color:var(--c-gold); font-size:1.3em; margin:0 0 4px 0;"><i class="fa-solid fa-search"></i> Temuan Studi Pendahuluan</h2>
<p style="font-size:0.75em; color:var(--c-fg2); margin:0 0 15px 0;">Penelitian pendahuluan (Primandhika et al., 2025a, 2025b, 2026) mengungkap 5 permasalahan kritis keterampilan berbicara mahasiswa:</p>

<style>
.study-card {
   background: var(--c-bg-card);
   border: 1px solid var(--c-border);
   border-radius: 12px;
   padding: 15px 12px;
   text-align: center;
   transition: transform 0.3s ease, box-shadow 0.3s ease, background 0.3s ease, border-color 0.3s ease;
   box-shadow: 0 4px 10px rgba(0,0,0,0.3);
   flex: 1 1 28%;
   min-width: 220px;
   cursor: default;
}
.study-card:hover {
   transform: translateY(-6px) scale(1.02);
   box-shadow: 0 10px 20px rgba(201,168,76,0.2);
   background: linear-gradient(145deg, var(--c-navy), #132040);
   border-color: var(--c-gold);
}
.study-card i {
   font-size: 1.8em;
   color: var(--c-gold);
   margin-bottom: 8px;
   transition: transform 0.3s ease;
}
.study-card:hover i {
   transform: scale(1.15) rotate(5deg);
}
.study-card h4 {
   font-size: 0.8em;
   color: var(--c-fg);
   margin: 0 0 6px 0;
   line-height: 1.2;
   font-family: var(--font-heading);
}
.study-card p {
   font-size: 0.6em;
   color: var(--c-fg3);
   margin: 0;
   line-height: 1.35;
}
</style>

<div style="display:flex; flex-wrap:wrap; justify-content:center; gap:12px; margin-bottom:15px;">
  
  <div class="study-card">
    <i class="fa-solid fa-comment-slash"></i>
    <h4>Sulit Lisan</h4>
    <p>Mampu pada ujian tertulis, tetapi kesulitan memberi penjelasan lisan yang runtut.</p>
  </div>
  
  <div class="study-card">
    <i class="fa-solid fa-rotate-right"></i>
    <h4>Deskriptif & Repetitif</h4>
    <p>Penyampaian lisan tanpa elaborasi makna yang memadai.</p>
  </div>
  
  <div class="study-card">
    <i class="fa-solid fa-users-slash"></i>
    <h4>Strategi Lemah</h4>
    <p>Kurang menyesuaikan komunikasi dengan audiens akademik.</p>
  </div>
  
  <div class="study-card">
    <i class="fa-solid fa-arrow-trend-down"></i>
    <h4>Hambatan Internal</h4>
    <p>Kosakata terbatas, kurang siap, & rendahnya kepercayaan diri.</p>
  </div>
  
  <div class="study-card">
    <i class="fa-solid fa-link-slash"></i>
    <h4>Kesenjangan Kognitif</h4>
    <p>Pemahaman materi tidak sejalan dengan kemampuan menyampaikannya.</p>
  </div>

</div>

<div style="font-size:0.5em; color:var(--c-fg3); text-align:left; border-left:3px solid var(--c-gold); padding-left:12px; line-height:1.4; background: rgba(0,0,0,0.15); padding-top:6px; padding-bottom:6px; border-radius: 0 6px 6px 0;">
  <strong>Referensi Utama:</strong><br/>
  1. Primandhika, R. B., Hikmat, A., Safii, I., & Yani, A. S. (2025a). <em>The Impact of Learning Technology...</em> KEMBARA, 11(1).<br/>
  2. Primandhika, R. B., Yani, A. S., Solihati, N., & Zulaiha, S. (2025b). <em>Analisis Potensi Microlearning...</em> Proceeding SIPBI, 1(1).<br/>
  3. Primandhika, R. B., Solihati, N., & Zulaiha, S. (2026). <em>AI's Impact on Students' Oral Presentation...</em> Edukasi, 13(1).
</div>
</div>
</section>"""
    
    html = html[:section_start] + new_slide + html[section_end:]
    
    with open(INPUT, "w", encoding="utf-8") as f:
        f.write(html)
    print("Slide 3 replaced successfully!")
else:
    print("Failed to find slide 3.")
