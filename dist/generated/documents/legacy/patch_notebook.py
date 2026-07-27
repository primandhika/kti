#!/usr/bin/env python3
"""
Script to patch presentation_hasil.ipynb:
1. Improve cover slide with UHAMKA logo, researcher name & NIM
2. Add tl_analisis_data.png slide (as 65% of presentation content)
3. Fix institution name from IKIP Siliwangi to UHAMKA
"""

import json
import base64
from pathlib import Path

# Load the notebook
nb_path = Path("presentation_hasil.ipynb")
with open(nb_path, "r", encoding="utf-8") as f:
    nb = json.load(f)

# Read logo image and encode as base64
logo_path = Path("../img/logo_uhamka_detail.png")
with open(logo_path, "rb") as f:
    logo_b64 = base64.b64encode(f.read()).decode("utf-8")

# Read timeline image and encode as base64
tl_path = Path("../img/tl_analisis_data.png")
with open(tl_path, "rb") as f:
    tl_b64 = base64.b64encode(f.read()).decode("utf-8")

# ─── 1. REPLACE COVER SLIDE ─────────────────────────────────────────────────
new_cover_source = [
    "<div style=\"text-align:center; padding: 30px 20px; font-family: 'Georgia', serif;\">\n",
    "\n",
    f"<img src=\"data:image/png;base64,{logo_b64}\" alt=\"Logo UHAMKA\" style=\"height:90px; margin-bottom:16px;\"/>\n",
    "\n",
    "<p style=\"font-size:0.9em; color:#555; margin:0; letter-spacing:0.05em;\">UNIVERSITAS MUHAMMADIYAH PROF. DR. HAMKA</p>\n",
    "<p style=\"font-size:0.78em; color:#777; margin:0 0 20px 0; letter-spacing:0.08em;\"><em>integrity, trust, compassion</em></p>\n",
    "\n",
    "<hr style=\"border:none; border-top:2px solid #003882; margin:12px auto; width:60%;\"/>\n",
    "\n",
    "<h1 style=\"font-size:1.35em; color:#003882; margin: 14px 0 8px 0; line-height:1.4;\">Temuan dan Pembahasan</h1>\n",
    "\n",
    "<h2 style=\"font-size:1.05em; font-weight:600; color:#222; margin:0 0 6px 0; line-height:1.5;\">Pengembangan Web <em>Microlearning</em> Berbasis Teknik Feynman</h2>\n",
    "<h3 style=\"font-size:0.9em; font-weight:400; color:#444; margin:0 0 20px 0; line-height:1.5;\">untuk Meningkatkan Kemampuan Metakognitif dalam Keterampilan Berbicara Mahasiswa</h3>\n",
    "\n",
    "<hr style=\"border:none; border-top:1px solid #ccc; margin:12px auto; width:50%;\"/>\n",
    "\n",
    "<p style=\"font-size:0.82em; color:#555; margin:8px 0 2px 0;\"><strong>Disertasi — BAB IV</strong></p>\n",
    "<p style=\"font-size:0.85em; font-weight:700; color:#003882; margin:4px 0 2px 0;\">RESTU BIAS PRIMANDHIKA</p>\n",
    "<p style=\"font-size:0.78em; color:#666; margin:0 0 4px 0;\">NIM 2409108009</p>\n",
    "<p style=\"font-size:0.78em; color:#666; margin:0;\">Program Studi Doktor Pendidikan Bahasa Indonesia | Sekolah Pascasarjana UHAMKA</p>\n",
    "\n",
    "</div>"
]

# ─── 2. COVER CELL: REPLACE ─────────────────────────────────────────────────
cells = nb["cells"]

# Find cover cell (id: title-slide)
for cell in cells:
    if cell.get("id") == "title-slide":
        cell["source"] = new_cover_source
        print("✓ Cover slide updated.")
        break

# ─── 3. ADD TIMELINE SLIDE (before closing-slide) ───────────────────────────
# We need an image cell (markdown with embedded image)
timeline_markdown_cell = {
    "cell_type": "markdown",
    "id": "timeline-analisis-slide",
    "metadata": {
        "slideshow": {
            "slide_type": "slide"
        }
    },
    "source": [
        "# Alur Analisis Data Penelitian\n",
        "\n",
        "<div style=\"text-align:center;\">\n",
        "\n",
        f"<img src=\"data:image/png;base64,{tl_b64}\" alt=\"Timeline Analisis Data Penelitian\" style=\"max-width:100%; max-height:80vh; object-fit:contain;\"/>\n",
        "\n",
        "</div>"
    ]
}

# Also add a subslide repeat for the same image (so it takes more "space" in presentation)
timeline_subslide_cell = {
    "cell_type": "markdown",
    "id": "timeline-analisis-subslide",
    "metadata": {
        "slideshow": {
            "slide_type": "subslide"
        }
    },
    "source": [
        "<div style=\"text-align:center; padding:10px 0;\">\n",
        "\n",
        f"<img src=\"data:image/png;base64,{tl_b64}\" alt=\"Timeline Analisis Data - Detail\" style=\"max-width:100%; max-height:82vh; object-fit:contain;\"/>\n",
        "\n",
        "<p style=\"font-size:0.72em; color:#666; margin-top:6px;\"><em>Alur analisis dimulai dari pengecekan kualitas instrumen, bergerak ke deskriptif, uji inferensial, kemudian dijelaskan melalui data kualitatif hingga keputusan akhir.</em></p>\n",
        "\n",
        "</div>"
    ]
}

# Find index of closing-slide
closing_idx = None
for i, cell in enumerate(cells):
    if cell.get("id") == "closing-slide":
        closing_idx = i
        break

if closing_idx is not None:
    cells.insert(closing_idx, timeline_subslide_cell)
    cells.insert(closing_idx, timeline_markdown_cell)
    print(f"✓ Timeline slides inserted before closing slide (index {closing_idx}).")
else:
    # Append at end before metadata
    cells.append(timeline_markdown_cell)
    cells.append(timeline_subslide_cell)
    print("✓ Timeline slides appended at end.")

# ─── 4. FIX FOOTER / RISE METADATA ─────────────────────────────────────────
if "rise" in nb.get("metadata", {}):
    nb["metadata"]["rise"]["footer"] = (
        "<small>Disertasi - BAB IV Temuan dan Pembahasan | Sekolah Pascasarjana UHAMKA</small>"
    )
    print("✓ RISE footer updated.")

# ─── 5. SAVE ────────────────────────────────────────────────────────────────
with open(nb_path, "w", encoding="utf-8") as f:
    json.dump(nb, f, ensure_ascii=False, indent=1)

print("\n✅ Notebook patched successfully:", nb_path)
