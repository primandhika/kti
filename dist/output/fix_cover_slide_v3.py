#!/usr/bin/env python3
import re

INPUT = "/home/primandhika/artikel/dist/output/presentasi_hasil.html"

with open(INPUT, "r", encoding="utf-8") as f:
    html = f.read()

# 1. Modify the CSS to ensure cover slide backgrounds and text colors work nicely
# remove background from .slide-content specifically for cover so data-background works 
new_css_rules = """
/* ── custom cover slide ── */
.reveal .slides section.cover-slide {
  background: #ffffff !important;
}
.reveal .slides section.cover-slide .slide-content {
  background: transparent !important;
}
</style>"""
if "/* ── custom cover slide ── */" not in html:
    html = html.replace('</style>', new_css_rules)

# 2. Replace cover slide
start_idx = html.find("<section>")
end_idx = html.find("</section>") + len("</section>")

if start_idx != -1 and end_idx != -1:
    old_cover = html[start_idx:end_idx]
    
    img_match = re.search(r'src="(data:image/png;base64,[^"]+)"', old_cover)
    img_src = img_match.group(1) if img_match else ""
    
    # New cover slide with:
    # - data-background-color="#ffffff"
    # - Removed white box from logo
    # - Combined title in one <h1>
    # - Added &copy; 2026
    new_cover = f"""<section class="cover-slide" data-background-color="#ffffff" style="background-color: #ffffff;">
<div class="slide-content" style="padding: 10px 30px; background: transparent;">
<div style="text-align:center; padding: 0px; font-family: var(--font-heading);">
<img src="{img_src}" alt="Logo UHAMKA" style="height:75px; margin: 0 0 6px 0; display:inline-block; border:none; box-shadow:none; background:transparent; padding:0;"/>
<p style="font-size:0.75em; margin:0; letter-spacing:0.05em; font-family:var(--font-heading); color:#132040 !important; font-weight: 600;">UNIVERSITAS MUHAMMADIYAH PROF. DR. HAMKA</p>
<p style="font-size:0.65em; margin:0 0 10px 0; letter-spacing:0.08em; color:#555 !important;"><em>integrity, trust, compassion</em></p>
<hr class="ornament" style="border:none; width:80px; height:2px; background:linear-gradient(90deg, transparent, #c9a84c, transparent); margin:6px auto;"/>

<p style="font-size:0.9em; font-weight:600; margin:6px 0 2px 0; line-height:1.2; font-family:var(--font-heading); text-transform: uppercase; color:#555 !important;">Temuan dan Pembahasan</p>

<h1 style="font-size:1.25em; margin:10px 0 10px 0; line-height:1.35; font-family:var(--font-heading); border-bottom:none; padding-bottom: 0; color:#0a1628 !important; text-shadow:none;">Pengembangan Web <em>Microlearning</em> Berbasis Teknik Feynman untuk Meningkatkan Kemampuan Metakognitif dalam Keterampilan Berbicara Mahasiswa</h1>

<hr class="ornament-thin" style="border:none; width:60px; height:1px; background:linear-gradient(90deg, transparent, rgba(201,168,76,0.8), transparent); margin:10px auto;"/>
<p style="font-size:0.85em; font-weight:700; margin:6px 0 2px 0; font-family:var(--font-heading); letter-spacing:0.05em; color:#0a1628 !important;">RESTU BIAS PRIMANDHIKA</p>
<p style="font-size:0.7em; margin:0 0 2px 0; color:#555 !important;">NIM 2409108009</p>
<p style="font-size:0.7em; margin:0 0 10px 0; color:#555 !important;">Program Studi Doktor Pendidikan Bahasa Indonesia | Sekolah Pascasarjana UHAMKA</p>

<p style="font-size:0.5em; margin:12px 0 0 0; color:#999 !important;">&copy; 2026</p>
</div>
</div>
</section>"""
    html = html[:start_idx] + new_cover + html[end_idx:]

with open(INPUT, "w", encoding="utf-8") as f:
    f.write(html)
print("Berhasil update cover slide V3.")
