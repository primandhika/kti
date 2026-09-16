#!/usr/bin/env python3
"""Gabungkan naskah Markdown; --update-source mencadangkan lalu memperbarui DOCX."""
from __future__ import annotations

import argparse
from datetime import datetime
from pathlib import Path
import re
import shutil
import subprocess
import tempfile
from zipfile import ZipFile
import xml.etree.ElementTree as ET

ROOT = Path(__file__).resolve().parents[1]
SOURCE = ROOT / 'Fonologi Bahasa Indonesia - Modul.docx'
REFERENCE = ROOT / 'reference' / 'modul-asli.docx'
DEFAULT_OUTPUT = ROOT / 'output' / 'Fonologi Bahasa Indonesia - Draf.docx'


def manuscript_files() -> list[Path]:
    main = ROOT / 'main'
    chapters = sorted(p for p in main.glob('[0-9][0-9]-*.md')
                      if 1 <= int(p.name[:2]) <= 98)
    numbers = [int(p.name[:2]) for p in chapters]
    if not chapters or numbers != list(range(1, len(chapters) + 1)):
        raise ValueError('Nomor berkas bab harus berurutan dari 01 tanpa duplikasi.')
    files = [main / '00-hal-awal.md', main / '00-daftar-isi.md',
             *chapters, main / '99-daftar-pustaka.md']
    for path in [*files, REFERENCE]:
        if not path.is_file():
            raise ValueError(f'Berkas wajib tidak ditemukan: {path}')
    return files


def assemble(files: list[Path]) -> str:
    anchors = {p.name: f'bagian-{p.stem}' for p in files}
    blocks = []
    for path in files:
        text = path.read_text(encoding='utf-8')
        if len(re.findall(r'^# ', text, re.M)) != 1:
            raise ValueError(f'Gunakan tepat satu judul tingkat 1 di {path.name}.')
        text = re.sub(r'^(# .+)$', lambda m: m[1] + ' {#' + anchors[path.name] + '}',
                      text, count=1, flags=re.M)
        for name, anchor in anchors.items():
            text = text.replace(f']({name})', f'](#{anchor})')
        blocks.append(text.strip())
    return '\n\n'.join(blocks) + '\n'


def check_docx(path: Path) -> None:
    with ZipFile(path) as archive:
        bad = archive.testzip()
        if bad:
            raise ValueError(f'Arsip DOCX rusak pada {bad}.')
        doc = ET.fromstring(archive.read('word/document.xml'))
        ns = {'w': 'http://schemas.openxmlformats.org/wordprocessingml/2006/main'}
        if doc.find('w:body', ns) is None:
            raise ValueError('Hasil tidak memuat badan dokumen Word.')


def compile_book(output: Path, update_source: bool) -> None:
    pandoc = shutil.which('pandoc')
    if not pandoc:
        raise ValueError('Pandoc belum tersedia. Pasang Pandoc dan jalankan ulang.')
    if output.suffix.lower() != '.docx':
        raise ValueError('Nama keluaran harus berakhiran .docx.')
    if output in (SOURCE.resolve(), REFERENCE.resolve()):
        raise ValueError('Gunakan --update-source untuk memperbarui DOCX utama; '
                         'keluaran draf harus memakai lokasi lain.')
    if update_source and not SOURCE.is_file():
        raise ValueError(f'DOCX utama tidak ditemukan: {SOURCE}')
    files = manuscript_files()
    text = assemble(files)
    output.parent.mkdir(parents=True, exist_ok=True)
    with tempfile.TemporaryDirectory(prefix='.compile-', dir=output.parent) as folder:
        temp = Path(folder)
        combined = temp / 'buku.md'
        combined.write_text(text, encoding='utf-8')
        result = temp / 'buku.docx'
        command = [pandoc, str(combined), '--from=markdown', '--to=docx',
                   '--standalone', '--fail-if-warnings',
                   f'--reference-doc={REFERENCE}',
                   f'--resource-path={ROOT / "main"}:{ROOT}',
                   f'--lua-filter={ROOT / "scripts" / "format_docx.lua"}',
                   '--output', str(result)]
        subprocess.run(command, cwd=ROOT / 'main', check=True)
        check_docx(result)
        result.replace(output)
    print(f'Draf DOCX: {output}')
    print(f'Digabung: {len(files)} berkas Markdown.')
    if update_source:
        backup_dir = ROOT / '_rev' / 'backup_docx'
        backup_dir.mkdir(parents=True, exist_ok=True)
        stamp = datetime.now().strftime('%Y-%m-%d_%H%M%S_%f')
        backup = backup_dir / f'{stamp}_{SOURCE.name}'
        shutil.copy2(SOURCE, backup)
        # Replace only after a complete new file and the old-file backup exist.
        with tempfile.TemporaryDirectory(prefix='.update-docx-', dir=ROOT) as folder:
            replacement = Path(folder) / SOURCE.name
            shutil.copy2(output, replacement)
            replacement.replace(SOURCE)
        print(f'Cadangan sebelum pembaruan: {backup}')
        print(f'DOCX utama diperbarui: {SOURCE}')


def main() -> None:
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument('--output', type=Path, default=DEFAULT_OUTPUT,
                        help='Lokasi draf DOCX (jalur relatif dihitung dari direktori perintah).')
    parser.add_argument('--update-source', action='store_true',
                        help='Cadangkan dan perbarui DOCX utama setelah kompilasi berhasil.')
    args = parser.parse_args()
    try:
        compile_book(args.output.resolve(), args.update_source)
    except (ValueError, OSError, subprocess.CalledProcessError) as error:
        parser.exit(1, f'Kompilasi gagal: {error}\n')


if __name__ == '__main__':
    main()
