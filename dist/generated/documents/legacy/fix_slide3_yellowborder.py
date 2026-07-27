#!/usr/bin/env python3
import re

INPUT = "/home/primandhika/artikel/dist/output/presentasi_hasil.html"

with open(INPUT, "r", encoding="utf-8") as f:
    html = f.read()

old_str = '<div class="study-card" style="border-color: rgba(201,168,76,0.6); box-shadow: 0 0 15px rgba(201,168,76,0.2);">'
new_str = '<div class="study-card">'

if old_str in html:
    html = html.replace(old_str, new_str)
    
    # Also optionally remove the inline gold color on the h4 if that's what they mean
    old_h4 = '<h4 style="color:var(--c-gold);"><i class="fa-solid fa-robot"></i> Ketergantungan AI</h4>'
    new_h4 = '<h4><i class="fa-solid fa-robot"></i> Ketergantungan AI</h4>'
    html = html.replace(old_h4, new_h4)

    with open(INPUT, "w", encoding="utf-8") as f:
        f.write(html)
    print("Yellow border removed from the 3rd card.")
else:
    print("Failed to find the highlighted card.")
