#!/usr/bin/env python3
import re

INPUT = "/home/primandhika/artikel/dist/output/presentasi_hasil.html"

with open(INPUT, "r", encoding="utf-8") as f:
    html = f.read()

# We need to find the references div and replace it.
# The references div starts with: <div style="font-size:0.45em; color:var(--c-fg3); text-align:left; border-left:3px solid var(--c-gold); padding-left:10px; line-height:1.4; background: rgba(0,0,0,0.15); padding-top:4px; padding-bottom:4px; border-radius: 0 6px 6px 0;">
# And ends with </div>\n</div>\n</section>

# Let's just do a regex replace for the references div in the slide 3
start_str = "<strong>Referensi Utama:</strong>"
start_idx = html.find(start_str)

if start_idx != -1:
    div_start = html.rfind("<div", 0, start_idx)
    div_end = html.find("</div>", start_idx) + len("</div>")
    
    old_refs = html[div_start:div_end]
    
    new_refs = """<div style="font-size:0.4em; color:var(--c-fg3); text-align:left; border-left:3px solid var(--c-gold); padding-left:10px; line-height:1.3; background: rgba(0,0,0,0.15); padding-top:6px; padding-bottom:6px; border-radius: 0 6px 6px 0;">
  <strong>Referensi Utama:</strong><br/>
  1. Primandhika, R. B., Hikmat, A., Safii, I., & Yani, A. S. (2025a). The Impact of Learning Technology on Cognitive Abilities: Exploring Digital Media Preferences of Indonesian Language Education Students. <em>KEMBARA: Jurnal Keilmuan Bahasa, Sastra, dan Pengajarannya</em>, 11(1).<br/>
  2. Primandhika, R. B., Yani, A. S., Solihati, N., & Zulaiha, S. (2025b). Analisis Potensi Microlearning berbasis Infografis dalam Meningkatkan Penguasaan Istilah Baku Bahasa Indonesia. <em>Proceeding Seminar Internasional Pendidikan Bahasa Indonesia</em>, 1(1), 47-61.<br/>
  3. Primandhika, R. B., Solihati, N., & Zulaiha, S. (2026). AI's Impact on Students' Oral Presentation Skills: Indonesian Lecturers' Pedagogical Responses. <em>Edukasi: Jurnal Pendidikan dan Pengajaran</em>, 13(1), 274-293.
</div>"""
    
    html = html.replace(old_refs, new_refs)
    
    with open(INPUT, "w", encoding="utf-8") as f:
        f.write(html)
    print("References updated successfully!")
else:
    print("Failed to find references section.")
