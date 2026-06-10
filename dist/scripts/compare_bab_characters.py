#!/usr/bin/env python3
from __future__ import annotations

from pathlib import Path


ROOT = Path(__file__).resolve().parents[1]

CHAPTERS = [
    ("Bab I", "BAB_I.md"),
    ("Bab II", "BAB_II.md"),
    ("Bab III", "BAB_III.md"),
    ("Bab IV", "BAB_IV.md"),
    ("Bab V", "BAB_V.md"),
]


def char_count(path: Path) -> int:
    return len(path.read_text(encoding="utf-8"))


def percent_difference(main_count: int, reference_count: int) -> str:
    if reference_count == 0:
        return "n/a"
    diff = ((main_count - reference_count) / reference_count) * 100
    return f"{diff:+.2f}%"


def format_number(value: int) -> str:
    return f"{value:,}".replace(",", ".")


def main() -> int:
    rows: list[tuple[str, int, int, str]] = []
    missing: list[Path] = []

    for chapter, filename in CHAPTERS:
        main_path = ROOT / "main" / filename
        reference_path = ROOT / "reference" / "Ika_Yatri" / filename

        if not main_path.exists():
            missing.append(main_path)
        if not reference_path.exists():
            missing.append(reference_path)
        if missing:
            continue

        main_count = char_count(main_path)
        reference_count = char_count(reference_path)
        rows.append(
            (
                chapter,
                main_count,
                reference_count,
                percent_difference(main_count, reference_count),
            )
        )

    if missing:
        print("File tidak ditemukan:")
        for path in missing:
            print(f"- {path.relative_to(ROOT)}")
        return 1

    headers = ("Bab", "Main", "Reference", "% Beda")
    table_rows = [
        (chapter, format_number(main_count), format_number(reference_count), percent)
        for chapter, main_count, reference_count, percent in rows
    ]

    totals = (
        "Total",
        sum(row[1] for row in rows),
        sum(row[2] for row in rows),
        percent_difference(sum(row[1] for row in rows), sum(row[2] for row in rows)),
    )
    table_rows.append(
        (
            totals[0],
            format_number(totals[1]),
            format_number(totals[2]),
            totals[3],
        )
    )

    widths = [
        max(len(str(row[i])) for row in (headers, *table_rows))
        for i in range(len(headers))
    ]

    def print_row(row: tuple[str, str, str, str]) -> None:
        print(
            f"{row[0]:<{widths[0]}}  "
            f"{row[1]:>{widths[1]}}  "
            f"{row[2]:>{widths[2]}}  "
            f"{row[3]:>{widths[3]}}"
        )

    print("Komparasi jumlah karakter Bab I-V")
    print("Persentase = (main - reference) / reference")
    print()
    print_row(headers)
    print("  ".join("-" * width for width in widths))
    for row in table_rows:
        print_row(row)

    return 0


if __name__ == "__main__":
    raise SystemExit(main())
