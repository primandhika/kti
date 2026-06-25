import re
import glob
import os

terms = [
    r"microlearning",
    r"self-monitoring",
    r"self-evaluation",
    r"conceptualisation",
    r"formulation",
    r"articulation",
    r"rater",
    r"inter-rater reliability",
    r"inter-rater",
    r"pretest",
    r"posttest",
    r"expert judgment",
    r"try out",
    r"smartphone",
    r"desktop",
    r"mobile",
    r"user interface",
    r"bite-sized",
    r"learning by teaching",
    r"problem based learning",
    r"effect size",
    r"paired t-test",
    r"independent t-test",
    r"joint display",
    r"peer review",
    r"gap",
    r"state of the art",
    r"mixed methods",
    r"sequential explanatory",
    r"focus group discussion",
    r"record",
    r"timestamp",
    r"outline",
    r"filler words",
    r"eye contact",
    r"performance assessment",
    r"user experience",
    r"usability",
    r"engagement"
]

# We want to match case-insensitively for the word, but preserve its original casing.
# Also, we must not match if the word is already surrounded by * or _
# We can use a regex replacement function.
# The negative lookbehind `(?<![\*\_])` and negative lookahead `(?![\*\_])` ensure it isn't already italicized.

# Note: terms might have spaces, so \b is fine at the boundaries.
# We also want to avoid matching words inside markdown links [Text](URL) or URLs.
# But for simplicity, the negative lookbehind for * or _ and maybe `[` or `]` is good.

def italicize(match):
    return f"*{match.group(1)}*"

def process_file(filepath):
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()

    total_replacements = 0
    new_content = content
    
    for term in terms:
        # Build regex for this term
        # It handles ignoring matches inside words (using \b)
        # and ignores if preceded by * or _, or followed by * or _
        # Ignore if inside an HTML tag or link, but a simple heuristic is just checking asterisks.
        pattern = re.compile(r'(?<![\*\_a-zA-Z])(' + term + r')\b(?![\*\_a-zA-Z])', re.IGNORECASE)
        
        # We need a custom replacement to count and preserve original case
        def repl(m):
            nonlocal total_replacements
            total_replacements += 1
            return f"*{m.group(1)}*"
            
        new_content = pattern.sub(repl, new_content)

    if total_replacements > 0:
        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(new_content)
            
    return total_replacements

files = glob.glob("/home/primandhika/artikel/dist/main/01_BAB_*.md")
total_all = 0
for filepath in files:
    count = process_file(filepath)
    total_all += count
    print(f"File {os.path.basename(filepath)}: {count} replacements")

print(f"Total replacements: {total_all}")

# Create revision note
rev_path = "/home/primandhika/artikel/dist/_rev/Istilah_Asing_rev.md"
rev_note = f"""# Catatan Revisi Istilah Asing

**Tanggal:** 22 Juni 2026
**File yang Direvisi:** Seluruh `main/01_BAB_*.md`

## Rincian Perubahan:
1. Melakukan pemindaian (*scanning*) otomatis pada seluruh bab utama (BAB I sampai BAB V).
2. Mencetak miring (mengubah format menjadi *italic*) pada seluruh kemunculan istilah asing/non-bahasa Indonesia yang sebelumnya luput dan tercetak tegak.
3. Total istilah yang diperbaiki sebanyak {total_all} kemunculan, mencakup kata-kata seperti *microlearning*, *pretest*, *posttest*, *mixed methods*, *expert judgment*, *self-evaluation*, *effect size*, dll.
"""
with open(rev_path, "w", encoding='utf-8') as f:
    f.write(rev_note)
