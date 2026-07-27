#!/usr/bin/env python3

INPUT = "/home/primandhika/artikel/dist/output/presentasi_hasil.html"

with open(INPUT, "r", encoding="utf-8") as f:
    html = f.read()

# 1. Remove the white border from .study-card on Slide 3
old_card_border = "border: 1px solid rgba(255,255,255,0.05);"
new_card_border = "border: none;"
html = html.replace(old_card_border, new_card_border)

# 2. Remove the yellow border from .reveal blockquote globally
old_bq_border = "border-left: 3px solid var(--c-gold) !important;"
new_bq_border = "border-left: none !important;"
html = html.replace(old_bq_border, new_bq_border)

# 3. Just in case, let's also ensure NO other study-card border exists on slide 3
# The new slide 3 uses border: 1px solid rgba(255,255,255,0.05); which is now removed.

with open(INPUT, "w", encoding="utf-8") as f:
    f.write(html)

print("Removed all subtle borders from cards and yellow borders from blockquotes.")
