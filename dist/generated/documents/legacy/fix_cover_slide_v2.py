#!/usr/bin/env python3
import re

INPUT = "/home/primandhika/artikel/dist/output/presentasi_hasil.html"

with open(INPUT, "r", encoding="utf-8") as f:
    html = f.read()

start_idx = html.find("<section>")
end_idx = html.find("</section>") + len("</section>")

if start_idx != -1 and end_idx != -1:
    old_cover = html[start_idx:end_idx]
    
    img_match = re.search(r'src="(data:image/png;base64,[^"]+)"', old_cover)
    img_src = img_match.group(1) if img_match else ""
    
    new_cover = f"""<section>
<div class="slide-content" style="padding: 10px 40px;">
<div style="text-align:center; padding: 0px; font-family: var(--font-heading);">
<img src="{img_src}" alt="Logo UHAMKA" style="height:60px; padding:6px 14px; background:#fff; border-radius:10px; margin: 0 0 8px 0; box-shadow:0 2px 8px rgba(0,0,0,0.3); display:inline-block;"/>
<p style="font-size:0.75em; color:var(--c-fg2); margin:0; letter-spacing:0.05em; font-family:var(--font-heading);">UNIVERSITAS MUHAMMADIYAH PROF. DR. HAMKA</p>
<p style="font-size:0.65em; color:var(--c-fg3); margin:0 0 6px 0; letter-spacing:0.08em;"><em>integrity, trust, compassion</em></p>
<hr class="ornament" style="border:none; width:80px; height:2px; background:linear-gradient(90deg, transparent, var(--c-gold), transparent); margin:6px auto;"/>
<h3 style="font-size:0.9em; font-weight:600; color:var(--c-fg); margin:6px 0 2px 0; line-height:1.2; font-family:var(--font-heading); text-transform: uppercase;">Temuan dan Pembahasan</h3>
<h1 style="font-size:1.55em; color:var(--c-gold); margin:4px 0 4px 0; line-height:1.15; font-family:var(--font-heading); text-shadow:0 1px 6px rgba(201,168,76,0.2); border-bottom:none; padding-bottom: 0;">Pengembangan Web <em>Microlearning</em> Berbasis Teknik Feynman</h1>
<h2 style="font-size:0.85em; font-weight:400; color:var(--c-fg2); margin:0 0 12px 0; line-height:1.25;">untuk Meningkatkan Kemampuan Metakognitif dalam Keterampilan Berbicara Mahasiswa</h2>
<hr class="ornament-thin" style="border:none; width:60px; height:1px; background:linear-gradient(90deg, transparent, rgba(201,168,76,0.4), transparent); margin:8px auto;"/>
<p style="font-size:0.85em; font-weight:700; color:var(--c-gold); margin:4px 0 2px 0; font-family:var(--font-heading); letter-spacing:0.05em;">RESTU BIAS PRIMANDHIKA</p>
<p style="font-size:0.7em; color:var(--c-fg3); margin:0 0 2px 0;">NIM 2409108009</p>
<p style="font-size:0.7em; color:var(--c-fg3); margin:0;">Program Studi Doktor Pendidikan Bahasa Indonesia | Sekolah Pascasarjana UHAMKA</p>
</div>
</div>
</section>"""
    html = html[:start_idx] + new_cover + html[end_idx:]

with open(INPUT, "w", encoding="utf-8") as f:
    f.write(html)
print("Berhasil update cover slide.")
