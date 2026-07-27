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
    
    # 1. Fix slide 3 hover background
    content = content.replace('background: #112038;', 'background: #f0f0f0;')
    
    # 2. Fix slide 3 refs container color (was white semi-transparent)
    content = content.replace('color: rgba(255,255,255,0.6);', 'color: rgba(0,0,0,0.6);')
    
    # 3. Ensure image shadow is less harsh for white theme
    content = content.replace('box-shadow: 0 4px 16px rgba(0,0,0,0.4);', 'box-shadow: 0 4px 12px rgba(0,0,0,0.1);')
    content = content.replace('box-shadow: 0 12px 30px rgba(0,0,0,0.6);', 'box-shadow: 0 8px 24px rgba(0,0,0,0.15);')
    
    # 4. Make sure --c-bg is really #ffffff everywhere needed, wait I already did that.
    # Let's also check table borders, earlier I set them to rgba(0,0,0,0.1).
    
    # 5. Fix cover slide if there's any remaining hardcoded dark colors
    content = content.replace('color:#0a1628 !important;', 'color:#111111 !important;')
    content = content.replace('color:#132040 !important;', 'color:#333333 !important;')
    content = content.replace('background:linear-gradient(90deg, transparent, rgba(201,168,76,0.8), transparent);', 'background:linear-gradient(90deg, transparent, rgba(201,168,76,0.5), transparent);')
    
    # Write back
    write_file(path, content)
    print("Fixed remaining dark-theme inline styles!")

if __name__ == '__main__':
    main()
