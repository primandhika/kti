#!/usr/bin/env python3
import re

INPUT = "/home/primandhika/artikel/dist/output/presentasi_hasil.html"

with open(INPUT, "r", encoding="utf-8") as f:
    html = f.read()

# Remove the border and background from .ref-col
old_css = """  .ref-col {
     flex: 1 1 30%;
     padding: 12px 14px;
     background: rgba(0,0,0,0.15);
     border-top: 3px solid var(--c-gold);
     border-radius: 6px;
     font-size: 0.45em !important;
     color: var(--c-fg3);
     line-height: 1.4 !important;
     text-align: left;
  }"""

new_css = """  .ref-col {
     flex: 1 1 30%;
     padding: 5px 10px;
     font-size: 0.45em !important;
     color: var(--c-fg3);
     line-height: 1.4 !important;
     text-align: left;
  }"""

if old_css in html:
    html = html.replace(old_css, new_css)
    with open(INPUT, "w", encoding="utf-8") as f:
        f.write(html)
    print("Border and background removed successfully from references.")
else:
    print("Failed to find the exact CSS block for ref-col.")
