#!/usr/bin/env python3
"""Apply native Word multilevel numbering to dissertation Heading 2-4."""

from __future__ import annotations

import argparse
import re
import zipfile
from pathlib import Path

from lxml import etree


W = "http://schemas.openxmlformats.org/wordprocessingml/2006/main"
NS = {"w": W}


def qn(name: str) -> str:
    return f"{{{W}}}{name}"


def set_val(element: etree._Element, value: str | int) -> None:
    element.set(qn("val"), str(value))


def add(parent: etree._Element, name: str, value: str | int | None = None) -> etree._Element:
    element = etree.SubElement(parent, qn(name))
    if value is not None:
        set_val(element, value)
    return element


def next_id(root: etree._Element, element_name: str, attribute_name: str) -> int:
    values = [
        int(value)
        for value in root.xpath(f"./w:{element_name}/@w:{attribute_name}", namespaces=NS)
    ]
    return max(values, default=0) + 1


def add_level(
    abstract: etree._Element,
    *,
    level: int,
    style: str,
    number_format: str,
    text: str,
    left: int,
    restart: int | None = None,
) -> None:
    lvl = etree.SubElement(abstract, qn("lvl"))
    lvl.set(qn("ilvl"), str(level))
    add(lvl, "start", 1)
    add(lvl, "numFmt", number_format)
    add(lvl, "pStyle", style)
    add(lvl, "lvlText", text)
    add(lvl, "lvlJc", "left")
    if restart is not None:
        add(lvl, "lvlRestart", restart)
    ppr = add(lvl, "pPr")
    tabs = add(ppr, "tabs")
    tab = add(tabs, "tab")
    tab.set(qn("val"), "num")
    tab.set(qn("pos"), str(left))
    ind = add(ppr, "ind")
    ind.set(qn("left"), str(left))
    ind.set(qn("hanging"), "360")


def strip_manual_prefix(paragraph: etree._Element, pattern: re.Pattern[str]) -> None:
    nodes = paragraph.xpath(".//w:t", namespaces=NS)
    joined = "".join(node.text or "" for node in nodes)
    match = pattern.match(joined)
    if not match:
        return
    remaining = match.end()
    for node in nodes:
        text = node.text or ""
        if remaining >= len(text):
            node.text = ""
            remaining -= len(text)
        else:
            node.text = text[remaining:]
            break


def apply_numbering(source: Path, output: Path) -> None:
    with zipfile.ZipFile(source) as archive:
        files = {name: archive.read(name) for name in archive.namelist()}

    numbering = etree.fromstring(files["word/numbering.xml"])
    abstract_id = next_id(numbering, "abstractNum", "abstractNumId")
    num_id = next_id(numbering, "num", "numId")

    abstract = etree.Element(qn("abstractNum"))
    abstract.set(qn("abstractNumId"), str(abstract_id))
    add(abstract, "multiLevelType", "multilevel")
    add_level(abstract, level=0, style="Heading2", number_format="upperLetter", text="%1.", left=360)
    add_level(abstract, level=1, style="Heading3", number_format="decimal", text="%2.", left=720, restart=1)
    add_level(abstract, level=2, style="Heading4", number_format="lowerLetter", text="%3.", left=1080, restart=2)

    first_num = numbering.find("w:num", NS)
    numbering.insert(numbering.index(first_num) if first_num is not None else len(numbering), abstract)
    num = add(numbering, "num")
    num.set(qn("numId"), str(num_id))
    add(num, "abstractNumId", abstract_id)
    files["word/numbering.xml"] = etree.tostring(
        numbering, xml_declaration=True, encoding="UTF-8", standalone="yes"
    )

    document = etree.fromstring(files["word/document.xml"])
    specifications = {
        "Heading2": (0, re.compile(r"^[A-Z]\.\s+")),
        "Heading3": (1, re.compile(r"^\d+\.\s+")),
        "Heading4": (2, re.compile(r"^[a-z]\.\s+")),
    }
    for paragraph in document.xpath("//w:p[w:pPr/w:pStyle]", namespaces=NS):
        style = paragraph.find("w:pPr/w:pStyle", NS)
        style_id = style.get(qn("val")) if style is not None else None
        if style_id not in specifications:
            continue
        level, prefix = specifications[style_id]
        strip_manual_prefix(paragraph, prefix)
        ppr = paragraph.find("w:pPr", NS)
        existing = ppr.find("w:numPr", NS)
        if existing is not None:
            ppr.remove(existing)
        numpr = etree.SubElement(ppr, qn("numPr"))
        add(numpr, "ilvl", level)
        add(numpr, "numId", num_id)

    files["word/document.xml"] = etree.tostring(
        document, xml_declaration=True, encoding="UTF-8", standalone="yes"
    )
    with zipfile.ZipFile(output, "w", zipfile.ZIP_DEFLATED) as archive:
        for name, data in files.items():
            archive.writestr(name, data)


def main() -> None:
    parser = argparse.ArgumentParser()
    parser.add_argument("source", type=Path)
    parser.add_argument("output", type=Path)
    args = parser.parse_args()
    apply_numbering(args.source, args.output)


if __name__ == "__main__":
    main()
