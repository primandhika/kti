from __future__ import annotations
import csv
import json
from pathlib import Path
from collections import defaultdict
import numpy as np

BASE = Path('/home/primandhika/artikel/dist/data')
OUT = Path('/home/primandhika/artikel/artikel_dist3/enhancing_speaking_E270826/notes/quick_verify_stats.json')


def read_csv(path: Path):
    with path.open(newline='', encoding='utf-8') as f:
        return list(csv.DictReader(f))


def mean_sd(values):
    arr = np.array(values, dtype=float)
    if len(arr) == 0:
        return None
    return {
        'n': int(arr.size),
        'mean': float(arr.mean()),
        'sd': float(arr.std(ddof=1)) if arr.size > 1 else 0.0,
        'min': float(arr.min()),
        'max': float(arr.max()),
    }


def group_stats(rows, value_key, group_key='kelompok'):
    groups = defaultdict(list)
    for r in rows:
        if r.get(value_key, '') != '':
            groups[r[group_key]].append(float(r[value_key]))
    return {g: mean_sd(v) for g, v in groups.items()}


pre = read_csv(BASE / 'field_test' / 'keterampilan_berbicara_pretes.csv')
post = read_csv(BASE / 'field_test' / 'keterampilan_berbicara_postes.csv')
meta = read_csv(BASE / 'field_test' / 'metakognitif.csv')
resp = read_csv(BASE / 'field_test' / 'respons_mahasiswa.csv')

pre_by_id = {r['id']: r for r in pre}
post_by_id = {r['id']: r for r in post}
common_ids = sorted(set(pre_by_id) & set(post_by_id))

speaking_gain_by_group = defaultdict(list)
for sid in common_ids:
    rpre = pre_by_id[sid]
    rpost = post_by_id[sid]
    assert rpre['kelompok'] == rpost['kelompok'], f'group mismatch for {sid}'
    g = rpre['kelompok']
    speaking_gain_by_group[g].append(float(rpost['post_nilai_akhir']) - float(rpre['pre_nilai_akhir']))

meta_gain_by_group = defaultdict(list)
for r in meta:
    g = r['kelompok']
    meta_gain_by_group[g].append(float(r['post_total']) - float(r['pre_total']))

payload = {
    'speaking_pre': group_stats(pre, 'pre_nilai_akhir'),
    'speaking_post': group_stats(post, 'post_nilai_akhir'),
    'speaking_gain': {g: mean_sd(v) for g, v in speaking_gain_by_group.items()},
    'speaking_counts': {
        'pre_rows': len(pre),
        'post_rows': len(post),
        'paired_ids': len(common_ids),
    },
    'metacognitive_total': {
        'pre': group_stats(meta, 'pre_total'),
        'post': group_stats(meta, 'post_total'),
        'gain': {g: mean_sd(v) for g, v in meta_gain_by_group.items()},
        'rows': len(meta),
    },
    'response_counts': {
        'rows': len(resp),
        'categories': {k: sum(1 for r in resp if r['kategori'] == k) for k in sorted(set(r['kategori'] for r in resp))},
    },
}

OUT.write_text(json.dumps(payload, indent=2), encoding='utf-8')
print(json.dumps(payload, indent=2))
