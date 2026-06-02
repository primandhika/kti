"""
Script pemformatan data mentah [SoT] → template CSV field_test/

Aturan:
- Template kolom dipertahankan
- Kolom SoT yang tidak ada di template ditambahkan
- Output: [v]{nama_file}.csv
- [SoT] files dipindah ke _history/ setelah selesai
"""

import csv
import os
import shutil

BASE = "/home/primandhika/artikel/dist/data"
UNFORMATTED = os.path.join(BASE, "_unformatted")
HISTORY = os.path.join(BASE, "_history")

# ─────────────────────────────────────────────
# 1. LOAD DATA SUMBER
# ─────────────────────────────────────────────

def load_pemetaan():
    """Kembalikan: dict nim→{class, nama}, list_exp (ordered), list_ctrl (ordered)"""
    pemetaan = {}
    ordered_exp = []
    ordered_ctrl = []
    seen = set()
    with open(os.path.join(UNFORMATTED, "[SoT] pemetaan_kelas.csv"), encoding="utf-8-sig") as f:
        for row in csv.DictReader(f):
            nim = row["nim"].strip()
            cls = row["class"].strip()
            if nim not in seen:
                seen.add(nim)
                pemetaan[nim] = {"class": cls, "nama": row["responden"].strip()}
                if cls == "Experiment":
                    ordered_exp.append(nim)
                else:
                    ordered_ctrl.append(nim)
    return pemetaan, ordered_exp, ordered_ctrl


def load_rekap():
    """Kembalikan: dict {nim: {pre: row, post: row}}"""
    data = {}
    with open(os.path.join(UNFORMATTED, "[SoT] rekap-pretes-postes.csv"), encoding="utf-8-sig") as f:
        for row in csv.DictReader(f):
            nim = row["NIM"].strip()
            sesi = row["Sesi"].strip().strip('"')
            if nim not in data:
                data[nim] = {}
            if sesi == "Uji Awal":
                data[nim]["pre"] = row
            elif sesi == "Ujian Akhir Semester":
                data[nim]["post"] = row
    return data


def load_metakognitif():
    """Kembalikan: dict {nim: {pre: row, post: row}}"""
    data = {}
    with open(os.path.join(UNFORMATTED, "[SoT] angket-metakognitif.csv"), encoding="utf-8-sig") as f:
        for row in csv.DictReader(f):
            nim = row["NIM"].strip()
            sesi = row["Sesi"].strip().strip('"')
            if nim not in data:
                data[nim] = {}
            if sesi == "Uji Awal":
                data[nim]["pre"] = row
            elif sesi == "Ujian Akhir Semester":
                data[nim]["post"] = row
    return data


def load_media():
    """Kembalikan: dict {nim: {pre: row, post: row}}"""
    data = {}
    with open(os.path.join(UNFORMATTED, "[SoT] angket-media.csv"), encoding="utf-8-sig") as f:
        for row in csv.DictReader(f):
            nim = row["NIM"].strip()
            sesi = row["Sesi"].strip().strip('"')
            if nim not in data:
                data[nim] = {}
            if sesi == "Uji Awal":
                data[nim]["pre"] = row
            elif sesi == "Ujian Akhir Semester":
                data[nim]["post"] = row
    return data


def safe(row, key, default=""):
    if row is None:
        return default
    return row.get(key, default)


# ─────────────────────────────────────────────
# 2. KETERAMPILAN BERBICARA
# ─────────────────────────────────────────────

def format_keterampilan_berbicara():
    """
    Template: id, kelompok, pre_pengorganisasian, pre_kejelasan, pre_ketepatan,
              pre_strategi, pre_metakognitif, pre_total,
              post_pengorganisasian, post_kejelasan, post_ketepatan,
              post_strategi, post_metakognitif, post_total

    Pemetaan konstruk (template → SoT):
      pengorganisasian → Struktur (/15)
      kejelasan        → Artikulasi (/15)
      ketepatan        → Kebahasaan (/15)
      strategi         → Penyesuaian (/15)
      metakognitif     → Dampak (/15)
      total            → skor_mentah (sum, max 75) | nilai_akhir (0-100)

    Kolom tambahan dari SoT (ditambahkan setelah kolom template):
      nim, nama, topik_pre, topik_post,
      pre_nilai_akhir, post_nilai_akhir,
      pre_diksi, pre_gramatikal, pre_retorika,
      pre_artikulasi_sub, pre_intonasi, pre_kecepatan,
      pre_urutan, pre_transisi, pre_simpulan,
      pre_bahasa, pre_contoh, pre_formalitas,
      pre_persuasi, pre_perhatian, pre_inspirasi,
      pre_metode, pre_penilai,
      post_diksi, post_gramatikal, post_retorika,
      post_artikulasi_sub, post_intonasi, post_kecepatan,
      post_urutan, post_transisi, post_simpulan,
      post_bahasa, post_contoh, post_formalitas,
      post_persuasi, post_perhatian, post_inspirasi,
      post_metode, post_penilai
    """
    pemetaan, ordered_exp, ordered_ctrl = load_pemetaan()
    rekap = load_rekap()

    fieldnames = [
        # Template kolom
        "id", "kelompok",
        "pre_pengorganisasian", "pre_kejelasan", "pre_ketepatan",
        "pre_strategi", "pre_metakognitif", "pre_total",
        "post_pengorganisasian", "post_kejelasan", "post_ketepatan",
        "post_strategi", "post_metakognitif", "post_total",
        # Kolom tambahan SoT
        "nim", "nama",
        "topik_pre", "topik_post",
        "pre_nilai_akhir", "post_nilai_akhir",
        "pre_diksi", "pre_gramatikal", "pre_retorika",
        "pre_artikulasi_sub", "pre_intonasi", "pre_kecepatan",
        "pre_urutan", "pre_transisi", "pre_simpulan",
        "pre_bahasa", "pre_contoh", "pre_formalitas",
        "pre_persuasi", "pre_perhatian", "pre_inspirasi",
        "pre_metode", "pre_penilai",
        "post_diksi", "post_gramatikal", "post_retorika",
        "post_artikulasi_sub", "post_intonasi", "post_kecepatan",
        "post_urutan", "post_transisi", "post_simpulan",
        "post_bahasa", "post_contoh", "post_formalitas",
        "post_persuasi", "post_perhatian", "post_inspirasi",
        "post_metode", "post_penilai",
    ]

    rows = []
    e_idx = 0
    k_idx = 0

    all_students = [
        (nim, "eksperimen") for nim in ordered_exp
    ] + [
        (nim, "kontrol") for nim in ordered_ctrl
    ]

    for nim, kelompok in all_students:
        nim_data = rekap.get(nim, {})
        pre = nim_data.get("pre")
        post = nim_data.get("post")

        # skip jika tidak ada data sama sekali
        if not pre and not post:
            continue

        if kelompok == "eksperimen":
            e_idx += 1
            id_code = f"E{e_idx:02d}"
        else:
            k_idx += 1
            id_code = f"K{k_idx:02d}"

        nama = pemetaan[nim]["nama"]

        # Template kolom = nilai dimensi dari SoT
        # Catatan: max per dimensi = 15 (3 sub × max 5)
        row = {
            "id": id_code,
            "kelompok": kelompok,
            # pre: template mapping
            "pre_pengorganisasian": safe(pre, "Struktur (/15)"),
            "pre_kejelasan": safe(pre, "Artikulasi (/15)"),
            "pre_ketepatan": safe(pre, "Kebahasaan (/15)"),
            "pre_strategi": safe(pre, "Penyesuaian (/15)"),
            "pre_metakognitif": safe(pre, "Dampak (/15)"),
            "pre_total": safe(pre, "Nilai Akhir"),  # 0-100
            # post: template mapping
            "post_pengorganisasian": safe(post, "Struktur (/15)"),
            "post_kejelasan": safe(post, "Artikulasi (/15)"),
            "post_ketepatan": safe(post, "Kebahasaan (/15)"),
            "post_strategi": safe(post, "Penyesuaian (/15)"),
            "post_metakognitif": safe(post, "Dampak (/15)"),
            "post_total": safe(post, "Nilai Akhir"),  # 0-100
            # Tambahan
            "nim": nim,
            "nama": nama,
            "topik_pre": safe(pre, "Topik"),
            "topik_post": safe(post, "Topik"),
            "pre_nilai_akhir": safe(pre, "Nilai Akhir"),
            "post_nilai_akhir": safe(post, "Nilai Akhir"),
            "pre_diksi": safe(pre, "Diksi"),
            "pre_gramatikal": safe(pre, "Gramatikal"),
            "pre_retorika": safe(pre, "Retorika"),
            "pre_artikulasi_sub": safe(pre, "Artikulasi (sub)"),
            "pre_intonasi": safe(pre, "Intonasi"),
            "pre_kecepatan": safe(pre, "Kecepatan"),
            "pre_urutan": safe(pre, "Urutan"),
            "pre_transisi": safe(pre, "Transisi"),
            "pre_simpulan": safe(pre, "Simpulan"),
            "pre_bahasa": safe(pre, "Bahasa"),
            "pre_contoh": safe(pre, "Contoh"),
            "pre_formalitas": safe(pre, "Formalitas"),
            "pre_persuasi": safe(pre, "Persuasi"),
            "pre_perhatian": safe(pre, "Perhatian"),
            "pre_inspirasi": safe(pre, "Inspirasi"),
            "pre_metode": safe(pre, "Metode Penilaian"),
            "pre_penilai": safe(pre, "Penilai"),
            "post_diksi": safe(post, "Diksi"),
            "post_gramatikal": safe(post, "Gramatikal"),
            "post_retorika": safe(post, "Retorika"),
            "post_artikulasi_sub": safe(post, "Artikulasi (sub)"),
            "post_intonasi": safe(post, "Intonasi"),
            "post_kecepatan": safe(post, "Kecepatan"),
            "post_urutan": safe(post, "Urutan"),
            "post_transisi": safe(post, "Transisi"),
            "post_simpulan": safe(post, "Simpulan"),
            "post_bahasa": safe(post, "Bahasa"),
            "post_contoh": safe(post, "Contoh"),
            "post_formalitas": safe(post, "Formalitas"),
            "post_persuasi": safe(post, "Persuasi"),
            "post_perhatian": safe(post, "Perhatian"),
            "post_inspirasi": safe(post, "Inspirasi"),
            "post_metode": safe(post, "Metode Penilaian"),
            "post_penilai": safe(post, "Penilai"),
        }
        rows.append(row)

    out_path = os.path.join(BASE, "field_test", "[v]keterampilan_berbicara.csv")
    with open(out_path, "w", newline="", encoding="utf-8") as f:
        writer = csv.DictWriter(f, fieldnames=fieldnames)
        writer.writeheader()
        writer.writerows(rows)

    print(f"✅ Ditulis: {out_path} ({len(rows)} baris)")
    return rows


# ─────────────────────────────────────────────
# 3. METAKOGNITIF
# ─────────────────────────────────────────────

def format_metakognitif():
    """
    Template: id, kelompok,
              pre_awareness, pre_evaluation, pre_regulation, pre_total,
              post_awareness, post_evaluation, post_regulation, post_total

    Pemetaan konstruk (template → SoT):
      awareness   → Planning (/16)   [perencanaan/kesadaran pra-bicara]
      evaluation  → Evaluation (/16) [evaluasi pasca-bicara]
      regulation  → Monitoring (/16) [pemantauan selama bicara = regulasi proses]
      (tidak ada padanan untuk Integratif, ditambahkan sebagai kolom extra)
      total → Total Skor (/64)

    Kolom tambahan:
      nim, nama,
      pre_planning, pre_monitoring, pre_evaluation_raw, pre_integratif,
      pre_total_skor, pre_persentase, pre_kategori,
      post_planning, post_monitoring, post_evaluation_raw, post_integratif,
      post_total_skor, post_persentase, post_kategori,
      butir_pre_P1..P4, M5..M8, E9..E12, I13..I16 (jika ada pre)
      butir_post_P1..P4, M5..M8, E9..E12, I13..I16
    """
    pemetaan, ordered_exp, ordered_ctrl = load_pemetaan()
    meta = load_metakognitif()

    butir_pre  = [f"pre_{b}" for b in ["P1","P2","P3","P4","M5","M6","M7","M8","E9","E10","E11","E12","I13","I14","I15","I16"]]
    butir_post = [f"post_{b}" for b in ["P1","P2","P3","P4","M5","M6","M7","M8","E9","E10","E11","E12","I13","I14","I15","I16"]]

    fieldnames = [
        "id", "kelompok",
        "pre_awareness", "pre_evaluation", "pre_regulation", "pre_total",
        "post_awareness", "post_evaluation", "post_regulation", "post_total",
        # Tambahan
        "nim", "nama",
        "pre_planning", "pre_monitoring", "pre_evaluation_raw", "pre_integratif",
        "pre_total_skor", "pre_persentase", "pre_kategori",
        "post_planning", "post_monitoring", "post_evaluation_raw", "post_integratif",
        "post_total_skor", "post_persentase", "post_kategori",
    ] + butir_pre + butir_post

    rows = []
    e_idx = 0
    k_idx = 0

    all_students = [
        (nim, "eksperimen") for nim in ordered_exp
    ] + [
        (nim, "kontrol") for nim in ordered_ctrl
    ]

    for nim, kelompok in all_students:
        nim_data = meta.get(nim, {})
        pre = nim_data.get("pre")
        post = nim_data.get("post")

        if not pre and not post:
            continue

        if kelompok == "eksperimen":
            e_idx += 1
            id_code = f"E{e_idx:02d}"
        else:
            k_idx += 1
            id_code = f"K{k_idx:02d}"

        nama = pemetaan[nim]["nama"]

        row = {
            "id": id_code,
            "kelompok": kelompok,
            # Template mapping
            "pre_awareness":  safe(pre, "Planning (/16)"),
            "pre_evaluation": safe(pre, "Evaluation (/16)"),
            "pre_regulation": safe(pre, "Monitoring (/16)"),
            "pre_total":      safe(pre, "Total Skor (/64)"),
            "post_awareness":  safe(post, "Planning (/16)"),
            "post_evaluation": safe(post, "Evaluation (/16)"),
            "post_regulation": safe(post, "Monitoring (/16)"),
            "post_total":      safe(post, "Total Skor (/64)"),
            # Tambahan
            "nim": nim,
            "nama": nama,
            "pre_planning":       safe(pre, "Planning (/16)"),
            "pre_monitoring":     safe(pre, "Monitoring (/16)"),
            "pre_evaluation_raw": safe(pre, "Evaluation (/16)"),
            "pre_integratif":     safe(pre, "Integratif (/16)"),
            "pre_total_skor":     safe(pre, "Total Skor (/64)"),
            "pre_persentase":     safe(pre, "Persentase (%)"),
            "pre_kategori":       safe(pre, "Kategori"),
            "post_planning":       safe(post, "Planning (/16)"),
            "post_monitoring":     safe(post, "Monitoring (/16)"),
            "post_evaluation_raw": safe(post, "Evaluation (/16)"),
            "post_integratif":     safe(post, "Integratif (/16)"),
            "post_total_skor":     safe(post, "Total Skor (/64)"),
            "post_persentase":     safe(post, "Persentase (%)"),
            "post_kategori":       safe(post, "Kategori"),
        }

        # Butir pre
        for b in ["P1","P2","P3","P4","M5","M6","M7","M8","E9","E10","E11","E12","I13","I14","I15","I16"]:
            row[f"pre_{b}"] = safe(pre, b)

        # Butir post
        for b in ["P1","P2","P3","P4","M5","M6","M7","M8","E9","E10","E11","E12","I13","I14","I15","I16"]:
            row[f"post_{b}"] = safe(post, b)

        rows.append(row)

    out_path = os.path.join(BASE, "field_test", "[v]metakognitif.csv")
    with open(out_path, "w", newline="", encoding="utf-8") as f:
        writer = csv.DictWriter(f, fieldnames=fieldnames)
        writer.writeheader()
        writer.writerows(rows)

    print(f"✅ Ditulis: {out_path} ({len(rows)} baris)")
    return rows


# ─────────────────────────────────────────────
# 4. RESPONS MAHASISWA (angket media)
# ─────────────────────────────────────────────

def format_respons_mahasiswa():
    """
    Template: id, kualitas_isi, kemudahan_penggunaan, interaktivitas,
              dampak_berbicara, dampak_metakognitif, rata_rata

    Pemetaan konstruk (template → SoT aspek media):
      kualitas_isi        → Kualitas Konten (/8)
      kemudahan_penggunaan→ Kemudahan Penggunaan (/8)
      interaktivitas      → Keterlibatan (/8)
      dampak_berbicara    → Dampak Berbicara (/8)
      dampak_metakognitif → Dukungan Metakognisi (/8)
      rata_rata           → rata-rata dari 5 aspek di atas (skala /8 → 1-4 range)

    Kolom tambahan (9 aspek penuh + butir per aspek):
      nim, nama, sesi,
      microlearning, aksesibilitas, teknik_feynman, refleksi_diri,
      total_skor, persentase, kategori,
      K1, K2, Kem3_neg, Kem4, Ket5, Ket6,
      D7_neg, D8, DM9, DM10,
      ML11_neg, ML12, Ak13, Ak14_neg,
      F15, F16, R17_neg, R18

    Catatan: angket media hanya diisi kelompok eksperimen (pengguna platform).
    Kelompok kontrol yang mengisi angket dimasukkan tapi ditandai.
    """
    pemetaan, ordered_exp, ordered_ctrl = load_pemetaan()
    media = load_media()

    fieldnames = [
        "id", "kelompok",
        "kualitas_isi", "kemudahan_penggunaan", "interaktivitas",
        "dampak_berbicara", "dampak_metakognitif", "rata_rata",
        # Tambahan
        "nim", "nama", "sesi",
        "microlearning", "aksesibilitas", "teknik_feynman", "refleksi_diri",
        "total_skor", "persentase", "kategori",
        "K1", "K2", "Kem3_neg", "Kem4", "Ket5", "Ket6",
        "D7_neg", "D8", "DM9", "DM10",
        "ML11_neg", "ML12", "Ak13", "Ak14_neg",
        "F15", "F16", "R17_neg", "R18",
        "tanggal_isi",
    ]

    rows = []
    e_idx = 0
    k_idx = 0

    # Hanya proses post-test (UAS) sebagai data respons utama
    all_students = [
        (nim, "eksperimen") for nim in ordered_exp
    ] + [
        (nim, "kontrol") for nim in ordered_ctrl
    ]

    for nim, kelompok in all_students:
        nim_data = media.get(nim, {})
        post = nim_data.get("post")

        if not post:
            continue

        if kelompok == "eksperimen":
            e_idx += 1
            id_code = f"E{e_idx:02d}"
        else:
            k_idx += 1
            id_code = f"K{k_idx:02d}"

        nama = pemetaan[nim]["nama"]

        # rata_rata = mean dari 5 aspek template (skala /8)
        aspek_vals = []
        for col in ["Kualitas Konten (/8)", "Kemudahan Penggunaan (/8)",
                    "Keterlibatan (/8)", "Dampak Berbicara (/8)", "Dukungan Metakognisi (/8)"]:
            v = safe(post, col)
            if v:
                try:
                    aspek_vals.append(float(v))
                except ValueError:
                    pass

        rata = round(sum(aspek_vals) / len(aspek_vals), 2) if aspek_vals else ""

        row = {
            "id": id_code,
            "kelompok": kelompok,
            # Template mapping
            "kualitas_isi":         safe(post, "Kualitas Konten (/8)"),
            "kemudahan_penggunaan": safe(post, "Kemudahan Penggunaan (/8)"),
            "interaktivitas":       safe(post, "Keterlibatan (/8)"),
            "dampak_berbicara":     safe(post, "Dampak Berbicara (/8)"),
            "dampak_metakognitif":  safe(post, "Dukungan Metakognisi (/8)"),
            "rata_rata":            rata,
            # Tambahan
            "nim": nim,
            "nama": nama,
            "sesi": safe(post, "Sesi"),
            "microlearning":  safe(post, "Microlearning (/8)"),
            "aksesibilitas":  safe(post, "Aksesibilitas (/8)"),
            "teknik_feynman": safe(post, "Teknik Feynman (/8)"),
            "refleksi_diri":  safe(post, "Refleksi Diri (/8)"),
            "total_skor":     safe(post, "Total Skor (/72)"),
            "persentase":     safe(post, "Persentase (%)"),
            "kategori":       safe(post, "Kategori"),
            "K1":      safe(post, "K1"),
            "K2":      safe(post, "K2"),
            "Kem3_neg":safe(post, "Kem3*"),
            "Kem4":    safe(post, "Kem4"),
            "Ket5":    safe(post, "Ket5"),
            "Ket6":    safe(post, "Ket6"),
            "D7_neg":  safe(post, "D7*"),
            "D8":      safe(post, "D8"),
            "DM9":     safe(post, "DM9"),
            "DM10":    safe(post, "DM10"),
            "ML11_neg":safe(post, "ML11*"),
            "ML12":    safe(post, "ML12"),
            "Ak13":    safe(post, "Ak13"),
            "Ak14_neg":safe(post, "Ak14*"),
            "F15":     safe(post, "F15"),
            "F16":     safe(post, "F16"),
            "R17_neg": safe(post, "R17*"),
            "R18":     safe(post, "R18"),
            "tanggal_isi": safe(post, "Tanggal Isi"),
        }
        rows.append(row)

    out_path = os.path.join(BASE, "field_test", "[v]respons_mahasiswa.csv")
    with open(out_path, "w", newline="", encoding="utf-8") as f:
        writer = csv.DictWriter(f, fieldnames=fieldnames)
        writer.writeheader()
        writer.writerows(rows)

    print(f"✅ Ditulis: {out_path} ({len(rows)} baris)")
    return rows


# ─────────────────────────────────────────────
# 5. QN_pemetaan_kelas (kuantitatif identitas)
# ─────────────────────────────────────────────

def format_pemetaan():
    """Buat QN_pemetaan_kelas.csv dengan kode E/K yang terurut"""
    pemetaan, ordered_exp, ordered_ctrl = load_pemetaan()

    fieldnames = ["id_kode", "nim", "kelompok", "nama", "pengajar"]
    rows = []

    # Load nama pengajar dari raw
    teacher_map = {}
    with open(os.path.join(UNFORMATTED, "[SoT] pemetaan_kelas.csv"), encoding="utf-8-sig") as f:
        seen = set()
        for row in csv.DictReader(f):
            nim = row["nim"].strip()
            if nim not in seen:
                seen.add(nim)
                teacher_map[nim] = row["teacher"].strip()

    for i, nim in enumerate(ordered_exp, 1):
        rows.append({
            "id_kode":  f"E{i:02d}",
            "nim":      nim,
            "kelompok": "eksperimen",
            "nama":     pemetaan[nim]["nama"],
            "pengajar": teacher_map.get(nim, ""),
        })
    for i, nim in enumerate(ordered_ctrl, 1):
        rows.append({
            "id_kode":  f"K{i:02d}",
            "nim":      nim,
            "kelompok": "kontrol",
            "nama":     pemetaan[nim]["nama"],
            "pengajar": teacher_map.get(nim, ""),
        })

    out_path = os.path.join(BASE, "QN_pemetaan_kelas.csv")
    with open(out_path, "w", newline="", encoding="utf-8") as f:
        writer = csv.DictWriter(f, fieldnames=fieldnames)
        writer.writeheader()
        writer.writerows(rows)

    print(f"✅ Ditulis: {out_path} ({len(rows)} baris)")


# ─────────────────────────────────────────────
# 6. PINDAHKAN [SoT] ke _history
# ─────────────────────────────────────────────

def move_to_history():
    sot_files = [
        "[SoT] pemetaan_kelas.csv",
        "[SoT] rekap-pretes-postes.csv",
        "[SoT] angket-media.csv",
        "[SoT] angket-metakognitif.csv",
    ]
    for fname in sot_files:
        src = os.path.join(UNFORMATTED, fname)
        dst = os.path.join(HISTORY, fname)
        if os.path.exists(src):
            shutil.move(src, dst)
            print(f"📦 Dipindah → _history: {fname}")
        else:
            print(f"⚠️  Tidak ditemukan: {fname}")


# ─────────────────────────────────────────────
# MAIN
# ─────────────────────────────────────────────

if __name__ == "__main__":
    print("=" * 55)
    print("PEMFORMATAN DATA → TEMPLATE CSV")
    print("=" * 55)

    print("\n[1] Keterampilan Berbicara")
    rows_kb = format_keterampilan_berbicara()

    print("\n[2] Metakognitif")
    rows_mk = format_metakognitif()

    print("\n[3] Respons Mahasiswa (Angket Media)")
    rows_rm = format_respons_mahasiswa()

    print("\n[4] Pemetaan Kelas (QN)")
    format_pemetaan()

    print("\n[5] Pindahkan [SoT] ke _history/")
    move_to_history()

    print("\n" + "=" * 55)
    print("RINGKASAN")
    print("=" * 55)
    print(f"  keterampilan_berbicara : {len(rows_kb)} mahasiswa")
    print(f"  metakognitif           : {len(rows_mk)} mahasiswa")
    print(f"  respons_mahasiswa      : {len(rows_rm)} mahasiswa")
    print("=" * 55)
    print("Selesai.")
