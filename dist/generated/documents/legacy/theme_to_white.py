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
    
    # 1. Update CSS variables to a white theme
    css_vars_dark = """:root {
  --c-navy:       #0a1628;
  --c-navy-mid:   #132040;
  --c-navy-light: #1a2744;
  --c-gold:       #c9a84c;
  --c-gold-light: #d4b85c;
  --c-gold-dim:   rgba(201,168,76,0.25);
  --c-fg:         #e8e4dc;
  --c-fg2:        #c8c2b6;
  --c-fg3:        #9e978a;
  --c-border:     rgba(201,168,76,0.3);
  --c-bg:         #0a1628;
  --c-bg-alt:     rgba(26,39,68,0.6);
  --c-bg-card:    rgba(19,32,64,0.85);"""
    
    css_vars_light = """:root {
  --c-navy:       #ffffff;
  --c-navy-mid:   #f5f5f5;
  --c-navy-light: #e0e0e0;
  --c-gold:       #c9a84c;
  --c-gold-light: #d4b85c;
  --c-gold-dim:   rgba(201,168,76,0.15);
  --c-fg:         #111111;
  --c-fg2:        #333333;
  --c-fg3:        #555555;
  --c-border:     rgba(0,0,0,0.1);
  --c-bg:         #ffffff;
  --c-bg-alt:     #f8f9fa;
  --c-bg-card:    #ffffff;"""
    
    content = content.replace(css_vars_dark, css_vars_light)
    
    # 2. Fix table colors (dark theme had different borders/bg for tables)
    table_css_dark = """.reveal table thead th {
  background: var(--c-navy-mid) !important;
  color: var(--c-gold-light) !important;
  font-weight: 700 !important;
  padding: 8px 12px !important;
  text-align: center !important;
  border-bottom: 2px solid var(--c-gold) !important;
}"""
    table_css_light = """.reveal table thead th {
  background: var(--c-fg) !important;
  color: var(--c-bg) !important;
  font-weight: 700 !important;
  padding: 8px 12px !important;
  text-align: center !important;
  border-bottom: 2px solid var(--c-fg) !important;
}"""
    content = content.replace(table_css_dark, table_css_light)
    
    # 3. Fix image sizing (user said slide 14 image is cut off)
    # Give all images a strict max-height and object-fit
    img_css_dark = """.reveal img {
  max-width: 100% !important;
  height: auto !important;
  border-radius: 6px;
}
.reveal img.chart {
  display: block;
  margin: 10px auto;
  max-height: 62vh;
  border: none;
  box-shadow: 0 4px 20px rgba(0,0,0,0.4);
}"""
    
    img_css_light = """.reveal img {
  max-width: 100% !important;
  max-height: 60vh !important;
  height: auto !important;
  width: auto !important;
  object-fit: contain !important;
  border-radius: 6px;
}
.reveal img.chart {
  display: block;
  margin: 10px auto;
  max-height: 60vh !important;
  border: none;
  box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}"""
    content = content.replace(img_css_dark, img_css_light)
    
    # Also fix any inline styles hardcoded to dark colors in slide 3 or cover
    # For example: data-background-color="#0a1628" -> "#ffffff"
    content = re.sub(r'data-background-color="#0a1628"', 'data-background-color="#ffffff"', content)
    content = re.sub(r'background-color:\s*#0a1628', 'background-color: #ffffff', content)
    
    # Make sure we add class="chart" to images that don't have it, or just ensure max-height: 60vh in style
    # The user said the image is cut off. Let's add style="max-height: 60vh; object-fit: contain;" to all <img> tags that have base64
    content = re.sub(r'(<img\s+src="data:image/png;base64,[^"]+")(/?\s*>)', r'\1 style="max-height:60vh; width:auto; object-fit:contain; box-shadow:none;"\2', content)

    write_file(path, content)
    print("Theme inverted to white and images constrained to safe zone!")

if __name__ == '__main__':
    main()
