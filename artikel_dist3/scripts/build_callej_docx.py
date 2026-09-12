from __future__ import annotations

import re
from itertools import count
from pathlib import Path
from typing import List, Tuple

from docx import Document
from docx.enum.table import WD_CELL_VERTICAL_ALIGNMENT, WD_TABLE_ALIGNMENT
from docx.enum.text import WD_ALIGN_PARAGRAPH
from docx.oxml import OxmlElement
from docx.oxml.ns import qn
from docx.opc.constants import RELATIONSHIP_TYPE as RT
from docx.shared import Pt, RGBColor

ORANGE = RGBColor(0xED, 0x7D, 0x31)
BLUE = RGBColor(0x44, 0x72, 0xC4)

ROOT = Path('/home/primandhika/artikel/artikel_dist3')
TEMPLATE = ROOT / 'template' / 'CALLEJ-Template.docx'
SOURCE_MD = ROOT / 'papers' / 'CALLEJ_article_draft.md'
OUTPUT = ROOT / 'papers' / 'CALLEJ_article_final.docx'

TITLE = 'Evaluating a Microlearning-Based Multimedia Platform for Speaking Performance and Metacognitive Awareness: A Mixed-Methods Quasi-Experimental Study'
AUTHOR_LINE = 'Restu Bias Primandhika1, Nani Solihati2*, and Siti Zulaiha3'
AFFILIATIONS = [
    '1 Universitas Muhammadiyah Prof. DR. HAMKA, Indonesia. Email: restubiasprimandhika@uhamka.ac.id',
    '2 Universitas Muhammadiyah Prof. DR. HAMKA, Indonesia. Email: nani_solihati@uhamka.ac.id',
    '3 Universitas Muhammadiyah Prof. DR. HAMKA, Indonesia. Email: siti.zulaiha@uhamka.ac.id',
]
CORR_EMAIL = '*Corresponding author’s email: nani_solihati@uhamka.ac.id'

INLINE_TOKEN_RE = re.compile(
    r'(?P<mdlink>\[(?P<mdtext>[^\]]+)\]\((?P<mdtarget>[^)]+)\))'
    r'|(?P<url>https?://[^\s<>()]+)'
    r'|(?P<italic>\*(?P<italictext>[^*]+)\*)'
)

# ---------------------------------------------------------------------------
# Citation cross-referencing helpers
# ---------------------------------------------------------------------------

# Regex to extract first-author surname + year from an APA reference line
_REF_KEY_RE = re.compile(
    r'^(?P<surname>[A-Z][A-Za-z\u00C0-\u024F\' -]+?),'   # first author surname
    r'.*?'
    r'\((?P<year>\d{4})\)'                                  # (year)
)

# In-text citation patterns (APA style)
# A single surname: letters, hyphens, apostrophes — NO spaces.
_SURNAME = r"[A-Z][A-Za-z\u00C0-\u024F\'\-]+"
_CITE_NARRATIVE_RE = re.compile(
    r'(?P<authors>'
    + _SURNAME +
    r'(?:\s+(?:&|and)\s+' + _SURNAME + r')?'
    r'(?:\s+et\s+al\.)?'
    r')(?:\'s)?\s*\((?P<year>\d{4})\)'
)

def _make_ref_bookmark(surname: str, year: str) -> str:
    """Normalise a surname+year into a bookmark name."""
    clean = re.sub(r'[^A-Za-z0-9]', '_', surname.strip())
    return f'ref_{clean}_{year}'[:40]


def build_reference_index(ref_lines: list[str]) -> dict[str, str]:
    """Map (surname, year) → bookmark_name from reference list texts."""
    idx: dict[tuple[str, str], str] = {}
    bk_map: dict[str, str] = {}          # "Surname_Year" key → bookmark name
    for line in ref_lines:
        m = _REF_KEY_RE.match(line.strip())
        if m:
            surname = m.group('surname').strip()
            year = m.group('year').strip()
            bk = _make_ref_bookmark(surname, year)
            # Store multiple lookup keys
            bk_map[f'{surname}_{year}'] = bk
            # Also store short surname (first word only) for "et al." matches
            short = surname.split()[0].split(',')[0].split('-')[0]
            bk_map[f'{short}_{year}'] = bk
    return bk_map


def _resolve_cite_bookmark(authors_str: str, year: str, ref_index: dict[str, str]) -> str | None:
    """Try to find a bookmark name for a narrative citation."""
    # Clean up author string
    author = authors_str.strip()
    # Remove "et al."
    author = re.sub(r'\s+et\s+al\.?$', '', author)
    # For "A & B", try first author
    author = re.split(r'\s+(?:&|and)\s+', author)[0].strip()
    # Try full surname
    key = f'{author}_{year}'
    if key in ref_index:
        return ref_index[key]
    # Try first word only
    short = author.split()[0].split(',')[0].split('-')[0]
    key2 = f'{short}_{year}'
    return ref_index.get(key2)


def add_markdown_runs_with_citations(paragraph, text, *, size=12, bold=False, color=None, ref_index=None):
    """Like add_markdown_runs but also linkifies APA in-text citations."""
    if ref_index is None or not ref_index:
        add_markdown_runs(paragraph, text, size=size, bold=bold, color=color)
        return

    text = text.replace('`', '')

    # Strategy: find all inline tokens AND citation tokens, process in order
    # First collect all match spans
    events = []

    for m in INLINE_TOKEN_RE.finditer(text):
        events.append((m.start(), m.end(), 'inline', m))

    for m in _CITE_NARRATIVE_RE.finditer(text):
        events.append((m.start(), m.end(), 'cite_narr', m))

    # Also handle parenthetical citations: (Author, Year; Author, Year)
    paren_cite_re = re.compile(
        r'\(([^()]*?'
        r'[A-Z][A-Za-z\u00C0-\u024F\' -]+'
        r'[^()]*?'
        r'\d{4}'
        r'[^()]*?)\)'
    )
    for m in paren_cite_re.finditer(text):
        inner = m.group(1)
        # Must contain at least one "Author, Year" or "Author & Author, Year"
        if re.search(r'[A-Z][A-Za-z\u00C0-\u024F\' -]+.*?\d{4}', inner):
            events.append((m.start(), m.end(), 'cite_paren', m))

    # Remove overlapping events (prefer inline tokens, then citations)
    events.sort(key=lambda e: (e[0], -e[1]))
    filtered = []
    last_end = 0
    for start, end, kind, m in events:
        if start >= last_end:
            filtered.append((start, end, kind, m))
            last_end = end

    cursor = 0
    for start, end, kind, m in filtered:
        # Plain text before this event
        if start > cursor:
            add_plain_run(paragraph, text[cursor:start], size=size, bold=bold, color=color)

        if kind == 'inline':
            # Reuse existing inline logic
            if m.lastgroup == 'mdlink':
                label = m.group('mdtext')
                target = m.group('mdtarget')
                if target.startswith('#'):
                    add_hyperlink(paragraph, label, anchor=normalize_bookmark_name(target[1:]),
                                  size=size, bold=bold, color=color, underline=False)
                elif target.startswith('http'):
                    add_hyperlink(paragraph, label, url=target, size=size, bold=bold,
                                  color=BLUE, underline=True)
                else:
                    add_plain_run(paragraph, label, size=size, bold=bold, color=color)
            elif m.lastgroup == 'url':
                url = m.group('url')
                add_hyperlink(paragraph, url, url=url, size=size, bold=bold, color=BLUE, underline=True)
            elif m.lastgroup == 'italic':
                add_plain_run(paragraph, m.group('italictext'), size=size, bold=bold, italic=True, color=color)

        elif kind == 'cite_narr':
            authors = m.group('authors')
            year = m.group('year')
            bk = _resolve_cite_bookmark(authors, year, ref_index)
            if bk:
                # "Author (Year)" → linked
                add_hyperlink(paragraph, f'{authors} ({year})', anchor=bk,
                              size=size, bold=bold, color=color, underline=False)
            else:
                add_plain_run(paragraph, m.group(0), size=size, bold=bold, color=color)

        elif kind == 'cite_paren':
            # Parse individual citations inside parentheses
            inner = m.group(1)
            add_plain_run(paragraph, '(', size=size, bold=bold, color=color)
            # Split by semicolons
            parts = [p.strip() for p in inner.split(';')]
            for pi, part in enumerate(parts):
                if pi > 0:
                    add_plain_run(paragraph, '; ', size=size, bold=bold, color=color)
                # Try to parse "Author(s), Year" from this part
                cite_m = re.match(
                    r'(?P<auth>[A-Z][A-Za-z\u00C0-\u024F\' &,.\- ]+?),?\s*(?P<yr>\d{4}[a-z]?)$',
                    part.strip()
                )
                if cite_m:
                    auth = cite_m.group('auth').strip().rstrip(',')
                    yr = cite_m.group('yr').strip()
                    bk = _resolve_cite_bookmark(auth, yr[:4], ref_index)
                    if bk:
                        add_hyperlink(paragraph, f'{auth}, {yr}', anchor=bk,
                                      size=size, bold=bold, color=color, underline=False)
                    else:
                        add_plain_run(paragraph, part, size=size, bold=bold, color=color)
                else:
                    add_plain_run(paragraph, part, size=size, bold=bold, color=color)
            add_plain_run(paragraph, ')', size=size, bold=bold, color=color)

        cursor = end

    if cursor < len(text):
        add_plain_run(paragraph, text[cursor:], size=size, bold=bold, color=color)
REFERENCE_ANCHOR_RE = re.compile(r'^\s*<a id="([^"]+)"></a>\s*(.*)$')
BOOKMARK_COUNTER = count(1)


def clear_paragraph(paragraph):
    p = paragraph._p
    for child in list(p):
        if child.tag == qn('w:pPr'):
            continue
        p.remove(child)


def set_run_format(run, *, size=12, bold=False, italic=False, superscript=False, color=None):
    run.font.name = 'Times New Roman'
    run.font.size = Pt(size)
    run.bold = bold
    run.italic = italic
    run.font.superscript = superscript
    if color is not None:
        run.font.color.rgb = color


def apply_paragraph_format(paragraph, *, align=None, before=6, after=6, line_spacing=None, left_indent=None, first_line_indent=None):
    paragraph.alignment = align
    fmt = paragraph.paragraph_format
    fmt.space_before = Pt(before) if before is not None else None
    fmt.space_after = Pt(after) if after is not None else None
    if line_spacing is not None:
        fmt.line_spacing = line_spacing
    if left_indent is not None:
        fmt.left_indent = Pt(left_indent)
    if first_line_indent is not None:
        fmt.first_line_indent = Pt(first_line_indent)


def set_paragraph_text(paragraph, text, *, size=12, bold=False, italic=False, align=None, color=None):
    clear_paragraph(paragraph)
    run = paragraph.add_run(text)
    set_run_format(run, size=size, bold=bold, italic=italic, color=color)
    if align is not None:
        paragraph.alignment = align


def normalize_bookmark_name(anchor_id: str) -> str:
    name = re.sub(r'[^A-Za-z0-9_]', '_', anchor_id)
    if not name or not name[0].isalpha():
        name = f'ref_{name}'
    return name[:40]


def extract_reference_anchor(text: str) -> tuple[str | None, str]:
    match = REFERENCE_ANCHOR_RE.match(text)
    if not match:
        return None, text
    anchor_id, remainder = match.groups()
    return normalize_bookmark_name(anchor_id), remainder.strip()


def add_plain_run(paragraph, text: str, *, size=12, bold=False, italic=False, superscript=False, color=None, underline=None):
    if not text:
        return None
    run = paragraph.add_run(text)
    set_run_format(run, size=size, bold=bold, italic=italic, superscript=superscript, color=color)
    if underline is not None:
        run.underline = underline
    return run


def _append_xml_run(parent, text: str, *, size=12, bold=False, italic=False, superscript=False, color=None, underline=None):
    if not text:
        return
    run = OxmlElement('w:r')
    rpr = OxmlElement('w:rPr')

    rfonts = OxmlElement('w:rFonts')
    rfonts.set(qn('w:ascii'), 'Times New Roman')
    rfonts.set(qn('w:hAnsi'), 'Times New Roman')
    rpr.append(rfonts)

    if bold:
        b = OxmlElement('w:b')
        rpr.append(b)
    if italic:
        i = OxmlElement('w:i')
        rpr.append(i)
    if underline is not None:
        u = OxmlElement('w:u')
        u.set(qn('w:val'), 'single' if underline else 'none')
        rpr.append(u)
    if color is not None:
        c = OxmlElement('w:color')
        c.set(qn('w:val'), str(color))
        rpr.append(c)
    if superscript:
        vert = OxmlElement('w:vertAlign')
        vert.set(qn('w:val'), 'superscript')
        rpr.append(vert)

    sz = OxmlElement('w:sz')
    sz.set(qn('w:val'), str(size * 2))
    rpr.append(sz)
    sz_cs = OxmlElement('w:szCs')
    sz_cs.set(qn('w:val'), str(size * 2))
    rpr.append(sz_cs)

    run.append(rpr)
    t = OxmlElement('w:t')
    if text[:1].isspace() or text[-1:].isspace() or '  ' in text:
        t.set('{http://www.w3.org/XML/1998/namespace}space', 'preserve')
    t.text = text
    run.append(t)
    parent.append(run)


def add_hyperlink(paragraph, text: str, *, url: str | None = None, anchor: str | None = None, size=12, bold=False, italic=False, color=None, underline=None):
    if not text:
        return
    hyperlink = OxmlElement('w:hyperlink')
    if url is not None:
        rel_id = paragraph.part.relate_to(url, RT.HYPERLINK, is_external=True)
        hyperlink.set(qn('r:id'), rel_id)
    if anchor is not None:
        hyperlink.set(qn('w:anchor'), anchor)
        hyperlink.set(qn('w:history'), '1')
    _append_xml_run(
        hyperlink,
        text,
        size=size,
        bold=bold,
        italic=italic,
        color=color,
        underline=underline,
    )
    paragraph._p.append(hyperlink)


def add_bookmark(paragraph, bookmark_name: str | None):
    if not bookmark_name:
        return
    bookmark_id = str(next(BOOKMARK_COUNTER))
    start = OxmlElement('w:bookmarkStart')
    start.set(qn('w:id'), bookmark_id)
    start.set(qn('w:name'), bookmark_name)
    end = OxmlElement('w:bookmarkEnd')
    end.set(qn('w:id'), bookmark_id)

    p = paragraph._p
    insert_at = 1 if len(p) and p[0].tag == qn('w:pPr') else 0
    p.insert(insert_at, start)
    p.append(end)


def add_markdown_runs(paragraph, text, *, size=12, bold=False, color=None):
    text = text.replace('`', '')
    cursor = 0
    for match in INLINE_TOKEN_RE.finditer(text):
        if match.start() > cursor:
            add_plain_run(paragraph, text[cursor:match.start()], size=size, bold=bold, color=color)

        if match.lastgroup == 'mdlink':
            label = match.group('mdtext')
            target = match.group('mdtarget')
            if target.startswith('#'):
                add_hyperlink(
                    paragraph,
                    label,
                    anchor=normalize_bookmark_name(target[1:]),
                    size=size,
                    bold=bold,
                    color=color,
                    underline=False,
                )
            elif target.startswith('http://') or target.startswith('https://'):
                add_hyperlink(
                    paragraph,
                    label,
                    url=target,
                    size=size,
                    bold=bold,
                    color=BLUE,
                    underline=True,
                )
            else:
                add_plain_run(paragraph, label, size=size, bold=bold, color=color)
        elif match.lastgroup == 'url':
            url = match.group('url')
            add_hyperlink(
                paragraph,
                url,
                url=url,
                size=size,
                bold=bold,
                color=BLUE,
                underline=True,
            )
        elif match.lastgroup == 'italic':
            add_plain_run(paragraph, match.group('italictext'), size=size, bold=bold, italic=True, color=color)

        cursor = match.end()

    if cursor < len(text):
        add_plain_run(paragraph, text[cursor:], size=size, bold=bold, color=color)


def add_text_paragraph(doc: Document, text: str, *, style='Normal', size=12, align=None, hanging=False, bookmark_name: str | None = None, ref_index=None):
    paragraph = doc.add_paragraph(style=style)
    apply_paragraph_format(
        paragraph,
        align=align,
        before=6,
        after=6,
        line_spacing=None,
        left_indent=24 if hanging else None,
        first_line_indent=-24 if hanging else None,
    )
    add_bookmark(paragraph, bookmark_name)
    if ref_index and not hanging:
        add_markdown_runs_with_citations(paragraph, text, size=size, ref_index=ref_index)
    else:
        add_markdown_runs(paragraph, text, size=size)
    return paragraph


def add_heading(doc: Document, text: str, level: int):
    """Create headings matching the CALL-EJ template exactly.
    Template pattern: style provides bold/italic, runs only override size + color.
    Do NOT set font.name or explicit bold/italic on runs — let style inherit."""
    if level == 1:
        p = doc.add_paragraph(style='Heading 1')
        apply_paragraph_format(p, align=None, before=6, after=6, line_spacing=1.0)
        run = p.add_run(text)
        run.font.size = Pt(13)
        run.font.color.rgb = ORANGE
        return p
    p = doc.add_paragraph(style='Heading 2')
    apply_paragraph_format(p, align=None, before=6, after=6, line_spacing=1.0)
    run = p.add_run(text)
    run.font.size = Pt(12)
    run.font.color.rgb = BLUE
    return p


def add_biodata_heading(doc: Document):
    """Biodata heading uses same format as Heading 1 per template."""
    p = doc.add_paragraph(style='Normal')
    apply_paragraph_format(p, align=None, before=6, after=6)
    run = p.add_run('Biodata')
    run.font.size = Pt(13)
    run.bold = True
    run.font.color.rgb = ORANGE
    return p


def set_cell_text(cell, text: str, *, size=11, bold=False, align=WD_ALIGN_PARAGRAPH.LEFT):
    cell.text = ''
    p = cell.paragraphs[0]
    apply_paragraph_format(p, align=align, before=0, after=0)
    add_markdown_runs(p, text, size=size, bold=bold)
    cell.vertical_alignment = WD_CELL_VERTICAL_ALIGNMENT.CENTER


def _set_cell_border(cell, **edges):
    tc = cell._tc
    tcPr = tc.get_or_add_tcPr()
    tcBorders = tcPr.first_child_found_in('w:tcBorders')
    if tcBorders is None:
        tcBorders = OxmlElement('w:tcBorders')
        tcPr.append(tcBorders)
    for edge_name in ('left', 'top', 'right', 'bottom'):
        edge_data = edges.get(edge_name)
        tag = qn(f'w:{edge_name}')
        existing = tcBorders.find(tag)
        if existing is not None:
            tcBorders.remove(existing)
        if edge_data is None:
            continue
        element = OxmlElement(f'w:{edge_name}')
        for key, value in edge_data.items():
            element.set(qn(f'w:{key}'), str(value))
        tcBorders.append(element)


def format_table_like_template(table, *, has_caption_row=False):
    tbl = table._tbl
    tblPr = tbl.tblPr
    tblBorders = tblPr.first_child_found_in('w:tblBorders')
    if tblBorders is not None:
        tblPr.remove(tblBorders)
    nrows = len(table.rows)
    ncols = len(table.columns)
    header_row = 1 if has_caption_row else 0
    for r in range(nrows):
        for c in range(ncols):
            cell = table.cell(r, c)
            top = None
            bottom = None
            if r == header_row:
                top = {'val': 'single', 'sz': 4, 'space': 0, 'color': 'auto'}
                bottom = {'val': 'single', 'sz': 4, 'space': 0, 'color': 'auto'}
            elif r == nrows - 1:
                bottom = {'val': 'single', 'sz': 4, 'space': 0, 'color': 'auto'}
            _set_cell_border(cell, top=top, bottom=bottom)


def parse_table(table_lines: List[str]) -> List[List[str]]:
    rows = []
    for idx, line in enumerate(table_lines):
        if idx == 1 and re.match(r'^\|?[\s:\-\|]+\|?$', line.strip()):
            continue
        parts = [c.strip() for c in line.strip().strip('|').split('|')]
        rows.append(parts)
    return rows


def build_table(doc: Document, rows: List[List[str]], caption_text: str | None = None):
    ncols = max(len(r) for r in rows)
    has_caption_row = caption_text is not None
    table = doc.add_table(rows=len(rows) + (1 if has_caption_row else 0), cols=ncols)
    table.style = 'Normal Table'
    table.alignment = WD_TABLE_ALIGNMENT.CENTER

    row_offset = 0
    if has_caption_row:
        merged = table.cell(0, 0)
        for c in range(1, ncols):
            merged = merged.merge(table.cell(0, c))
        merged.text = ''
        m = re.match(r'^(Table\s+\d+\.)\s*(.*)$', caption_text)
        first_para = merged.paragraphs[0]
        apply_paragraph_format(first_para, align=None, before=0, after=0)
        if m:
            r1 = first_para.add_run(m.group(1))
            set_run_format(r1, size=11, bold=True)
            second_para = merged.add_paragraph()
            apply_paragraph_format(second_para, align=None, before=0, after=0)
            add_markdown_runs(second_para, m.group(2), size=11)
        else:
            add_markdown_runs(first_para, caption_text, size=11)
        row_offset = 1

    for r_idx, row in enumerate(rows):
        for c_idx in range(ncols):
            text = row[c_idx] if c_idx < len(row) else ''
            align = WD_ALIGN_PARAGRAPH.CENTER if re.fullmatch(r'[-–—]?\d+(\.\d+)?%?|[A-Za-z]?\(?\d+[A-Za-z.\- ]*\)?', text) else WD_ALIGN_PARAGRAPH.LEFT
            if re.fullmatch(r'\d+(\.\d+)?%?|[-–—]?\d+(\.\d+)?', text):
                align = WD_ALIGN_PARAGRAPH.CENTER
            set_cell_text(table.cell(r_idx + row_offset, c_idx), text, size=11, bold=(r_idx == 0), align=align)
    format_table_like_template(table, has_caption_row=has_caption_row)
    return table


def remove_body_after_abstract(doc: Document):
    body = doc._element.body
    children = list(body.iterchildren())
    # Keep title/front matter + abstract table + following blank paragraph if present.
    for child in children[12:-1]:
        body.remove(child)


def parse_md(md_path: Path):
    lines = md_path.read_text().splitlines()
    abstract_idx = lines.index('## ABSTRACT')
    intro_idx = lines.index('## Introduction')
    abstract_lines = []
    keywords = ''
    for line in lines[abstract_idx + 1:intro_idx]:
        if line.startswith('Keywords:'):
            keywords = line.replace('Keywords:', '').strip()
        elif line.strip():
            abstract_lines.append(line.strip())
    abstract = ' '.join(abstract_lines)
    body_lines = lines[intro_idx:]
    blocks: List[Tuple[str, object]] = []
    buffer: List[str] = []
    i = 0

    def flush_paragraph():
        nonlocal buffer
        if buffer:
            blocks.append(('paragraph', ' '.join(buffer).strip()))
            buffer = []

    while i < len(body_lines):
        line = body_lines[i].rstrip()
        stripped = line.strip()
        if stripped.startswith('## '):
            flush_paragraph()
            blocks.append(('heading1', stripped[3:]))
            i += 1
            continue
        if stripped.startswith('### '):
            flush_paragraph()
            blocks.append(('heading2', stripped[4:]))
            i += 1
            continue
        if not stripped:
            flush_paragraph()
            i += 1
            continue
        if re.match(r'^Table\s+\d+\.', stripped):
            flush_paragraph()
            blocks.append(('caption', stripped))
            i += 1
            continue
        if stripped.startswith('|'):
            flush_paragraph()
            table_lines = []
            while i < len(body_lines) and body_lines[i].strip().startswith('|'):
                table_lines.append(body_lines[i].strip())
                i += 1
            blocks.append(('table', parse_table(table_lines)))
            continue
        if re.match(r'^\d+\.\s', stripped):
            flush_paragraph()
            items = []
            while i < len(body_lines) and re.match(r'^\d+\.\s', body_lines[i].strip()):
                items.append(re.sub(r'^\d+\.\s*', '', body_lines[i].strip()))
                i += 1
            blocks.append(('numbered_list', items))
            continue
        buffer.append(stripped)
        i += 1
    flush_paragraph()
    return abstract, keywords, blocks


def fill_front_matter(doc: Document, abstract: str, keywords: str):
    set_paragraph_text(doc.paragraphs[0], TITLE, size=13, bold=True, align=WD_ALIGN_PARAGRAPH.CENTER)

    p1 = doc.paragraphs[1]
    clear_paragraph(p1)
    p1.alignment = WD_ALIGN_PARAGRAPH.CENTER
    for chunk, sup in [
        ('Restu Bias Primandhika', False),
        ('1', True),
        (', ', False),
        ('Nani Solihati', False),
        ('2*', True),
        (', and Siti Zulaiha', False),
        ('3', True),
    ]:
        run = p1.add_run(chunk)
        set_run_format(run, size=12, superscript=sup)

    aff_paragraphs = [doc.paragraphs[3], doc.paragraphs[4], doc.paragraphs[5]]
    for paragraph, text in zip(aff_paragraphs, AFFILIATIONS):
        clear_paragraph(paragraph)
        marker, rest = text.split(' ', 1)
        r0 = paragraph.add_run(marker + ' ')
        set_run_format(r0, size=11, superscript=True)
        r1 = paragraph.add_run(rest)
        set_run_format(r1, size=11)

    set_paragraph_text(doc.paragraphs[6], CORR_EMAIL, size=11)
    set_paragraph_text(doc.paragraphs[7], '', size=11)
    set_paragraph_text(doc.paragraphs[8], '', size=11)
    set_paragraph_text(doc.paragraphs[9], '', size=10)

    apply_paragraph_format(doc.paragraphs[0], align=WD_ALIGN_PARAGRAPH.CENTER, before=None, after=None, line_spacing=1.0)
    apply_paragraph_format(doc.paragraphs[1], align=WD_ALIGN_PARAGRAPH.CENTER, before=None, after=None, line_spacing=1.0)
    for idx in [3, 4, 5, 6, 7, 8, 9]:
        apply_paragraph_format(doc.paragraphs[idx], align=None, before=6, after=6)

    abstract_table = doc.tables[0]
    # Heading cell
    heading_para = abstract_table.rows[0].cells[1].paragraphs[0]
    set_paragraph_text(heading_para, 'ABSTRACT', size=12, bold=True, align=WD_ALIGN_PARAGRAPH.CENTER)
    if heading_para.runs:
        heading_para.runs[0].font.color.rgb = RGBColor(0xED, 0x7D, 0x31)

    kw_para = abstract_table.rows[1].cells[0].paragraphs[0]
    set_paragraph_text(kw_para, f'Keywords: {keywords}', size=12)
    abs_cell = abstract_table.rows[1].cells[1]
    abs_cell.text = ''
    abs_para = abs_cell.paragraphs[0]
    abs_para.alignment = WD_ALIGN_PARAGRAPH.JUSTIFY
    add_markdown_runs(abs_para, abstract, size=12)


def add_caption(doc: Document, caption_text: str):
    m = re.match(r'^(Table\s+\d+\.)\s*(.*)$', caption_text)
    if m:
        p1 = doc.add_paragraph(style='Caption')
        apply_paragraph_format(p1, align=WD_ALIGN_PARAGRAPH.JUSTIFY, before=6, after=6)
        run = p1.add_run(m.group(1))
        set_run_format(run, size=12)
        if m.group(2):
            p2 = doc.add_paragraph(style='Caption')
            apply_paragraph_format(p2, align=WD_ALIGN_PARAGRAPH.JUSTIFY, before=6, after=6)
            add_markdown_runs(p2, m.group(2), size=12)
        return
    p = doc.add_paragraph(style='Caption')
    apply_paragraph_format(p, align=WD_ALIGN_PARAGRAPH.JUSTIFY, before=6, after=6)
    add_markdown_runs(p, caption_text, size=12)


def fill_headers(doc: Document):
    """Update only the even-page header 'Authors' names' placeholder.
    All three headers (first, odd, even) are preserved from the template;
    only the author-name slot in the even-page header is touched."""
    sec = doc.sections[0]
    if not sec.different_first_page_header_footer:
        return
    even_p = sec.even_page_header.paragraphs[0]
    # The template even-page header has runs like:
    #   "https://callej.org" TAB "Authors' names" SPACE TAB "Vol. …; ..."
    # We only need to replace the "Authors' names" run (index 4).
    for run in even_p.runs:
        if 'Authors' in run.text or 'authors' in run.text.lower():
            run.text = 'Primandhika, Solihati, and Zulaiha'
            break


def build_docx():
    abstract, keywords, blocks = parse_md(SOURCE_MD)
    doc = Document(str(TEMPLATE))
    fill_front_matter(doc, abstract, keywords)
    fill_headers(doc)
    remove_body_after_abstract(doc)

    # --- Build reference cross-reference index ---
    ref_lines = []
    in_refs = False
    for kind, payload in blocks:
        if kind == 'heading1' and str(payload) == 'References':
            in_refs = True
            continue
        if in_refs:
            if kind == 'heading1':
                break
            if kind == 'paragraph':
                _, clean = extract_reference_anchor(str(payload))
                ref_lines.append(clean)
    ref_index = build_reference_index(ref_lines)

    # --- Render document ---
    current_section = None
    pending_caption = None
    for kind, payload in blocks:
        if kind == 'heading1':
            if pending_caption is not None:
                add_caption(doc, pending_caption)
                pending_caption = None
            text = str(payload)
            if text == 'ABSTRACT':
                continue
            current_section = text
            if text == 'Biodata':
                add_biodata_heading(doc)
            else:
                add_heading(doc, text, 1)
        elif kind == 'heading2':
            if pending_caption is not None:
                add_caption(doc, pending_caption)
                pending_caption = None
            add_heading(doc, str(payload), 2)
        elif kind == 'paragraph':
            if pending_caption is not None:
                add_caption(doc, pending_caption)
                pending_caption = None
            paragraph_text = str(payload)
            bookmark_name = None
            if current_section == 'References':
                # Auto-generate bookmark from first-author + year
                m = _REF_KEY_RE.match(paragraph_text.strip())
                if m:
                    bookmark_name = _make_ref_bookmark(m.group('surname'), m.group('year'))
            add_text_paragraph(
                doc,
                paragraph_text,
                style='Normal',
                size=12,
                align=None,
                hanging=(current_section == 'References'),
                bookmark_name=bookmark_name,
                ref_index=ref_index if current_section != 'References' else None,
            )
        elif kind == 'numbered_list':
            if pending_caption is not None:
                add_caption(doc, pending_caption)
                pending_caption = None
            for idx, item in enumerate(payload, start=1):
                p = doc.add_paragraph(style='List Paragraph')
                apply_paragraph_format(p, align=None, before=6, after=6)
                add_markdown_runs_with_citations(p, f'{idx}. {item}', size=12, ref_index=ref_index)
        elif kind == 'caption':
            pending_caption = str(payload)
        elif kind == 'table':
            build_table(doc, payload, caption_text=pending_caption)
            pending_caption = None

    if pending_caption is not None:
        add_caption(doc, pending_caption)

    doc.save(str(OUTPUT))
    return OUTPUT


if __name__ == '__main__':
    out = build_docx()
    print(out)
