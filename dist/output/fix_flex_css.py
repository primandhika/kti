#!/usr/bin/env python3
import re

INPUT = "/home/primandhika/artikel/dist/output/presentasi_hasil.html"

with open(INPUT, "r", encoding="utf-8") as f:
    html = f.read()

# The specific string to remove, from the comment down to the h3 block
pattern = r'/\* fallback for inline-styled cards \*/.*?\.reveal section div\[style\*="display:flex"\] > div h3 \{\s*color: var\(--c-gold\) !important;\s*\}'

new_html = re.sub(pattern, '', html, flags=re.DOTALL)

with open(INPUT, "w", encoding="utf-8") as f:
    f.write(new_html)

print("Successfully removed the toxic global flex container CSS rule.")
