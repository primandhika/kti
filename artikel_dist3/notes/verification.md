# Verification Notes

Date: 2026-08-27

## Scope of checks performed

1. Confirmed current CALL-EJ author guidance from the live journal site.
2. Extracted the local CALL-EJ template structure from `CALLEJ-Template.docx`.
3. Recomputed key descriptive statistics from source CSV files using `scripts/quick_verify_stats.py`.
4. Retrieved metadata and abstracts for selected CALL-EJ speaking-related articles and the Mahdi (2022) reference article.

## Important journal notes

- CALL-EJ currently requires APA 7, full-length articles around 6,000-8,000 words, abstract no more than 200 words, and up to 5 keywords.
- The local template still contains an outdated line that says `References [APA sixth edition]`. For drafting, APA 7 should follow the live site guidance, not the outdated template line.

## Descriptive statistics rechecked from source CSV

### Speaking scores
- Experimental group: pre `M=70.84`, post `M=82.70`, gain `M=11.86`, `n=40`
- Control group: pre `M=71.21`, post `M=79.46`, gain `M=8.25`, `n=37`
- Paired dataset size: `77`

### Metacognitive awareness
- Experimental group: pre `M=26.40`, post `M=50.95`, gain `M=24.55`, `n=40`
- Control group: pre `M=31.89`, post `M=48.35`, gain `M=16.46`, `n=37`

### Post-use response data
- `respons_mahasiswa.csv`: `n=40`

## Inferential figures used in the draft

The draft also uses inferential values that are documented in the project analysis notes:
- `dist/data/field_test/ALUR_PERHITUNGAN_KETERAMPILAN_BERBICARA.md`
- `dist/data/field_test/TINDAK_LANJUT_ANALISIS_KETERAMPILAN_BERBICARA.md`
- `dist/data/field_test/catatan_pembahasan_metakognitif.md`

These values were not fully re-derived from scratch in this pass. Before submission, the notebooks should be rerun from a clean kernel and the final manuscript tables should be matched line by line against notebook outputs.

## Main limitations already visible

- Speaking results are promising, but the design remains quasi-experimental.
- The speaking analysis notes explicitly warn about rater-related issues and the need to preserve the frozen 77-pair dataset.
- The media-use questionnaire produced weak internal consistency in the existing project note, so its results should be treated descriptively and cautiously.
