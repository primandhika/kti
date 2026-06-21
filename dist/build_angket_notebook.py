"""
build_angket_notebook.py
Membangun dua notebook:
  1. data/olahdata_angket_media.ipynb            — QN + QL angket penggunaan media
  2. data/field_test/olahdata_metakognitif.ipynb — pre-post metakognitif (Bab III methodology)
"""
import contextlib, io, json, traceback
from pathlib import Path


def md(text):
    return {"cell_type": "markdown", "metadata": {}, "source": text.splitlines(True)}

def code(text):
    return {
        "cell_type": "code", "execution_count": None,
        "metadata": {}, "outputs": [], "source": text.splitlines(True),
    }

def build_and_run(cells, out_path):
    namespace = {"__name__": "__main__"}
    execution = 0
    for cell in cells:
        if cell["cell_type"] != "code":
            continue
        execution += 1
        cell["execution_count"] = execution
        source = "".join(cell["source"])
        stream = io.StringIO()
        try:
            with contextlib.redirect_stdout(stream), contextlib.redirect_stderr(stream):
                exec(compile(source, f"cell-{execution}", "exec"), namespace)
            output = stream.getvalue()
            if output:
                cell["outputs"] = [{"name": "stdout", "output_type": "stream",
                                     "text": output.splitlines(True)}]
        except Exception as exc:
            cell["outputs"] = [{
                "ename": type(exc).__name__, "evalue": str(exc),
                "output_type": "error",
                "traceback": traceback.format_exc().splitlines(),
            }]
            raise
    notebook = {
        "cells": cells,
        "metadata": {
            "kernelspec": {"display_name": "Python 3", "language": "python", "name": "python3"},
            "language_info": {"name": "python", "version": "3.12"},
        },
        "nbformat": 4, "nbformat_minor": 5,
    }
    Path(out_path).write_text(
        json.dumps(notebook, ensure_ascii=False, indent=1) + "\n", encoding="utf-8"
    )
    print(f"OK  {out_path}")


# ═══════════════════════════════════════════════════════════════════════
# NOTEBOOK 1 — ANGKET PENGGUNAAN MEDIA
# ═══════════════════════════════════════════════════════════════════════
CELLS_MEDIA = [

md("""# Analisis Angket Penggunaan Media (QN + QL)

Notebook ini mengolah data angket penggunaan media Bicaranta dari kelompok eksperimen
sesuai metodologi Bab III (analisis deskriptif, reliabilitas, korelasi, persentase respons).

**Cakupan:**
1. Validasi data QN (Q1–Q15 skala Likert 1–4)
2. Statistik deskriptif per dimensi dan total
3. Alpha Cronbach per dimensi dan keseluruhan
4. Distribusi respons per item (profil)
5. Persentase skor (rumus P = ΣR/N × 100%) dan kategori
6. Korelasi Pearson skor media ↔ nilai postes berbicara
7. Ringkasan respons kualitatif (QL)
"""),

code("""import csv, math
from pathlib import Path
from collections import Counter
from statistics import mean, median, stdev, variance

def find_root():
    for p in [Path.cwd(), *Path.cwd().parents]:
        if (p / 'data' / 'QN_angket_penggunaan_media.csv').exists():
            return p
    raise FileNotFoundError("Root proyek tidak ditemukan")

ROOT     = find_root()
QN_PATH  = ROOT / 'data' / 'QN_angket_penggunaan_media.csv'
QL_PATH  = ROOT / 'data' / 'QL_angket_penggunaan_media.csv'
POST_PATH = ROOT / 'data' / 'field_test' / 'keterampilan_berbicara_postes.csv'

ITEM_COLS = [f'Q{i}' for i in range(1, 16)]
DIM_COLS  = ['skor_planning', 'skor_monitoring', 'skor_evaluation']
DIM_LABEL = {'skor_planning': 'Perencanaan', 'skor_monitoring': 'Pemantauan',
             'skor_evaluation': 'Evaluasi'}
DIMS_ITEMS = {
    'Perencanaan': ['Q1','Q4','Q6','Q8','Q15'],
    'Pemantauan' : ['Q5','Q7','Q9','Q10','Q11'],
    'Evaluasi'   : ['Q2','Q3','Q12','Q13','Q14'],
}

def load_qn():
    with open(QN_PATH, encoding='utf-8-sig', newline='') as f:
        rows = list(csv.DictReader(f))
    out = []
    for row in rows:
        if row.get('kelompok') != 'eksperimen' or not row.get('Q1'):
            continue
        for col in ITEM_COLS + DIM_COLS + ['skor_total']:
            if row.get(col):
                row[col] = float(row[col])
        out.append(row)
    return out

def load_post():
    with open(POST_PATH, encoding='utf-8-sig', newline='') as f:
        return {r['id']: float(r['post_nilai_akhir']) for r in csv.DictReader(f)}

def load_ql():
    with open(QL_PATH, encoding='utf-8-sig', newline='') as f:
        return list(csv.DictReader(f))

qn   = load_qn()
post = load_post()
ql   = load_ql()

print(f"Root              : {ROOT}")
print(f"QN eksperimen     : {len(qn)} responden")
ql_eks = [r for r in ql if r.get('id','').startswith('E') and any(r.get(f'ql{i}') for i in range(1,7))]
print(f"QL eksperimen     : {len(ql_eks)} responden dengan jawaban")
"""),

md("## 1. Validasi Data QN"),

code("""issues = []
for row in qn:
    sid = row['id']
    for col in ITEM_COLS:
        v = row.get(col)
        if isinstance(v, float) and not (1 <= v <= 4):
            issues.append(f"{sid} {col}={v} di luar [1,4]")
    if all(row.get(c) for c in DIM_COLS) and row.get('skor_total'):
        calc = round(sum(row[c] for c in DIM_COLS) / 3, 2)
        stored = round(row['skor_total'], 2)
        if abs(calc - stored) > 0.11:
            issues.append(f"{sid}: skor_total={stored} != rerata_dim={calc:.2f}")

if issues:
    print("Isu ditemukan:")
    for i in issues: print(" -", i)
else:
    print(f"PASS: {len(qn)} baris valid — semua item dalam [1,4], skor konsisten")
"""),

md("## 2. Statistik Deskriptif"),

code("""def describe(x):
    return {'n':len(x),'mean':mean(x),'sd':stdev(x) if len(x)>1 else 0,
            'median':median(x),'min':min(x),'max':max(x)}

def fmt(v, d=3):
    return f"{v:.{d}f}" if isinstance(v, float) else str(v)

def print_table(headers, rows):
    text = [[fmt(v) for v in row] for row in rows]
    widths = [max(len(str(h)), *(len(r[i]) for r in text)) for i,h in enumerate(headers)]
    sep = ' | '
    print(sep.join(str(h).ljust(widths[i]) for i,h in enumerate(headers)))
    print('-+-'.join('-'*w for w in widths))
    for row in text:
        print(sep.join(row[i].ljust(widths[i]) for i in range(len(headers))))

desc_rows = []
for col in DIM_COLS + ['skor_total']:
    vals = [row[col] for row in qn if isinstance(row.get(col), float)]
    d = describe(vals)
    label = DIM_LABEL.get(col, 'Total')
    desc_rows.append([label, d['n'], d['mean'], d['sd'], d['median'], d['min'], d['max']])
print_table(['Dimensi','n','Mean','SD','Median','Min','Max'], desc_rows)

print()
cat = Counter(row.get('kategori','') for row in qn)
print("Distribusi kategori skor total:")
order = ['Sangat Tinggi','Tinggi','Sedang','Rendah']
for k in order:
    v = cat.get(k, 0)
    print(f"  {k:<15}: {v:>2}  ({v/len(qn)*100:.1f}%)")
"""),

md("""## 3. Persentase Skor (Rumus Bab III)

**Rumus:** P = (ΣR / N) × 100%  
ΣR = jumlah skor jawaban seluruh responden; N = jumlah skor ideal (jumlah item × skor maks × n responden)
"""),

code("""n_resp = len(qn)
skor_maks_item = 4

# Total keseluruhan
sigma_r_total = sum(sum(row[q] for q in ITEM_COLS) for row in qn)
n_ideal_total = len(ITEM_COLS) * skor_maks_item * n_resp
p_total = sigma_r_total / n_ideal_total * 100

print(f"Total (15 item): sigmaR={sigma_r_total:.0f}, N_ideal={n_ideal_total}, P={p_total:.2f}%")
print()

# Per dimensi
print(f"{'Dimensi':<15} {'sigmaR':>8} {'N_ideal':>8} {'P (%)':>7}")
print('-' * 42)
for dim_label, items in DIMS_ITEMS.items():
    sr = sum(sum(row[q] for q in items) for row in qn)
    ni = len(items) * skor_maks_item * n_resp
    p  = sr / ni * 100
    print(f"{dim_label:<15} {sr:>8.0f} {ni:>8} {p:>7.2f}%")
"""),

md("## 4. Alpha Cronbach"),

code("""def cronbach_alpha(matrix):
    k = len(matrix[0])
    if k < 2: return float('nan')
    item_vars = sum(variance([row[j] for row in matrix]) for j in range(k))
    total_var = variance([sum(row) for row in matrix])
    if total_var == 0: return float('nan')
    return k / (k-1) * (1 - item_vars / total_var)

def interp_alpha(a):
    if a >= 0.9: return "Sangat Baik"
    if a >= 0.8: return "Baik"
    if a >= 0.7: return "Dapat Diterima"
    if a >= 0.6: return "Dipertanyakan"
    return "Buruk"

alpha_rows = []
# Semua 15 item
m_all = [[row[q] for q in ITEM_COLS] for row in qn]
a_all = cronbach_alpha(m_all)
alpha_rows.append(['Total (15 item)', 15, a_all, interp_alpha(a_all)])
# Per dimensi
for dim, items in DIMS_ITEMS.items():
    m = [[row[q] for q in items] for row in qn]
    a = cronbach_alpha(m)
    alpha_rows.append([dim, len(items), a, interp_alpha(a)])

print_table(['Dimensi','k','Alpha Cronbach','Interpretasi'], alpha_rows)
"""),

md("## 5. Profil Respons Per Item (% frekuensi pilihan 1–4)"),

code("""print(f"{'Item':<6}  {'1%':>6} {'2%':>6} {'3%':>6} {'4%':>6}  {'Mean':>6} {'SD':>5}")
print('-' * 52)
for col in ITEM_COLS:
    vals = [row[col] for row in qn if isinstance(row.get(col), float)]
    freq = Counter(int(v) for v in vals)
    n = len(vals)
    m = mean(vals)
    s = stdev(vals) if len(vals)>1 else 0
    print(f"{col:<6}  {freq.get(1,0)/n*100:6.1f} {freq.get(2,0)/n*100:6.1f} "
          f"{freq.get(3,0)/n*100:6.1f} {freq.get(4,0)/n*100:6.1f}  {m:6.3f} {s:5.3f}")
"""),

md("## 6. Korelasi Skor Media × Nilai Postes Berbicara"),

code("""def pearson_r(x, y):
    n = len(x); mx, my = mean(x), mean(y)
    num = sum((a-mx)*(b-my) for a,b in zip(x,y))
    den = math.sqrt(sum((a-mx)**2 for a in x) * sum((b-my)**2 for b in y))
    return num/den if den else 0

def betacf(a,b,x):
    qab,qap,qam=a+b,a+1,a-1; c,d=1.0,max(abs(1-qab*x/qap),3e-300)
    d=1/d; h=d
    for m in range(1,201):
        m2=2*m; aa=m*(b-m)*x/((qam+m2)*(a+m2))
        d=1+aa*d; d=1/max(abs(d),3e-300)*(1 if d>=0 else -1)
        c=1+aa/c; c=max(abs(c),3e-300)*(1 if c>=0 else -1)
        h*=d*c; aa=-(a+m)*(qab+m)*x/((a+m2)*(qap+m2))
        d=1+aa*d; d=1/max(abs(d),3e-300)*(1 if d>=0 else -1)
        c=1+aa/c; c=max(abs(c),3e-300)*(1 if c>=0 else -1)
        delta=d*c; h*=delta
        if abs(delta-1)<3e-14: break
    return h

def ibeta(a,b,x):
    if x<=0: return 0.0
    if x>=1: return 1.0
    bt=math.exp(math.lgamma(a+b)-math.lgamma(a)-math.lgamma(b)+a*math.log(x)+b*math.log1p(-x))
    return bt*betacf(a,b,x)/a if x<(a+1)/(a+b+2) else 1-bt*betacf(b,a,1-x)/b

def p_from_r(r, n):
    if abs(r) >= 1: return 0.0
    t = r * math.sqrt(n-2) / math.sqrt(1-r**2)
    return ibeta((n-2)/2, 0.5, (n-2)/(n-2+t**2))

pairs = [(row['skor_total'], post[row['id']])
         for row in qn if row['id'] in post and isinstance(row.get('skor_total'), float)]
xs, ys = zip(*pairs)
n_pairs = len(pairs)
r_tot = pearson_r(list(xs), list(ys))
p_tot = p_from_r(r_tot, n_pairs)
interp = 'Kuat' if abs(r_tot)>=0.6 else ('Sedang' if abs(r_tot)>=0.4 else 'Lemah')

print(f"Korelasi skor_total media x postes berbicara:")
print(f"  n={n_pairs},  r={r_tot:.3f},  p={p_tot:.4f}  ({interp})")
print()
print("Korelasi per dimensi:")
for col in DIM_COLS:
    pairs_d = [(row[col], post[row['id']])
               for row in qn if row['id'] in post and isinstance(row.get(col), float)]
    if len(pairs_d) < 3: continue
    xd, yd = zip(*pairs_d)
    r = pearson_r(list(xd), list(yd))
    p = p_from_r(r, len(pairs_d))
    label = DIM_LABEL[col]
    print(f"  {label:<12}: r={r:.3f},  p={p:.4f},  n={len(pairs_d)}")
"""),

md("## 7. Ringkasan Respons Kualitatif (QL)"),

code("""ql_filled = [r for r in ql if r.get('id','').startswith('E') and r.get('ql1')]
print(f"Responden QL eksperimen dengan data: {len(ql_filled)}")
print()
ql_cols = [c for c in (ql_filled[0].keys() if ql_filled else []) if c.startswith('ql')]
for col in ql_cols[:5]:
    resps = [r[col] for r in ql_filled if r.get(col) and len(r[col]) > 5]
    if not resps: continue
    print(f"=== {col.upper()} (n={len(resps)}) ===")
    for i, resp in enumerate(resps[:3], 1):
        trunc = resp[:110] + '...' if len(resp) > 110 else resp
        print(f"  [{i}] {trunc}")
    print()
"""),

]  # end CELLS_MEDIA


# ═══════════════════════════════════════════════════════════════════════
# NOTEBOOK 2 — METAKOGNITIF
# ═══════════════════════════════════════════════════════════════════════
CELLS_META = [

md("""# Analisis Kemampuan Metakognitif (Pre–Post)

Notebook ini mengolah data angket metakognitif (4 dimensi, skor 4–16 per dimensi, total 16–64).
Metodologi sesuai Bab III: N-Gain Hake, paired t-test, independent t-test (Welch), Cohen's d.

**Cakupan:**
1. Validasi data (rentang, konsistensi total, duplikasi)
2. Statistik deskriptif pre dan post per kelompok dan dimensi
3. Kategorisasi skor metakognitif
4. Gain absolut dan N-Gain ternormalisasi (Hake, 1998)
5. Uji normalitas Jarque–Bera pada gain
6. Paired sample t-test (perubahan dalam kelompok)
7. Independent sample t-test / Welch (perbedaan N-Gain antarkelompok)
8. Effect size Cohen's d / Hedges' g
9. Analisis per dimensi
10. Korelasi metakognitif postes × keterampilan berbicara postes
"""),

code("""import csv, math
from pathlib import Path
from collections import Counter
from statistics import mean, median, stdev, variance

def find_root():
    for p in [Path.cwd(), *Path.cwd().parents]:
        if (p / 'data' / 'field_test' / 'metakognitif.csv').exists():
            return p
    raise FileNotFoundError("Root proyek tidak ditemukan")

ROOT = find_root()
META_PATH = ROOT / 'data' / 'field_test' / 'metakognitif.csv'
POST_PATH = ROOT / 'data' / 'field_test' / 'keterampilan_berbicara_postes.csv'

DIMS = ['planning', 'monitoring', 'evaluation', 'integratif']
DIM_LABEL = {'planning':'Perencanaan','monitoring':'Pemantauan',
             'evaluation':'Evaluasi','integratif':'Integratif (Feynman)'}
MAX_TOTAL = 64.0
MAX_DIM   = 16.0

def load_meta():
    with open(META_PATH, encoding='utf-8-sig', newline='') as f:
        raw = list(csv.DictReader(f))
    result = []
    for row in raw:
        rec = {'id': row['id'], 'kelompok': row['kelompok'], 'nama': row['nama']}
        for phase in ('pre', 'post'):
            for dim in DIMS:
                col = f'{phase}_{dim}'
                rec[col] = float(row[col]) if row.get(col) else None
            col_t = f'{phase}_total'
            rec[col_t] = float(row[col_t]) if row.get(col_t) else None
        result.append(rec)
    return result

def load_post_scores():
    with open(POST_PATH, encoding='utf-8-sig', newline='') as f:
        return {r['id']: float(r['post_nilai_akhir']) for r in csv.DictReader(f)}

meta  = load_meta()
post  = load_post_scores()

complete_e = [r for r in meta if r['kelompok']=='eksperimen'
              and r['pre_total'] is not None and r['post_total'] is not None]
complete_k = [r for r in meta if r['kelompok']=='kontrol'
              and r['pre_total'] is not None and r['post_total'] is not None]

print(f"Root                : {ROOT}")
print(f"Total data          : {len(meta)}")
print(f"Eksperimen lengkap  : {len(complete_e)}")
print(f"Kontrol lengkap     : {len(complete_k)}")
"""),

md("## 1. Validasi Data"),

code("""issues = []
seen = set()
for row in meta:
    sid = row['id']
    if sid in seen:
        issues.append(f"Duplikat ID: {sid}")
    seen.add(sid)
    for phase in ('pre', 'post'):
        dims = [row.get(f'{phase}_{d}') for d in DIMS]
        total = row.get(f'{phase}_total')
        if any(v is not None for v in dims):
            for d, v in zip(DIMS, dims):
                if v is not None and not (4 <= v <= 16):
                    issues.append(f"{sid} {phase}_{d}={v} di luar [4,16]")
            if total is not None and all(v is not None for v in dims):
                if abs(sum(dims) - total) > 0.01:
                    issues.append(f"{sid} {phase}_total={total} != sum={sum(dims)}")

if issues:
    print("Isu ditemukan:")
    for i in issues: print(" -", i)
else:
    print(f"PASS: {len(meta)} baris valid — semua dimensi dalam [4,16], total konsisten")
"""),

md("## 2. Statistik Deskriptif Pre dan Post"),

code("""def describe(x):
    if not x: return {}
    return {'n':len(x),'mean':mean(x),'sd':stdev(x) if len(x)>1 else 0,
            'median':median(x),'min':min(x),'max':max(x)}

def fmt(v, d=3):
    return f"{v:.{d}f}" if isinstance(v, float) else str(v)

def print_table(headers, rows):
    text = [[fmt(v) for v in row] for row in rows]
    widths = [max(len(str(h)), *(len(r[i]) for r in text)) for i,h in enumerate(headers)]
    sep = ' | '
    print(sep.join(str(h).ljust(widths[i]) for i,h in enumerate(headers)))
    print('-+-'.join('-'*w for w in widths))
    for row in text:
        print(sep.join(row[i].ljust(widths[i]) for i in range(len(headers))))

print("=== SKOR TOTAL (maks 64) ===")
rows_d = []
for group, subset in [('Eksperimen', complete_e), ('Kontrol', complete_k)]:
    for phase in ('pre', 'post'):
        vals = [r[f'{phase}_total'] for r in subset if r.get(f'{phase}_total') is not None]
        d = describe(vals)
        rows_d.append([group, phase.capitalize(), d['n'], d['mean'], d['sd'],
                       d['median'], d['min'], d['max']])
print_table(['Kelompok','Fase','n','Mean','SD','Median','Min','Max'], rows_d)

print()
print("=== PER DIMENSI (maks 16) ===")
dim_rows = []
for group, subset in [('Eksperimen', complete_e), ('Kontrol', complete_k)]:
    for dim in DIMS:
        for phase in ('pre', 'post'):
            col = f'{phase}_{dim}'
            vals = [r[col] for r in subset if r.get(col) is not None]
            d = describe(vals)
            dim_rows.append([group, DIM_LABEL[dim], phase.capitalize(),
                             d['n'], d['mean'], d['sd'], d['min'], d['max']])
print_table(['Kelompok','Dimensi','Fase','n','Mean','SD','Min','Max'], dim_rows)
"""),

md("""## 3. Kategorisasi Skor Metakognitif

Berdasarkan rentang skor total (16–64):

| Kategori | Rentang |
|---|---|
| Sangat Tinggi | 55–64 |
| Tinggi | 43–54 |
| Sedang | 31–42 |
| Rendah | 16–30 |
"""),

code("""def kategori(v):
    if v is None: return '-'
    if v >= 55: return 'Sangat Tinggi'
    if v >= 43: return 'Tinggi'
    if v >= 31: return 'Sedang'
    return 'Rendah'

ORDER = ['Rendah','Sedang','Tinggi','Sangat Tinggi']
print("Distribusi kategori skor total:")
for group, subset in [('Eksperimen', complete_e), ('Kontrol', complete_k)]:
    print(f"\\n  {group}:")
    for phase in ('pre', 'post'):
        col = f'{phase}_total'
        cats = Counter(kategori(r[col]) for r in subset)
        n = len(subset)
        parts = '  '.join(f"{k}:{cats.get(k,0):>2} ({cats.get(k,0)/n*100:4.1f}%)" for k in ORDER)
        print(f"    {phase.upper()}: {parts}")
"""),

md("""## 4. Gain Absolut dan N-Gain Ternormalisasi (Hake, 1998)

**Gain absolut** = post − pre  
**N-Gain** = (post − pre) / (skor_maks − pre)

Kategori N-Gain (Hake):
- Tinggi : g ≥ 0,70
- Sedang : 0,30 ≤ g < 0,70
- Rendah : g < 0,30
"""),

code("""def ngain(pre, post, smax=MAX_TOTAL):
    if pre is None or post is None: return None
    if smax - pre == 0: return None
    return (post - pre) / (smax - pre)

def ngain_kat(g):
    if g is None: return '-'
    if g >= 0.7: return 'Tinggi'
    if g >= 0.3: return 'Sedang'
    return 'Rendah'

gain_e  = [r['post_total'] - r['pre_total'] for r in complete_e]
gain_k  = [r['post_total'] - r['pre_total'] for r in complete_k]
ng_e    = [v for v in (ngain(r['pre_total'], r['post_total']) for r in complete_e) if v is not None]
ng_k    = [v for v in (ngain(r['pre_total'], r['post_total']) for r in complete_k) if v is not None]

print("=== GAIN ABSOLUT ===")
rows_g = []
for label, vals in [('Eksperimen', gain_e), ('Kontrol', gain_k)]:
    d = describe(vals)
    rows_g.append([label, d['n'], d['mean'], d['sd'], d['median'], d['min'], d['max']])
print_table(['Kelompok','n','Mean Gain','SD','Median','Min','Max'], rows_g)

print()
print("=== N-GAIN TERNORMALISASI ===")
rows_ng = []
for label, vals in [('Eksperimen', ng_e), ('Kontrol', ng_k)]:
    d = describe(vals)
    rows_ng.append([label, d['n'], d['mean'], d['sd'], d['median'], d['min'], d['max']])
print_table(['Kelompok','n','Mean N-Gain','SD','Median','Min','Max'], rows_ng)

print()
print("Distribusi kategori N-Gain:")
for group, vals in [('Eksperimen', ng_e), ('Kontrol', ng_k)]:
    cats = Counter(ngain_kat(v) for v in vals)
    n = len(vals)
    parts = '  '.join(f"{k}:{cats.get(k,0)} ({cats.get(k,0)/n*100:.0f}%)"
                     for k in ['Rendah','Sedang','Tinggi'])
    print(f"  {group}: {parts}")
"""),

md("""## 5. Uji Normalitas (Jarque–Bera) pada Gain

Digunakan sebagai diagnostik sebelum memilih uji parametrik vs. nonparametrik.
p_JB besar → distribusi mendekati normal.
"""),

code("""def jb_test(x):
    n=len(x); m=mean(x); s=math.sqrt(sum((v-m)**2 for v in x)/n)
    if s == 0: return 0,0,0,1.0
    skew=sum((v-m)**3 for v in x)/n/s**3
    excess=sum((v-m)**4 for v in x)/n/s**4-3
    jb=n/6*(skew**2+excess**2/4)
    return skew,excess,jb,math.exp(-jb/2)

print("Diagnostik normalitas pada distribusi Gain dan N-Gain:")
print()
rows_jb = []
for label, vals in [('Eksperimen Gain',gain_e),('Kontrol Gain',gain_k),
                    ('Eksperimen N-Gain',ng_e),('Kontrol N-Gain',ng_k)]:
    sk,ku,jb,p = jb_test(vals)
    normal = "Mendekati normal" if p > 0.05 else "Non-normal"
    rows_jb.append([label, len(vals), sk, ku, jb, p, normal])
print_table(['Kelompok','n','Skewness','ExcessKurt','JB','p_JB','Keterangan'], rows_jb)
"""),

md("""## 6. Paired Sample t-Test (Perubahan Dalam Kelompok)

Uji t berpasangan — apakah terdapat peningkatan signifikan pre → post dalam masing-masing kelompok.
"""),

code("""def betacf(a,b,x):
    qab,qap,qam=a+b,a+1,a-1; c,d=1.0,max(abs(1-qab*x/qap),3e-300)
    d=1/d; h=d
    for m in range(1,201):
        m2=2*m; aa=m*(b-m)*x/((qam+m2)*(a+m2))
        d=1+aa*d; d=1/max(abs(d),3e-300)*(1 if d>=0 else -1)
        c=1+aa/c; c=max(abs(c),3e-300)*(1 if c>=0 else -1)
        h*=d*c; aa=-(a+m)*(qab+m)*x/((a+m2)*(qap+m2))
        d=1+aa*d; d=1/max(abs(d),3e-300)*(1 if d>=0 else -1)
        c=1+aa/c; c=max(abs(c),3e-300)*(1 if c>=0 else -1)
        delta=d*c; h*=delta
        if abs(delta-1)<3e-14: break
    return h

def ibeta(a,b,x):
    if x<=0: return 0.0
    if x>=1: return 1.0
    bt=math.exp(math.lgamma(a+b)-math.lgamma(a)-math.lgamma(b)+a*math.log(x)+b*math.log1p(-x))
    return bt*betacf(a,b,x)/a if x<(a+1)/(a+b+2) else 1-bt*betacf(b,a,1-x)/b

def t_p(t,df): return ibeta(df/2,0.5,df/(df+t*t))

def t_crit(df,alpha=.05):
    lo,hi=0.0,20.0
    for _ in range(80):
        mid=(lo+hi)/2
        if t_p(mid,df)>alpha: lo=mid
        else: hi=mid
    return (lo+hi)/2

def paired_t(pre_x, post_x):
    change=[b-a for a,b in zip(pre_x,post_x)]
    n=len(change); se=stdev(change)/math.sqrt(n); t=mean(change)/se; df=n-1; crit=t_crit(df)
    return {'n':n,'mean_gain':mean(change),'ci_low':mean(change)-crit*se,
            'ci_high':mean(change)+crit*se,'t':t,'df':df,'p':t_p(t,df),
            'cohen_dz':mean(change)/stdev(change)}

rows_pt = []
for group, subset in [('Eksperimen', complete_e), ('Kontrol', complete_k)]:
    pre_v  = [r['pre_total'] for r in subset]
    post_v = [r['post_total'] for r in subset]
    r = paired_t(pre_v, post_v)
    sig = "Signifikan" if r['p'] < 0.05 else "Tidak Signifikan"
    rows_pt.append([group, r['n'], r['mean_gain'], r['ci_low'], r['ci_high'],
                    r['t'], r['df'], r['p'], r['cohen_dz'], sig])
print_table(['Kelompok','n','Mean Gain','CI low','CI high','t','df','p','Cohen dz','Keterangan'],
            rows_pt)
"""),

md("""## 7. Independent Sample t-Test / Welch — Perbedaan N-Gain Antarkelompok

Uji ini menjawab apakah peningkatan metakognitif (N-Gain) kelas eksperimen secara signifikan lebih besar daripada kelas kontrol.
"""),

code("""def welch(a, b):
    va,vb,na,nb=variance(a),variance(b),len(a),len(b)
    se=math.sqrt(va/na+vb/nb); diff=mean(a)-mean(b); t=diff/se
    df=(va/na+vb/nb)**2/((va/na)**2/(na-1)+(vb/nb)**2/(nb-1))
    crit=t_crit(df)
    sp=math.sqrt(((na-1)*va+(nb-1)*vb)/(na+nb-2))
    g=diff/sp*(1-3/(4*(na+nb)-9))
    return {'diff':diff,'ci_low':diff-crit*se,'ci_high':diff+crit*se,
            't':t,'df':df,'p':t_p(t,df),'hedges_g':g}

r = welch(ng_e, ng_k)
print("Welch t-test: N-Gain Eksperimen vs Kontrol")
print(f"  n eksperimen        : {len(ng_e)}")
print(f"  n kontrol           : {len(ng_k)}")
print(f"  Mean N-Gain Eks     : {mean(ng_e):.4f}")
print(f"  Mean N-Gain Kont    : {mean(ng_k):.4f}")
print(f"  Selisih             : {r['diff']:.4f}  (95% CI [{r['ci_low']:.4f}, {r['ci_high']:.4f}])")
print(f"  t({r['df']:.2f})           = {r['t']:.4f}")
print(f"  p                   = {r['p']:.5f}")
print(f"  Hedges' g (effect)  = {r['hedges_g']:.4f}")
print()
if r['p'] < 0.05:
    g_label = 'Besar' if abs(r['hedges_g'])>=0.8 else ('Sedang' if abs(r['hedges_g'])>=0.5 else 'Kecil')
    print(f"Kesimpulan: Perbedaan signifikan (p<0.05), effect size {g_label} (g={r['hedges_g']:.3f})")
else:
    print("Kesimpulan: Perbedaan tidak signifikan (p>=0.05)")
"""),

md("## 8. Analisis Per Dimensi (N-Gain + Welch t-test)"),

code("""print("Analisis per dimensi (N-Gain Hake, Welch t-test):")
print()
dim_rows2 = []
for dim in DIMS:
    label = DIM_LABEL[dim]
    pre_col, post_col = f'pre_{dim}', f'post_{dim}'
    ng_ed = [ngain(r[pre_col], r[post_col], MAX_DIM) for r in complete_e
             if r[pre_col] is not None and r[post_col] is not None]
    ng_kd = [ngain(r[pre_col], r[post_col], MAX_DIM) for r in complete_k
             if r[pre_col] is not None and r[post_col] is not None]
    ng_ed = [v for v in ng_ed if v is not None]
    ng_kd = [v for v in ng_kd if v is not None]
    if len(ng_ed) < 2 or len(ng_kd) < 2:
        print(f"  {label}: data tidak cukup")
        continue
    r = welch(ng_ed, ng_kd)
    sig = "*" if r['p'] < 0.05 else ""
    dim_rows2.append([label, f"{mean(ng_ed):.3f}", f"{mean(ng_kd):.3f}",
                      f"{r['diff']:.3f}", f"t({r['df']:.1f})={r['t']:.3f}",
                      f"{r['p']:.4f}{sig}", f"{r['hedges_g']:.3f}"])

print_table(['Dimensi','NG_Eks','NG_Kont','Selisih','t(df)','p','Hedges g'], dim_rows2)
print("* = signifikan p<0.05")
"""),

md("## 9. Korelasi Metakognitif Post × Postes Berbicara"),

code("""def pearson_r(x, y):
    n=len(x); mx,my=mean(x),mean(y)
    num=sum((a-mx)*(b-my) for a,b in zip(x,y))
    den=math.sqrt(sum((a-mx)**2 for a in x)*sum((b-my)**2 for b in y))
    return num/den if den else 0

def p_from_r(r, n):
    if abs(r) >= 1: return 0.0
    t = r * math.sqrt(n-2) / math.sqrt(1-r**2)
    return ibeta((n-2)/2, 0.5, (n-2)/(n-2+t**2))

pairs_e = [(r['post_total'], post[r['id']])
           for r in complete_e if r['id'] in post and r['post_total'] is not None]
if pairs_e:
    xm, yp = zip(*pairs_e)
    r_val = pearson_r(list(xm), list(yp))
    p_val = p_from_r(r_val, len(pairs_e))
    interp = 'Kuat' if abs(r_val)>=0.6 else ('Sedang' if abs(r_val)>=0.4 else 'Lemah')
    print(f"Korelasi post_meta x postes berbicara (eksperimen):")
    print(f"  n={len(pairs_e)},  r={r_val:.3f},  p={p_val:.4f}  ({interp})")

# Juga untuk kontrol
pairs_k = [(r['post_total'], post[r['id']])
           for r in complete_k if r['id'] in post and r['post_total'] is not None]
if pairs_k:
    xm, yp = zip(*pairs_k)
    r_val = pearson_r(list(xm), list(yp))
    p_val = p_from_r(r_val, len(pairs_k))
    interp = 'Kuat' if abs(r_val)>=0.6 else ('Sedang' if abs(r_val)>=0.4 else 'Lemah')
    print(f"Korelasi post_meta x postes berbicara (kontrol):")
    print(f"  n={len(pairs_k)},  r={r_val:.3f},  p={p_val:.4f}  ({interp})")
"""),

md("""## Catatan Metodologis dan Keterbatasan

1. **Instrumen metakognitif** terdiri atas 16 item (4 dimensi × 4 item, skala Likert 1–4), total skor 16–64.
2. **N-Gain Hake (1998)**: mahasiswa dengan pre = skor_maks dikecualikan dari perhitungan (pembagi = 0). 
3. **Alpha Cronbach** angket metakognitif dihitung terpisah di notebook instrumen.
4. **Kelompok kontrol**: tidak mendapatkan perlakuan media; beberapa mahasiswa memiliki data pre yang sudah lengkap dari awal pengambilan data.
5. **Effect size**: Cohen's dz untuk paired t-test; Hedges' g untuk uji antarkelompok (koreksi bias sampel kecil).
6. **Taraf signifikansi**: α = 0,05 sesuai Bab III.
7. **Desain kuasi-eksperimen**: estimasi kausal rentan terhadap confounding kelas/dosen.
"""),

]  # end CELLS_META


# ─── BUILD ───────────────────────────────────────────────────────────────────
from pathlib import Path
for p in [Path.cwd(), *Path.cwd().parents]:
    if (p / 'data' / 'QN_angket_penggunaan_media.csv').exists():
        ROOT_P = p; break

print("Building notebook 1: angket media ...")
build_and_run(CELLS_MEDIA, ROOT_P / 'data' / 'olahdata_angket_media.ipynb')

print("Building notebook 2: metakognitif ...")
build_and_run(CELLS_META, ROOT_P / 'data' / 'field_test' / 'olahdata_metakognitif.ipynb')

print("\nSelesai!")
