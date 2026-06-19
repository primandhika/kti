import contextlib
import io
import json
import traceback
from pathlib import Path


def md(text):
    return {"cell_type": "markdown", "metadata": {}, "source": text.splitlines(True)}


def code(text):
    return {
        "cell_type": "code",
        "execution_count": None,
        "metadata": {},
        "outputs": [],
        "source": text.splitlines(True),
    }


cells = [
md("""# Analisis Uji Lapangan Keterampilan Berbicara

Notebook ini merupakan analisis auditable untuk desain kuasi-eksperimen pretes–postes dengan kelompok kontrol. Urutan analisis: validasi data, deskriptif, reliabilitas internal, pemeriksaan asumsi, perubahan berpasangan, *difference-in-differences* (DiD), ANCOVA, analisis per aspek, dan uji ketahanan berbasis permutasi.

> **Batas inferensi:** desain menggunakan kelas yang sudah tersedia, sehingga estimasi tetap rentan terhadap efek kelas/dosen dan confounding lain yang tidak diukur. Identitas penilai diperiksa karena pergantian penilai dapat bercampur dengan efek waktu.
"""),
code("""import csv, math, random
from collections import Counter
from pathlib import Path
from statistics import mean, median, stdev

FILE_NAMES = {
    'pre': 'keterampilan_berbicara_pretes.csv',
    'post': 'keterampilan_berbicara_postes.csv',
}

def locate_data_dir():
    # Berjalan dari root proyek maupun langsung dari folder notebook.
    roots = [Path.cwd(), *Path.cwd().parents]
    candidates = []
    for root in roots:
        candidates.extend((root, root / 'data' / 'field_test'))
    for candidate in candidates:
        if all((candidate / name).is_file() for name in FILE_NAMES.values()):
            return candidate.resolve()
    checked = '\\n'.join(f'- {p}' for p in candidates)
    raise FileNotFoundError(f'CSV tidak ditemukan. Lokasi yang diperiksa:\\n{checked}')

DATA_DIR = locate_data_dir()
FILES = {kind: DATA_DIR / name for kind, name in FILE_NAMES.items()}

ASPECTS = ['pengorganisasian', 'kejelasan', 'ketepatan', 'strategi', 'metakognitif']
SUBITEMS = ['diksi', 'gramatikal', 'retorika', 'artikulasi_sub', 'intonasi',
            'kecepatan', 'urutan', 'transisi', 'simpulan', 'bahasa', 'contoh',
            'formalitas', 'persuasi', 'perhatian', 'inspirasi']

def load(kind):
    prefix = 'pre' if kind == 'pre' else 'post'
    with FILES[kind].open(newline='', encoding='utf-8-sig') as handle:
        rows = list(csv.DictReader(handle))
    numeric = [f'{prefix}_{a}' for a in ASPECTS + SUBITEMS] + [f'{prefix}_total', f'{prefix}_nilai_akhir']
    for row in rows:
        for col in numeric:
            row[col] = float(row[col])
    return rows

pre, post = load('pre'), load('post')
print(f'Direktori data : {DATA_DIR}')
print(f'Baris pre/post : {len(pre)}/{len(post)}')
print('Notebook tidak menampilkan NIM atau nama untuk menjaga minimisasi data pribadi.')
"""),
md("""## 1. Validasi integritas dan keterpasangan data

Sel ini menghentikan analisis bila ID tidak unik, pasangan pre–post tidak lengkap, label kelompok berubah, nilai di luar rentang, atau skor akhir tidak sesuai agregat lima aspek. Kegagalan harus diperbaiki pada sumber data—bukan disembunyikan pada tahap analisis.
"""),
code("""def assert_data_integrity(pre, post):
    issues = []
    for label, rows in [('pre', pre), ('post', post)]:
        ids = [r['id'] for r in rows]
        dup = sorted(k for k, n in Counter(ids).items() if n > 1)
        if dup: issues.append(f'{label}: ID duplikat {dup}')
        for r in rows:
            if r['kelompok'] not in {'eksperimen', 'kontrol'}:
                issues.append(f\"{label}: kelompok tidak valid pada {r['id']}\")
            prefix = label
            aspects = [r[f'{prefix}_{a}'] for a in ASPECTS]
            if any(not 0 <= x <= 15 for x in aspects):
                issues.append(f\"{label}: aspek di luar 0–15 pada {r['id']}\")
            expected = sum(aspects) * 100 / 75
            if abs(expected - r[f'{prefix}_nilai_akhir']) > 0.05:
                issues.append(f\"{label}: skor agregat tidak konsisten pada {r['id']}\")
    pm, qm = {r['id']: r for r in pre}, {r['id']: r for r in post}
    missing_post, missing_pre = sorted(pm.keys()-qm.keys()), sorted(qm.keys()-pm.keys())
    if missing_post: issues.append(f'Tanpa postes: {missing_post}')
    if missing_pre: issues.append(f'Tanpa pretes: {missing_pre}')
    for sid in pm.keys() & qm.keys():
        if pm[sid]['kelompok'] != qm[sid]['kelompok']:
            issues.append(f'Kelompok berubah: {sid}')
    if issues:
        raise ValueError('Validasi gagal:\\n- ' + '\\n- '.join(issues))
    return pm, qm

pre_map, post_map = assert_data_integrity(pre, post)
print('PASS: ID unik, seluruh kasus berpasangan, label konsisten, dan skor berada pada rentang yang ditetapkan.')
for group in ('eksperimen', 'kontrol'):
    ids = [sid for sid,r in pre_map.items() if r['kelompok'] == group]
    print(f"{group:11s}: n={len(ids):2d}")
print('Penilai pretes :', ', '.join(sorted({r['pre_penilai'] for r in pre})))
print('Penilai postes :', ', '.join(sorted({r['post_penilai'] for r in post})))
"""),
code("""# Fungsi statistik tanpa dependensi eksternal: p-value memakai distribusi t, bukan aproksimasi normal.
def variance(x): return stdev(x) ** 2

def quantile(x, p):
    y = sorted(x); pos = (len(y)-1)*p; lo = int(pos); hi = min(lo+1, len(y)-1)
    return y[lo] + (pos-lo)*(y[hi]-y[lo])

def betacf(a, b, x):
    qab, qap, qam = a+b, a+1, a-1
    c, d = 1.0, 1.0 - qab*x/qap
    d = 1.0 / max(abs(d), 3e-300) * (1 if d >= 0 else -1)
    h = d
    for m in range(1, 201):
        m2 = 2*m
        aa = m*(b-m)*x/((qam+m2)*(a+m2))
        d = 1 + aa*d; d = 1/max(abs(d), 3e-300)*(1 if d >= 0 else -1)
        c = 1 + aa/c; c = max(abs(c), 3e-300)*(1 if c >= 0 else -1)
        h *= d*c
        aa = -(a+m)*(qab+m)*x/((a+m2)*(qap+m2))
        d = 1 + aa*d; d = 1/max(abs(d), 3e-300)*(1 if d >= 0 else -1)
        c = 1 + aa/c; c = max(abs(c), 3e-300)*(1 if c >= 0 else -1)
        delta = d*c; h *= delta
        if abs(delta-1) < 3e-14: break
    return h

def ibeta(a, b, x):
    if x <= 0: return 0.0
    if x >= 1: return 1.0
    bt = math.exp(math.lgamma(a+b)-math.lgamma(a)-math.lgamma(b)+a*math.log(x)+b*math.log1p(-x))
    return bt*betacf(a,b,x)/a if x < (a+1)/(a+b+2) else 1-bt*betacf(b,a,1-x)/b

def t_p_two(t, df):
    return ibeta(df/2, 0.5, df/(df+t*t))

def t_critical(df, alpha=.05):
    lo, hi = 0.0, 20.0
    for _ in range(80):
        mid = (lo+hi)/2
        if t_p_two(mid, df) > alpha: lo = mid
        else: hi = mid
    return (lo+hi)/2

def describe(x):
    return {'n':len(x), 'mean':mean(x), 'sd':stdev(x), 'median':median(x),
            'q1':quantile(x,.25), 'q3':quantile(x,.75), 'min':min(x), 'max':max(x)}

def welch(a, b):
    va, vb, na, nb = variance(a), variance(b), len(a), len(b)
    se = math.sqrt(va/na + vb/nb); diff = mean(a)-mean(b); t = diff/se
    df = (va/na+vb/nb)**2 / ((va/na)**2/(na-1)+(vb/nb)**2/(nb-1))
    crit = t_critical(df)
    sp = math.sqrt(((na-1)*va+(nb-1)*vb)/(na+nb-2))
    d = diff/sp; correction = 1-3/(4*(na+nb)-9)
    return {'diff':diff, 'ci_low':diff-crit*se, 'ci_high':diff+crit*se,
            't':t, 'df':df, 'p':t_p_two(t,df), 'hedges_g':d*correction}

def paired(pre_x, post_x):
    change = [b-a for a,b in zip(pre_x,post_x)]
    n=len(change); se=stdev(change)/math.sqrt(n); t=mean(change)/se; df=n-1; crit=t_critical(df)
    return {'n':n, 'change':mean(change), 'ci_low':mean(change)-crit*se,
            'ci_high':mean(change)+crit*se, 't':t, 'df':df,
            'p':t_p_two(t,df), 'cohen_dz':mean(change)/stdev(change)}

def fmt(x, digits=3):
    if isinstance(x, int): return str(x)
    if isinstance(x, float): return f'{x:.{digits}f}'
    return str(x)

def print_table(headers, rows):
    text = [[fmt(v) for v in row] for row in rows]
    widths = [max(len(str(h)), *(len(row[i]) for row in text)) for i,h in enumerate(headers)]
    print(' | '.join(str(h).ljust(widths[i]) for i,h in enumerate(headers)))
    print('-+-'.join('-'*w for w in widths))
    for row in text: print(' | '.join(row[i].ljust(widths[i]) for i in range(len(headers))))
"""),
md("""## 2. Statistik deskriptif dan reliabilitas internal

Skor utama berada pada skala 0–100. Alpha Cronbach dihitung atas 15 butir rubrik (masing-masing skala 1–5), bukan atas lima subtotal yang sudah diagregasi. Alpha bukan bukti validitas konstruk dan harus dibaca bersama desain rubrik serta proses penilaian.
"""),
code("""def paired_values(group, field='nilai_akhir'):
    ids = [sid for sid,r in pre_map.items() if r['kelompok']==group]
    return ([pre_map[i][f'pre_{field}'] for i in ids],
            [post_map[i][f'post_{field}'] for i in ids])

rows=[]
for group in ('eksperimen','kontrol'):
    a,b=paired_values(group); ch=[y-x for x,y in zip(a,b)]
    for phase, vals in [('Pre',a),('Post',b),('Gain',ch)]:
        d=describe(vals); rows.append([group,phase,d['n'],d['mean'],d['sd'],d['median'],d['q1'],d['q3'],d['min'],d['max']])
print_table(['Kelompok','Fase','n','Mean','SD','Median','Q1','Q3','Min','Max'],rows)

def cronbach_alpha(rows, prefix):
    matrix=[[r[f'{prefix}_{item}'] for item in SUBITEMS] for r in rows]
    k=len(SUBITEMS); item_vars=sum(variance([row[j] for row in matrix]) for j in range(k))
    total_var=variance([sum(row) for row in matrix])
    return k/(k-1)*(1-item_vars/total_var)

print('\\nAlpha Cronbach 15 butir rubrik:')
for group in ('eksperimen','kontrol'):
    for phase, data in [('pre',pre),('post',post)]:
        subset=[r for r in data if r['kelompok']==group]
        print(f'{group:11s} {phase}: alpha={cronbach_alpha(subset,phase):.3f} (n={len(subset)})')
"""),
md("""## 3. Diagnostik distribusi, homogenitas, dan kesetaraan awal

Diagnostik normalitas menggunakan skewness, excess kurtosis, dan Jarque–Bera (aproksimasi chi-square df=2). Keputusan tidak didasarkan hanya pada satu p-value. Brown–Forsythe menguji perbedaan dispersi menggunakan deviasi absolut dari median. Uji utama antarkelompok tetap memakai Welch yang tidak mengasumsikan varians sama.
"""),
code("""def shape_diagnostics(x):
    n=len(x); m=mean(x); s=math.sqrt(sum((v-m)**2 for v in x)/n)
    skew=sum((v-m)**3 for v in x)/n/s**3
    excess=sum((v-m)**4 for v in x)/n/s**4-3
    jb=n/6*(skew**2+excess**2/4)
    return skew, excess, jb, math.exp(-jb/2)

diag=[]
for group in ('eksperimen','kontrol'):
    a,b=paired_values(group); change=[y-x for x,y in zip(a,b)]
    for phase,x in [('pre',a),('post',b),('gain',change)]:
        sk,ku,jb,p=shape_diagnostics(x); diag.append([group,phase,len(x),sk,ku,jb,p])
print_table(['Kelompok','Variabel','n','Skew','ExcessK','JB','p_JB'],diag)

pre_e,_=paired_values('eksperimen'); pre_c,_=paired_values('kontrol')
dev_e=[abs(x-median(pre_e)) for x in pre_e]; dev_c=[abs(x-median(pre_c)) for x in pre_c]
bf=welch(dev_e,dev_c); baseline=welch(pre_e,pre_c)
_,post_e=paired_values('eksperimen'); _,post_c=paired_values('kontrol')
gain_e=[b-a for a,b in zip(pre_e,post_e)]; gain_c=[b-a for a,b in zip(pre_c,post_c)]
bf_gain=welch([abs(x-median(gain_e)) for x in gain_e], [abs(x-median(gain_c)) for x in gain_c])
print('\\nBrown–Forsythe baseline: t={:.3f}, df={:.2f}, p={:.4f}'.format(bf['t'],bf['df'],bf['p']))
print('Brown–Forsythe gain    : t={:.3f}, df={:.2f}, p={:.4f}'.format(bf_gain['t'],bf_gain['df'],bf_gain['p']))
print('Kesetaraan baseline (Welch): selisih={:.2f}, 95% CI [{:.2f}, {:.2f}], p={:.4f}, Hedges g={:.3f}'.format(
    baseline['diff'],baseline['ci_low'],baseline['ci_high'],baseline['p'],baseline['hedges_g']))
"""),
md("""## 4. Analisis utama: perubahan berpasangan dan difference-in-differences

Uji berpasangan menjawab apakah masing-masing kelompok berubah. Efek intervensi yang lebih relevan adalah perbedaan perubahan antarkelompok (DiD): `(post−pre) eksperimen − (post−pre) kontrol`. Semua hasil melaporkan 95% CI dan effect size.
"""),
code("""gains={}; results=[]
for group in ('eksperimen','kontrol'):
    a,b=paired_values(group); gains[group]=[y-x for x,y in zip(a,b)]
    r=paired(a,b); results.append([group,r['n'],r['change'],r['ci_low'],r['ci_high'],r['t'],r['df'],r['p'],r['cohen_dz']])
print_table(['Kelompok','n','Mean gain','CI low','CI high','t','df','p','Cohen dz'],results)
did=welch(gains['eksperimen'],gains['kontrol'])
print('\\nDiD (Welch): {:.2f} poin; 95% CI [{:.2f}, {:.2f}]; t({:.2f})={:.3f}; p={:.5f}; Hedges g={:.3f}'.format(
    did['diff'],did['ci_low'],did['ci_high'],did['df'],did['t'],did['p'],did['hedges_g']))
"""),
md("""## 5. ANCOVA postes dengan kontrol skor awal

Model: `post = intercept + kelompok + pre`. Koefisien kelompok adalah selisih postes tersesuaikan. Interval kepercayaan dan p-value memakai standard error HC3 karena diagnostik menunjukkan heteroskedastisitas. Asumsi homogenitas kemiringan diperiksa melalui interaksi `kelompok × pre`; bila interaksi bermakna, ANCOVA sederhana tidak boleh menjadi satu-satunya estimasi.
"""),
code("""def invert(a):
    n=len(a); aug=[list(map(float,row))+[float(i==j) for j in range(n)] for i,row in enumerate(a)]
    for col in range(n):
        pivot=max(range(col,n),key=lambda r:abs(aug[r][col])); aug[col],aug[pivot]=aug[pivot],aug[col]
        if abs(aug[col][col])<1e-12: raise ValueError('Matriks singular')
        scale=aug[col][col]; aug[col]=[v/scale for v in aug[col]]
        for r in range(n):
            if r!=col:
                factor=aug[r][col]; aug[r]=[x-factor*y for x,y in zip(aug[r],aug[col])]
    return [row[n:] for row in aug]

def ols(X,y):
    n,p=len(X),len(X[0]); xtx=[[sum(X[r][i]*X[r][j] for r in range(n)) for j in range(p)] for i in range(p)]
    inv=invert(xtx); xty=[sum(X[r][i]*y[r] for r in range(n)) for i in range(p)]
    beta=[sum(inv[i][j]*xty[j] for j in range(p)) for i in range(p)]
    resid=[y[r]-sum(X[r][j]*beta[j] for j in range(p)) for r in range(n)]
    df=n-p; mse=sum(e*e for e in resid)/df
    se=[math.sqrt(mse*inv[i][i]) for i in range(p)]
    leverage=[sum(X[r][i]*inv[i][j]*X[r][j] for i in range(p) for j in range(p)) for r in range(n)]
    meat=[[sum((resid[r]/(1-leverage[r]))**2*X[r][i]*X[r][j] for r in range(n))
           for j in range(p)] for i in range(p)]
    cov_hc3=[[sum(inv[i][a]*meat[a][b]*inv[b][j] for a in range(p) for b in range(p))
              for j in range(p)] for i in range(p)]
    se_hc3=[math.sqrt(cov_hc3[i][i]) for i in range(p)]
    return beta,se,se_hc3,df,resid

def ancova(interaction=False):
    ids=list(pre_map)
    prex=[pre_map[i]['pre_nilai_akhir'] for i in ids]; center=mean(prex)
    group=[1.0 if pre_map[i]['kelompok']=='eksperimen' else 0.0 for i in ids]
    centered=[x-center for x in prex]
    X=[[1.0,g,x]+([g*x] if interaction else []) for g,x in zip(group,centered)]
    y=[post_map[i]['post_nilai_akhir'] for i in ids]
    beta,se,se_hc3,df,resid=ols(X,y)
    return ids,beta,se,se_hc3,df,resid

ids,b,se,se_hc3,df,resid=ancova(); t=b[1]/se_hc3[1]; crit=t_critical(df)
_,bi,sei,sei_hc3,dfi,_=ancova(interaction=True); ti=bi[3]/sei_hc3[3]
print('Efek kelompok tersesuaikan (HC3) = {:.2f}; 95% CI [{:.2f}, {:.2f}]; t({})={:.3f}; p={:.5f}; partial eta²={:.3f}'.format(
    b[1],b[1]-crit*se_hc3[1],b[1]+crit*se_hc3[1],df,t,t_p_two(t,df),t*t/(t*t+df)))
print('Uji interaksi kelompok × pre: b={:.3f}; t({})={:.3f}; p={:.4f}'.format(bi[3],dfi,ti,t_p_two(ti,dfi)))
sk,ku,jb,p_jb=shape_diagnostics(resid)
res_e=[e for i,e in zip(ids,resid) if pre_map[i]['kelompok']=='eksperimen']
res_c=[e for i,e in zip(ids,resid) if pre_map[i]['kelompok']=='kontrol']
bf_res=welch([abs(x-median(res_e)) for x in res_e], [abs(x-median(res_c)) for x in res_c])
print('Residual ANCOVA: skew={:.3f}; excess kurtosis={:.3f}; JB={:.3f}; p_JB={:.5f}'.format(sk,ku,jb,p_jb))
print('Brown–Forsythe residual: t={:.3f}; df={:.2f}; p={:.5f}'.format(bf_res['t'],bf_res['df'],bf_res['p']))
"""),
md("""## 6. Analisis lima aspek dan koreksi multipel

DiD dihitung untuk setiap aspek. Koreksi Holm menjaga family-wise error rate pada lima pengujian. Kolom `p_Holm` adalah p-value tersesuaikan; interpretasi aspek bersifat sekunder terhadap skor total.
"""),
code("""aspect_results=[]
for aspect in ASPECTS:
    ge=[]; gc=[]
    for group,target in [('eksperimen',ge),('kontrol',gc)]:
        a,b=paired_values(group,aspect); target.extend(y-x for x,y in zip(a,b))
    r=welch(ge,gc); aspect_results.append({'aspect':aspect,**r})

ordered=sorted(enumerate(aspect_results),key=lambda z:z[1]['p']); running=0
for rank,(idx,r) in enumerate(ordered):
    adjusted=min(1.0,(len(ordered)-rank)*r['p']); running=max(running,adjusted)
    aspect_results[idx]['p_holm']=running
rows=[[r['aspect'],r['diff'],r['ci_low'],r['ci_high'],r['p'],r['p_holm'],r['hedges_g']] for r in aspect_results]
print_table(['Aspek','DiD','CI low','CI high','p raw','p Holm','Hedges g'],rows)
"""),
md("""## 7. Uji ketahanan: permutasi perbedaan gain

Karena distribusi gain menyimpang dari normal, label kelompok dipermutasi 50.000 kali. Uji ini tidak bergantung pada asumsi normalitas, tetapi pada desain kelas yang tidak diacak tetap merupakan pemeriksaan ketahanan, bukan pengganti argumentasi desain.
"""),
code("""observed = mean(gains['eksperimen']) - mean(gains['kontrol'])
pooled = gains['eksperimen'] + gains['kontrol']
n_e = len(gains['eksperimen'])
rng = random.Random(20260619)
extreme = 0
B = 50000
for _ in range(B):
    shuffled = pooled[:]
    rng.shuffle(shuffled)
    statistic = mean(shuffled[:n_e]) - mean(shuffled[n_e:])
    extreme += abs(statistic) >= abs(observed)
p_permutation = (extreme + 1) / (B + 1)
print(f'DiD teramati       : {observed:.3f}')
print(f'Permutasi          : {B:,}')
print(f'p permutasi 2-arah : {p_permutation:.5f}')
"""),
md("""## 8. Aturan pelaporan dan keterbatasan

1. Gunakan **ANCOVA skor postes yang mengontrol pretes** sebagai estimasi utama, dengan DiD sebagai uji ketahanan. Jangan memilih uji hanya berdasarkan mana yang signifikan.
2. Laporkan estimasi, 95% CI, p-value, effect size, dan ukuran sampel; jangan berhenti pada “signifikan/tidak signifikan”.
3. Bila penilai pretes dan postes berbeda atau hanya satu skor per rekaman, **reliabilitas antarpenilai/ICC tidak dapat dihitung** dan efek waktu bercampur dengan efek penilai. Idealnya sebagian rekaman dinilai silang secara buta oleh ≥2 penilai dengan rubrik yang sama.
4. Desain menggunakan kelas yang sudah ada, sehingga asosiasi kausal tetap rentan terhadap confounding tingkat kelas, sejarah, kontaminasi perlakuan, dan ketidakacakan alokasi.
5. Alpha internal tidak menggantikan bukti validitas isi/struktur rubrik. Analisis aspek adalah sekunder dan telah dikoreksi dengan Holm.
6. Notebook tidak mengekspor data identitas. Saat publikasi, gunakan dataset terdeidentifikasi dan simpan data identitas pada akses terbatas.
"""),
]

namespace = {}
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
            cell["outputs"] = [{"name": "stdout", "output_type": "stream", "text": output.splitlines(True)}]
    except Exception as exc:
        cell["outputs"] = [{
            "ename": type(exc).__name__, "evalue": str(exc), "output_type": "error",
            "traceback": traceback.format_exc().splitlines(),
        }]
        raise

notebook = {
    "cells": cells,
    "metadata": {
        "kernelspec": {"display_name": "Python 3", "language": "python", "name": "python3"},
        "language_info": {"name": "python", "version": "3.12"},
    },
    "nbformat": 4,
    "nbformat_minor": 5,
}

target = Path("data/field_test/olahdata_keterampilan_berbicara.ipynb")
target.write_text(json.dumps(notebook, ensure_ascii=False, indent=1) + "\n", encoding="utf-8")
print(target)
