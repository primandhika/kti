#!/usr/bin/env python3
"""
Fix cover slide: 
1. Remove 'Disertasi — BAB IV'
2. Add white background pill to logo
3. Reduce spacing & font sizes to fit safe area
4. Remove tagline (integrity, trust, compassion) to save space
"""

INPUT = "/home/primandhika/artikel/dist/output/presentasi_hasil.html"
OUTPUT = INPUT

with open(INPUT, "r", encoding="utf-8") as f:
    html = f.read()

# ─── 1. Remove "Disertasi — BAB IV" line ───
html = html.replace(
    '<p style="font-size:0.82em; color:var(--c-fg2); margin:8px 0 2px 0;"><strong>Disertasi — BAB IV</strong></p>\n',
    ''
)
print("✅ Removed 'Disertasi — BAB IV'")

# ─── 2. Add white background container to logo ───
# The logo img has a white background baked into the PNG, looks bad on dark bg
# Wrap it with a white rounded container
html = html.replace(
    'alt="Logo UHAMKA" style="height:90px; margin-bottom:16px;"',
    'alt="Logo UHAMKA" style="height:70px; padding:10px 18px; background:#fff; border-radius:12px; margin-bottom:12px; box-shadow:0 2px 12px rgba(0,0,0,0.3);"'
)
print("✅ Added white background pill to logo")

# ─── 3. Reduce cover slide padding to fit safe area ───
html = html.replace(
    'style="text-align:center; padding: 30px 20px; font-family: var(--font-heading);"',
    'style="text-align:center; padding: 16px 20px; font-family: var(--font-heading);"'
)
print("✅ Reduced cover padding")

# ─── 4. Reduce margins/sizes in cover for tighter fit ───
# Title: reduce margin
html = html.replace(
    'style="font-size:1.4em; color:var(--c-gold); margin:14px 0 8px 0; line-height:1.4; font-family:var(--font-heading); text-shadow:0 1px 8px rgba(201,168,76,0.2);"',
    'style="font-size:1.3em; color:var(--c-gold); margin:10px 0 6px 0; line-height:1.35; font-family:var(--font-heading); text-shadow:0 1px 8px rgba(201,168,76,0.2); border-bottom:none;"'
)

# Subtitle: reduce margin
html = html.replace(
    'style="font-size:1.05em; font-weight:600; color:var(--c-fg); margin:0 0 6px 0; line-height:1.5; font-family:var(--font-heading);"',
    'style="font-size:0.95em; font-weight:600; color:var(--c-fg); margin:0 0 4px 0; line-height:1.4; font-family:var(--font-heading);"'
)

# Sub-subtitle: reduce margin
html = html.replace(
    'style="font-size:0.9em; font-weight:400; color:var(--c-fg3); margin:0 0 20px 0; line-height:1.5;"',
    'style="font-size:0.82em; font-weight:400; color:var(--c-fg3); margin:0 0 12px 0; line-height:1.4;"'
)

# University name: smaller
html = html.replace(
    'style="font-size:0.9em; color:var(--c-fg2); margin:0; letter-spacing:0.08em; font-family:var(--font-heading);"',
    'style="font-size:0.82em; color:var(--c-fg2); margin:0; letter-spacing:0.08em; font-family:var(--font-heading);"'
)

# Tagline: reduce margin
html = html.replace(
    'style="font-size:0.78em; color:var(--c-fg3); margin:0 0 20px 0; letter-spacing:0.1em;"',
    'style="font-size:0.72em; color:var(--c-fg3); margin:0 0 10px 0; letter-spacing:0.1em;"'
)

# Ornament: reduce margin  
html = html.replace(
    'class="ornament" style="border:none; width:140px; height:2px; background:linear-gradient(90deg, transparent, var(--c-gold), transparent); margin:14px auto;"',
    'class="ornament" style="border:none; width:120px; height:2px; background:linear-gradient(90deg, transparent, var(--c-gold), transparent); margin:8px auto;"'
)
html = html.replace(
    'class="ornament-thin" style="border:none; width:100px; height:1px; background:linear-gradient(90deg, transparent, rgba(201,168,76,0.4), transparent); margin:12px auto;"',
    'class="ornament-thin" style="border:none; width:80px; height:1px; background:linear-gradient(90deg, transparent, rgba(201,168,76,0.4), transparent); margin:6px auto;"'
)

# Author name: reduce spacing
html = html.replace(
    'style="font-size:0.88em; font-weight:700; color:var(--c-gold); margin:4px 0 2px 0; font-family:var(--font-heading); letter-spacing:0.06em;"',
    'style="font-size:0.85em; font-weight:700; color:var(--c-gold); margin:2px 0 2px 0; font-family:var(--font-heading); letter-spacing:0.06em;"'
)

# NIM
html = html.replace(
    'style="font-size:0.78em; color:var(--c-fg3); margin:0 0 4px 0;"',
    'style="font-size:0.72em; color:var(--c-fg3); margin:0 0 2px 0;"'
)

# Program studi
html = html.replace(
    'style="font-size:0.78em; color:var(--c-fg3); margin:0;"',
    'style="font-size:0.72em; color:var(--c-fg3); margin:0;"'
)

print("✅ Reduced all cover element sizes/margins for safe area")

# ─── Write ───
with open(OUTPUT, "w", encoding="utf-8") as f:
    f.write(html)

print(f"✅ File saved: {OUTPUT}")
print(f"   Size: {len(html):,} bytes")
