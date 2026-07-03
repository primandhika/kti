#!/usr/bin/env python3
import re

INPUT = "/home/primandhika/artikel/dist/output/presentasi_hasil.html"

with open(INPUT, "r", encoding="utf-8") as f:
    html = f.read()

start_str = "Identifikasi Masalah Utama"
start_idx = html.find(start_str)

if start_idx != -1:
    section_start = html.rfind("<section>", 0, start_idx)
    section_end = html.find("</section>", start_idx) + len("</section>")
    
    old_slide = html[section_start:section_end]
    
    new_slides = """<section>
<div class="slide-content" style="padding: 10px 40px;">
<h2 style="color:var(--c-gold); font-size:1.4em; margin:0 0 15px 0;"><i class="fa-solid fa-triangle-exclamation"></i> Identifikasi Masalah Utama</h2>

<style>
.study-card {
   background: var(--c-bg-card);
   border: 1px solid var(--c-border);
   border-radius: 12px;
   padding: 18px 14px;
   text-align: center;
   transition: transform 0.3s ease, box-shadow 0.3s ease, background 0.3s ease, border-color 0.3s ease;
   box-shadow: 0 4px 10px rgba(0,0,0,0.3);
   flex: 1 1 30%;
   min-width: 200px;
   cursor: default;
}
.study-card:hover {
   transform: translateY(-4px) scale(1.02);
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
   font-size: 0.85em !important;
   color: var(--c-fg);
   margin: 0 0 6px 0 !important;
   line-height: 1.15 !important;
   font-family: var(--font-heading);
}
.study-card p {
   font-size: 0.65em !important;
   color: var(--c-fg3);
   margin: 0 !important;
   line-height: 1.25 !important;
}
</style>

<div style="display:flex; flex-wrap:wrap; justify-content:center; gap:14px; margin-bottom:12px;">
  
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
  
  <div class="study-card" style="border-color: rgba(201,168,76,0.6); box-shadow: 0 0 12px rgba(201,168,76,0.2);">
    <i class="fa-solid fa-robot"></i>
    <h4 style="color:var(--c-gold);">Ketergantungan AI</h4>
    <p><em>Overreliance</em> pada AI generatif menggeser praktik bicara lisan menjadi membaca teks.</p>
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
</div>
</section>

<section>
<div class="slide-content" style="padding: 20px 40px;">
<h2 style="color:var(--c-gold); font-size:1.3em; margin:0 0 20px 0;"><i class="fa-solid fa-book-open"></i> Rujukan Studi Pendahuluan</h2>
<div style="font-size:0.7em; color:var(--c-fg2); text-align:left; border-left:4px solid var(--c-gold); line-height:1.4; background: rgba(0,0,0,0.15); padding: 20px 25px; border-radius: 0 8px 8px 0; display:flex; flex-direction:column; gap:20px;">
  <div>
    <strong style="color: var(--c-fg);">1. Primandhika, R. B., Hikmat, A., Safii, I., & Yani, A. S. (2025a).</strong><br/>
    The Impact of Learning Technology on Cognitive Abilities: Exploring Digital Media Preferences of Indonesian Language Education Students.<br/>
    <em style="color:var(--c-gold);">KEMBARA: Jurnal Keilmuan Bahasa, Sastra, dan Pengajarannya</em>, 11(1).
  </div>
  <div>
    <strong style="color: var(--c-fg);">2. Primandhika, R. B., Yani, A. S., Solihati, N., & Zulaiha, S. (2025b).</strong><br/>
    Analisis Potensi Microlearning berbasis Infografis dalam Meningkatkan Penguasaan Istilah Baku Bahasa Indonesia.<br/>
    <em style="color:var(--c-gold);">Proceeding Seminar Internasional Pendidikan Bahasa Indonesia</em>, 1(1), 47-61.
  </div>
  <div>
    <strong style="color: var(--c-fg);">3. Primandhika, R. B., Solihati, N., & Zulaiha, S. (2026).</strong><br/>
    AI's Impact on Students' Oral Presentation Skills: Indonesian Lecturers' Pedagogical Responses.<br/>
    <em style="color:var(--c-gold);">Edukasi: Jurnal Pendidikan dan Pengajaran</em>, 13(1), 274-293.
  </div>
</div>
</div>
</section>"""
    
    html = html.replace(old_slide, new_slides)
    
    with open(INPUT, "w", encoding="utf-8") as f:
        f.write(html)
    print("Slide split successfully!")
else:
    print("Failed to find slide 3.")
