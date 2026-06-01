#!/usr/bin/env python3
from pathlib import Path
import re
import subprocess


ROOT = Path(__file__).resolve().parents[1]
PDF = ROOT / "[Kemajuan] DRAF6 - Disertasi Restu (1).pdf"
OUT = ROOT / "outputs" / "Restu_Draf6"

CHAPTERS = [
    ("BAB_I.md", "BAB I", "BAB II"),
    ("BAB_II.md", "BAB II", "BAB III"),
]

LETTER_HEADINGS = {
    "A. Latar Belakang Masalah",
    "B. Identifikasi Masalah Penelitian",
    "C. Pembatasan Masalah Penelitian",
    "D. Perumusan Masalah Penelitian",
    "E. Tujuan Penelitian",
    "F. Kegunaan Hasil Penelitian",
    "A. Deskripsi Teori",
    "B. Penelitian yang Relevan",
    "C. Kerangka Berpikir",
    "D. Definisi Operasional",
}

NUMBER_HEADINGS = {
    "1. Keterampilan Berbicara",
    "2. Kemampuan Metakognitif",
    "3. Media Pembelajaran Berbasis Web",
    "4. Microlearning",
    "5. Teknik Feynman",
    "6. Cognitive Theory of Multimedia Learning (CTML)",
    "1. Kegunaan Teoretis",
    "2. Kegunaan Praktis",
    "1. Metacognitive Awareness",
    "2. Metacognitive Evaluation",
    "3. Metacognitive Regulation",
}


def extract_text() -> str:
    result = subprocess.run(
        ["pdftotext", "-layout", str(PDF), "-"],
        check=True,
        text=True,
        stdout=subprocess.PIPE,
    )
    return result.stdout.replace("\r\n", "\n")


def clean_line(line: str) -> str:
    line = line.replace("\f", "")
    line = line.replace("“", '"').replace("”", '"').replace("’", "'")
    line = line.replace("–", "-").replace("—", "-")
    return re.sub(r"\s+$", "", line)


def normalized(line: str) -> str:
    return re.sub(r"\s+", " ", line.strip())


def find_heading(lines: list[str], heading: str, start: int = 0) -> int:
    pattern = re.compile(r"^\s*" + re.escape(heading) + r"\s*$", re.I)
    for index in range(start, len(lines)):
        if pattern.match(lines[index]):
            return index
    raise ValueError(f"Heading not found: {heading}")


def is_noise(line: str) -> bool:
    text = line.strip()
    if not text:
        return False
    return bool(re.fullmatch(r"\d{1,3}", text) or re.fullmatch(r"[ivxlcdm]{1,8}", text, re.I))


def is_chapter(line: str) -> bool:
    return bool(re.fullmatch(r"BAB\s+[IVX]+", line.strip(), re.I))


def is_all_caps_title(line: str) -> bool:
    text = line.strip()
    if len(text) < 3 or len(text) > 90:
        return False
    letters = re.sub(r"[^A-Za-zÀ-ÿ]", "", text)
    return bool(letters) and text.upper() == text


def section_heading(line: str) -> str | None:
    text = normalized(line)

    if text in LETTER_HEADINGS:
        return "## " + text

    if text in NUMBER_HEADINGS:
        return "### " + text

    return None


def is_list_item(line: str) -> bool:
    text = normalized(line)
    if text in NUMBER_HEADINGS:
        return False
    if re.match(r"^(\d+\.|[a-z]\.)\s+\S", text):
        return True
    if re.match(r"^\d+\)\s+\S", text):
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
    expect_chapter_title = False
    last_text = ""

    for raw in lines:
        text = normalized(raw)

        if is_noise(raw):
            continue

        if not text:
            continue

        indent = len(raw) - len(raw.lstrip())
        paragraph_is_list = bool(paragraph and re.match(r"^(\d+\.|\d+\)|[a-z]\.)\s+\S", paragraph[0]))
        if paragraph_is_list and indent <= 4 and re.match(r"^[A-ZÀ-Ý]", text) and re.search(r'[.!?)]$', last_text):
            flush_paragraph(paragraph, output)

        if paragraph and indent >= 6 and re.search(r'[.!?:;)"\]]$', last_text):
            flush_paragraph(paragraph, output)

        if is_chapter(raw):
            flush_paragraph(paragraph, output)
            output.append(f"# {text.upper()}")
            output.append("")
            expect_chapter_title = True
            continue

        if expect_chapter_title and is_all_caps_title(raw):
            output.append(f"# {text}")
            output.append("")
            expect_chapter_title = False
            continue
        expect_chapter_title = False

        heading = section_heading(raw)
        if heading:
            flush_paragraph(paragraph, output)
            output.append(heading)
            output.append("")
            continue

        if is_list_item(raw):
            flush_paragraph(paragraph, output)
            paragraph.append(text)
            last_text = text
            continue

        paragraph.append(text)
        last_text = text

    flush_paragraph(paragraph, output)
    markdown = "\n".join(output).strip() + "\n"
    return re.sub(r"\n{3,}", "\n\n", markdown)


def main() -> None:
    lines = extract_text().splitlines()
    OUT.mkdir(parents=True, exist_ok=True)

    positions: dict[str, int] = {}
    cursor = 0
    for _, start, _ in CHAPTERS:
        positions[start] = find_heading(lines, start, cursor)
        cursor = positions[start] + 1

    for filename, start, end in CHAPTERS:
        start_index = positions[start]
        end_index = find_heading(lines, end, start_index + 1)
        target = OUT / filename
        target.write_text(to_markdown(lines[start_index:end_index]), encoding="utf-8")
        print(f"wrote {target}")


if __name__ == "__main__":
    main()
