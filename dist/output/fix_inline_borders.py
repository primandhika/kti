#!/usr/bin/env python3
import re

INPUT = "/home/primandhika/artikel/dist/output/presentasi_hasil.html"

with open(INPUT, "r", encoding="utf-8") as f:
    html = f.read()

# Replace all inline borders that use --c-border or --c-gold
html = re.sub(r'border:\s*1px\s*solid\s*var\(--c-border\);?', 'border: none;', html)
html = re.sub(r'border:\s*1px\s*solid\s*var\(--c-gold\);?', 'border: none;', html)
html = re.sub(r'border-left:\s*3px\s*solid\s*var\(--c-gold\);?', 'border-left: none;', html)

with open(INPUT, "w", encoding="utf-8") as f:
    f.write(html)

print("Removed all inline borders using var(--c-border) and var(--c-gold).")
