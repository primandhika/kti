import os
import re

main_dir = '/home/primandhika/artikel/dist/main'
files_to_check = [
    '01_BAB_I_PENDAHULUAN.md',
    '01_BAB_II_TINJAUAN_TEORI_KERANGKA_TEORETIK.md',
    '01_BAB_III_METODOLOGI_PENELITIAN.md',
    '01_BAB_IV_TEMUAN_DAN_PEMBAHASAN.md',
    '01_BAB_V_KESIMPULAN_IMPLIKASI_DAN_SARAN.md'
]

tabel_pattern = re.compile(r'\*\*Tabel\s+(\d+\.\d+)\*\*\s+(.*)')
gambar_pattern = re.compile(r'Gambar\s+(\d+\.\d+)\s+(.*)')

daftar_tabel = []
daftar_gambar = []

for filename in files_to_check:
    filepath = os.path.join(main_dir, filename)
    if os.path.exists(filepath):
        with open(filepath, 'r', encoding='utf-8') as f:
            for line in f:
                t_match = tabel_pattern.search(line)
                if t_match:
                    daftar_tabel.append((t_match.group(1), t_match.group(2).strip()))
                
                # Biar nggak dobel dengan gambar yang ada di dalam deskripsi tabel atau yang lain
                if line.startswith('Gambar ') and not line.startswith('Gambar ini'):
                    g_match = gambar_pattern.search(line)
                    if g_match:
                        # Pengecualian, pastikan tidak mengambil kalimat biasa. Biasanya caption di markdown standalone
                        caption = g_match.group(2).strip()
                        # Jika terlalu panjang mungkin itu paragraf yang dimulai dengan 'Gambar ...'
                        if len(caption) < 150:
                            daftar_gambar.append((g_match.group(1), caption))

# Tulis Daftar Tabel
with open(os.path.join(main_dir, '00_Daftar_Tabel.md'), 'w', encoding='utf-8') as f:
    f.write('# DAFTAR TABEL\n\n')
    for num, caption in daftar_tabel:
        f.write(f"Tabel {num} {caption}\n\n")

# Tulis Daftar Gambar
with open(os.path.join(main_dir, '00_Daftar_Gambar.md'), 'w', encoding='utf-8') as f:
    f.write('# DAFTAR GAMBAR\n\n')
    for num, caption in daftar_gambar:
        f.write(f"Gambar {num} {caption}\n\n")

print(f"Generated {len(daftar_tabel)} tables and {len(daftar_gambar)} figures.")
