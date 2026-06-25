import re

file_path = "/home/primandhika/artikel/dist/main/01_BAB_IV_TEMUAN_DAN_PEMBAHASAN.md"

with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

# We want to replace Tabel 4.N with Tabel 4.(N+1) for N from 23 down to 2
for i in range(23, 1, -1):
    old_str = f"Tabel 4.{i}"
    new_str = f"Tabel 4.{i+1}"
    content = content.replace(old_str, new_str)

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)

print("Tables renumbered successfully.")
