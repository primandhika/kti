import csv

lampiran_file = "/home/primandhika/artikel/dist/main/02_Lampiran_02_Pengujian_Validitas_dan_Reliabilitas.md"

def csv_to_markdown(file_path):
    with open(file_path, newline='', encoding='utf-8') as f:
        reader = csv.reader(f)
        data = list(reader)
    if not data:
        return ""
    headers = data[0]
    md = "| " + " | ".join(headers) + " |\n"
    md += "| " + " | ".join(["---"] * len(headers)) + " |\n"
    for row in data[1:]:
        md += "| " + " | ".join(row) + " |\n"
    return md

materi_md = csv_to_markdown("/home/primandhika/artikel/dist/data/validasi/validasi_materi.csv")
media_md = csv_to_markdown("/home/primandhika/artikel/dist/data/validasi/validasi_media.csv")

append_content = f"""

## D. Data Asli dan Skrip Pengolahan

Berikut adalah rekapitulasi data asli hasil validasi dari ahli materi dan ahli media dari direktori `dist/data/validasi/`, beserta skrip Python yang digunakan untuk mengolah data penelitian ini.

### 1. Data Validasi Ahli Materi
{materi_md}

### 2. Data Validasi Ahli Media
{media_md}

### 3. Skrip Python Pengolahan Aiken's V

```python
def calculate_aiken_v(df_rows, validators_cols_indices, c=4, lo=1):
    \"\"\"
    Menghitung Aiken's V untuk setiap butir
    V = Sum(s) / (n * (c - 1))
    s = r - lo
    \"\"\"
    n = len(validators_cols_indices)
    v_scores = []
    
    for row in df_rows:
        sum_s = 0
        for col_idx in validators_cols_indices:
            r = float(row[col_idx])
            s = r - lo
            sum_s += s
        
        v = sum_s / (n * (c - 1))
        v_scores.append(round(v, 3))
        
    return v_scores
```

### 4. Skrip Pengolahan Validitas Empiris (Pearson) dan Reliabilitas (Cronbach's Alpha)

Berikut adalah algoritma Python yang digunakan dalam *Jupyter Notebook* peneliti untuk menghitung *r-hitung* (*Pearson Product Moment*) dan konsistensi internal (*Alpha Cronbach*).

```python
import math

def pearson_r(x, y):
    \"\"\"Menghitung korelasi Pearson Product Moment\"\"\"
    n = len(x)
    sum_x = sum(x)
    sum_y = sum(y)
    sum_xy = sum(xi*yi for xi, yi in zip(x, y))
    sum_x2 = sum(xi**2 for xi in x)
    sum_y2 = sum(yi**2 for yi in y)
    
    numerator = n * sum_xy - sum_x * sum_y
    denominator = math.sqrt((n * sum_x2 - sum_x**2) * (n * sum_y2 - sum_y**2))
    
    if denominator == 0: return 0
    return numerator / denominator

def cronbach_alpha(matrix):
    \"\"\"
    Menghitung Alpha Cronbach
    matrix = list of lists, [responden][butir]
    \"\"\"
    n_items = len(matrix[0])
    n_resp = len(matrix)
    
    item_variances = []
    for i in range(n_items):
        item_scores = [resp[i] for resp in matrix]
        mean_i = sum(item_scores) / n_resp
        var_i = sum((x - mean_i)**2 for x in item_scores) / (n_resp - 1)
        item_variances.append(var_i)
        
    total_scores = [sum(resp) for resp in matrix]
    mean_tot = sum(total_scores) / n_resp
    var_tot = sum((x - mean_tot)**2 for x in total_scores) / (n_resp - 1)
    
    if var_tot == 0: return 0
    
    alpha = (n_items / (n_items - 1)) * (1 - (sum(item_variances) / var_tot))
    return alpha
```
"""

with open(lampiran_file, "a", encoding='utf-8') as f:
    f.write(append_content)

print("Berhasil menambahkan tabel data dan script ke Lampiran 02 menggunakan library csv standar")
