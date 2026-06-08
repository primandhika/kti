import csv
from pathlib import Path


BASE = Path("data/field_test")
PLAIN = BASE / "keterampilan_berbicara.csv"
VERBOSE = BASE / "[v]keterampilan_berbicara.csv"

MERGED = BASE / "keterampilan_berbicara_gabungan.csv"
PRE_COMPLETE = BASE / "keterampilan_berbicara_pretes_lengkap.csv"
POST_COMPLETE = BASE / "keterampilan_berbicara_postes_lengkap.csv"


def read_rows(path):
    with path.open(newline="", encoding="utf-8") as handle:
        return list(csv.DictReader(handle))


def nonempty(value):
    return value not in (None, "")


def ordered_union(*field_lists):
    fields = []
    seen = set()
    for field_list in field_lists:
        for field in field_list:
            if field not in seen:
                fields.append(field)
                seen.add(field)
    return fields


def write_rows(path, fieldnames, rows):
    with path.open("w", newline="", encoding="utf-8") as handle:
        writer = csv.DictWriter(handle, fieldnames=fieldnames)
        writer.writeheader()
        writer.writerows(rows)


def phase_fields(fieldnames, phase):
    base = ["id", "nim", "nama", "kelompok"]
    prefix = f"{phase}_"
    fields = [field for field in fieldnames if field.startswith(prefix)]

    topik = f"topik_{phase}"
    if topik in fieldnames:
        fields.insert(0, topik)

    return ordered_union(base, fields)


def complete_for_phase(row, fields):
    return all(nonempty(row.get(field)) for field in fields)


def main():
    plain_rows = read_rows(PLAIN)
    verbose_rows = read_rows(VERBOSE)

    plain_fields = list(plain_rows[0].keys())
    verbose_fields = list(verbose_rows[0].keys())
    merged_fields = ordered_union(verbose_fields, plain_fields)

    by_id = {row["id"]: row for row in plain_rows}
    for row in verbose_rows:
        current = by_id.get(row["id"], {})
        merged = {}
        for field in merged_fields:
            verbose_value = row.get(field)
            plain_value = current.get(field)
            merged[field] = verbose_value if nonempty(verbose_value) else (plain_value or "")
        by_id[row["id"]] = merged

    plain_order = [row["id"] for row in plain_rows]
    extra_verbose_order = [row["id"] for row in verbose_rows if row["id"] not in set(plain_order)]
    merged_rows = [by_id[row_id] for row_id in plain_order + extra_verbose_order]

    for row in merged_rows:
        for field in merged_fields:
            row.setdefault(field, "")

    pre_fields = phase_fields(merged_fields, "pre")
    post_fields = phase_fields(merged_fields, "post")

    pre_rows = [
        {field: row.get(field, "") for field in pre_fields}
        for row in merged_rows
        if complete_for_phase(row, pre_fields)
    ]
    post_rows = [
        {field: row.get(field, "") for field in post_fields}
        for row in merged_rows
        if complete_for_phase(row, post_fields)
    ]

    write_rows(MERGED, merged_fields, merged_rows)
    write_rows(PRE_COMPLETE, pre_fields, pre_rows)
    write_rows(POST_COMPLETE, post_fields, post_rows)

    print(f"{MERGED}: {len(merged_rows)} rows, {len(merged_fields)} columns")
    print(f"{PRE_COMPLETE}: {len(pre_rows)} complete rows, {len(pre_fields)} columns")
    print(f"{POST_COMPLETE}: {len(post_rows)} complete rows, {len(post_fields)} columns")


if __name__ == "__main__":
    main()
