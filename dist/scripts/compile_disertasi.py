#!/usr/bin/env python3
"""Compile the dissertation Markdown sources to styled DOCX and PDF.

The formatting contract lives in FORMAT.md.  This compiler intentionally uses
only dependencies already available in the project environment: Pandoc, lxml,
and (for PDF) LibreOffice.
"""

from __future__ import annotations

import argparse
import re
import shutil
import subprocess
import sys
import tempfile
import zipfile
from pathlib import Path

from lxml import etree


ROOT = Path(__file__).resolve().parents[1]
MAIN = ROOT / "main"
IMG = ROOT / "img"
DEFAULT_OUTPUT = ROOT / "outputs"
DEFAULT_NAME = "disertasi-restu"

FRONT = [
    "00_Cover.md",
    "00_Lembar_Persetujuan.md",
    "00_Kata_Pengantar.md",
    "00_Daftar_Isi.md",
]
CHAPTERS = [
    "01_BAB_I_PENDAHULUAN.md",
    "01_BAB_II_TINJAUAN_TEORI_KERANGKA_TEORETIK.md",
    "01_BAB_III_METODOLOGI_PENELITIAN.md",
    "01_BAB_IV_TEMUAN_DAN_PEMBAHASAN.md",
    "01_BAB_V_KESIMPULAN_IMPLIKASI_DAN_SARAN.md",
]
BIBLIOGRAPHY = "01_DAFTAR_PUSTAKA.md"

MARKER_PREFIX = "COMPILER_9281_"

W = "http://schemas.openxmlformats.org/wordprocessingml/2006/main"
R = "http://schemas.openxmlformats.org/officeDocument/2006/relationships"
REL = "http://schemas.openxmlformats.org/package/2006/relationships"
CT = "http://schemas.openxmlformats.org/package/2006/content-types"
NS = {"w": W, "r": R}


def qn(ns: str, name: str) -> str:
    return f"{{{ns}}}{name}"


def run(cmd: list[str], *, cwd: Path = ROOT) -> None:
    printable = " ".join(str(x) for x in cmd)
    print(f"[run] {printable}")
    completed = subprocess.run(cmd, cwd=cwd, text=True, capture_output=True)
    if completed.stdout.strip():
        print(completed.stdout.rstrip())
    if completed.stderr.strip():
        print(completed.stderr.rstrip(), file=sys.stderr)
    if completed.returncode:
        raise RuntimeError(f"Command failed ({completed.returncode}): {printable}")


def require_tool(name: str, explicit: str | None = None) -> str:
    candidate = explicit or shutil.which(name)
    if not candidate:
        raise RuntimeError(f"Required tool not found: {name}")
    return candidate


def nonempty_appendices() -> tuple[list[Path], list[Path]]:
    all_files = sorted(MAIN.glob("02_Lampiran_*.md"))
    return ([p for p in all_files if p.stat().st_size], [p for p in all_files if not p.stat().st_size])


def validate_manifest(include_appendices: bool) -> list[Path]:
    paths = [MAIN / name for name in FRONT + CHAPTERS + [BIBLIOGRAPHY]]
    missing = [p for p in paths if not p.is_file()]
    if missing:
        raise RuntimeError("Missing required input:\n  " + "\n  ".join(map(str, missing)))
    if include_appendices:
        populated, empty = nonempty_appendices()
        paths.extend(populated)
        if empty:
            print(f"[warn] skipping {len(empty)} empty appendix file(s)", file=sys.stderr)
    return paths


def normalize_bibliography(text: str) -> str:
    lines = text.splitlines()
    if lines and lines[0].strip().upper() == "DAFTAR PUSTAKA":
        lines = lines[1:]
    entries: list[str] = []
    current: list[str] = []
    for raw in lines:
        if not raw.strip():
            if current:
                entries.append(" ".join(current))
                current = []
            continue
        continuation = raw.startswith((" ", "\t"))
        if not continuation and current:
            entries.append(" ".join(current))
            current = []
        current.append(raw.strip())
    if current:
        entries.append(" ".join(current))
    return "# DAFTAR PUSTAKA\n\n" + "\n\n".join(entries) + "\n"


def resolve_illustrations(text: str) -> str:
    pattern = re.compile(r"^\*\[Ilustrasi 4\.(\d+):\s*([^]]+)\]\*\s*$", re.MULTILINE)

    def replacement(match: re.Match[str]) -> str:
        number = int(match.group(1))
        candidates = sorted(p for p in IMG.glob(f"gambar_4_{number:02d}_*") if p.stat().st_size)
        if not candidates:
            print(f"[warn] image for Ilustrasi 4.{number} was not found", file=sys.stderr)
            return match.group(0)
        relative = candidates[0].relative_to(ROOT).as_posix()
        return f"![{match.group(2)}]({relative})"

    return pattern.sub(replacement, text)


def marker(value: str) -> str:
    return f"\n\n{MARKER_PREFIX}{value}\n\n"


def normalize_single_chapter_heading(text: str) -> str:
    """Merge the opening chapter number and title into one two-line Heading 1."""
    return re.sub(
        r"\A# (BAB [IVXLCDM]+)\n# ([^\n]+)",
        r"# \1 `<w:br/>`{=openxml} \2",
        text,
        count=1,
    )


def build_combined(paths: list[Path], *, standalone: bool = False) -> str:
    chunks: list[str] = []
    for index, path in enumerate(paths):
        name = path.name
        text = path.read_text(encoding="utf-8").strip() + "\n"
        text = resolve_illustrations(text)
        if standalone and name in CHAPTERS:
            text = normalize_single_chapter_heading(text)

        if standalone and name in CHAPTERS:
            boundary = ""
            style = "STYLE_BODY"
        elif name == BIBLIOGRAPHY:
            text = normalize_bibliography(text)
            boundary = "SECTION_BODY_TO_REGULAR"
            style = "STYLE_BIBLIOGRAPHY"
        elif name == "00_Cover.md":
            boundary = ""
            style = "STYLE_COVER"
        elif name == "00_Lembar_Persetujuan.md":
            boundary = "SECTION_COVER_END"
            style = "STYLE_APPROVAL"
        elif name in {"00_Kata_Pengantar.md", "00_Daftar_Isi.md"}:
            boundary = "PAGE"
            style = "STYLE_FRONT"
        elif name == CHAPTERS[0]:
            boundary = "SECTION_FRONT_END"
            style = "STYLE_BODY"
        elif name == CHAPTERS[1]:
            boundary = "SECTION_BODY_START"
            style = "STYLE_BODY"
        elif name in CHAPTERS[2:]:
            boundary = "SECTION_BODY_CONTINUE"
            style = "STYLE_BODY"
        elif name.startswith("02_Lampiran_"):
            boundary = "PAGE"
            style = "STYLE_APPENDIX"
        else:
            boundary = "PAGE" if index else ""
            style = "STYLE_BODY"

        if boundary:
            chunks.append(marker(boundary))
        chunks.append(marker(style))
        chunks.append(text)
    return "\n".join(chunks)


def child(parent: etree._Element, tag: str) -> etree._Element:
    found = parent.find(f"w:{tag}", NS)
    if found is None:
        found = etree.SubElement(parent, qn(W, tag))
    return found


def set_attr(element: etree._Element, **attrs: str) -> None:
    for key, value in attrs.items():
        element.set(qn(W, key), str(value))


def sort_rpr(rpr: etree._Element) -> None:
    order = ["rStyle", "rFonts", "b", "bCs", "i", "iCs", "caps", "smallCaps", "strike", "dstrike", "outline", "shadow", "emboss", "imprint", "noProof", "snapToGrid", "color", "spacing", "w", "kern", "position", "sz", "szCs", "highlight", "u", "effect", "bdr", "shd", "fitText", "vertAlign", "rtl", "cs", "em", "lang", "eastAsianLayout", "specVanish", "oMath"]
    def get_index(tag: str) -> int:
        local = tag.split("}")[-1]
        try: return order.index(local)
        except ValueError: return 999
    children = list(rpr)
    children.sort(key=lambda c: get_index(c.tag))
    for c in children:
        rpr.append(c)

def style_by_id(styles: etree._Element, style_id: str) -> etree._Element | None:
    return styles.find(f"w:style[@w:styleId='{style_id}']", NS)


def set_run_format(style: etree._Element, size: int, *, bold: bool = False) -> None:
    rpr = child(style, "rPr")
    fonts = child(rpr, "rFonts")
    set_attr(fonts, ascii="Times New Roman", hAnsi="Times New Roman", eastAsia="Times New Roman", cs="Times New Roman")
    set_attr(child(rpr, "sz"), val=str(size * 2))
    set_attr(child(rpr, "szCs"), val=str(size * 2))
    color = child(rpr, "color")
    set_attr(color, val="000000")
    for attribute in ("themeColor", "themeTint", "themeShade"):
        color.attrib.pop(qn(W, attribute), None)
    b = rpr.find("w:b", NS)
    if bold and b is None:
        etree.SubElement(rpr, qn(W, "b"))
    elif not bold and b is not None:
        rpr.remove(b)
    sort_rpr(rpr)


def set_paragraph_format(
    style: etree._Element,
    *,
    align: str,
    line: int,
    before: int = 0,
    after: int = 0,
    left: int = 0,
    first_line: int = 0,
    hanging: int = 0,
    keep_next: bool = False,
) -> None:
    ppr = child(style, "pPr")
    set_attr(child(ppr, "jc"), val=align)
    spacing = child(ppr, "spacing")
    set_attr(spacing, before=str(before), after=str(after), line=str(line), lineRule="auto")
    ind = child(ppr, "ind")
    set_attr(ind, left=str(left), firstLine=str(first_line), hanging=str(hanging))
    if keep_next and ppr.find("w:keepNext", NS) is None:
        etree.SubElement(ppr, qn(W, "keepNext"))
    if ppr.find("w:keepLines", NS) is None:
        etree.SubElement(ppr, qn(W, "keepLines"))


def patch_reference(base: Path, output: Path) -> None:
    with zipfile.ZipFile(base) as source:
        files = {name: source.read(name) for name in source.namelist()}
    styles = etree.fromstring(files["word/styles.xml"])

    defaults = styles.find("w:docDefaults/w:rPrDefault/w:rPr", NS)
    if defaults is not None:
        fonts = child(defaults, "rFonts")
        set_attr(fonts, ascii="Times New Roman", hAnsi="Times New Roman", eastAsia="Times New Roman", cs="Times New Roman")
        set_attr(child(defaults, "sz"), val="24")
        set_attr(child(defaults, "szCs"), val="24")

    for style_id in ("Normal", "BodyText", "FirstParagraph"):
        style = style_by_id(styles, style_id)
        if style is not None:
            set_run_format(style, 12)
            set_paragraph_format(style, align="both", line=480, first_line=720)

    specs = {
        "Heading1": (14, 0, 0, 360),
        "Heading2": (12, 0, 240, 240),
        "Heading3": (12, 360, 240, 240),
        "Heading4": (12, 720, 0, 240),
        "Heading5": (12, 1080, 0, 240),
    }
    for style_id, (size, left, before, line) in specs.items():
        style = style_by_id(styles, style_id)
        if style is not None:
            set_run_format(style, size, bold=style_id != "Heading5")
            set_paragraph_format(style, align="center" if style_id == "Heading1" else "left", line=line, before=before, after=240 if style_id in {"Heading2", "Heading3", "Heading4"} else 0, left=left, keep_next=True)

    caption = style_by_id(styles, "Caption")
    if caption is not None:
        set_run_format(caption, 12)
        set_paragraph_format(caption, align="center", line=240, keep_next=True)

    bibliography = style_by_id(styles, "Bibliography")
    if bibliography is not None:
        set_run_format(bibliography, 12)
        set_paragraph_format(bibliography, align="left", line=300, after=160, left=480, hanging=480)

    files["word/styles.xml"] = etree.tostring(styles, xml_declaration=True, encoding="UTF-8", standalone="yes")
    with zipfile.ZipFile(output, "w", zipfile.ZIP_DEFLATED) as target:
        for name, data in files.items():
            target.writestr(name, data)


def paragraph_text(p: etree._Element) -> str:
    return "".join(p.xpath(".//w:t/text()", namespaces=NS)).strip()


def set_p_style(p: etree._Element, style_id: str) -> None:
    ppr = p.find("w:pPr", NS)
    if ppr is None:
        ppr = etree.Element(qn(W, "pPr"))
        p.insert(0, ppr)
    pstyle = child(ppr, "pStyle")
    set_attr(pstyle, val=style_id)


def page_layout(sect: etree._Element) -> None:
    set_attr(child(sect, "pgSz"), w="11906", h="16838")
    set_attr(child(sect, "pgMar"), top="2268", right="1134", bottom="1701", left="2268", header="720", footer="720", gutter="0")


def relation_element(rel_id: str, rel_type: str, target: str) -> etree._Element:
    el = etree.Element(qn(REL, "Relationship"))
    el.set("Id", rel_id)
    el.set("Type", f"http://schemas.openxmlformats.org/officeDocument/2006/relationships/{rel_type}")
    el.set("Target", target)
    return el


def page_field_part(tag: str, alignment: str) -> bytes:
    root = etree.Element(qn(W, tag), nsmap={"w": W})
    p = etree.SubElement(root, qn(W, "p"))
    ppr = etree.SubElement(p, qn(W, "pPr"))
    set_attr(etree.SubElement(ppr, qn(W, "jc")), val=alignment)
    run_el = etree.SubElement(p, qn(W, "r"))
    rpr = etree.SubElement(run_el, qn(W, "rPr"))
    fonts = etree.SubElement(rpr, qn(W, "rFonts"))
    set_attr(fonts, ascii="Times New Roman", hAnsi="Times New Roman")
    set_attr(etree.SubElement(rpr, qn(W, "sz")), val="24")
    for field_type, value in (("begin", None), (None, " PAGE "), ("end", None)):
        r = etree.SubElement(p, qn(W, "r"))
        if field_type:
            fld = etree.SubElement(r, qn(W, "fldChar"))
            set_attr(fld, fldCharType=field_type)
        else:
            instr = etree.SubElement(r, qn(W, "instrText"))
            instr.set("{http://www.w3.org/XML/1998/namespace}space", "preserve")
            instr.text = value
    return etree.tostring(root, xml_declaration=True, encoding="UTF-8", standalone="yes")


def section(kind: str) -> etree._Element:
    sect = etree.Element(qn(W, "sectPr"))
    page_layout(sect)
    set_attr(child(sect, "type"), val="nextPage")
    if kind == "cover":
        return sect
    if kind == "front":
        ref = etree.Element(qn(W, "footerReference"))
        set_attr(ref, type="default")
        ref.set(qn(R, "id"), "rIdCompilerFrontFooter")
        sect.insert(0, ref)
        set_attr(child(sect, "pgNumType"), fmt="lowerRoman", start="1")
        return sect
    header = etree.Element(qn(W, "headerReference"))
    set_attr(header, type="default")
    header.set(qn(R, "id"), "rIdCompilerHeader")
    sect.insert(0, header)
    if kind in {"body_start", "body_continue"}:
        footer = etree.Element(qn(W, "footerReference"))
        set_attr(footer, type="first")
        footer.set(qn(R, "id"), "rIdCompilerFirstFooter")
        sect.insert(1, footer)
        etree.SubElement(sect, qn(W, "titlePg"))
    pgnum = child(sect, "pgNumType")
    set_attr(pgnum, fmt="decimal")
    if kind == "body_start":
        set_attr(pgnum, start="1")
    return sect


def replace_with_section(p: etree._Element, kind: str) -> None:
    for item in list(p):
        p.remove(item)
    ppr = etree.SubElement(p, qn(W, "pPr"))
    ppr.append(section(kind))


def replace_with_page_break(p: etree._Element) -> None:
    for item in list(p):
        p.remove(item)
    r = etree.SubElement(p, qn(W, "r"))
    br = etree.SubElement(r, qn(W, "br"))
    set_attr(br, type="page")


def replace_with_toc(p: etree._Element) -> None:
    for item in list(p):
        p.remove(item)
    simple = etree.SubElement(p, qn(W, "fldSimple"))
    set_attr(simple, instr='TOC \\o "1-3" \\h \\z \\u')
    r = etree.SubElement(simple, qn(W, "r"))
    t = etree.SubElement(r, qn(W, "t"))
    t.text = "Daftar isi diperbarui saat dokumen dibuka."


def force_run_font(container: etree._Element, size_half_points: str = "24") -> None:
    for run_el in container.xpath(".//w:r", namespaces=NS):
        rpr = run_el.find("w:rPr", NS)
        if rpr is None:
            rpr = etree.Element(qn(W, "rPr"))
            run_el.insert(0, rpr)
        fonts = child(rpr, "rFonts")
        set_attr(fonts, ascii="Times New Roman", hAnsi="Times New Roman", eastAsia="Times New Roman", cs="Times New Roman")
        set_attr(child(rpr, "sz"), val=size_half_points)
        set_attr(child(rpr, "szCs"), val=size_half_points)
        sort_rpr(rpr)


def patch_document(raw_docx: Path, output: Path) -> None:
    with zipfile.ZipFile(raw_docx) as source:
        files = {name: source.read(name) for name in source.namelist()}
    document = etree.fromstring(files["word/document.xml"])
    body = document.find("w:body", NS)
    if body is None:
        raise RuntimeError("Invalid DOCX: word/document.xml has no body")

    mode = "body"
    boundary_map = {
        "SECTION_COVER_END": "cover",
        "SECTION_FRONT_END": "front",
        "SECTION_BODY_START": "body_start",
        "SECTION_BODY_CONTINUE": "body_continue",
        "SECTION_BODY_TO_REGULAR": "body_continue",
    }
    for element in list(body):
        if element.tag != qn(W, "p"):
            if mode == "cover":
                force_run_font(element)
            continue
        text = paragraph_text(element)
        if text.startswith(MARKER_PREFIX):
            code = text.removeprefix(MARKER_PREFIX)
            if code.startswith("STYLE_"):
                mode = code.removeprefix("STYLE_").lower()
                body.remove(element)
            elif code == "PAGE":
                replace_with_page_break(element)
            elif code == "AUTO_TOC":
                replace_with_toc(element)
            elif code in boundary_map:
                kind = boundary_map[code]
                if code == "SECTION_FRONT_END":
                    kind = "front"
                replace_with_section(element, kind)
            continue

        upper = text.upper()
        if mode == "cover":
            ppr = element.find("w:pPr", NS)
            if ppr is None:
                ppr = etree.Element(qn(W, "pPr"))
                element.insert(0, ppr)
            set_attr(child(ppr, "jc"), val="center")
            set_attr(child(ppr, "ind"), firstLine="0", left="0")
            pstyle = element.find("w:pPr/w:pStyle", NS)
            is_heading = pstyle is not None and pstyle.get(qn(W, "val")) == "Heading1"
            force_run_font(element, "28" if is_heading else "24")
        elif mode == "approval":
            if True:
                ppr = element.find("w:pPr", NS)
                if ppr is None:
                    ppr = etree.Element(qn(W, "pPr"))
                    element.insert(0, ppr)
                set_attr(child(ppr, "jc"), val="center")
                set_attr(child(ppr, "ind"), firstLine="0", left="0")
        elif mode == "bibliography" and upper != "DAFTAR PUSTAKA":
            set_p_style(element, "Bibliography")

        if re.match(r"^(TABEL|GAMBAR)\s+\d", upper):
            set_p_style(element, "Caption")
        if upper in {"KATA PENGANTAR", "DAFTAR ISI", "LEMBAR PERSETUJUAN", "DAFTAR PUSTAKA"}:
            for node in element.xpath(".//w:t", namespaces=NS):
                if node.text:
                    node.text = node.text.upper()
            set_p_style(element, "Heading1")
            force_run_font(element, "24")

    # Normalize table typography and spacing.
    for table in body.xpath(".//w:tbl", namespaces=NS):
        force_run_font(table, "24")
        for p in table.xpath(".//w:p", namespaces=NS):
            ppr = p.find("w:pPr", NS)
            if ppr is None:
                ppr = etree.Element(qn(W, "pPr"))
                p.insert(0, ppr)
            set_attr(child(ppr, "spacing"), before="0", after="0", line="240", lineRule="auto")
            set_attr(child(ppr, "ind"), firstLine="0")

    final_sect = body.find("w:sectPr", NS)
    if final_sect is None:
        final_sect = etree.SubElement(body, qn(W, "sectPr"))
    for item in list(final_sect):
        final_sect.remove(item)
    template = section("regular")
    for item in list(template):
        final_sect.append(item)

    files["word/document.xml"] = etree.tostring(document, xml_declaration=True, encoding="UTF-8", standalone="yes")

    rels = etree.fromstring(files["word/_rels/document.xml.rels"])
    rels.append(relation_element("rIdCompilerHeader", "header", "header-compiler.xml"))
    rels.append(relation_element("rIdCompilerFrontFooter", "footer", "footer-front-compiler.xml"))
    rels.append(relation_element("rIdCompilerFirstFooter", "footer", "footer-first-compiler.xml"))
    files["word/_rels/document.xml.rels"] = etree.tostring(rels, xml_declaration=True, encoding="UTF-8", standalone="yes")
    files["word/header-compiler.xml"] = page_field_part("hdr", "right")
    files["word/footer-front-compiler.xml"] = page_field_part("ftr", "center")
    files["word/footer-first-compiler.xml"] = page_field_part("ftr", "center")

    content_types = etree.fromstring(files["[Content_Types].xml"])
    for part, content_type in (
        ("/word/header-compiler.xml", "application/vnd.openxmlformats-officedocument.wordprocessingml.header+xml"),
        ("/word/footer-front-compiler.xml", "application/vnd.openxmlformats-officedocument.wordprocessingml.footer+xml"),
        ("/word/footer-first-compiler.xml", "application/vnd.openxmlformats-officedocument.wordprocessingml.footer+xml"),
    ):
        override = etree.Element(qn(CT, "Override"))
        override.set("PartName", part)
        override.set("ContentType", content_type)
        content_types.append(override)
    files["[Content_Types].xml"] = etree.tostring(content_types, xml_declaration=True, encoding="UTF-8", standalone="yes")

    settings = etree.fromstring(files["word/settings.xml"])
    if settings.find("w:updateFields", NS) is None:
        update = etree.SubElement(settings, qn(W, "updateFields"))
        set_attr(update, val="true")
    files["word/settings.xml"] = etree.tostring(settings, xml_declaration=True, encoding="UTF-8", standalone="yes")

    with zipfile.ZipFile(output, "w", zipfile.ZIP_DEFLATED) as target:
        for name, data in files.items():
            target.writestr(name, data)


def validate_docx(path: Path, *, minimum_sections: int = 3) -> None:
    with zipfile.ZipFile(path) as archive:
        required = {"word/document.xml", "word/styles.xml", "word/header-compiler.xml", "word/footer-front-compiler.xml"}
        missing = required - set(archive.namelist())
        if missing:
            raise RuntimeError(f"DOCX validation failed; missing: {sorted(missing)}")
        document = etree.fromstring(archive.read("word/document.xml"))
        sects = document.xpath("//w:sectPr", namespaces=NS)
        if len(sects) < minimum_sections:
            raise RuntimeError(f"DOCX validation failed; only {len(sects)} section(s)")
        for sect in sects:
            size = sect.find("w:pgSz", NS)
            if size is None or size.get(qn(W, "w")) != "11906" or size.get(qn(W, "h")) != "16838":
                raise RuntimeError("DOCX validation failed; non-A4 section found")
    print(f"[ok] DOCX validated: {path}")


def convert_pdf(soffice: str, docx: Path, output_dir: Path, temp_dir: Path) -> Path:
    profile = temp_dir / "libreoffice-profile"
    profile.mkdir()
    cmd = [
        soffice,
        "--headless",
        f"-env:UserInstallation={profile.as_uri()}",
        "--convert-to",
        "pdf:writer_pdf_Export",
        "--outdir",
        str(output_dir),
        str(docx),
    ]
    run(cmd)
    pdf = output_dir / f"{docx.stem}.pdf"
    if not pdf.is_file() or not pdf.stat().st_size:
        raise RuntimeError("LibreOffice did not produce the expected PDF")
    return pdf


def validate_pdf(pdf: Path, pdfinfo: str) -> None:
    completed = subprocess.run([pdfinfo, str(pdf)], text=True, capture_output=True)
    if completed.returncode:
        raise RuntimeError(completed.stderr.strip() or "pdfinfo failed")
    info = completed.stdout
    match = re.search(r"Page size:\s+([\d.]+) x ([\d.]+) pts", info)
    if not match:
        raise RuntimeError("PDF validation failed; page size was not reported")
    width, height = map(float, match.groups())
    if abs(width - 595.28) > 1 or abs(height - 841.89) > 1:
        raise RuntimeError(f"PDF validation failed; expected A4, got {width} x {height} pt")
    pages = re.search(r"Pages:\s+(\d+)", info)
    print(f"[ok] PDF validated: A4, {pages.group(1) if pages else '?'} pages: {pdf}")


def parse_args() -> argparse.Namespace:
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("--target", choices=("docx", "pdf", "both"), default="both")
    parser.add_argument("--output-dir", type=Path, default=DEFAULT_OUTPUT)
    parser.add_argument("--name", default=DEFAULT_NAME, help="Output basename without extension")
    parser.add_argument("--without-appendices", action="store_true")
    parser.add_argument("--keep-intermediate", action="store_true")
    parser.add_argument("--soffice", help="Explicit LibreOffice/soffice binary")
    parser.add_argument("--single", type=Path, help="Compile one Markdown chapter with dissertation heading hierarchy")
    return parser.parse_args()


def main() -> int:
    args = parse_args()
    pandoc = require_tool("pandoc")
    pdfinfo = require_tool("pdfinfo") if args.target in {"pdf", "both"} else ""
    soffice = ""
    if args.target in {"pdf", "both"}:
        soffice = require_tool("libreoffice", args.soffice or ("/snap/bin/libreoffice" if Path("/snap/bin/libreoffice").exists() else None))

    if args.single:
        single = args.single if args.single.is_absolute() else ROOT / args.single
        if not single.is_file():
            raise RuntimeError(f"Single input was not found: {single}")
        sources = [single]
    else:
        sources = validate_manifest(not args.without_appendices)
    args.output_dir.mkdir(parents=True, exist_ok=True)
    final_docx = args.output_dir / f"{args.name}.docx"

    with tempfile.TemporaryDirectory(prefix="compile-disertasi-") as tmp_name:
        tmp = Path(tmp_name)
        combined = tmp / "combined.md"
        base_reference = tmp / "pandoc-reference.docx"
        reference = tmp / "reference.docx"
        raw_docx = tmp / "raw.docx"

        combined.write_text(build_combined(sources, standalone=bool(args.single)), encoding="utf-8")
        with base_reference.open("wb") as stream:
            completed = subprocess.run([pandoc, "--print-default-data-file", "reference.docx"], cwd=ROOT, stdout=stream)
        if completed.returncode:
            raise RuntimeError("Could not extract Pandoc reference.docx")
        patch_reference(base_reference, reference)

        run([
            pandoc,
            str(combined),
            "--from=markdown+pipe_tables+raw_attribute+fenced_divs",
            "--to=docx",
            "--standalone",
            f"--reference-doc={reference}",
            f"--resource-path={ROOT}:{MAIN}:{IMG}",
            "--columns=999",
            "--wrap=none",
            "--output",
            str(raw_docx),
        ])
        patch_document(raw_docx, final_docx)
        validate_docx(final_docx, minimum_sections=1 if args.single else 3)

        if args.keep_intermediate:
            shutil.copy2(combined, args.output_dir / f"{args.name}.combined.md")
            shutil.copy2(reference, args.output_dir / f"{args.name}.reference.docx")

        if args.target in {"pdf", "both"}:
            pdf = convert_pdf(soffice, final_docx, args.output_dir, tmp)
            validate_pdf(pdf, pdfinfo)

    if args.target == "pdf":
        final_docx.unlink(missing_ok=True)
    print("[done] compilation completed")
    return 0


if __name__ == "__main__":
    try:
        raise SystemExit(main())
    except (RuntimeError, OSError, etree.XMLSyntaxError, zipfile.BadZipFile) as exc:
        print(f"[error] {exc}", file=sys.stderr)
        raise SystemExit(1)
