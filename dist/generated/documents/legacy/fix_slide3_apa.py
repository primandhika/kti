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
<div class="slide-content" style="padding: 0 10px; width:100%; height:100%; display:flex; flex-direction:column; justify-content:flex-start; box-sizing:border-box;">
  
  <h2 style="color:var(--c-gold); font-size:1.3em; margin:0 0 10px 0; text-align:center;"><i class="fa-solid fa-triangle-exclamation"></i> Identifikasi Masalah Utama</h2>

  <style>
  .study-card {
     background: var(--c-bg-card);
     border: 1px solid var(--c-border);
     border-radius: 12px;
     padding: 14px 12px;
     text-align: left;
     transition: transform 0.3s ease, box-shadow 0.3s ease;
     box-shadow: 0 4px 12px rgba(0,0,0,0.25);
     flex: 1 1 30%;
     min-width: 220px;
  }
  .study-card:hover {
     transform: translateY(-5px) scale(1.02);
     box-shadow: 0 8px 24px rgba(201,168,76,0.25);
     background: var(--c-navy);
     border-color: var(--c-gold);
  }
  .study-card h4 {
     font-size: 0.85em !important;
     color: var(--c-fg);
     margin: 0 0 6px 0 !important;
     line-height: 1.15 !important;
     font-family: var(--font-heading);
     display: flex;
     align-items: center;
     gap: 8px;
  }
  .study-card h4 i {
     color: var(--c-gold);
     font-size: 1.2em;
     transition: transform 0.3s ease;
  }
  .study-card:hover h4 i {
     transform: scale(1.2) rotate(5deg);
  }
  .study-card p {
     font-size: 0.65em !important;
     color: var(--c-fg3);
     margin: 0 !important;
     line-height: 1.25 !important;
  }
  .refs-container {
     width: 90%; 
     margin: 0 auto;
     font-size: 0.55em !important;
     color: var(--c-fg3);
     line-height: 1.35 !important;
     text-align: justify;
     padding-bottom: 20px;
  }
  .refs-container p {
     margin: 0 0 6px 0 !important;
     padding-left: 20px;
     text-indent: -20px;
  }
  </style>

  <div style="display:flex; flex-wrap:wrap; justify-content:center; gap:12px; margin-bottom:15px;">
    
    <div class="study-card">
      <h4><i class="fa-solid fa-microphone-lines-slash"></i> Keterampilan Lisan</h4>
      <p>Lemah dalam mengorganisasi ide, argumen, dan strategi komunikasi akademik.</p>
    </div>
    
    <div class="study-card">
      <h4><i class="fa-solid fa-brain"></i> Metakognitif Rendah</h4>
      <p>Kelemahan dimensi penyusunan konsep hingga evaluasi diri saat berbicara.</p>
    </div>
    
    <div class="study-card" style="border-color: rgba(201,168,76,0.6); box-shadow: 0 0 15px rgba(201,168,76,0.2);">
      <h4 style="color:var(--c-gold);"><i class="fa-solid fa-robot"></i> Ketergantungan AI</h4>
      <p><em>Overreliance</em> pada AI generatif menggeser praktik bicara lisan menjadi membaca teks.</p>
    </div>
    
    <div class="study-card">
      <h4><i class="fa-solid fa-person-chalkboard"></i> Fokus Performa</h4>
      <p>Pembelajaran mengabaikan proses kognitif-reflektif yang melandasinya.</p>
    </div>
    
    <div class="study-card">
      <h4><i class="fa-solid fa-desktop"></i> Media Satu Arah</h4>
      <p>Media didominasi teks tanpa ruang latihan interaktif & refleksi lisan memadai.</p>
    </div>
    
    <div class="study-card">
      <h4><i class="fa-solid fa-puzzle-piece"></i> Ketiadaan Integrasi</h4>
      <p>Belum ada media web <em>microlearning</em> + Teknik Feynman tersistematisasi.</p>
    </div>

  </div>

  <div class="refs-container">
    <p>Primandhika, R. B., Hikmat, A., Safii, I., & Yani, A. S. (2025a). The Impact of Learning Technology on Cognitive Abilities: Exploring Digital Media Preferences of Indonesian Language Education Students. <em>KEMBARA</em>, 11(1).</p>
    <p>Primandhika, R. B., Yani, A. S., Solihati, N., & Zulaiha, S. (2025b). Analisis Potensi Microlearning berbasis Infografis dalam Meningkatkan Penguasaan Istilah Baku Bahasa Indonesia. <em>Proceeding SIPBI</em>, 1(1), 47-61.</p>
    <p>Primandhika, R. B., Solihati, N., & Zulaiha, S. (2026). AI's Impact on Students' Oral Presentation Skills: Indonesian Lecturers' Pedagogical Responses. <em>Edukasi</em>, 13(1), 274-293.</p>
  </div>

</div>
</section>"""
    
    html = html.replace(old_slide, new_slide)
    
    with open(INPUT, "w", encoding="utf-8") as f:
        f.write(html)
    print("Slide redesigned perfectly: APA formatting, unified references, no borders.")
else:
    print("Failed to find slide 3.")
