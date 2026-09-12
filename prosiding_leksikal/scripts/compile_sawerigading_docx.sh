#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "$0")/.." && pwd)"
INPUT_MD="$ROOT_DIR/outputs/draf_prosiding_framing_leksikal_sawerigading_v1.md"
REFERENCE_DOC="$ROOT_DIR/template/Sawergading_Template.docx"
OUTPUT_DOCX="$ROOT_DIR/outputs/draf_prosiding_framing_leksikal_sawerigading_v1.docx"

if ! command -v pandoc >/dev/null 2>&1; then
  echo "pandoc tidak ditemukan di PATH" >&2
  exit 1
fi

if [[ ! -f "$INPUT_MD" ]]; then
  echo "File input tidak ditemukan: $INPUT_MD" >&2
  exit 1
fi

if [[ ! -f "$REFERENCE_DOC" ]]; then
  echo "Reference doc tidak ditemukan: $REFERENCE_DOC" >&2
  exit 1
fi

pandoc \
  "$INPUT_MD" \
  --from=gfm+smart \
  --to=docx \
  --standalone \
  --reference-doc="$REFERENCE_DOC" \
  --output="$OUTPUT_DOCX"

python3 "$ROOT_DIR/scripts/postprocess_sawerigading_docx.py" "$OUTPUT_DOCX"

echo "Berhasil membuat: $OUTPUT_DOCX"
