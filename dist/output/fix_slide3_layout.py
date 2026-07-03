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
<div class="slide-content" style="padding: 0 10px; width:100%; height:100%; display:flex; flex-direction:column; justify-content:space-between; box-sizing:border-box;">
  
  <h2 style="color:var(--c-gold); font-size:1.3em; margin:0 0 15px 0; text-align:center;"><i class="fa-solid fa-triangle-exclamation"></i> Identifikasi Masalah Utama</h2>

  <style>
  .study-card {
     background: var(--c-bg-card);
     border: 1px solid var(--c-border);
     border-radius: 12px;
     padding: 16px 14px;
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
     line-height: 1.3 !important;
  }
  
  .ref-col {
     flex: 1 1 30%;
     padding: 12px 14px;
     background: rgba(0,0,0,0.15);
     border-top: 3px solid var(--c-gold);
     border-radius: 6px;
     font-size: 0.45em !important;
     color: var(--c-fg3);
     line-height: 1.4 !important;
     text-align: left;
  }
  .ref-col strong { 
      color: var(--c-gold); 
      margin-bottom:6px; 
      display:block; 
      font-size: 1.05em;
  }
  </style>

  <div style="display:flex; flex-wrap:wrap; justify-content:center; gap:16px; margin-bottom:20px;">
    
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
      <p><em>Overreliance</em> pada AI generatif menggeser praktik bicara lisan menjadi sekadar membaca teks.</p>
    </div>
    
    <div class="study-card">
      <h4><i class="fa-solid fa-person-chalkboard"></i> Fokus Performa</h4>
      <p>Pembelajaran masih mengabaikan proses kognitif-reflektif yang melandasinya.</p>
    </div>
    
    <div class="study-card">
      <h4><i class="fa-solid fa-desktop"></i> Media Satu Arah</h4>
      <p>Media didominasi teks tanpa ruang latihan interaktif & refleksi lisan memadai.</p>
    </div>
    
    <div class="study-card">
      <h4><i class="fa-solid fa-puzzle-piece"></i> Ketiadaan Integrasi</h4>
      <p>Belum ada media web <em>microlearning</em> + Teknik Feynman yang tersistematisasi.</p>
    </div>

  </div>

  <div style="width: 88%; display:flex; flex-wrap:wrap; gap:14px; justify-content:space-between; margin-bottom: 20px;">
    <div class="ref-col">
      <strong>1. Primandhika, R. B., Hikmat, A., Safii, I., & Yani, A. S. (2025a).</strong>
      The Impact of Learning Technology on Cognitive Abilities: Exploring Digital Media Preferences of Indonesian Language Education Students. <em>KEMBARA</em>, 11(1).
    </div>
    <div class="ref-col">
      <strong>2. Primandhika, R. B., Yani, A. S., Solihati, N., & Zulaiha, S. (2025b).</strong>
      Analisis Potensi Microlearning berbasis Infografis dalam Meningkatkan Penguasaan Istilah Baku Bahasa Indonesia. <em>Proceeding SIPBI</em>, 1(1), 47-61.
    </div>
    <div class="ref-col">
      <strong>3. Primandhika, R. B., Solihati, N., & Zulaiha, S. (2026).</strong>
      AI's Impact on Students' Oral Presentation Skills: Indonesian Lecturers' Pedagogical Responses. <em>Edukasi</em>, 13(1), 274-293.
    </div>
  </div>

</div>
</section>"""
    
    html = html.replace(old_slide, new_slide)
    
    with open(INPUT, "w", encoding="utf-8") as f:
        f.write(html)
    print("Slide redesigned perfectly: beautiful padding, large fonts, 3-column references.")
else:
    print("Failed to find slide 3.")
