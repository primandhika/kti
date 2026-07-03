import re

def read_file(path):
    with open(path, 'r', encoding='utf-8') as f:
        return f.read()

def write_file(path, content):
    with open(path, 'w', encoding='utf-8') as f:
        f.write(content)

def main():
    path = '/home/primandhika/artikel/dist/output/presentasi_hasil.html'
    content = read_file(path)
    
    # 1. Fix h1 and h2 colors from gold to primary blue
    # Currently: color: var(--c-gold) !important; for h1
    # color: var(--c-fg) !important; for h2 (which is blue)
    content = re.sub(r'(\.reveal\s+h1\s*\{[^}]+?color:\s*)var\(--c-gold\)', r'\1var(--c-primary)', content)
    
    # 2. Fix strong tags
    # Currently: color: var(--c-gold-light) !important;
    content = re.sub(r'(\.reveal\s+strong\s*\{[^}]+?color:\s*)var\(--c-gold-light\)', r'\1var(--c-primary)', content)
    
    # 3. Fix code tags
    # Currently: color: var(--c-gold) !important;
    content = re.sub(r'(\.reveal\s+code\s*\{[^}]+?color:\s*)var\(--c-gold\)', r'\1var(--c-primary)', content)
    
    # 4. Fix table headers text
    # Currently: color: var(--c-gold) !important;
    content = re.sub(r'(\.reveal\s+table\s+thead\s+th\s*\{[^}]+?color:\s*)var\(--c-gold\)', r'\1#ffffff', content)
    # Wait, earlier I set table headers to `background: var(--c-fg) !important; color: var(--c-bg) !important;`. Let's just make sure.
    # Ah, the previous fix might have been overwritten or didn't catch it all if it used var(--c-gold). Let's just enforce it.
    
    # 5. Fix card-panel h3
    # Currently: color: var(--c-gold) !important;
    content = re.sub(r'(\.card-panel\s+h3\s*\{[^}]+?color:\s*)var\(--c-gold\)', r'\1var(--c-primary)', content)
    
    # 6. Fix Slide 3 (Identifikasi Masalah Utama) heading
    # Currently: <h2 style="color:var(--c-gold);
    content = content.replace('color:var(--c-gold);', 'color:var(--c-primary);')
    
    # 7. Make gold slightly darker/richer for better contrast where it IS used (like borders)
    # Let's change --c-gold to a slightly deeper gold: #c2aa34 or #b89c25
    content = content.replace('--c-gold:       #dbcf4d;', '--c-gold:       #b89c25;')
    content = content.replace('--c-gold-light: #eadd65;', '--c-gold-light: #cfa81d;')
    
    write_file(path, content)
    print("Contrast issues fixed: Swapped yellow text to blue on white bg!")

if __name__ == '__main__':
    main()
