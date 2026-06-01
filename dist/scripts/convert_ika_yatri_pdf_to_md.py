#!/usr/bin/env python3
from pathlib import Path
import re
import subprocess


ROOT = Path(__file__).resolve().parents[1]
PDF = ROOT / "reference" / "PENGEMBANGAN MEDIA PEMBELAJARAN IPS BERBASIS.pdf"
OUT = ROOT / "outputs" / "Ika_Yatri"

CHAPTERS = [
    ("BAB_I.md", "BAB I", "BAB II"),
    ("BAB_II.md", "BAB II", "BAB III"),
    ("BAB_III.md", "BAB III", "BAB IV"),
    ("BAB_IV.md", "BAB IV", "BAB V"),
    ("BAB_V.md", "BAB V", "DAFTAR PUSTAKA"),
    ("DAFTAR_PUSTAKA.md", "DAFTAR PUSTAKA", "Lampiran 1. Hasil Wawancara"),
]


def extract_text() -> str:
    result = subprocess.run(
        ["pdftotext", "-layout", str(PDF), "-"],
        check=True,
        text=True,
        stdout=subprocess.PIPE,
    )
    return result.stdout.replace("\r\n", "\n")


def find_heading(lines: list[str], heading: str, start: int = 0) -> int:
    pattern = re.compile(r"^\s*" + re.escape(heading) + r"\s*$", re.I)
    for index in range(start, len(lines)):
        if pattern.match(lines[index]):
            return index
    raise ValueError(f"Heading not found: {heading}")


def clean_line(line: str) -> str:
    line = line.replace("\f", "")
    line = line.replace("“", '"').replace("”", '"').replace("’", "'")
    line = line.replace("–", "-").replace("—", "-")
    return re.sub(r"\s+$", "", line)


def is_page_number(line: str) -> bool:
    text = line.strip()
    return bool(re.fullmatch(r"\d{1,3}", text))


def is_noise(line: str) -> bool:
    text = line.strip()
    if not text:
        return False
    if is_page_number(text):
        return True
    if re.fullmatch(r"[ivxlcdm]{1,8}", text, re.I):
        return True
    return False


def is_chapter(line: str) -> bool:
    return bool(re.fullmatch(r"BAB\s+[IVX]+", line.strip(), re.I))


def is_all_caps_title(line: str) -> bool:
    text = line.strip()
    if len(text) < 3 or len(text) > 90:
        return False
    letters = re.sub(r"[^A-Za-zÀ-ÿ]", "", text)
    return bool(letters) and text.upper() == text


def section_heading(line: str) -> str | None:
    indent = len(line) - len(line.lstrip())
    text = line.strip()
    if indent <= 8 and re.match(r"^[A-Z]\.\s+\S", text):
        return "## " + text
    if indent <= 4 and re.match(r"^\d+\.\s+\S", text) and len(text) <= 100:
        return "### " + text
    if indent <= 10 and re.match(r"^[a-z]\.\s+\S", text) and len(text) <= 50:
        return "#### " + text
    return None


def list_marker(line: str) -> bool:
    indent = len(line) - len(line.lstrip())
    text = line.strip()
    if indent > 4 and re.match(r"^(\d+\.|[a-z]\.)\s+\S", text):
        return True
    if re.match(r"^[a-z]\.\s+\S", text) and len(text) > 50:
        return True
    if re.match(r"^\d+\.\s+\S", text) and len(text) > 70 and "," in text[:60]:
        return True
    return False


def flush_paragraph(parts: list[str], output: list[str]) -> None:
    if not parts:
        return
    paragraph = " ".join(part.strip() for part in parts if part.strip())
    paragraph = re.sub(r"\s+", " ", paragraph).strip()
    if paragraph:
        output.append(paragraph)
        output.append("")
    parts.clear()


def to_markdown(raw_lines: list[str]) -> str:
    lines = [clean_line(line) for line in raw_lines]
    output: list[str] = []
    paragraph: list[str] = []
    skip_next_title = False

    for raw in lines:
        text = raw.strip()

        if is_noise(raw):
            continue

        if not text:
            flush_paragraph(paragraph, output)
            continue

        if is_chapter(raw):
            flush_paragraph(paragraph, output)
            output.append(f"# {text.upper()}")
            output.append("")
            skip_next_title = True
            continue

        if skip_next_title and is_all_caps_title(raw):
            output.append(f"# {text}")
            output.append("")
            skip_next_title = False
            continue
        skip_next_title = False

        if list_marker(raw):
            flush_paragraph(paragraph, output)
            output.append(re.sub(r"\s{2,}", " ", text))
            output.append("")
            continue

        heading = section_heading(raw)
        if heading:
            flush_paragraph(paragraph, output)
            output.append(heading)
            output.append("")
            continue

        paragraph.append(re.sub(r"\s{2,}", " ", text))

    flush_paragraph(paragraph, output)

    md = "\n".join(output).strip() + "\n"
    md = re.sub(r"\n{3,}", "\n\n", md)
    return md


def main() -> None:
    text = extract_text()
    lines = text.splitlines()
    OUT.mkdir(parents=True, exist_ok=True)

    positions: dict[str, int] = {}
    cursor = 0
    for _, start, _ in CHAPTERS:
        positions[start] = find_heading(lines, start, cursor)
        cursor = positions[start] + 1

    for filename, start, end in CHAPTERS:
        start_index = positions[start]
        try:
            end_index = find_heading(lines, end, start_index + 1)
        except ValueError:
            end_index = len(lines)
        markdown = to_markdown(lines[start_index:end_index])
        (OUT / filename).write_text(markdown, encoding="utf-8")
        print(f"wrote {OUT / filename}")


if __name__ == "__main__":
    main()
