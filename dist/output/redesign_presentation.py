#!/usr/bin/env python3
"""
Redesign presentasi_hasil.html dari tema B&W flat (RISE-like)
ke tema Academic Premium (navy + gold, serif premium, animasi halus).
"""

import re

INPUT  = "/home/primandhika/artikel/dist/output/presentasi_hasil.html"
OUTPUT = "/home/primandhika/artikel/dist/output/presentasi_hasil.html"

with open(INPUT, "r", encoding="utf-8") as f:
    html = f.read()

# ────────────────────────────────────────────
# 1. Replace Google Fonts link
# ────────────────────────────────────────────
html = html.replace(
    '<link rel="preconnect" href="https://fonts.googleapis.com"/>',
    '<link rel="preconnect" href="https://fonts.googleapis.com"/>\n<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>'
)
html = html.replace(
    '<link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400;0,500;0,600;0,700;1,400;1,600&display=swap" rel="stylesheet"/>',
    '<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400;1,600&family=Source+Serif+4:ital,opsz,wght@0,8..60,300;0,8..60,400;0,8..60,500;0,8..60,600;0,8..60,700;1,8..60,400;1,8..60,600&display=swap" rel="stylesheet"/>'
)

# Also replace the white theme with a black theme base (we override everything anyway)
html = html.replace(
    '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/reveal.js@5.1.0/dist/theme/white.css"/>',
    '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/reveal.js@5.1.0/dist/theme/black.css"/>'
)

# ────────────────────────────────────────────
# 2. Replace entire <style> block
# ────────────────────────────────────────────
NEW_CSS = r"""<style>
/* ══════════════════════════════════════════════════════════════ */
/*  THEME: Academic Premium — Navy & Gold, Playfair Display      */
/* ══════════════════════════════════════════════════════════════ */

:root {
  --c-navy:       #0a1628;
  --c-navy-mid:   #132040;
  --c-navy-light: #1a2744;
  --c-gold:       #c9a84c;
  --c-gold-light: #d4b85c;
  --c-gold-dim:   rgba(201,168,76,0.25);
  --c-fg:         #e8e4dc;
  --c-fg2:        #c8c2b6;
  --c-fg3:        #9e978a;
  --c-border:     rgba(201,168,76,0.3);
  --c-bg:         #0a1628;
  --c-bg-alt:     rgba(26,39,68,0.6);
  --c-bg-card:    rgba(19,32,64,0.85);
  --font-heading: "Playfair Display", "Georgia", serif;
  --font-body:    "Source Serif 4", "Georgia", serif;
}

/* ── base ── */
.reveal, .reveal p, .reveal li, .reveal span {
  font-family: var(--font-body) !important;
}
.reveal h1, .reveal h2, .reveal h3, .reveal h4, .reveal h5, .reveal h6 {
  font-family: var(--font-heading) !important;
}
.reveal {
  font-size: 24px !important;
  color: var(--c-fg) !important;
  background: linear-gradient(160deg, var(--c-navy) 0%, var(--c-navy-mid) 40%, var(--c-navy-light) 100%) !important;
}
.reveal .slides {
  text-align: left !important;
}
.reveal .slides section {
  padding: 0 !important;
}
.slide-content {
  padding: 34px 52px 40px 52px;
  box-sizing: border-box;
  overflow-y: auto;
  max-height: 100%;
}

/* custom scrollbar */
.slide-content::-webkit-scrollbar { width: 5px; }
.slide-content::-webkit-scrollbar-track { background: transparent; }
.slide-content::-webkit-scrollbar-thumb { background: var(--c-gold-dim); border-radius: 3px; }

/* ── headings ── */
.reveal h1 {
  font-size: 1.4em !important;
  font-weight: 700 !important;
  color: var(--c-gold) !important;
  border-bottom: 2px solid var(--c-gold);
  padding-bottom: 8px;
  margin: 0 0 20px 0 !important;
  letter-spacing: 0.01em;
  text-shadow: 0 1px 6px rgba(201,168,76,0.15);
}
.reveal h2 {
  font-size: 1.1em !important;
  font-weight: 600 !important;
  color: var(--c-fg) !important;
  margin: 0 0 12px 0 !important;
}
.reveal h3 {
  font-size: 0.92em !important;
  font-weight: 600 !important;
  color: var(--c-fg2) !important;
  margin: 0 0 10px 0 !important;
}
.reveal h1 i, .reveal h2 i {
  color: var(--c-gold-light) !important;
  margin-right: 14px;
  filter: drop-shadow(0 0 4px rgba(201,168,76,0.3));
}

/* ── text ── */
.reveal p {
  font-size: 0.84em !important;
  line-height: 1.72 !important;
  color: var(--c-fg2) !important;
  margin: 0 0 10px 0 !important;
}
.reveal li {
  font-size: 0.84em !important;
  line-height: 1.65 !important;
  color: var(--c-fg2) !important;
  margin-bottom: 5px !important;
}
.reveal ul, .reveal ol {
  margin: 0 0 10px 1.3em !important;
  padding: 0 !important;
}
.reveal ul li::marker {
  color: var(--c-gold) !important;
}
.reveal strong {
  color: var(--c-gold-light) !important;
  font-weight: 700 !important;
}
.reveal em {
  color: var(--c-fg3) !important;
  font-style: italic !important;
}

/* ── tables ── */
.reveal table {
  width: 100% !important;
  border-collapse: collapse !important;
  font-size: 0.74em !important;
  margin: 10px 0 !important;
  line-height: 1.5;
  border-radius: 6px;
  overflow: hidden;
}
.reveal table thead th {
  background: var(--c-navy-mid) !important;
  color: var(--c-gold) !important;
  font-weight: 600 !important;
  padding: 8px 10px !important;
  text-align: center !important;
  border: 1px solid var(--c-border) !important;
  border-bottom: 2px solid var(--c-gold) !important;
  font-family: var(--font-heading) !important;
  letter-spacing: 0.03em;
  text-transform: none;
}
.reveal table tbody td {
  padding: 6px 10px !important;
  border: 1px solid rgba(201,168,76,0.15) !important;
  text-align: center !important;
  color: var(--c-fg2) !important;
  transition: background 0.25s ease;
}
.reveal table tbody tr:nth-child(even) {
  background: var(--c-bg-alt) !important;
}
.reveal table tbody tr:hover {
  background: rgba(201,168,76,0.08) !important;
}
.reveal table tbody td:first-child {
  text-align: left !important;
  font-weight: 600 !important;
  color: var(--c-fg) !important;
}

/* ── code / pre ── */
.reveal pre {
  background: rgba(10,22,40,0.8) !important;
  border: 1px solid var(--c-border) !important;
  border-left: 3px solid var(--c-gold) !important;
  border-radius: 6px !important;
  padding: 12px 16px !important;
  font-size: 0.74em !important;
  box-shadow: 0 2px 12px rgba(0,0,0,0.3) !important;
  color: var(--c-fg) !important;
}
.reveal code {
  font-family: "JetBrains Mono", "Fira Code", "Consolas", monospace !important;
  color: var(--c-gold-light) !important;
}

/* ── blockquotes ── */
.reveal blockquote {
  background: var(--c-bg-card) !important;
  border-left: 3px solid var(--c-gold) !important;
  padding: 10px 16px !important;
  margin: 12px 0 !important;
  font-size: 0.8em !important;
  font-style: italic !important;
  border-radius: 0 6px 6px 0;
  color: var(--c-fg2) !important;
  box-shadow: 0 2px 8px rgba(0,0,0,0.2);
}
.reveal blockquote p {
  color: var(--c-fg2) !important;
  margin: 0 !important;
}

/* ── images ── */
.reveal img {
  max-width: 100% !important;
  height: auto !important;
  border-radius: 6px;
}
.reveal img.chart {
  display: block;
  margin: 10px auto;
  max-height: 62vh;
  border: 1px solid var(--c-border);
  box-shadow: 0 4px 20px rgba(0,0,0,0.4);
}

/* ── hr ── */
.reveal hr {
  border: none !important;
  border-top: 1px solid var(--c-border) !important;
  margin: 14px 0 !important;
}

/* ── slide number ── */
.reveal .slide-number {
  font-family: var(--font-body) !important;
  font-size: 12px !important;
  color: var(--c-gold) !important;
  background: rgba(10,22,40,0.6) !important;
  padding: 4px 10px !important;
  border-radius: 12px 0 0 0 !important;
  right: 0 !important;
  bottom: 0 !important;
  letter-spacing: 0.05em;
}

/* ── progress bar ── */
.reveal .progress {
  height: 3px !important;
  color: var(--c-gold) !important;
}
.reveal .progress span {
  background: linear-gradient(90deg, var(--c-gold), var(--c-gold-light)) !important;
}

/* ── controls ── */
.reveal .controls {
  color: var(--c-gold) !important;
}
.reveal .controls button {
  opacity: 0.5;
  transition: opacity 0.3s ease;
}
.reveal .controls button:hover {
  opacity: 1;
}

/* ── flex card panels ── */
.card-panel {
  background: var(--c-bg-card) !important;
  border: 1px solid var(--c-border) !important;
  border-radius: 10px !important;
  padding: 22px !important;
  flex: 1;
  text-align: center;
  transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease;
  box-shadow: 0 4px 16px rgba(0,0,0,0.3);
}
.card-panel:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 28px rgba(0,0,0,0.4);
  border-color: var(--c-gold) !important;
}
.card-panel h3 {
  color: var(--c-gold) !important;
  font-family: var(--font-heading) !important;
  margin-bottom: 12px !important;
}
/* fallback for inline-styled cards */
.reveal section div[style*="display:flex"] > div {
  background: var(--c-bg-card) !important;
  border: 1px solid var(--c-border) !important;
  border-radius: 10px !important;
  box-shadow: 0 4px 16px rgba(0,0,0,0.3);
  transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease;
}
.reveal section div[style*="display:flex"] > div:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 28px rgba(0,0,0,0.4);
  border-color: var(--c-gold) !important;
}
.reveal section div[style*="display:flex"] > div h3 {
  color: var(--c-gold) !important;
}

/* ── cover slide ── */
.reveal section:first-child .slide-content {
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  text-align: center;
  min-height: 100%;
  background: radial-gradient(ellipse at center, rgba(26,39,68,0.4) 0%, transparent 70%);
}

/* ── decorative ornaments ── */
.ornament {
  display: block;
  width: 120px;
  height: 2px;
  background: linear-gradient(90deg, transparent, var(--c-gold), transparent);
  margin: 16px auto;
}
.ornament-thin {
  display: block;
  width: 80px;
  height: 1px;
  background: linear-gradient(90deg, transparent, var(--c-gold-dim), transparent);
  margin: 10px auto;
}

/* ── animations ── */
@keyframes fadeInUp {
  from { opacity: 0; transform: translateY(20px); }
  to   { opacity: 1; transform: translateY(0); }
}
@keyframes fadeInLeft {
  from { opacity: 0; transform: translateX(-20px); }
  to   { opacity: 1; transform: translateX(0); }
}
@keyframes shimmer {
  0%   { background-position: -200px 0; }
  100% { background-position: 200px 0; }
}
.reveal .slides section.present .slide-content > * {
  animation: fadeInUp 0.6s ease-out both;
}
.reveal .slides section.present .slide-content > *:nth-child(1) { animation-delay: 0.05s; }
.reveal .slides section.present .slide-content > *:nth-child(2) { animation-delay: 0.12s; }
.reveal .slides section.present .slide-content > *:nth-child(3) { animation-delay: 0.19s; }
.reveal .slides section.present .slide-content > *:nth-child(4) { animation-delay: 0.26s; }
.reveal .slides section.present .slide-content > *:nth-child(5) { animation-delay: 0.33s; }
.reveal .slides section.present .slide-content > *:nth-child(6) { animation-delay: 0.40s; }
.reveal .slides section.present .slide-content > *:nth-child(7) { animation-delay: 0.47s; }
.reveal .slides section.present .slide-content > *:nth-child(8) { animation-delay: 0.54s; }

/* ── print adjustments ── */
@media print {
  .reveal { background: #fff !important; }
  .reveal h1 { color: #0a1628 !important; border-color: #0a1628 !important; }
  .reveal p, .reveal li, .reveal td { color: #333 !important; }
  .reveal table thead th { background: #0a1628 !important; color: #fff !important; }
}
</style>"""

old_style = re.search(r'<style>.*?</style>', html, re.DOTALL)
if old_style:
    html = html[:old_style.start()] + NEW_CSS + html[old_style.end():]
    print("✅ CSS theme replaced successfully")
else:
    print("❌ Could not find <style> block")

# ────────────────────────────────────────────
# 3. Update cover slide styling
# ────────────────────────────────────────────
# Replace inline colors in cover slide
html = html.replace(
    'style="font-size:0.9em; color:#555; margin:0; letter-spacing:0.05em;"',
    'style="font-size:0.9em; color:var(--c-fg2); margin:0; letter-spacing:0.08em; font-family:var(--font-heading);"'
)
html = html.replace(
    'style="font-size:0.78em; color:#777; margin:0 0 20px 0; letter-spacing:0.08em;"',
    'style="font-size:0.78em; color:var(--c-fg3); margin:0 0 20px 0; letter-spacing:0.1em;"'
)
html = html.replace(
    'style="border:none; border-top:2px solid #003882; margin:12px auto; width:60%;"',
    'class="ornament" style="border:none; width:140px; height:2px; background:linear-gradient(90deg, transparent, var(--c-gold), transparent); margin:14px auto;"'
)
html = html.replace(
    'style="font-size:1.35em; color:#003882; margin: 14px 0 8px 0; line-height:1.4;"',
    'style="font-size:1.4em; color:var(--c-gold); margin:14px 0 8px 0; line-height:1.4; font-family:var(--font-heading); text-shadow:0 1px 8px rgba(201,168,76,0.2);"'
)
html = html.replace(
    'style="font-size:1.05em; font-weight:600; color:#222; margin:0 0 6px 0; line-height:1.5;"',
    'style="font-size:1.05em; font-weight:600; color:var(--c-fg); margin:0 0 6px 0; line-height:1.5; font-family:var(--font-heading);"'
)
html = html.replace(
    'style="font-size:0.9em; font-weight:400; color:#444; margin:0 0 20px 0; line-height:1.5;"',
    'style="font-size:0.9em; font-weight:400; color:var(--c-fg3); margin:0 0 20px 0; line-height:1.5;"'
)
html = html.replace(
    'style="border:none; border-top:1px solid #ccc; margin:12px auto; width:50%;"',
    'class="ornament-thin" style="border:none; width:100px; height:1px; background:linear-gradient(90deg, transparent, rgba(201,168,76,0.4), transparent); margin:12px auto;"'
)
html = html.replace(
    'style="font-size:0.82em; color:#555; margin:8px 0 2px 0;"',
    'style="font-size:0.82em; color:var(--c-fg2); margin:8px 0 2px 0;"'
)
html = html.replace(
    'style="font-size:0.85em; font-weight:700; color:#003882; margin:4px 0 2px 0;"',
    'style="font-size:0.88em; font-weight:700; color:var(--c-gold); margin:4px 0 2px 0; font-family:var(--font-heading); letter-spacing:0.06em;"'
)
html = html.replace(
    'style="font-size:0.78em; color:#666; margin:0 0 4px 0;"',
    'style="font-size:0.78em; color:var(--c-fg3); margin:0 0 4px 0;"'
)
html = html.replace(
    'style="font-size:0.78em; color:#666; margin:0;"',
    'style="font-size:0.78em; color:var(--c-fg3); margin:0;"'
)

# Also fix the font-family on the cover wrapper
html = html.replace(
    "font-family: 'Georgia', serif;",
    "font-family: var(--font-heading);"
)

# ────────────────────────────────────────────
# 4. Update solution cards (inline backgrounds → dark theme)
# ────────────────────────────────────────────
html = html.replace(
    'style="background:#e3f2fd; border-radius:12px; padding:20px; flex:1; text-align:center;"',
    'style="background:var(--c-bg-card); border:1px solid var(--c-border); border-radius:10px; padding:22px; flex:1; text-align:center; box-shadow:0 4px 16px rgba(0,0,0,0.3); transition:transform 0.3s ease, box-shadow 0.3s ease;"'
)
html = html.replace(
    'style="background:#e8f5e9; border-radius:12px; padding:20px; flex:1; text-align:center;"',
    'style="background:var(--c-bg-card); border:1px solid var(--c-border); border-radius:10px; padding:22px; flex:1; text-align:center; box-shadow:0 4px 16px rgba(0,0,0,0.3); transition:transform 0.3s ease, box-shadow 0.3s ease;"'
)
html = html.replace(
    'style="background:#fff3e0; border-radius:12px; padding:20px; flex:1; text-align:center;"',
    'style="background:var(--c-bg-card); border:1px solid var(--c-border); border-radius:10px; padding:22px; flex:1; text-align:center; box-shadow:0 4px 16px rgba(0,0,0,0.3); transition:transform 0.3s ease, box-shadow 0.3s ease;"'
)

# ────────────────────────────────────────────
# 5. Update conclusion slide center padding
# ────────────────────────────────────────────
html = html.replace(
    'style="text-align:center; padding: 60px 20px;"',
    'style="text-align:center; padding: 50px 24px;"'
)

# ────────────────────────────────────────────
# 6. Update Reveal.js config
# ────────────────────────────────────────────
OLD_CONFIG = """Reveal.initialize({
  hash: true,
  slideNumber: 'c/t',
  progress: true,
  center: false,
  controls: true,
  controlsTutorial: false,
  transition: 'slide',
  backgroundTransition: 'slide',
  transitionSpeed: 'default',
  width: 1200,
  height: 700,
  margin: 0.06,
  minScale: 0.5,
  maxScale: 1.5,
});"""

NEW_CONFIG = """Reveal.initialize({
  hash: true,
  slideNumber: 'c/t',
  progress: true,
  center: false,
  controls: true,
  controlsTutorial: false,
  transition: 'fade',
  backgroundTransition: 'fade',
  transitionSpeed: 'slow',
  width: 1200,
  height: 700,
  margin: 0.06,
  minScale: 0.5,
  maxScale: 1.5,
  autoAnimateEasing: 'ease-in-out',
  autoAnimateDuration: 0.8,
});"""

html = html.replace(OLD_CONFIG, NEW_CONFIG)
print("✅ Reveal.js config updated")

# ────────────────────────────────────────────
# Write output
# ────────────────────────────────────────────
with open(OUTPUT, "w", encoding="utf-8") as f:
    f.write(html)

print(f"✅ File saved to {OUTPUT}")
print(f"   File size: {len(html):,} bytes")
