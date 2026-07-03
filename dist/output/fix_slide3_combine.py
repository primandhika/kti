#!/usr/bin/env python3
import re

INPUT = "/home/primandhika/artikel/dist/output/presentasi_hasil.html"

with open(INPUT, "r", encoding="utf-8") as f:
    html = f.read()

# I need to find the start of slide 3 and end of slide 4.
# Slide 3 title: Identifikasi Masalah Utama
# Slide 4 title: Rujukan Studi Pendahuluan

start_idx = html.find("Identifikasi Masalah Utama")
end_idx = html.find("Rujukan Studi Pendahuluan")

if start_idx != -1 and end_idx != -1:
    section_start = html.rfind("<section>", 0, start_idx)
    section_end = html.find("</section>", end_idx) + len("</section>")
    
    old_slides = html[section_start:section_end]
    
    new_slide = """<section>
<div class="slide-content" style="padding: 5px 30px;">
<h2 style="color:var(--c-gold); font-size:1.15em; margin:0 0 8px 0;"><i class="fa-solid fa-triangle-exclamation"></i> Identifikasi Masalah Utama</h2>

<style>
.study-card {
   background: var(--c-bg-card);
   border: 1px solid var(--c-border);
   border-radius: 12px;
   padding: 10px 10px;
   text-align: center;
   transition: transform 0.3s ease, box-shadow 0.3s ease, background 0.3s ease, border-color 0.3s ease;
   box-shadow: 0 4px 8px rgba(0,0,0,0.3);
   flex: 1 1 30%;
   min-width: 170px;
   cursor: default;
}
.study-card:hover {
   transform: translateY(-4px) scale(1.02);
   box-shadow: 0 8px 16px rgba(201,168,76,0.2);
   background: linear-gradient(145deg, var(--c-navy), #132040);
   border-color: var(--c-gold);
}
.study-card i {
   font-size: 1.35em;
   color: var(--c-gold);
   margin-bottom: 5px;
   transition: transform 0.3s ease;
}
.study-card:hover i {
   transform: scale(1.15) rotate(5deg);
}
.study-card h4 {
   font-size: 0.72em !important;
   color: var(--c-fg);
   margin: 0 0 4px 0 !important;
   line-height: 1.1 !important;
   font-family: var(--font-heading);
}
.study-card p {
   font-size: 0.52em !important;
   color: var(--c-fg3);
   margin: 0 !important;
   line-height: 1.2 !important;
}
</style>

<div style="display:flex; flex-wrap:wrap; justify-content:center; gap:10px; margin-bottom:10px;">
  
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
  
  <div class="study-card" style="border-color: rgba(201,168,76,0.6); box-shadow: 0 0 10px rgba(201,168,76,0.2);">
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

<div style="font-size:0.42em; color:var(--c-fg3); text-align:left; border-left:3px solid var(--c-gold); padding-left:10px; line-height:1.2; background: rgba(0,0,0,0.15); padding-top:6px; padding-bottom:6px; border-radius: 0 6px 6px 0;">
  <strong style="color:var(--c-gold); margin-bottom:2px; display:inline-block;">Rujukan Studi Pendahuluan:</strong><br/>
  1. Primandhika, R. B., Hikmat, A., Safii, I., & Yani, A. S. (2025a). The Impact of Learning Technology on Cognitive Abilities: Exploring Digital Media Preferences of Indonesian Language Education Students. <em>KEMBARA: Jurnal Keilmuan Bahasa, Sastra, dan Pengajarannya</em>, 11(1).<br/>
  2. Primandhika, R. B., Yani, A. S., Solihati, N., & Zulaiha, S. (2025b). Analisis Potensi Microlearning berbasis Infografis dalam Meningkatkan Penguasaan Istilah Baku Bahasa Indonesia. <em>Proceeding Seminar Internasional Pendidikan Bahasa Indonesia</em>, 1(1), 47-61.<br/>
  3. Primandhika, R. B., Solihati, N., & Zulaiha, S. (2026). AI's Impact on Students' Oral Presentation Skills: Indonesian Lecturers' Pedagogical Responses. <em>Edukasi: Jurnal Pendidikan dan Pengajaran</em>, 13(1), 274-293.
</div>
</div>
</section>"""
    
    html = html.replace(old_slides, new_slide)
    
    with open(INPUT, "w", encoding="utf-8") as f:
        f.write(html)
    print("Slide merged back with optimized sizes successfully!")
else:
    print("Failed to find slide 3 or 4.")
