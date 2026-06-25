import re

bib_path = "/home/primandhika/artikel/dist/main/01_DAFTAR_PUSTAKA.md"
with open(bib_path, 'r', encoding='utf-8') as f:
    lines = f.readlines()

# The file might have a title like "# DAFTAR PUSTAKA\n\n"
header = ""
entries_raw = []
current_entry = ""

in_header = True
for line in lines:
    if line.strip() == "```":
        continue # skip stray backticks
    
    if in_header:
        if line.startswith("#") or line.strip() == "":
            header += line
            continue
        else:
            in_header = False
            
    # If the line starts with a word and a comma, or a parenthesis (like a year), it's probably continuing the same entry unless it looks like a new author.
    # But actually, many entries are broken across lines.
    # Usually, an entry starts with an Author's last name (Capital letter, no indent).
    # If it's a completely blank line, it might separate entries.
    if line.strip() == "":
        if current_entry.strip() != "":
            entries_raw.append(current_entry.strip())
            current_entry = ""
        continue
    
    # Heuristic: if current_entry is empty, start a new one.
    # If current_entry is NOT empty, does this line look like a new entry?
    # Actually, the original file had newlines inside a single entry.
    # Let's just join them if they don't have a blank line between them?
    # Wait, in the original file (e.g. lines 125-127), Khlaif spans 3 lines WITHOUT blank lines.
    # BUT, the next entry "Kim, D., & Downey..." starts on the next line!
    # So there ARE NO blank lines between most entries!
    # How to distinguish a new entry from a wrapped line?
    # A new entry usually starts with a Capital letter, followed by letters, a comma, and initials.
    # E.g., "Mayer, R. E." or "Miles, M. B."
    
    # A better approach: an entry ends when the next line starts a new entry.
    # A line starts a new entry if it matches `^[A-Z][a-z]+, [A-Z]\.` or similar author patterns.
    # Alternatively, let's just use the fact that I can re-format everything.
    pass

# Let's read the whole file text instead
with open(bib_path, 'r', encoding='utf-8') as f:
    content = f.read()

# Remove stray backticks
content = content.replace("```\n", "").replace("```", "")

# Remove header
header_match = re.match(r'^(# DAFTAR PUSTAKA\s*\n+)', content)
if header_match:
    header = header_match.group(1)
    content = content[len(header):]
else:
    header = "# DAFTAR PUSTAKA\n\n"

# The text has multiple entries. Many are wrapped with single newlines.
# Blank lines separate my appended entries at the bottom.
# Let's first normalize: replace double newlines with single newlines? No, that merges distinct appended entries.

# Let's split by double newlines first to get chunks.
chunks = re.split(r'\n\s*\n', content)

processed_entries = []

for chunk in chunks:
    chunk = chunk.strip()
    if not chunk:
        continue
    
    # In the main block, entries are separated by a single newline? NO, a single entry can span multiple lines!
    # Example:
    # Kim, D., & Downey, S. (2016). Examining the Use of the ASSURE Model by K–12
    # Teachers. Computers in the Schools, 33(3), 153–168.
    # https://doi.org/10.1080/07380569.2016.1203208
    # Latifah, A., & Prastowo, A. (2020). Analisis Pembelajaran Daring Model Website Dan M-...
    
    # Notice that there are NO blank lines between Kim and Latifah.
    # We must use regex to find the START of an entry.
    # An entry usually starts with: Word, Initial. (Year).
    # Regex for start of entry: ^[A-Z][A-Za-z\-]+, (?:[A-Z]\.\s*)+(?:& [A-Z][A-Za-z\-]+, (?:[A-Z]\.\s*)+)*(?:\(\d{4}[a-z]?\))?
    # This is too complex.
    
    # A simpler heuristic: lines that start with an uppercase letter or bracket, and don't start with a lowercase letter or number or "http".
    # Actually, let's just split by looking for ` (20` or ` (19` which indicates a year.
    # Every valid bibliography entry has ` (Year).` near the beginning.
    
    # Let's split the chunk into lines.
    lines = chunk.split('\n')
    current = ""
    for line in lines:
        line_clean = line.strip()
        if not line_clean: continue
        
        # Check if line_clean starts a new entry
        # Typically: "Author, A. (Year)." or "Author, A., & Author, B. (Year)."
        # If the line contains " (" followed by 4 digits and ")", it's likely a new entry, 
        # UNLESS it's a continuation of a title that happens to have a year.
        # But usually new entries start at the beginning of the line.
        
        # Let's check if the line starts with a Capital letter and has a year like (20xx) or (19xx) in the first 100 chars.
        is_new_entry = False
        if re.match(r'^[A-Z]', line_clean):
            # Check for year
            match = re.search(r'\(\d{4}[a-z]?\)', line_clean[:150])
            if match:
                is_new_entry = True
                
        # Some entries like "Kementerian Pendidikan..."
        if line_clean.startswith("Kementerian"):
            is_new_entry = True
            
        # If it's a new entry and we have a current one, save it
        if is_new_entry and current:
            processed_entries.append(current.strip())
            current = ""
            
        # Append line to current
        if current:
            current += " " + line_clean
        else:
            current = line_clean
            
    if current:
        processed_entries.append(current.strip())

# Clean up whitespace in entries
final_entries = []
for e in processed_entries:
    # replace multiple spaces with single space
    e = re.sub(r'\s+', ' ', e)
    final_entries.append(e)

# Sort alphabetically
final_entries.sort(key=lambda x: x.lower())

# Output
with open(bib_path, 'w', encoding='utf-8') as f:
    f.write(header)
    for e in final_entries:
        f.write(e + "\n\n")

print(f"Berhasil mengurutkan {len(final_entries)} referensi!")
