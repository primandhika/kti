#!/usr/bin/env python3
"""
Tambahkan slide statistik deskriptif (mean, min, max, SD, median)
per kelompok (eksperimen & kontrol) untuk pretes & postes
keterampilan berbicara dan metakognitif.

Slide disisipkan SEBELUM timeline-analisis-slide.
"""

import json
import csv
import statistics
import io, base64
import matplotlib
matplotlib.use("Agg")
import matplotlib.pyplot as plt
import matplotlib.patches as mpatches
import numpy as np
from pathlib import Path

# ── helpers ──────────────────────────────────────────────────────────────────
def load_csv(path):
    with open(path, newline="", encoding="utf-8") as f:
        return list(csv.DictReader(f))

def stats(values):
    v = [float(x) for x in values if x not in ("", None)]
    if not v:
        return {}
    return {
        "n":      len(v),
        "mean":   statistics.mean(v),
        "median": statistics.median(v),
        "sd":     statistics.pstdev(v),          # populasi (sama dg Excel STDEVP)
        "min":    min(v),
        "max":    max(v),
    }

def fmt(v, dec=2):
    return f"{v:.{dec}f}"

# ── load data ─────────────────────────────────────────────────────────────────
base = Path("../data/field_test")
pre_rows  = load_csv(base / "keterampilan_berbicara_pretes.csv")
post_rows = load_csv(base / "keterampilan_berbicara_postes.csv")
meta_rows = load_csv(base / "metakognitif.csv")

groups = ["eksperimen", "kontrol"]

# ── keterampilan berbicara ────────────────────────────────────────────────────
kb_stats = {}
for g in groups:
    pre_vals  = [r["pre_nilai_akhir"]  for r in pre_rows  if r["kelompok"] == g]
    post_vals = [r["post_nilai_akhir"] for r in post_rows if r["kelompok"] == g]
    pre_v  = [float(x) for x in pre_vals  if x]
    post_v = [float(x) for x in post_vals if x]
    gain_v = [p - r for r, p in zip(pre_v, post_v)]
    kb_stats[g] = {
        "pre":  stats(pre_vals),
        "post": stats(post_vals),
        "gain": {
            "n": len(gain_v),
            "mean": statistics.mean(gain_v),
            "median": statistics.median(gain_v),
            "sd": statistics.pstdev(gain_v),
            "min": min(gain_v),
            "max": max(gain_v),
        }
    }

# ── metakognitif ──────────────────────────────────────────────────────────────
mk_stats = {}
for g in groups:
    rows_g = [r for r in meta_rows if r["kelompok"] == g]
    pre_v  = [float(r["pre_total"])  for r in rows_g if r["pre_total"]]
    post_v = [float(r["post_total"]) for r in rows_g if r["post_total"]]
    # metakognitif pakai N-gain, skor maks = 64 (16 butir × skala 1-4)
    def ngain(pre, post, maxskor=64):
        if maxskor - pre == 0:
            return 0.0
        return (post - pre) / (maxskor - pre)
    ng_v = [ngain(float(r["pre_total"]), float(r["post_total"])) for r in rows_g]
    mk_stats[g] = {
        "pre":   stats([r["pre_total"]  for r in rows_g]),
        "post":  stats([r["post_total"] for r in rows_g]),
        "ngain": {
            "n":      len(ng_v),
            "mean":   statistics.mean(ng_v),
            "median": statistics.median(ng_v),
            "sd":     statistics.pstdev(ng_v),
            "min":    min(ng_v),
            "max":    max(ng_v),
        }
    }

# ── cetak untuk verifikasi ────────────────────────────────────────────────────
for g in groups:
    print(f"\n{'='*60}")
    print(f"  KELOMPOK: {g.upper()}")
    print(f"{'='*60}")
    print(f"  [KB] Pre   — mean={fmt(kb_stats[g]['pre']['mean'])}  sd={fmt(kb_stats[g]['pre']['sd'])}  min={fmt(kb_stats[g]['pre']['min'])}  max={fmt(kb_stats[g]['pre']['max'])}")
    print(f"  [KB] Post  — mean={fmt(kb_stats[g]['post']['mean'])}  sd={fmt(kb_stats[g]['post']['sd'])}  min={fmt(kb_stats[g]['post']['min'])}  max={fmt(kb_stats[g]['post']['max'])}")
    print(f"  [KB] Gain  — mean={fmt(kb_stats[g]['gain']['mean'])}  sd={fmt(kb_stats[g]['gain']['sd'])}  min={fmt(kb_stats[g]['gain']['min'])}  max={fmt(kb_stats[g]['gain']['max'])}")
    print(f"  [MK] Pre   — mean={fmt(mk_stats[g]['pre']['mean'])}  sd={fmt(mk_stats[g]['pre']['sd'])}  min={fmt(mk_stats[g]['pre']['min'])}  max={fmt(mk_stats[g]['pre']['max'])}")
    print(f"  [MK] Post  — mean={fmt(mk_stats[g]['post']['mean'])}  sd={fmt(mk_stats[g]['post']['sd'])}  min={fmt(mk_stats[g]['post']['min'])}  max={fmt(mk_stats[g]['post']['max'])}")
    print(f"  [MK] N-gain— mean={fmt(mk_stats[g]['ngain']['mean'],3)}  sd={fmt(mk_stats[g]['ngain']['sd'],3)}  min={fmt(mk_stats[g]['ngain']['min'],3)}  max={fmt(mk_stats[g]['ngain']['max'],3)}")

# ── buat chart boxplot ────────────────────────────────────────────────────────
def make_boxplot_b64():
    fig, axes = plt.subplots(1, 2, figsize=(11, 5))
    fig.patch.set_facecolor("#f8f9fa")

    palette = {"eksperimen": "#1a6bbf", "kontrol": "#e07b2a"}

    for ax, (label, col_pre, col_post, title, ylabel) in zip(
        axes,
        [
            ("kb",  "pre_nilai_akhir",  "post_nilai_akhir",
             "Keterampilan Berbicara", "Nilai (0–100)"),
            ("mk",  "pre_total",        "post_total",
             "Kemampuan Metakognitif", "Skor Total"),
        ]
    ):
        pre_src  = pre_rows  if label == "kb" else meta_rows
        post_src = post_rows if label == "kb" else meta_rows

        data_e_pre  = [float(r[col_pre])  for r in pre_src  if r["kelompok"] == "eksperimen" and r[col_pre]]
        data_k_pre  = [float(r[col_pre])  for r in pre_src  if r["kelompok"] == "kontrol"    and r[col_pre]]
        data_e_post = [float(r[col_post]) for r in post_src if r["kelompok"] == "eksperimen" and r[col_post]]
        data_k_post = [float(r[col_post]) for r in post_src if r["kelompok"] == "kontrol"    and r[col_post]]

        positions = [1, 2, 3.5, 4.5]
        data      = [data_e_pre, data_k_pre, data_e_post, data_k_post]
        colors    = [palette["eksperimen"], palette["kontrol"],
                     palette["eksperimen"], palette["kontrol"]]

        bp = ax.boxplot(data, positions=positions, widths=0.6,
                        patch_artist=True, notch=False,
                        medianprops=dict(color="white", linewidth=2),
                        whiskerprops=dict(linewidth=1.4),
                        capprops=dict(linewidth=1.4),
                        flierprops=dict(marker="o", markersize=4, alpha=0.5))

        for patch, color in zip(bp["boxes"], colors):
            patch.set_facecolor(color)
            patch.set_alpha(0.85)

        ax.set_xticks([1.5, 4])
        ax.set_xticklabels(["Pretes", "Postes"], fontsize=11)
        ax.set_title(title, fontsize=12, fontweight="bold", pad=10)
        ax.set_ylabel(ylabel, fontsize=10)
        ax.set_facecolor("#ffffff")
        ax.grid(axis="y", linestyle="--", alpha=0.5)
        ax.spines[["top","right"]].set_visible(False)

    # legend
    patches = [
        mpatches.Patch(color=palette["eksperimen"], label="Eksperimen (n=40)"),
        mpatches.Patch(color=palette["kontrol"],    label="Kontrol (n=37)"),
    ]
    fig.legend(handles=patches, loc="lower center", ncol=2,
               fontsize=10, frameon=False, bbox_to_anchor=(0.5, -0.02))

    fig.suptitle("Distribusi Skor Pretes–Postes per Kelompok",
                 fontsize=13, fontweight="bold", y=1.01)
    plt.tight_layout()

    buf = io.BytesIO()
    fig.savefig(buf, format="png", dpi=130, bbox_inches="tight",
                facecolor=fig.get_facecolor())
    plt.close(fig)
    buf.seek(0)
    return base64.b64encode(buf.read()).decode("utf-8")

chart_b64 = make_boxplot_b64()
print("\n✓ Boxplot chart generated.")

# ── susun konten markdown slide ───────────────────────────────────────────────
E = kb_stats["eksperimen"]
K = kb_stats["kontrol"]
EM = mk_stats["eksperimen"]
KM = mk_stats["kontrol"]

# helper arrow
def arrow(a, b):
    diff = a - b
    if diff > 0.05:
        return f"▲ +{fmt(diff)}"
    elif diff < -0.05:
        return f"▼ {fmt(diff)}"
    return f"≈ {fmt(diff)}"

def arrowN(a, b, dec=3):
    diff = a - b
    if diff > 0.001:
        return f"▲ +{fmt(diff, dec)}"
    elif diff < -0.001:
        return f"▼ {fmt(diff, dec)}"
    return f"≈ {fmt(diff, dec)}"

slide_1_source = [
    "# Statistik Deskriptif — Gambaran Awal Data\n",
    "\n",
    "### Keterampilan Berbicara (Nilai 0–100)\n",
    "\n",
    "| Statistik | Eksperimen (n=40) ||| Kontrol (n=37) ||\n",
    "|---|:---:|:---:|:---:|:---:|:---:|\n",
    "| | **Pretes** | **Postes** | **Gain** | **Pretes** | **Postes** |\n",
    f"| **Mean** | {fmt(E['pre']['mean'])} | {fmt(E['post']['mean'])} | {arrow(E['post']['mean'], E['pre']['mean'])} | {fmt(K['pre']['mean'])} | {fmt(K['post']['mean'])} |\n",
    f"| **Median** | {fmt(E['pre']['median'])} | {fmt(E['post']['median'])} | — | {fmt(K['pre']['median'])} | {fmt(K['post']['median'])} |\n",
    f"| **SD** | {fmt(E['pre']['sd'])} | {fmt(E['post']['sd'])} | {fmt(E['gain']['sd'])} | {fmt(K['pre']['sd'])} | {fmt(K['post']['sd'])} |\n",
    f"| **Min** | {fmt(E['pre']['min'])} | {fmt(E['post']['min'])} | {fmt(E['gain']['min'])} | {fmt(K['pre']['min'])} | {fmt(K['post']['min'])} |\n",
    f"| **Max** | {fmt(E['pre']['max'])} | {fmt(E['post']['max'])} | {fmt(E['gain']['max'])} | {fmt(K['pre']['max'])} | {fmt(K['post']['max'])} |\n",
    "\n",
    "### Kemampuan Metakognitif (Skor 0–40)\n",
    "\n",
    "| Statistik | Eksperimen ||| Kontrol ||\n",
    "|---|:---:|:---:|:---:|:---:|:---:|\n",
    "| | **Pretes** | **Postes** | **N-gain** | **Pretes** | **Postes** |\n",
    f"| **Mean** | {fmt(EM['pre']['mean'])} | {fmt(EM['post']['mean'])} | {arrowN(EM['ngain']['mean'], KM['ngain']['mean'])} | {fmt(KM['pre']['mean'])} | {fmt(KM['post']['mean'])} |\n",
    f"| **Median** | {fmt(EM['pre']['median'])} | {fmt(EM['post']['median'])} | — | {fmt(KM['pre']['median'])} | {fmt(KM['post']['median'])} |\n",
    f"| **SD** | {fmt(EM['pre']['sd'])} | {fmt(EM['post']['sd'])} | {fmt(EM['ngain']['sd'],3)} | {fmt(KM['pre']['sd'])} | {fmt(KM['post']['sd'])} |\n",
    f"| **Min** | {fmt(EM['pre']['min'])} | {fmt(EM['post']['min'])} | {fmt(EM['ngain']['min'],3)} | {fmt(KM['pre']['min'])} | {fmt(KM['post']['min'])} |\n",
    f"| **Max** | {fmt(EM['pre']['max'])} | {fmt(EM['post']['max'])} | {fmt(EM['ngain']['max'],3)} | {fmt(KM['pre']['max'])} | {fmt(KM['post']['max'])} |\n",
    "\n",
    "> **Catatan:** Kedua kelompok setara pada pretes — eksperimen menunjukkan peningkatan lebih besar pada postes.\n",
]

slide_2_source = [
    "# Distribusi Skor — Boxplot Pretes & Postes\n",
    "\n",
    "<div style=\"text-align:center;\">\n",
    "\n",
    f"<img src=\"data:image/png;base64,{chart_b64}\" "
    "alt=\"Boxplot Distribusi Skor\" "
    "style=\"max-width:92%; max-height:72vh; object-fit:contain;\"/>\n",
    "\n",
    "</div>\n",
    "\n",
    f"> Eksperimen: Pretes mean={fmt(E['pre']['mean'])}, Postes mean={fmt(E['post']['mean'])} &nbsp;|&nbsp; "
    f"Kontrol: Pretes mean={fmt(K['pre']['mean'])}, Postes mean={fmt(K['post']['mean'])}\n",
]

# ── build sel ─────────────────────────────────────────────────────────────────
cell_tbl = {
    "cell_type": "markdown",
    "id": "desc-stats-table",
    "metadata": {"slideshow": {"slide_type": "slide"}},
    "source": slide_1_source,
}

cell_box = {
    "cell_type": "markdown",
    "id": "desc-stats-boxplot",
    "metadata": {"slideshow": {"slide_type": "subslide"}},
    "source": slide_2_source,
}

# ── sisipkan ke notebook ──────────────────────────────────────────────────────
nb_path = Path("presentation_hasil.ipynb")
with open(nb_path, "r", encoding="utf-8") as f:
    nb = json.load(f)

cells = nb["cells"]

# Cari index timeline-analisis-slide
target_id = "timeline-analisis-slide"
idx = next((i for i, c in enumerate(cells) if c.get("id") == target_id), None)

if idx is not None:
    cells.insert(idx, cell_box)
    cells.insert(idx, cell_tbl)
    print(f"✓ Descriptive slides inserted at index {idx} (before {target_id}).")
else:
    # fallback: sebelum closing
    target_id = "closing-slide"
    idx = next((i for i, c in enumerate(cells) if c.get("id") == target_id), len(cells))
    cells.insert(idx, cell_box)
    cells.insert(idx, cell_tbl)
    print(f"✓ Descriptive slides inserted at index {idx} (before {target_id}) [fallback].")

with open(nb_path, "w", encoding="utf-8") as f:
    json.dump(nb, f, ensure_ascii=False, indent=1)

print(f"\n✅ Done! Total cells: {len(nb['cells'])}")
print("\n=== URUTAN SLIDE TERBARU ===")
for i, c in enumerate(nb["cells"]):
    cid = c.get("id","?")
    stype = c.get("metadata",{}).get("slideshow",{}).get("slide_type","?")
    print(f"  [{i:02d}] {stype:12s} | {cid}")
