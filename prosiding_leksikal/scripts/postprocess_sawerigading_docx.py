#!/usr/bin/env python3
from __future__ import annotations

import shutil
import sys
import tempfile
import zipfile
from pathlib import Path
from xml.etree import ElementTree as ET

W = 'http://schemas.openxmlformats.org/wordprocessingml/2006/main'
NS = {'w': W}
ET.register_namespace('w', W)
ET.register_namespace('wpc', 'http://schemas.microsoft.com/office/word/2010/wordprocessingCanvas')
ET.register_namespace('mc', 'http://schemas.openxmlformats.org/markup-compatibility/2006')
ET.register_namespace('o', 'urn:schemas-microsoft-com:office:office')
ET.register_namespace('r', 'http://schemas.openxmlformats.org/officeDocument/2006/relationships')
ET.register_namespace('m', 'http://schemas.openxmlformats.org/officeDocument/2006/math')
ET.register_namespace('v', 'urn:schemas-microsoft-com:vml')
ET.register_namespace('wp14', 'http://schemas.microsoft.com/office/word/2010/wordprocessingDrawing')
ET.register_namespace('wp', 'http://schemas.openxmlformats.org/drawingml/2006/wordprocessingDrawing')
ET.register_namespace('w10', 'urn:schemas-microsoft-com:office:word')
ET.register_namespace('w14', 'http://schemas.microsoft.com/office/word/2010/wordml')
ET.register_namespace('w15', 'http://schemas.microsoft.com/office/word/2012/wordml')
ET.register_namespace('w16cex', 'http://schemas.microsoft.com/office/word/2018/wordml/cex')
ET.register_namespace('w16cid', 'http://schemas.microsoft.com/office/word/2016/wordml/cid')
ET.register_namespace('w16', 'http://schemas.microsoft.com/office/word/2018/wordml')
ET.register_namespace('w16du', 'http://schemas.microsoft.com/office/word/2023/wordml/word16du')
ET.register_namespace('w16sdtdh', 'http://schemas.microsoft.com/office/word/2020/wordml/sdtdatahash')
ET.register_namespace('w16sdtfl', 'http://schemas.microsoft.com/office/word/2024/wordml/sdtformatlock')
ET.register_namespace('w16se', 'http://schemas.microsoft.com/office/word/2015/wordml/symex')
ET.register_namespace('wps', 'http://schemas.microsoft.com/office/word/2010/wordprocessingShape')


def q(tag: str) -> str:
    return f'{{{W}}}{tag}'


def ensure(parent: ET.Element, tag: str) -> ET.Element:
    child = parent.find(f'w:{tag}', NS)
    if child is None:
        child = ET.SubElement(parent, q(tag))
    return child


def set_para_props(p: ET.Element, *, align: str | None = None, left: str | None = None,
                   right: str | None = None, first_line: str | None = None,
                   spacing_after: str = '0', line: str = '240', line_rule: str = 'auto') -> None:
    pPr = ensure(p, 'pPr')
    spacing = ensure(pPr, 'spacing')
    spacing.set(q('after'), spacing_after)
    spacing.set(q('line'), line)
    spacing.set(q('lineRule'), line_rule)
    if align:
        jc = ensure(pPr, 'jc')
        jc.set(q('val'), align)
    ind = pPr.find('w:ind', NS)
    if left is not None or right is not None or first_line is not None:
        if ind is None:
            ind = ET.SubElement(pPr, q('ind'))
        if left is not None:
            ind.set(q('left'), left)
        if right is not None:
            ind.set(q('right'), right)
        if first_line is not None:
            ind.set(q('firstLine'), first_line)
    elif ind is not None:
        pPr.remove(ind)


def ensure_run_props(r: ET.Element) -> ET.Element:
    rPr = r.find('w:rPr', NS)
    if rPr is None:
        rPr = ET.Element(q('rPr'))
        r.insert(0, rPr)
    return rPr


def set_run_format(r: ET.Element, *, size_half_pt: int, bold: bool | None = None,
                   italic: bool | None = None, font: str = 'Times New Roman') -> None:
    rPr = ensure_run_props(r)
    rFonts = ensure(rPr, 'rFonts')
    for k in ('ascii', 'hAnsi', 'cs'):
        rFonts.set(q(k), font)
    sz = ensure(rPr, 'sz')
    sz.set(q('val'), str(size_half_pt))
    szCs = ensure(rPr, 'szCs')
    szCs.set(q('val'), str(size_half_pt))
    if bold is not None:
        b = rPr.find('w:b', NS)
        bCs = rPr.find('w:bCs', NS)
        if bold:
            if b is None:
                ET.SubElement(rPr, q('b'))
            if bCs is None:
                ET.SubElement(rPr, q('bCs'))
        else:
            if b is not None:
                rPr.remove(b)
            if bCs is not None:
                rPr.remove(bCs)
    if italic is not None:
        i = rPr.find('w:i', NS)
        iCs = rPr.find('w:iCs', NS)
        if italic:
            if i is None:
                ET.SubElement(rPr, q('i'))
            if iCs is None:
                ET.SubElement(rPr, q('iCs'))
        else:
            if i is not None:
                rPr.remove(i)
            if iCs is not None:
                rPr.remove(iCs)


def para_text(p: ET.Element) -> str:
    return ''.join((t.text or '') for t in p.findall('.//w:t', NS)).strip()


def all_runs_in_para(p: ET.Element):
    return p.findall('.//w:r', NS)


def set_para_runs(p: ET.Element, *, size_half_pt: int, bold: bool | None = None,
                  italic: bool | None = None) -> None:
    for r in all_runs_in_para(p):
        set_run_format(r, size_half_pt=size_half_pt, bold=bold, italic=italic)


def set_all_runs_document(root: ET.Element, size_half_pt: int = 24) -> None:
    for r in root.findall('.//w:r', NS):
        set_run_format(r, size_half_pt=size_half_pt)


def patch_docx(path: Path) -> None:
    with tempfile.TemporaryDirectory() as td:
        td_path = Path(td)
        with zipfile.ZipFile(path, 'r') as zin:
            zin.extractall(td_path)

        document_xml = td_path / 'word' / 'document.xml'
        styles_xml = td_path / 'word' / 'styles.xml'

        root = ET.parse(document_xml).getroot()
        body = root.find('w:body', NS)
        if body is None:
            raise RuntimeError('word/document.xml tidak memiliki body')

        # Global font pass: Times New Roman 12 pt
        set_all_runs_document(root, size_half_pt=24)

        paragraphs = body.findall('w:p', NS)
        text_to_idx = {para_text(p): i for i, p in enumerate(paragraphs)}

        # Section break: single column for title/abstract, two columns from PENDAHULUAN onward.
        body_sect = body.find('w:sectPr', NS)

        pend_idx = text_to_idx.get('PENDAHULUAN')
        if pend_idx is not None and pend_idx > 0:
            prev_p = paragraphs[pend_idx - 1]
            pPr = ensure(prev_p, 'pPr')
            sectPr = pPr.find('w:sectPr', NS)
            if sectPr is None:
                sectPr = ET.SubElement(pPr, q('sectPr'))
            # copy critical page geometry from template/body section so Word keeps portrait layout
            if body_sect is not None:
                for tag in ['pgSz', 'pgMar', 'docGrid']:
                    src = body_sect.find(f'w:{tag}', NS)
                    dst = sectPr.find(f'w:{tag}', NS)
                    if src is not None and dst is None:
                        sectPr.append(ET.fromstring(ET.tostring(src, encoding='unicode')))
            cols = ensure(sectPr, 'cols')
            # single-column end of front matter
            if q('num') in cols.attrib:
                del cols.attrib[q('num')]
            cols.set(q('space'), '708')
            typ = ensure(sectPr, 'type')
            typ.set(q('val'), 'continuous')

        if body_sect is not None:
            cols = ensure(body_sect, 'cols')
            cols.set(q('num'), '2')
            cols.set(q('space'), '708')
            typ = ensure(body_sect, 'type')
            typ.set(q('val'), 'continuous')

        section_heads = {
            'PENDAHULUAN', 'KERANGKA TEORI', 'METODE', 'PEMBAHASAN', 'PENUTUP', 'DAFTAR PUSTAKA'
        }

        for p in paragraphs:
            text = para_text(p)
            if not text:
                continue

            # Top matter
            if text.startswith('Framing Leksikal dan Aktivasi Prototipe'):
                set_para_props(p, align='center')
                set_para_runs(p, size_half_pt=24, bold=True, italic=False)
                continue
            if text.startswith('(Lexical Framing and Prototype Activation'):
                set_para_props(p, align='center')
                set_para_runs(p, size_half_pt=24, bold=False, italic=True)
                continue
            if text.startswith('Aurelia Sakti Yani'):
                set_para_props(p, align='center')
                set_para_runs(p, size_half_pt=24, bold=False, italic=False)
                continue
            if 'Institut Keguruan dan Ilmu Pendidikan Siliwangi' in text:
                set_para_props(p, align='center')
                set_para_runs(p, size_half_pt=22, bold=False, italic=False)
                continue
            if text.startswith('Jl. Terusan Jendral Sudirman'):
                set_para_props(p, align='center')
                set_para_runs(p, size_half_pt=22, bold=False, italic=False)
                continue
            if text.startswith('Pos-el:'):
                set_para_props(p, align='center')
                set_para_runs(p, size_half_pt=22, bold=False, italic=False)
                continue
            if text.startswith('(Naskah diterima tanggal:'):
                set_para_props(p, align='center')
                set_para_runs(p, size_half_pt=22, bold=False, italic=True)
                continue

            # Abstract block
            if text == 'Abstract' or text == 'Abstrak':
                set_para_props(p, align='center')
                set_para_runs(p, size_half_pt=22, bold=True, italic=True)
                continue
            if text.startswith('Keywords:') or text.startswith('Kata kunci:'):
                set_para_props(p, align='both')
                set_para_runs(p, size_half_pt=22, italic=True)
                # bold only the first run label if present
                runs = all_runs_in_para(p)
                if runs:
                    set_run_format(runs[0], size_half_pt=22, bold=True, italic=True)
                continue
            if pend_idx is not None and paragraphs.index(p) < pend_idx:
                set_para_props(p, align='both')
                set_para_runs(p, size_half_pt=22, italic=True)
                continue

            # Main headings
            if text in section_heads:
                set_para_props(p, align='left')
                set_para_runs(p, size_half_pt=24, bold=True, italic=False)
                continue

            # Body paragraphs and references
            if text.startswith('|'):
                continue
            set_para_props(p, align='both')
            set_para_runs(p, size_half_pt=24)

        # Style defaults: Times New Roman in Normal, NoSpacing, NormalWeb.
        styles_root = ET.parse(styles_xml).getroot()
        for style_id in ['Normal', 'NoSpacing', 'NormalWeb']:
            style = styles_root.find(f".//w:style[@w:styleId='{style_id}']", NS)
            if style is None:
                continue
            rPr = ensure(style, 'rPr')
            rFonts = ensure(rPr, 'rFonts')
            for k in ('ascii', 'hAnsi', 'cs'):
                rFonts.set(q(k), 'Times New Roman')

        ET.ElementTree(root).write(document_xml, encoding='UTF-8', xml_declaration=True)
        ET.ElementTree(styles_root).write(styles_xml, encoding='UTF-8', xml_declaration=True)

        tmp_out = path.with_suffix('.patched.docx')
        with zipfile.ZipFile(tmp_out, 'w', zipfile.ZIP_DEFLATED) as zout:
            for file in td_path.rglob('*'):
                if file.is_file():
                    zout.write(file, file.relative_to(td_path))
        shutil.move(tmp_out, path)


if __name__ == '__main__':
    if len(sys.argv) != 2:
        print('Usage: postprocess_sawerigading_docx.py <file.docx>', file=sys.stderr)
        sys.exit(1)
    patch_docx(Path(sys.argv[1]))
