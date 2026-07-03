#!/usr/bin/env python3
import re

INPUT = "/home/primandhika/artikel/dist/output/presentasi_hasil.html"

with open(INPUT, "r", encoding="utf-8") as f:
    html = f.read()

start_idx = html.find("Identifikasi Masalah Utama")

if start_idx != -1:
    section_start = html.rfind("<section>", 0, start_idx)
    section_end = html.find("</section>", start_idx) + len("</section>")
    
    old_slide = html[section_start:section_end]
    
    new_slide = """<section>
<div class="slide-content" style="padding: 0 10px;">
<h2 style="color:var(--c-gold); font-size:1.15em; margin:0 0 5px 0;"><i class="fa-solid fa-triangle-exclamation"></i> Identifikasi Masalah Utama</h2>

<style>
.study-card {
   background: var(--c-bg-card);
   border: 1px solid var(--c-border);
   border-radius: 10px;
   padding: 10px 12px;
   text-align: left;
   transition: transform 0.2s ease, box-shadow 0.2s ease;
   box-shadow: 0 4px 8px rgba(0,0,0,0.3);
   flex: 1 1 30%;
   min-width: 170px;
}
.study-card:hover {
   transform: translateY(-2px) scale(1.01);
   box-shadow: 0 6px 12px rgba(201,168,76,0.3);
   background: var(--c-navy);
   border-color: var(--c-gold);
}
.study-card h4 {
   font-size: 0.8em !important;
   color: var(--c-fg);
   margin: 0 0 4px 0 !important;
   line-height: 1.1 !important;
   font-family: var(--font-heading);
   display: flex;
   align-items: center;
   gap: 6px;
}
.study-card h4 i {
   color: var(--c-gold);
   font-size: 1.1em;
   transition: transform 0.3s ease;
}
.study-card:hover h4 i {
   transform: scale(1.2) rotate(5deg);
}
.study-card p {
   font-size: 0.6em !important;
   color: var(--c-fg3);
   margin: 0 !important;
   line-height: 1.15 !important;
}
</style>

<div style="display:flex; flex-wrap:wrap; justify-content:center; gap:8px; margin-bottom:8px;">
  
  <div class="study-card">
    <h4><i class="fa-solid fa-microphone-lines-slash"></i> Keterampilan Lisan</h4>
    <p>Lemah dalam mengorganisasi ide, argumen, dan strategi komunikasi akademik.</p>
  </div>
  
  <div class="study-card">
    <h4><i class="fa-solid fa-brain"></i> Metakognitif Rendah</h4>
    <p>Kelemahan dimensi penyusunan konsep hingga evaluasi diri saat berbicara.</p>
  </div>
  
  <div class="study-card" style="border-color: rgba(201,168,76,0.6); box-shadow: 0 0 10px rgba(201,168,76,0.2);">
    <h4 style="color:var(--c-gold);"><i class="fa-solid fa-robot"></i> Ketergantungan AI</h4>
    <p><em>Overreliance</em> pada AI menggeser praktik lisan menjadi membaca teks.</p>
  </div>
  
  <div class="study-card">
    <h4><i class="fa-solid fa-person-chalkboard"></i> Fokus Performa</h4>
    <p>Pembelajaran mengabaikan proses kognitif-reflektif yang melandasinya.</p>
  </div>
  
  <div class="study-card">
    <h4><i class="fa-solid fa-desktop"></i> Media Satu Arah</h4>
    <p>Didominasi teks tanpa ruang latihan interaktif & refleksi lisan memadai.</p>
  </div>
  
  <div class="study-card">
    <h4><i class="fa-solid fa-puzzle-piece"></i> Ketiadaan Integrasi</h4>
    <p>Belum ada web <em>microlearning</em> + Teknik Feynman tersistematisasi.</p>
  </div>

</div>

<!-- width: 85% to ensure it does not overlap the Reveal.js navigation arrows at the bottom right! -->
<div style="width: 85%; font-size: 0.5em; color:var(--c-fg3); text-align:left; border-left:3px solid var(--c-gold); padding-left:10px; line-height:1.2; background: rgba(0,0,0,0.15); padding-top:6px; padding-bottom:6px; border-radius: 0 6px 6px 0; margin-bottom: 20px;">
  <strong style="color:var(--c-gold); margin-bottom:2px; display:inline-block;">Rujukan Studi Pendahuluan:</strong><br/>
  1. Primandhika, R. B., Hikmat, A., Safii, I., & Yani, A. S. (2025a). The Impact of Learning Technology on Cognitive Abilities: Exploring Digital Media Preferences of Indonesian Language Education Students. <em>KEMBARA</em>, 11(1).<br/>
  2. Primandhika, R. B., Yani, A. S., Solihati, N., & Zulaiha, S. (2025b). Analisis Potensi Microlearning berbasis Infografis dalam Meningkatkan Penguasaan Istilah Baku Bahasa Indonesia. <em>Proceeding SIPBI</em>, 1(1), 47-61.<br/>
  3. Primandhika, R. B., Solihati, N., & Zulaiha, S. (2026). AI's Impact on Students' Oral Presentation Skills: Indonesian Lecturers' Pedagogical Responses. <em>Edukasi</em>, 13(1), 274-293.
</div>
</div>
</section>"""
    
    html = html.replace(old_slide, new_slide)
    
    with open(INPUT, "w", encoding="utf-8") as f:
        f.write(html)
    print("Slide patched to fix control overlap and restore icon animations.")
else:
    print("Failed to find slide 3.")
