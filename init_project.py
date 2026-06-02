import os
import sys
import time
from datetime import datetime

# --- CONFIGURATION / GLOBAL RULES ---
AUTHOR_NAME = "Restu Bias Primandhika"
DEFAULT_REF_STYLE = "APA 7"
GLOBAL_RULES = [
    "Pertahankan sistematika struktur naskah akademik.",
    "Hindari penggunaan em dash (—) dalam penulisan apalagi emoji.",
    "Pastikan sitasi dipertahankan jika masih relevan.",
    "Gunakan variasi sitasi, jangan hanya 1 jenis. Kadang Author (Year) atau 'text text text text (Author,Year)' atau (Author 1, Year;Author 2, Year dst.) atau 'Author 1 (Year) and Author 2 (Year) argues that...'; ",
    "Buat catatan revisi di folder /_rev untuk setiap perubahan besar.",
    "Gunakan gaya bahasa yang humanis, paragraf tidak perlu berpola kalimatnya."
]

def print_banner():
    print("\033[94m" + "="*50)
    print("   ACADEMIC PROJECT INITIALIZER (ULTIMATE v3)")
    print("="*50 + "\033[0m")

def normalize_slug(text):
    words = "".join(c if c.isalnum() or c.isspace() else "" for c in text).lower().split()
    return "_".join(words)

def get_input_with_validation(prompt, default=None):
    prompt_text = f"{prompt} [{default}]" if default else prompt
    while True:
        user_input = input(f"\033[93m{prompt_text} (atau 'q' untuk batal):\033[0m ").strip()
        if user_input.lower() == 'q':
            print("\033[91mOperasi dibatalkan.\033[0m")
            sys.exit(0)
        if not user_input and default:
            return default
        if not user_input:
            print("Input tidak boleh kosong.")
            continue
        return user_input

def progress_bar(task_name, duration=1.0):
    print(f"\033[92m{task_name:30}\033[0m", end="")
    steps = 20
    for i in range(steps + 1):
        percent = int((i / steps) * 100)
        bar = "█" * i + "-" * (steps - i)
        print(f"\r\033[92m{task_name:30} [{bar}] {percent}%\033[0m", end="")
        time.sleep(duration / steps)
    print()

def create_project():
    print_banner()
    
    # 1. Verification of Rules
    print("\n\033[1mAturan Penulisan Global:\033[0m")
    for i, rule in enumerate(GLOBAL_RULES, 1):
        print(f"{i}. {rule}")
    
    confirm_rules = input("\nApakah aturan ini sudah sesuai untuk projek baru Anda? (y/n/q): ").strip().lower()
    if confirm_rules == 'q': sys.exit(0)
    if confirm_rules != 'y':
        print("\n\033[93mSilakan ubah variabel 'GLOBAL_RULES' di dalam skrip init_project.py untuk menyesuaikan aturan.\033[0m")
        sys.exit(0)

    # 2. Project Metadata
    full_title = get_input_with_validation("Judul Panjang Projek")
    
    # Folder naming logic
    timestamp = datetime.now().strftime("%d%m%y")
    suggested_slug = normalize_slug(full_title)
    words = suggested_slug.split("_")
    short_slug = "_".join(words[:2]) if len(words) > 2 else suggested_slug
    project_id = f"{short_slug}_E{timestamp}"
    
    print(f"\nSaran ID Folder: \033[96m{project_id}\033[0m")
    if input("Gunakan saran ini? (y/n): ").lower() != 'y':
        manual_id = get_input_with_validation("Masukkan ID singkat")
        project_id = f"{normalize_slug(manual_id)}_E{timestamp}"

    # 3. Project Type & Templates
    print("\nJenis Projek:")
    types = {
        "1": ("Artikel Jurnal Ilmiah", ["01_Pendahuluan.md", "02_Kajian_Pustaka.md", "03_Metodologi.md", "04_Hasil_dan_Pembahasan.md", "05_Kesimpulan.md", "06_Referensi.md"]),
        "2": ("Karya Tulis Akhir (Skripsi/Tesis/Disertasi)", ["BAB_I_Pendahuluan.md", "BAB_II_Kajian_Pustaka.md", "BAB_III_Metodologi.md", "BAB_IV_Temuan_dan_Pembahasan.md", "BAB_V_Kesimpulan.md", "Daftar_Pustaka.md"]),
        "3": ("Buku/Monograf", ["Prakata.md", "Bab_1.md", "Bab_2.md", "Bab_3.md", "Daftar_Pustaka.md"]),
        "4": ("Artikel Populer", ["Naskah_Utama.md", "Referensi_Singkat.md"]),
    }
    for k, v in types.items(): print(f"{k}. {v[0]}")
    
    choice = get_input_with_validation("Pilihan", "1")
    project_type, section_files = types.get(choice, ("Lainnya", ["Draft.md"]))

    focus = get_input_with_validation("Fokus Penelitian")
    method = get_input_with_validation("Metode Penelitian")
    ref_style = get_input_with_validation("Gaya Referensi", DEFAULT_REF_STYLE)

    # 4. Generation Process
    print("\n\033[1mMengeksekusi Inisialisasi...\033[0m")
    
    try:
        if os.path.exists(project_id):
            print(f"\033[91mError: Folder {project_id} sudah ada.\033[0m")
            return

        os.makedirs(project_id)
        
        # Folders
        folders = ["data", "main", "outputs", "template", "references", "scripts", "_rev"]
        for f in folders:
            os.makedirs(os.path.join(project_id, f))
            with open(os.path.join(project_id, f, ".gitkeep"), "w") as empty: pass
        progress_bar("Membangun Struktur Folder", 0.5)

        # Main sections
        for sf in section_files:
            with open(os.path.join(project_id, "main", sf), "w") as f:
                f.write(f"# {sf.replace('.md', '').replace('_', ' ')}\n\n(Mulai menulis di sini...)")
        progress_bar("Menyiapkan Template Naskah", 0.5)

        # PROJEK.md
        rules_text = "\n".join([f"- {r}" for r in GLOBAL_RULES])
        projek_md = f"""# PROJEK: {full_title}

## Metadata
- **ID Folder**: {project_id}
- **Penulis**: {AUTHOR_NAME}
- **Jenis**: {project_type}
- **Fokus**: {focus}
- **Metode**: {method}
- **Gaya Referensi**: {ref_style}
- **Status**: Persiapan Awal

## Aturan Penulisan (Writing Rules)
{rules_text}

## Struktur Kerja
- `data/`: Data mentah dan instrumen.
- `main/`: Naskah akademik (terbagi per bagian).
- `outputs/`: Hasil final siap cetak/submisi.
- `_rev/`: Catatan revisi dan draf sebelumnya.

---
*Generated by Academic Initializer on {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}*
"""
        with open(os.path.join(project_id, "PROJEK.md"), "w") as f:
            f.write(projek_md)
        progress_bar("Membuat File Konfigurasi", 0.3)

        # README.md
        readme_md = f"""# {full_title}

Arsip kerja untuk proyek: **{project_type}**.

## Deskripsi Singkat
{focus} menggunakan metode {method}.

## Cara Menggunakan Repositori Ini
1. Tulis naskah di folder `main/`.
2. Simpan referensi PDF di folder `references/`.
3. Gunakan folder `_rev/` untuk mendokumentasikan setiap masukan dari reviewer/promotor.

## Standar Penulisan
Proyek ini mengikuti aturan internal yang didefinisikan dalam `PROJEK.md`.
"""
        with open(os.path.join(project_id, "README.md"), "w") as f:
            f.write(readme_md)
        progress_bar("Finalisasi Dokumentasi", 0.3)

        print(f"\n\033[92m[DONE] Projek '{project_id}' siap digunakan!\033[0m")
        print(f"Jalankan: \033[96mcd {project_id}\033[0m\n")

    except Exception as e:
        print(f"\033[91mGagal: {e}\033[0m")

if __name__ == "__main__":
    try:
        create_project()
    except KeyboardInterrupt:
        print("\nKeluar...")
