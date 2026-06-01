#!/usr/bin/env python3
import sys
from pathlib import Path
try:
    import pikepdf
except ImportError:
    print("Error: 'pikepdf' library is not installed.")
    print("You can install it using: pip install pikepdf")
    sys.exit(1)

def find_pdfs(directory):
    return sorted(list(Path(directory).rglob("*.pdf")))

def main():
    print("=== PDF Page Extractor ===")
    
    # Search for PDFs in the current directory and subdirectories
    root_dir = Path(__file__).resolve().parents[1]
    pdfs = find_pdfs(root_dir)
    
    if not pdfs:
        print(f"Tidak ada file PDF ditemukan di {root_dir}")
        return

    print("\nDaftar file PDF ditemukan:")
    for i, pdf_path in enumerate(pdfs, 1):
        # Display relative path for clarity
        rel_path = pdf_path.relative_to(root_dir)
        print(f"{i}. {rel_path}")

    # User selection
    try:
        choice = int(input(f"\nPilih nomor file (1-{len(pdfs)}): "))
        if choice < 1 or choice > len(pdfs):
            print("Pilihan tidak valid.")
            return
        file_path = pdfs[choice - 1]
    except ValueError:
        print("Input harus berupa angka.")
        return

    try:
        pdf = pikepdf.Pdf.open(file_path)
        total_pages = len(pdf.pages)
        print(f"\nFile terpilih: {file_path.name}")
        print(f"Total halaman: {total_pages}")
        
        # Start page
        try:
            start_page = int(input(f"Halaman mulai (1-{total_pages}): "))
        except ValueError:
            print("Error: Input harus berupa angka.")
            return
            
        # End page
        try:
            end_page = int(input(f"Halaman selesai (1-{total_pages}): "))
        except ValueError:
            print("Error: Input harus berupa angka.")
            return

        if start_page < 1 or end_page > total_pages or start_page > end_page:
            print("Error: Rentang halaman tidak valid.")
            return

        # Output file path
        default_output = f"{file_path.stem}_extracted_{start_page}-{end_page}.pdf"
        output_dir = root_dir / "outputs"
        output_dir.mkdir(exist_ok=True)
        
        print(f"\nNama file output (default: {default_output})")
        output_path_str = input(f"Simpan di folder 'outputs/' dengan nama: ").strip()
        
        if not output_path_str:
            output_path = output_dir / default_output
        else:
            output_path = output_dir / output_path_str
            if output_path.suffix.lower() != '.pdf':
                output_path = output_path.with_suffix('.pdf')

        # Create new PDF and add pages
        new_pdf = pikepdf.Pdf.new()
        for i in range(start_page - 1, end_page):
            new_pdf.pages.append(pdf.pages[i])
            
        new_pdf.save(output_path)
        print(f"\nBerhasil! File disimpan di: {output_path}")
        
    except Exception as e:
        print(f"Terjadi kesalahan: {e}")
    finally:
        if 'pdf' in locals():
            pdf.close()

if __name__ == "__main__":
    main()
