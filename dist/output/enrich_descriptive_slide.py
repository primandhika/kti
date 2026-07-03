#!/usr/bin/env python3
"""
Perkaya slide field-descriptive (idx 11) dengan:
- Tambah baris Median, Min, Max ke tabel yang sudah ada
- Tambah subslide boxplot distribusi skor
- Hapus sel desc-stats-table dan desc-stats-boxplot yang disisipkan tadi (tidak perlu)
"""

import json, csv, statistics, io, base64
from pathlib import Path
import matplotlib
matplotlib.use("Agg")
import matplotlib.pyplot as plt
import matplotlib.patches as mpatches

# ── helpers ───────────────────────────────────────────────────────────────────
def load_csv(p):
    with open(p, newline="", encoding="utf-8") as f:
        return list(csv.DictReader(f))

def stats(vals):
    v = [float(x) for x in vals if x not in ("", None)]
    if not v: return {}
    return dict(n=len(v), mean=statistics.mean(v), median=statistics.median(v),
                sd=statistics.pstdev(v), min=min(v), max=max(v))

def fmt(v, d=2): return f"{v:.{d}f}"

# ── load data ─────────────────────────────────────────────────────────────────
base = Path("../data/field_test")
pre_rows  = load_csv(base / "keterampilan_berbicara_pretes.csv")
post_rows = load_csv(base / "keterampilan_berbicara_postes.csv")
meta_rows = load_csv(base / "metakognitif.csv")

groups = ["eksperimen", "kontrol"]

kb = {}
for g in groups:
    pv  = [float(r["pre_nilai_akhir"])  for r in pre_rows  if r["kelompok"]==g and r["pre_nilai_akhir"]]
    qv  = [float(r["post_nilai_akhir"]) for r in post_rows if r["kelompok"]==g and r["post_nilai_akhir"]]
    gv  = [q-p for p,q in zip(pv,qv)]
    ngv = [(q-p)/(100-p) if 100-p>0 else 0 for p,q in zip(pv,qv)]
    kb[g] = dict(
        pre  = dict(n=len(pv), mean=statistics.mean(pv), median=statistics.median(pv), sd=statistics.pstdev(pv), min=min(pv), max=max(pv)),
        post = dict(n=len(qv), mean=statistics.mean(qv), median=statistics.median(qv), sd=statistics.pstdev(qv), min=min(qv), max=max(qv)),
        gain = dict(mean=statistics.mean(gv), sd=statistics.pstdev(gv), min=min(gv), max=max(gv)),
        ngain_mean = statistics.mean(ngv),
        pre_raw=pv, post_raw=qv,
    )

mk = {}
for g in groups:
    rows_g = [r for r in meta_rows if r["kelompok"]==g]
    pv  = [float(r["pre_total"])  for r in rows_g if r["pre_total"]]
    qv  = [float(r["post_total"]) for r in rows_g if r["post_total"]]
    def ngain(p,q,mx=64): return (q-p)/(mx-p) if mx-p!=0 else 0.0
    ngv = [ngain(p,q) for p,q in zip(pv,qv)]
    mk[g] = dict(
        pre  = dict(n=len(pv), mean=statistics.mean(pv), median=statistics.median(pv), sd=statistics.pstdev(pv), min=min(pv), max=max(pv)),
        post = dict(n=len(qv), mean=statistics.mean(qv), median=statistics.median(qv), sd=statistics.pstdev(qv), min=min(qv), max=max(qv)),
        ngain_mean = statistics.mean(ngv),
        pre_raw=pv, post_raw=qv,
    )

E, K = kb["eksperimen"], kb["kontrol"]
EM, KM = mk["eksperimen"], mk["kontrol"]

# ── buat boxplot chart ────────────────────────────────────────────────────────
def make_boxplot():
    fig, axes = plt.subplots(1, 2, figsize=(11, 5))
    fig.patch.set_facecolor("#f8f9fa")
    pal = {"eksperimen": "#1a6bbf", "kontrol": "#e07b2a"}

    datasets = [
        (axes[0], "Keterampilan Berbicara (0–100)",
         E["pre_raw"], K["pre_raw"], E["post_raw"], K["post_raw"]),
        (axes[1], "Kemampuan Metakognitif (16–64)",
         EM["pre_raw"], KM["pre_raw"], EM["post_raw"], KM["post_raw"]),
    ]
    for ax, title, ep, kp, eq, kq in datasets:
        data     = [ep, kp, eq, kq]
        pos      = [1, 2, 3.5, 4.5]
        colors   = [pal["eksperimen"], pal["kontrol"],
                    pal["eksperimen"], pal["kontrol"]]
        bp = ax.boxplot(data, positions=pos, widths=0.62, patch_artist=True,
                        medianprops=dict(color="white", linewidth=2.5),
                        whiskerprops=dict(linewidth=1.4),
                        capprops=dict(linewidth=1.4),
                        flierprops=dict(marker="o", markersize=4, alpha=0.5))
        for patch, color in zip(bp["boxes"], colors):
            patch.set_facecolor(color); patch.set_alpha(0.85)
        ax.set_xticks([1.5, 4]); ax.set_xticklabels(["Pretes","Postes"], fontsize=11)
        ax.set_title(title, fontsize=11, fontweight="bold", pad=8)
        ax.set_facecolor("#ffffff")
        ax.grid(axis="y", linestyle="--", alpha=0.4)
        ax.spines[["top","right"]].set_visible(False)

    patches = [mpatches.Patch(color=pal["eksperimen"], label=f"Eksperimen (n={E['pre']['n']})"),
               mpatches.Patch(color=pal["kontrol"],    label=f"Kontrol (n={K['pre']['n']})")]
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

chart_b64 = make_boxplot()
print("✓ Boxplot generated.")

# ── baru slide content ────────────────────────────────────────────────────────
# Slide 1: Tabel lengkap (menggantikan field-descriptive)
new_field_descriptive = [
    "# Statistik Deskriptif Keterampilan Berbicara\n",
    "\n",
    "**(Tabel 4.17)** — Nilai 0–100\n",
    "\n",
    f"| Statistik | Eksperimen (n={E['pre']['n']}) ||| Kontrol (n={K['pre']['n']}) |||\n",
    "|---|:---:|:---:|:---:|:---:|:---:|:---:|\n",
    "| | **Pretes** | **Postes** | **Gain** | **Pretes** | **Postes** | **Gain** |\n",
    f"| **Mean** | {fmt(E['pre']['mean'])} | {fmt(E['post']['mean'])} | {fmt(E['gain']['mean'])} | {fmt(K['pre']['mean'])} | {fmt(K['post']['mean'])} | {fmt(K['gain']['mean'])} |\n",
    f"| **Median** | {fmt(E['pre']['median'])} | {fmt(E['post']['median'])} | — | {fmt(K['pre']['median'])} | {fmt(K['post']['median'])} | — |\n",
    f"| **SD** | {fmt(E['pre']['sd'])} | {fmt(E['post']['sd'])} | {fmt(E['gain']['sd'])} | {fmt(K['pre']['sd'])} | {fmt(K['post']['sd'])} | {fmt(K['gain']['sd'])} |\n",
    f"| **Min** | {fmt(E['pre']['min'])} | {fmt(E['post']['min'])} | {fmt(E['gain']['min'])} | {fmt(K['pre']['min'])} | {fmt(K['post']['min'])} | {fmt(K['gain']['min'])} |\n",
    f"| **Max** | {fmt(E['pre']['max'])} | {fmt(E['post']['max'])} | {fmt(E['gain']['max'])} | {fmt(K['pre']['max'])} | {fmt(K['post']['max'])} | {fmt(K['gain']['max'])} |\n",
    f"| **N-gain** | — | — | **{fmt(E['ngain_mean'],3)}** | — | — | **{fmt(K['ngain_mean'],3)}** |\n",
    "\n",
    "> Selisih mean gain antarkelompok: **"
    f"{fmt(E['gain']['mean'] - K['gain']['mean'])} poin** — kedua kelompok meningkat, eksperimen lebih besar.\n",
]

# Slide 2 (subslide): boxplot distribusi
boxplot_slide = [
    "## Distribusi Skor — Boxplot Pretes & Postes\n",
    "\n",
    "<div style=\"text-align:center;\">\n",
    f"<img src=\"data:image/png;base64,{chart_b64}\" "
    "alt=\"Boxplot Distribusi\" style=\"max-width:96%; max-height:70vh; object-fit:contain;\"/>\n",
    "</div>\n",
    "\n",
    f"> KB — Eksp: pre {fmt(E['pre']['mean'])} → post {fmt(E['post']['mean'])} "
    f"&nbsp;|&nbsp; "
    f"Kontrol: pre {fmt(K['pre']['mean'])} → post {fmt(K['post']['mean'])}\n",
]

# ── patch notebook ────────────────────────────────────────────────────────────
nb_path = Path("presentation_hasil.ipynb")
with open(nb_path) as f:
    nb = json.load(f)

cells = nb["cells"]

# 1. Hapus sel deskriptif yang disisipkan sebelumnya (tidak diperlukan lagi)
ids_remove = {"desc-stats-table", "desc-stats-boxplot"}
cells[:] = [c for c in cells if c.get("id") not in ids_remove]
print(f"✓ Removed old descriptive cells. Cells now: {len(cells)}")

# 2. Update field-descriptive content
for c in cells:
    if c.get("id") == "field-descriptive":
        c["source"] = new_field_descriptive
        print("✓ field-descriptive slide updated.")
        break

# 3. Insert boxplot subslide right after field-descriptive
fd_idx = next((i for i, c in enumerate(cells) if c.get("id") == "field-descriptive"), None)
if fd_idx is not None:
    # Check if boxplot subslide already right after (avoid duplicate)
    next_id = cells[fd_idx+1].get("id", "") if fd_idx+1 < len(cells) else ""
    if next_id != "desc-boxplot-subslide":
        box_cell = {
            "cell_type": "markdown",
            "id": "desc-boxplot-subslide",
            "metadata": {"slideshow": {"slide_type": "subslide"}},
            "source": boxplot_slide,
        }
        cells.insert(fd_idx + 1, box_cell)
        print(f"✓ Boxplot subslide inserted at index {fd_idx+1}.")
    else:
        # Update existing
        cells[fd_idx+1]["source"] = boxplot_slide
        print("✓ Existing boxplot subslide updated.")

with open(nb_path, "w") as f:
    json.dump(nb, f, ensure_ascii=False, indent=1)

print(f"\n✅ Done! Total cells: {len(nb['cells'])}")
print("\n=== URUTAN SLIDE ===")
for i, c in enumerate(nb["cells"]):
    stype = c.get("metadata",{}).get("slideshow",{}).get("slide_type","?")
    print(f"  [{i:02d}] {stype:12s} | {c.get('id','?')}")
