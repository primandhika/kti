#!/bin/bash

# Script to convert markdown draft to a template-ready DOCX using pandoc.
# This makes it easier to copy and paste the content into the official
# EduLearn Word template.

INPUT_FILE="draft-v2-edulearn.md"
OUTPUT_DIR="final-output"
OUTPUT_FILE="${OUTPUT_DIR}/draft-v2-edulearn-template-ready.docx"

# Ensure output directory exists
mkdir -p "${OUTPUT_DIR}"

# Check if pandoc is installed
if ! command -v pandoc &> /dev/null; then
    echo "Error: pandoc is not installed."
    echo "Please install pandoc first (e.g., 'sudo apt install pandoc' on Debian/Ubuntu, or 'brew install pandoc' on macOS)."
    exit 1
fi

echo "Converting ${INPUT_FILE} to ${OUTPUT_FILE}..."

# Convert using pandoc with the template as a reference document
# This applies the styles (fonts, spacing, etc.) from the template to the output.
pandoc "${INPUT_FILE}" \
    -o "${OUTPUT_FILE}" \
    --from markdown \
    --to docx \
    --reference-doc="template/Article-Template-edulearn.docx" \
    --highlight-style=tango

if [ $? -eq 0 ]; then
    echo "Conversion successful!"
    echo "You can now open ${OUTPUT_FILE} and copy the content into 'template/Article-Template-edulearn.docx'."
else
    echo "Conversion failed."
    exit 1
fi
