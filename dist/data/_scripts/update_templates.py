"""
Update semua template CSV dengan subjek riil dari QN_pemetaan_kelas.csv.
- Skor yang sudah ada (dari [v] files) dipertahankan
- Skor yang belum ada dikosongkan (user isi manual)
- nim dan nama ditambahkan ke semua template
"""

import csv, os

BASE = "/home/primandhika/artikel/dist/data"

# ─────────────────────────────────────────────
# LOAD REFERENSI
# ─────────────────────────────────────────────

def load_pemetaan():
    exp, ctrl = [], []
    with open(os.path.join(BASE, "QN_pemetaan_kelas.csv"), encoding="utf-8") as f:
        for row in csv.DictReader(f):
            if row["kelompok"] == "eksperimen":
                exp.append(row)
            else:
                ctrl.append(row)
    return exp, ctrl  # list of {id_kode, nim, kelompok, nama, pengajar}


def load_v_csv(path, id_field="id"):
    """Load [v] CSV ke dict {id_kode: row}"""
    d = {}
    if not os.path.exists(path): return d
    with open(path, encoding="utf-8") as f:
        for row in csv.DictReader(f):
            d[row[id_field]] = row
    return d


def write_csv(path, fieldnames, rows):
    with open(path, "w", newline="", encoding="utf-8") as f:
        w = csv.DictWriter(f, fieldnames=fieldnames, extrasaction="ignore")
        w.writeheader()
        w.writerows(rows)
    print(f"✅ {os.path.relpath(path, BASE)} ({len(rows)} baris)")


# ─────────────────────────────────────────────
# 1. field_test/keterampilan_berbicara.csv
# ─────────────────────────────────────────────

def update_keterampilan_berbicara():
    exp, ctrl = load_pemetaan()
    v = load_v_csv(os.path.join(BASE, "field_test", "[v]keterampilan_berbicara.csv"))

    fields = [
        "id", "nim", "nama", "kelompok",
        "pre_pengorganisasian", "pre_kejelasan", "pre_ketepatan",
        "pre_strategi", "pre_metakognitif", "pre_total",
        "post_pengorganisasian", "post_kejelasan", "post_ketepatan",
        "post_strategi", "post_metakognitif", "post_total",
    ]

    rows = []
    for student in exp + ctrl:
        id_kode = student["id_kode"]
        v_row = v.get(id_kode, {})
        rows.append({
            "id":      id_kode,
            "nim":     student["nim"],
            "nama":    student["nama"],
            "kelompok":student["kelompok"],
            # pre — ambil dari [v] jika ada
            "pre_pengorganisasian": v_row.get("pre_pengorganisasian", ""),
            "pre_kejelasan":        v_row.get("pre_kejelasan", ""),
            "pre_ketepatan":        v_row.get("pre_ketepatan", ""),
            "pre_strategi":         v_row.get("pre_strategi", ""),
            "pre_metakognitif":     v_row.get("pre_metakognitif", ""),
            "pre_total":            v_row.get("pre_total", ""),
            # post
            "post_pengorganisasian":v_row.get("post_pengorganisasian", ""),
            "post_kejelasan":       v_row.get("post_kejelasan", ""),
            "post_ketepatan":       v_row.get("post_ketepatan", ""),
            "post_strategi":        v_row.get("post_strategi", ""),
            "post_metakognitif":    v_row.get("post_metakognitif", ""),
            "post_total":           v_row.get("post_total", ""),
        })

    write_csv(os.path.join(BASE, "field_test", "keterampilan_berbicara.csv"), fields, rows)


# ─────────────────────────────────────────────
# 2. field_test/metakognitif.csv
# ─────────────────────────────────────────────

def update_metakognitif():
    exp, ctrl = load_pemetaan()
    v = load_v_csv(os.path.join(BASE, "field_test", "[v]metakognitif.csv"))

    fields = [
        "id", "nim", "nama", "kelompok",
        "pre_planning", "pre_monitoring", "pre_evaluation", "pre_integratif", "pre_total",
        "post_planning", "post_monitoring", "post_evaluation", "post_integratif", "post_total",
    ]
    # Note: template asli pakai awareness/evaluation/regulation
    # Di sini kita pakai nama konstruk aktual dari SoT (planning/monitoring/evaluation/integratif)

    rows = []
    for student in exp + ctrl:
        id_kode = student["id_kode"]
        v_row = v.get(id_kode, {})
        rows.append({
            "id":      id_kode,
            "nim":     student["nim"],
            "nama":    student["nama"],
            "kelompok":student["kelompok"],
            "pre_planning":    v_row.get("pre_planning", ""),
            "pre_monitoring":  v_row.get("pre_monitoring", ""),
            "pre_evaluation":  v_row.get("pre_evaluation_raw", ""),
            "pre_integratif":  v_row.get("pre_integratif", ""),
            "pre_total":       v_row.get("pre_total_skor", ""),
            "post_planning":   v_row.get("post_planning", ""),
            "post_monitoring": v_row.get("post_monitoring", ""),
            "post_evaluation": v_row.get("post_evaluation_raw", ""),
            "post_integratif": v_row.get("post_integratif", ""),
            "post_total":      v_row.get("post_total_skor", ""),
        })

    write_csv(os.path.join(BASE, "field_test", "metakognitif.csv"), fields, rows)


# ─────────────────────────────────────────────
# 3. field_test/respons_mahasiswa.csv
# Hanya kelompok eksperimen (pengguna platform Bicaranta)
# ─────────────────────────────────────────────

def update_respons_mahasiswa():
    exp, _ = load_pemetaan()
    v = load_v_csv(os.path.join(BASE, "field_test", "[v]respons_mahasiswa.csv"))

    fields = [
        "id", "nim", "nama",
        # 9 aspek sesuai angket media
        "kualitas_konten", "kemudahan_penggunaan", "keterlibatan",
        "dampak_berbicara", "dukungan_metakognisi",
        "microlearning", "aksesibilitas", "teknik_feynman", "refleksi_diri",
        "total_skor", "persentase", "kategori",
    ]

    rows = []
    for student in exp:
        id_kode = student["id_kode"]
        v_row = v.get(id_kode, {})
        rows.append({
            "id":   id_kode,
            "nim":  student["nim"],
            "nama": student["nama"],
            "kualitas_konten":       v_row.get("kualitas_isi", ""),
            "kemudahan_penggunaan":  v_row.get("kemudahan_penggunaan", ""),
            "keterlibatan":          v_row.get("interaktivitas", ""),
            "dampak_berbicara":      v_row.get("dampak_berbicara", ""),
            "dukungan_metakognisi":  v_row.get("dampak_metakognitif", ""),
            "microlearning":         v_row.get("microlearning", ""),
            "aksesibilitas":         v_row.get("aksesibilitas", ""),
            "teknik_feynman":        v_row.get("teknik_feynman", ""),
            "refleksi_diri":         v_row.get("refleksi_diri", ""),
            "total_skor":            v_row.get("total_skor", ""),
            "persentase":            v_row.get("persentase", ""),
            "kategori":              v_row.get("kategori", ""),
        })

    write_csv(os.path.join(BASE, "field_test", "respons_mahasiswa.csv"), fields, rows)


# ─────────────────────────────────────────────
# 4. uji_coba/small_group_prepost.csv  (n=10)
#    uji_coba/small_group_respons.csv  (n=10)
# ─────────────────────────────────────────────

def update_small_group():
    fields_pre = [
        "no", "id", "nim", "nama",
        "pre_pengorganisasian", "pre_kejelasan", "pre_ketepatan",
        "pre_strategi", "pre_metakognitif", "pre_total",
        "post_pengorganisasian", "post_kejelasan", "post_ketepatan",
        "post_strategi", "post_metakognitif", "post_total",
    ]
    rows_pre = [{
        "no": i, "id": f"UCS{i:02d}", "nim": "", "nama": "",
        **{c: "" for c in fields_pre[4:]}
    } for i in range(1, 11)]
    write_csv(os.path.join(BASE, "uji_coba", "small_group_prepost.csv"), fields_pre, rows_pre)

    fields_res = [
        "no", "id", "nim", "nama",
        "tampilan", "navigasi", "multimedia", "interaktivitas",
        "materi", "teknik_feynman", "total", "rata_rata", "persentase",
    ]
    rows_res = [{
        "no": i, "id": f"UCS{i:02d}", "nim": "", "nama": "",
        **{c: "" for c in fields_res[4:]}
    } for i in range(1, 11)]
    write_csv(os.path.join(BASE, "uji_coba", "small_group_respons.csv"), fields_res, rows_res)


# ─────────────────────────────────────────────
# 5. uji_coba/large_group_prepost.csv  (n=25)
#    uji_coba/large_group_respons.csv  (n=25)
# ─────────────────────────────────────────────

def update_large_group():
    fields_pre = [
        "no", "id", "nim", "nama",
        "pre_pengorganisasian", "pre_kejelasan", "pre_ketepatan",
        "pre_strategi", "pre_metakognitif", "pre_total",
        "post_pengorganisasian", "post_kejelasan", "post_ketepatan",
        "post_strategi", "post_metakognitif", "post_total",
    ]
    rows_pre = [{
        "no": i, "id": f"UCL{i:02d}", "nim": "", "nama": "",
        **{c: "" for c in fields_pre[4:]}
    } for i in range(1, 26)]
    write_csv(os.path.join(BASE, "uji_coba", "large_group_prepost.csv"), fields_pre, rows_pre)

    fields_res = [
        "no", "id", "nim", "nama",
        "tampilan", "navigasi", "multimedia", "interaktivitas",
        "materi", "teknik_feynman", "total", "rata_rata", "persentase",
    ]
    rows_res = [{
        "no": i, "id": f"UCL{i:02d}", "nim": "", "nama": "",
        **{c: "" for c in fields_res[4:]}
    } for i in range(1, 26)]
    write_csv(os.path.join(BASE, "uji_coba", "large_group_respons.csv"), fields_res, rows_res)


# ─────────────────────────────────────────────
# 6. kualitatif/wawancara.csv
# MHS_XX → E_XX (asumsi nomor urut = kode eksperimen)
# ─────────────────────────────────────────────

def update_wawancara():
    exp, _ = load_pemetaan()
    id_by_num = {int(s["id_kode"][1:]): s for s in exp}  # {1: row, 2: row, ...}

    fields = [
        "no", "tema", "subtema",
        "kode_subjek", "nim", "nama",
        "kutipan", "tanggal_wawancara", "catatan_analisis",
    ]

    # Data wawancara (tema + subtema dipertahankan, kode diganti)
    src_data = [
        (1,  "Peningkatan Kesadaran Metakognitif", "Kesadaran strategi berbicara",   3),
        (2,  "Peningkatan Kesadaran Metakognitif", "Self-monitoring",                9),
        (3,  "Peningkatan Kesadaran Metakognitif", "Identifikasi kelemahan",         14),
        (4,  "Kemudahan Akses dan Fleksibilitas",  "Belajar mandiri",                7),
        (5,  "Kemudahan Akses dan Fleksibilitas",  "Durasi konten",                  11),
        (6,  "Peningkatan Kepercayaan Diri",       "Pengurangan kecemasan",          5),
        (7,  "Peningkatan Kepercayaan Diri",       "Penguasaan materi",              22),
        (8,  "Pengorganisasian Ide Terstruktur",   "Kerangka berpikir",              8),
        (9,  "Pengorganisasian Ide Terstruktur",   "Urutan penyampaian",             19),
        (10, "Refleksi dan Evaluasi Diri",         "Fitur playback",                 12),
        (11, "Refleksi dan Evaluasi Diri",         "Perbaikan iteratif",             25),
        (12, "Refleksi dan Evaluasi Diri",         "Kesadaran kesalahan",            16),
    ]

    rows = []
    for no, tema, subtema, mhs_num in src_data:
        s = id_by_num.get(mhs_num)
        rows.append({
            "no":                no,
            "tema":              tema,
            "subtema":           subtema,
            "kode_subjek":       s["id_kode"] if s else f"E{mhs_num:02d}",
            "nim":               s["nim"] if s else "",
            "nama":              s["nama"] if s else "",
            "kutipan":           "",
            "tanggal_wawancara": "",
            "catatan_analisis":  "",
        })

    write_csv(os.path.join(BASE, "kualitatif", "wawancara.csv"), fields, rows)


# ─────────────────────────────────────────────
# MAIN
# ─────────────────────────────────────────────

if __name__ == "__main__":
    print("=" * 55)
    print("UPDATE TEMPLATE CSV → SUBJEK RIIL")
    print("=" * 55)

    print("\n[field_test]")
    update_keterampilan_berbicara()
    update_metakognitif()
    update_respons_mahasiswa()

    print("\n[uji_coba]")
    update_small_group()
    update_large_group()

    print("\n[kualitatif]")
    update_wawancara()

    print("\nSelesai.")
