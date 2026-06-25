import re

def clean_text(text):
    # Remove weird indentation for headers
    text = re.sub(r'^[ \t]+(DESKRIPSI|INSTRUKSI|PETUNJUK|KISI|IDENTITAS)', r'## \1', text, flags=re.MULTILINE)
    text = re.sub(r'^[ \t]+([A-Z\s]{5,})$', r'### \1', text, flags=re.MULTILINE)
    return text

def convert_to_md(filepath, title):
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()
    
    content = clean_text(content)
    md = f"\n\n---\n\n## {title}\n\n"
    # Wrap in markdown quote or just plain text so it keeps its shape
    md += content
    return md

l1_path = "/home/primandhika/artikel/dist/main/02_Lampiran_01_Instrumen_Penelitian.md"
with open(l1_path, "r", encoding='utf-8') as f:
    existing = f.read()

# I will keep the existing A (Angket Metakognitif) and B (Prompt Tes Bicara) and C (Rubrik Bicara) 
# and then replace D, E, F with the FULL content of the remaining files.
parts = existing.split("## D. Angket Respons Mahasiswa Pengguna Media")
top_part = parts[0]

files_to_append = [
    ("/home/primandhika/artikel/dist/instruments/md/angket_respons_mahasiswa.md", "D. Angket Respons Mahasiswa"),
    ("/home/primandhika/artikel/dist/instruments/md/lembar_validasi_materi.md", "E. Lembar Validasi Ahli Materi"),
    ("/home/primandhika/artikel/dist/instruments/md/lembar_validasi_media.md", "F. Lembar Validasi Ahli Media"),
    ("/home/primandhika/artikel/dist/instruments/md/pedoman_wawancara.md", "G. Pedoman Wawancara"),
    ("/home/primandhika/artikel/dist/instruments/md/lembar_observasi.md", "H. Lembar Observasi")
]

final_md = top_part
for fp, title in files_to_append:
    final_md += convert_to_md(fp, title)

with open(l1_path, "w", encoding='utf-8') as f:
    f.write(final_md)

print("Berhasil menggabungkan teks instrumen secara penuh!")
