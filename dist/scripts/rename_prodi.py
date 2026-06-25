import os

main_dir = '/home/primandhika/artikel/dist/main'
files_to_check = [
    '00_Cover.md',
    '00_Lembar_Persetujuan.md',
    '00_Kata_Pengantar.md',
    '00_Daftar_Isi.md',
    '01_BAB_I_PENDAHULUAN.md',
    '01_BAB_II_TINJAUAN_TEORI_KERANGKA_TEORETIK.md',
    '01_BAB_III_METODOLOGI_PENELITIAN.md',
    '01_BAB_IV_TEMUAN_DAN_PEMBAHASAN.md',
    '01_BAB_V_KESIMPULAN_IMPLIKASI_DAN_SARAN.md'
]

for filename in files_to_check:
    filepath = os.path.join(main_dir, filename)
    if os.path.exists(filepath):
        with open(filepath, 'r', encoding='utf-8') as f:
            content = f.read()
        
        # Avoid double replacing
        content = content.replace("Pendidikan Bahasa dan Sastra Indonesia", "Pendidikan Bahasa Indonesia")
        content = content.replace("pendidikan bahasa dan sastra Indonesia", "pendidikan bahasa Indonesia")
        
        # Apply the fix
        content = content.replace("Pendidikan Bahasa Indonesia", "Pendidikan Bahasa dan Sastra Indonesia")
        content = content.replace("pendidikan bahasa Indonesia", "pendidikan bahasa dan sastra Indonesia")
        
        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(content)
        
        print(f"Processed {filename}")
