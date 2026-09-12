# DOCX build pass

Date: 2026-08-27

## What changed
- Created `scripts/build_callej_docx.py` to generate a CALL-EJ-style `.docx` directly from the current Markdown draft.
- Used `template/CALLEJ-Template.docx` as the base document shell rather than exporting to a free-form Word file.
- Generated the working submission file at `papers/CALLEJ_article_final.docx`.

## Validation
- The build script executed successfully.
- The resulting `.docx` reopened successfully with `python-docx`.
- The output contains the expected title block, abstract panel, manuscript sections, and 5 total tables.

## Notes
- The `.docx` preserves the journal shell more closely than a generic conversion by filling the existing template structure and rebuilding the article body inside that document.
