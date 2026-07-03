#!/usr/bin/env python3
"""
Konversi presentation_hasil.ipynb → standalone reveal.js HTML.
- Font: Lora (serif) untuk semua teks, termasuk heading.
- Dominan hitam-putih
- Chart matplotlib di-extract dari output cell
- Menambahkan FontAwesome dan icon mapping untuk headings.
- Transition menggunakan 'slide'.
- Single-file, no dependencies
"""

import json, re, base64, textwrap
from pathlib import Path

nb_path = Path("presentation_hasil.ipynb")
out_path = Path("presentasi_hasil.html")

with open(nb_path) as f:
    nb = json.load(f)

# ── icon mapping ──────────────────────────────────────────────────────────────
def get_icon_for_heading(text: str) -> str:
    text_lower = text.lower()
    if "peta temuan" in text_lower:
        return '<i class="fa-solid fa-map-location-dot"></i> '
    if "masalah" in text_lower or "penting" in text_lower:
        return '<i class="fa-solid fa-triangle-exclamation"></i> '
    if "solusi" in text_lower:
        return '<i class="fa-solid fa-lightbulb"></i> '
    if "produk" in text_lower:
        return '<i class="fa-solid fa-laptop-code"></i> '
    if "analisis" in text_lower and "karakteristik" in text_lower:
        return '<i class="fa-solid fa-users-viewfinder"></i> '
    if "validasi" in text_lower:
        return '<i class="fa-solid fa-check-double"></i> '
    if "uji coba" in text_lower:
        return '<i class="fa-solid fa-flask-vial"></i> '
    if "uji lapangan" in text_lower:
        return '<i class="fa-solid fa-vial-circle-check"></i> '
    if "statistik" in text_lower:
        return '<i class="fa-solid fa-chart-bar"></i> '
    if "distribusi" in text_lower:
        return '<i class="fa-solid fa-chart-pie"></i> '
    if "inferensial" in text_lower:
        return '<i class="fa-solid fa-chart-line"></i> '
    if "metakognitif" in text_lower:
        return '<i class="fa-solid fa-brain"></i> '
    if "kualitatif" in text_lower or "perspektif" in text_lower:
        return '<i class="fa-solid fa-comments"></i> '
    if "revisi" in text_lower:
        return '<i class="fa-solid fa-pen-ruler"></i> '
    if "temuan" in text_lower:
        return '<i class="fa-solid fa-magnifying-glass-chart"></i> '
    if "implikasi" in text_lower:
        return '<i class="fa-solid fa-arrow-trend-up"></i> '
    if "simpulan" in text_lower:
        return '<i class="fa-solid fa-flag-checkered"></i> '
    if "alur" in text_lower:
        return '<i class="fa-solid fa-diagram-project"></i> '
    if "perbedaan" in text_lower or "aspek" in text_lower:
        return '<i class="fa-solid fa-layer-group"></i> '
    return ''

# ── helpers ───────────────────────────────────────────────────────────────────
def md_to_html(src: str) -> str:
    """Minimal markdown→HTML. Handles headers, bold, italic, tables, lists,
       blockquotes, code blocks, images, <div> passthrough, hr."""
    lines = src.split("\n")
    out = []
    in_table = False
    in_code = False
    in_list = False
    in_blockquote = False
    thead_done = False

    def inline(t):
        t = re.sub(r'!\[([^\]]*)\]\(([^)]+)\)', r'<img src="\2" alt="\1"/>', t)
        t = re.sub(r'\[([^\]]+)\]\(([^)]+)\)', r'<a href="\2">\1</a>', t)
        t = re.sub(r'\*\*\*(.+?)\*\*\*', r'<strong><em>\1</em></strong>', t)
        t = re.sub(r'\*\*(.+?)\*\*', r'<strong>\1</strong>', t)
        t = re.sub(r'\*(.+?)\*', r'<em>\1</em>', t)
        t = re.sub(r'`([^`]+)`', r'<code>\1</code>', t)
        return t

    def close_list():
        nonlocal in_list
        if in_list:
            out.append("</ul>")
            in_list = False

    def close_blockquote():
        nonlocal in_blockquote
        if in_blockquote:
            out.append("</blockquote>")
            in_blockquote = False

    def close_table():
        nonlocal in_table, thead_done
        if in_table:
            out.append("</tbody></table>")
            in_table = False
            thead_done = False

    for line in lines:
        stripped = line.strip()

        if stripped.startswith("```"):
            if in_code:
                out.append("</code></pre>")
                in_code = False
            else:
                close_list(); close_blockquote(); close_table()
                lang = stripped[3:].strip()
                out.append(f'<pre><code class="{lang}">')
                in_code = True
            continue
        if in_code:
            import html as html_mod
            out.append(html_mod.escape(line))
            continue

        if not stripped:
            close_list()
            close_blockquote()
            continue

        if stripped.startswith("<") and not stripped.startswith("<img"):
            close_list(); close_blockquote(); close_table()
            out.append(line)
            continue

        m = re.match(r'^(#{1,6})\s+(.+)$', stripped)
        if m:
            close_list(); close_blockquote(); close_table()
            lvl = len(m.group(1))
            heading_text = inline(m.group(2))
            icon = get_icon_for_heading(m.group(2))
            out.append(f'<h{lvl}>{icon}{heading_text}</h{lvl}>')
            continue

        if re.match(r'^---+$', stripped):
            close_list(); close_blockquote(); close_table()
            out.append("<hr/>")
            continue

        if "|" in stripped and stripped.startswith("|"):
            close_list(); close_blockquote()
            cells = [c.strip() for c in stripped.split("|")[1:-1]]
            if all(re.match(r'^:?-+:?$', c) for c in cells):
                if in_table and not thead_done:
                    out.append("</thead><tbody>")
                    thead_done = True
                continue
            if not in_table:
                out.append('<table><thead>')
                in_table = True
                thead_done = False
            tag = "th" if not thead_done else "td"
            row = "".join(f"<{tag}>{inline(c)}</{tag}>" for c in cells)
            out.append(f"<tr>{row}</tr>")
            continue
        else:
            close_table()

        if stripped.startswith(">"):
            close_list()
            if not in_blockquote:
                out.append("<blockquote>")
                in_blockquote = True
            text = stripped.lstrip("> ").strip()
            out.append(f"<p>{inline(text)}</p>")
            continue
        else:
            close_blockquote()

        if re.match(r'^[-*]\s', stripped):
            if not in_list:
                out.append("<ul>")
                in_list = True
            text = re.sub(r'^[-*]\s+', '', stripped)
            out.append(f"<li>{inline(text)}</li>")
            continue

        m2 = re.match(r'^(\d+)\.\s+(.+)$', stripped)
        if m2:
            if not in_list:
                out.append("<ol>")
                in_list = True
            out.append(f"<li>{inline(m2.group(2))}</li>")
            continue

        close_list()
        out.append(f"<p>{inline(stripped)}</p>")

    close_list(); close_blockquote(); close_table()
    if in_code:
        out.append("</code></pre>")

    return "\n".join(out)


def extract_output_images(cell):
    imgs = []
    for o in cell.get("outputs", []):
        data = o.get("data", {})
        if "image/png" in data:
            b64 = data["image/png"]
            if isinstance(b64, list):
                b64 = "".join(b64)
            b64 = b64.strip().replace("\n", "")
            imgs.append(f'<img src="data:image/png;base64,{b64}" class="chart"/>')
    return "\n".join(imgs)


# ── build slides ──────────────────────────────────────────────────────────────
slides_html2 = []
for cell in nb["cells"]:
    slide_type = cell.get("metadata", {}).get("slideshow", {}).get("slide_type", "")
    if slide_type == "skip":
        continue

    ctype = cell.get("cell_type", "")
    source = "".join(cell.get("source", []))

    content = ""
    if ctype == "markdown":
        content = md_to_html(source)
    elif ctype == "code":
        content = extract_output_images(cell)

    if not content.strip():
        continue

    if slide_type == "slide":
        slides_html2.append(("slide", content))
    elif slide_type == "subslide":
        slides_html2.append(("subslide", content))

grouped = []
for stype, content in slides_html2:
    if stype == "slide":
        grouped.append([content])
    elif stype == "subslide" and grouped:
        grouped[-1].append(content)
    else:
        grouped.append([content])

sections = []
for group in grouped:
    if len(group) == 1:
        sections.append(f'<section>\n<div class="slide-content">\n{group[0]}\n</div>\n</section>')
    else:
        inner = "\n".join(
            f'<section>\n<div class="slide-content">\n{c}\n</div>\n</section>'
            for c in group
        )
        sections.append(f'<section>\n{inner}\n</section>')

all_slides = "\n\n".join(sections)

# ── HTML template ─────────────────────────────────────────────────────────────
html = f'''<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Temuan dan Pembahasan — Disertasi BAB IV</title>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/reveal.js@5.1.0/dist/reveal.css"/>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/reveal.js@5.1.0/dist/theme/white.css"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
<link rel="preconnect" href="https://fonts.googleapis.com"/>
<link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400;0,500;0,600;0,700;1,400;1,600&display=swap" rel="stylesheet"/>

<style>
/* ═══════════════════════════════════════════════════════ */
/*  THEME: Black & White, Lora, Academic Dissertation     */
/* ═══════════════════════════════════════════════════════ */

:root {{
  --c-fg:       #111;
  --c-fg2:      #333;
  --c-fg3:      #555;
  --c-border:   #bbb;
  --c-bg:       #fff;
  --c-bg-alt:   #f5f5f5;
  --c-accent:   #111;
  --font-main:  "Lora", "Georgia", serif;
}}

/* ── base ── */
.reveal, .reveal h1, .reveal h2, .reveal h3, .reveal h4, .reveal h5, .reveal h6 {{
  font-family: var(--font-main) !important;
}}
.reveal {{
  font-size: 24px !important;
  color: var(--c-fg) !important;
  background: var(--c-bg) !important;
}}
.reveal .slides {{
  text-align: left !important;
}}
.reveal .slides section {{
  padding: 0 !important;
}}
.slide-content {{
  padding: 30px 48px 36px 48px;
  box-sizing: border-box;
  overflow-y: auto;
  max-height: 100%;
}}

/* ── headings ── */
.reveal h1 {{
  font-size: 1.35em !important;
  font-weight: 700 !important;
  color: var(--c-fg) !important;
  border-bottom: 2px solid var(--c-fg);
  padding-bottom: 6px;
  margin: 0 0 16px 0 !important;
  letter-spacing: -0.01em;
}}
.reveal h2 {{
  font-size: 1.05em !important;
  font-weight: 600 !important;
  color: var(--c-fg) !important;
  margin: 0 0 10px 0 !important;
}}
.reveal h3 {{
  font-size: 0.9em !important;
  font-weight: 600 !important;
  color: var(--c-fg2) !important;
  margin: 0 0 8px 0 !important;
}}
.reveal h1 i, .reveal h2 i {{
  color: #444 !important;
  margin-right: 12px;
}}

/* ── text ── */
.reveal p {{
  font-size: 0.82em !important;
  line-height: 1.65 !important;
  color: var(--c-fg2) !important;
  margin: 0 0 8px 0 !important;
}}
.reveal li {{
  font-size: 0.82em !important;
  line-height: 1.6 !important;
  color: var(--c-fg2) !important;
  margin-bottom: 3px !important;
}}
.reveal ul, .reveal ol {{
  margin: 0 0 8px 1.3em !important;
  padding: 0 !important;
}}
.reveal strong {{
  color: var(--c-fg) !important;
  font-weight: 700 !important;
}}
.reveal em {{
  color: var(--c-fg3) !important;
}}

/* ── tables ── */
.reveal table {{
  width: 100% !important;
  border-collapse: collapse !important;
  font-size: 0.72em !important;
  margin: 8px 0 !important;
  line-height: 1.45;
}}
.reveal table thead th {{
  background: var(--c-fg) !important;
  color: #fff !important;
  font-weight: 600 !important;
  padding: 6px 8px !important;
  text-align: center !important;
  border: 1px solid var(--c-fg) !important;
}}
.reveal table tbody td {{
  padding: 5px 8px !important;
  border: 1px solid var(--c-border) !important;
  text-align: center !important;
}}
.reveal table tbody tr:nth-child(even) {{
  background: var(--c-bg-alt) !important;
}}
.reveal table tbody td:first-child {{
  text-align: left !important;
  font-weight: 600 !important;
}}

/* ── code ── */
.reveal pre {{
  background: var(--c-bg-alt) !important;
  border: 1px solid #ddd !important;
  border-radius: 4px !important;
  padding: 10px 14px !important;
  font-size: 0.72em !important;
  box-shadow: none !important;
  color: var(--c-fg2) !important;
}}
.reveal code {{
  font-family: "Consolas", "Monaco", monospace !important;
}}

/* ── blockquotes ── */
.reveal blockquote {{
  background: var(--c-bg-alt) !important;
  border-left: 3px solid var(--c-fg) !important;
  padding: 8px 14px !important;
  margin: 10px 0 !important;
  font-size: 0.78em !important;
  font-style: normal !important;
  border-radius: 0 3px 3px 0;
  color: var(--c-fg2) !important;
}}
.reveal blockquote p {{
  color: var(--c-fg2) !important;
  margin: 0 !important;
}}

/* ── images ── */
.reveal img {{
  max-width: 100% !important;
  height: auto !important;
}}
.reveal img.chart {{
  display: block;
  margin: 8px auto;
  max-height: 62vh;
}}

/* ── hr ── */
.reveal hr {{
  border: none !important;
  border-top: 1px solid var(--c-border) !important;
  margin: 12px 0 !important;
}}

/* ── slide number ── */
.reveal .slide-number {{
  font-family: var(--font-main) !important;
  font-size: 13px !important;
  color: var(--c-fg3) !important;
  background: transparent !important;
  right: 20px !important;
  bottom: 14px !important;
}}

/* ── progress bar ── */
.reveal .progress {{
  height: 3px !important;
  color: var(--c-fg) !important;
}}
.reveal .progress span {{
  background: var(--c-fg) !important;
}}

/* ── controls ── */
.reveal .controls {{
  color: var(--c-fg3) !important;
}}

/* ── flex card panels (solution slide) ── */
.reveal section div[style*="display:flex"] > div {{
  border: 1px solid var(--c-border) !important;
  border-radius: 6px !important;
  background: var(--c-bg-alt) !important;
}}

/* ── cover slide ── */
.reveal section:first-child .slide-content {{
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  text-align: center;
  min-height: 100%;
}}
</style>
</head>

<body>
<div class="reveal">
<div class="slides">

{all_slides}

</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/reveal.js@5.1.0/dist/reveal.js"></script>
<script>
Reveal.initialize({{
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
}});
</script>
</body>
</html>
'''

with open(out_path, "w", encoding="utf-8") as f:
    f.write(html)

print(f"✅ Generated: {{out_path}}")
print(f"   Slides: {{len(grouped)}}")
print(f"   Size: {{out_path.stat().st_size / 1024:.0f}} KB")
print(f"\\n   Buka di browser: file://{{out_path.resolve()}}")
