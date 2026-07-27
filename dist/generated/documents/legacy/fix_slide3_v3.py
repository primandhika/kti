#!/usr/bin/env python3
import re

INPUT = "/home/primandhika/artikel/dist/output/presentasi_hasil.html"

with open(INPUT, "r", encoding="utf-8") as f:
    html = f.read()

start_str = "Temuan Studi Pendahuluan"
start_idx = html.find(start_str)

if start_idx != -1:
    section_start = html.rfind("<section>", 0, start_idx)
    section_end = html.find("</section>", start_idx) + len("</section>")
    
    old_slide = html[section_start:section_end]
    
    new_slide = """<section>
<div class="slide-content" style="padding: 10px 40px;">
<h2 style="color:var(--c-gold); font-size:1.3em; margin:0 0 4px 0;"><i class="fa-solid fa-triangle-exclamation"></i> Identifikasi Masalah</h2>
<p style="font-size:0.7em; color:var(--c-fg2); margin:0 0 12px 0;">Berdasarkan studi pendahuluan (Primandhika et al., 2025a, 2025b, 2026), terdapat 6 akar permasalahan utama:</p>

<style>
.study-card {
   background: var(--c-bg-card);
   border: 1px solid var(--c-border);
   border-radius: 12px;
   padding: 12px 10px;
   text-align: center;
   transition: transform 0.3s ease, box-shadow 0.3s ease, background 0.3s ease, border-color 0.3s ease;
   box-shadow: 0 4px 10px rgba(0,0,0,0.3);
   flex: 1 1 30%;
   min-width: 180px;
   cursor: default;
}
.study-card:hover {
   transform: translateY(-6px) scale(1.02);
   box-shadow: 0 10px 20px rgba(201,168,76,0.2);
   background: linear-gradient(145deg, var(--c-navy), #132040);
   border-color: var(--c-gold);
}
.study-card i {
   font-size: 1.6em;
   color: var(--c-gold);
   margin-bottom: 6px;
   transition: transform 0.3s ease;
}
.study-card:hover i {
   transform: scale(1.15) rotate(5deg);
}
.study-card h4 {
   font-size: 0.75em;
   color: var(--c-fg);
   margin: 0 0 4px 0;
   line-height: 1.2;
   font-family: var(--font-heading);
}
.study-card p {
   font-size: 0.55em;
   color: var(--c-fg3);
   margin: 0;
   line-height: 1.35;
}
</style>

<div style="display:flex; flex-wrap:wrap; justify-content:center; gap:10px; margin-bottom:12px;">
  
  <div class="study-card">
    <i class="fa-solid fa-microphone-lines-slash"></i>
    <h4>Keterampilan Lisan Lemah</h4>
    <p>Lemah dalam mengorganisasi ide, argumen, dan strategi komunikasi akademik.</p>
  </div>
  
  <div class="study-card">
    <i class="fa-solid fa-brain"></i>
    <h4>Metakognitif Rendah</h4>
    <p>Kelemahan pada dimensi penyusunan konsep hingga evaluasi diri saat berbicara.</p>
  </div>
  
  <div class="study-card" style="border-color: rgba(201,168,76,0.6); box-shadow: 0 0 15px rgba(201,168,76,0.2);">
    <i class="fa-solid fa-robot"></i>
    <h4 style="color:var(--c-gold);">Ketergantungan AI</h4>
    <p><em>Overreliance</em> pada AI generatif menggeser praktik bicara lisan menjadi sekadar aktivitas membaca ulang teks.</p>
  </div>
  
  <div class="study-card">
    <i class="fa-solid fa-person-chalkboard"></i>
    <h4>Fokus Performa Akhir</h4>
    <p>Pembelajaran masih mengabaikan proses kognitif-reflektif yang melandasinya.</p>
  </div>
  
  <div class="study-card">
    <i class="fa-solid fa-desktop"></i>
    <h4>Media Satu Arah</h4>
    <p>Media didominasi teks tanpa ruang latihan interaktif & refleksi lisan yang memadai.</p>
  </div>
  
  <div class="study-card">
    <i class="fa-solid fa-puzzle-piece"></i>
    <h4>Ketiadaan Integrasi</h4>
    <p>Belum ada media web <em>microlearning</em> + Teknik Feynman yang tersistematisasi.</p>
  </div>

</div>

<div style="font-size:0.45em; color:var(--c-fg3); text-align:left; border-left:3px solid var(--c-gold); padding-left:10px; line-height:1.4; background: rgba(0,0,0,0.15); padding-top:4px; padding-bottom:4px; border-radius: 0 6px 6px 0;">
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
