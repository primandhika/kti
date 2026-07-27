#!/usr/bin/env python3
import re

INPUT = "/home/primandhika/artikel/dist/output/presentasi_hasil.html"

with open(INPUT, "r", encoding="utf-8") as f:
    html = f.read()

# I will replace the CSS block for .study-card
# Current CSS block:
# .study-card { ... padding: 12px 10px; ... }
# .study-card h4 { ... line-height: 1.2; ... }
# .study-card p { ... line-height: 1.35; ... }

# New CSS block
start_str = "<style>\n.study-card {"
end_str = "</style>\n\n<div style=\"display:flex"

start_idx = html.find(start_str)
end_idx = html.find(end_str)

if start_idx != -1 and end_idx != -1:
    old_style = html[start_idx:end_idx + len("</style>")]
    
    new_style = """<style>
.study-card {
   background: var(--c-bg-card);
   border: 1px solid var(--c-border);
   border-radius: 14px;
   padding: 16px 14px;
   text-align: center;
   transition: transform 0.3s ease, box-shadow 0.3s ease, background 0.3s ease, border-color 0.3s ease;
   box-shadow: 0 4px 12px rgba(0,0,0,0.25);
   flex: 1 1 30%;
   min-width: 180px;
   cursor: default;
}
.study-card:hover {
   transform: translateY(-6px) scale(1.02);
   box-shadow: 0 10px 24px rgba(201,168,76,0.25);
   background: linear-gradient(145deg, var(--c-navy), #132040);
   border-color: var(--c-gold);
}
.study-card i {
   font-size: 1.6em;
   color: var(--c-gold);
   margin-bottom: 6px;
   transition: transform 0.3s ease;
}
.study-card:hover i {
   transform: scale(1.15) rotate(5deg);
}
.study-card h4 {
   font-size: 0.75em;
   color: var(--c-fg);
   margin: 0 0 6px 0;
   line-height: 1.1;
   font-family: var(--font-heading);
}
.study-card p {
   font-size: 0.55em;
   color: var(--c-fg3);
   margin: 0;
   line-height: 1.15;
}
</style>"""
    
    html = html.replace(old_style, new_style)

    # I'll also modify the gap of the flex container to be slightly more spacious
    html = html.replace('gap:10px; margin-bottom:12px;', 'gap:14px; margin-bottom:16px;')

    with open(INPUT, "w", encoding="utf-8") as f:
        f.write(html)
    print("Slide 3 CSS updated successfully with better UI padding and line-height!")
else:
    print("Failed to find slide 3 style block.")
