#!/usr/bin/env python3
import re

INPUT = "/home/primandhika/artikel/dist/output/presentasi_hasil.html"

with open(INPUT, "r", encoding="utf-8") as f:
    html = f.read()

# Add a small note at the bottom of the card container in slide 3
target_str = """  <div class="study-card">
    <i class="fa-solid fa-puzzle-piece"></i>
    <h4>Ketiadaan Integrasi</h4>
    <p>Belum ada media web <em>microlearning</em> + Teknik Feynman yang tersistematisasi.</p>
  </div>

</div>"""

replacement_str = """  <div class="study-card">
    <i class="fa-solid fa-puzzle-piece"></i>
    <h4>Ketiadaan Integrasi</h4>
    <p>Belum ada media web <em>microlearning</em> + Teknik Feynman yang tersistematisasi.</p>
  </div>

</div>
<p style="font-size:0.5em; color:var(--c-gold); text-align:right; margin:10px 10px 0 0; font-style:italic;">* Rujukan lengkap tersedia di slide berikutnya &rarr;</p>"""

if target_str in html:
    html = html.replace(target_str, replacement_str)
    with open(INPUT, "w", encoding="utf-8") as f:
        f.write(html)
    print("Added indicator for references!")
else:
    print("Could not find the target string to add indicator.")
