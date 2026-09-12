# CHANGELOG

## 2026-09-07: Data-Driven Placeholder Fill + Statistical Rerun

### Analysis rerun from source data (`/home/primandhika/artikel/dist/data/`)
- Loaded all field-test CSVs: speaking pre/post, metacognitive, post-use responses, qualitative interviews
- Ran baseline-adjusted ANCOVA (OLS + HC3 robust SE) for speaking and metacognitive outcomes
- Script: `experiments/full_analysis.py`, results: `experiments/analysis_results.json`

### Statistical results filled in manuscript
- **Speaking ANCOVA**: adj diff = 3.32, 95% CI [1.29, 5.34], p = .002, Hedges' g = 0.75 (HC3 SEs used due to heteroskedasticity, Levene's F = 6.87, p = .011)
- **Metacognitive ANCOVA**: adj diff = 4.14, 95% CI [0.40, 7.88], p = .030, Hedges' g = 0.51
- **Speaking gain sensitivity**: diff = 3.61, 95% CI [1.17, 6.06], p = .004, g = 0.65
- **Metacognitive gain sensitivity**: diff = 8.09, 95% CI [3.16, 13.02], p = .002, g = 0.73
- **Metacognitive reliability**: subscale α = .69 (pre) / .73 (post); item-level α = .95 (post, n = 63), subscale alphas .81–.89

### Factual placeholders filled
- University: Institut Keguruan dan Ilmu Pendidikan (IKIP) Siliwangi
- Instructor exp: Aditya Permana, M.Pd. (14 yr experience); instructor con: Via Nugraha, M.Pd. (10 yr)
- Students: 2nd-semester undergraduates, ~18–20 yr, speaking skills course (Keterampilan Berbicara)
- Interviews: 12 students, ~15 min each, March 18–27, 2026; 2 lecturers, 17 field statements
- Control condition: PBL regular instruction without Bicaranta
- Contamination: ~4–5 control students accessed Bicaranta voluntarily (per D02 lecturer report)
- Platform modules: video segments 90 s–2.5 min per section; modular explain-back cycle
- Ethics: doctoral research at UHAMKA, institutional governance procedures, de-identified data
- 3 raters (2 instructors + 1 additional); no ICC available (recordings not preserved)
- Post-use questionnaire: 18 items (2 × 9 dimensions), 1–4 scale, max = 72

### Conditional templates resolved
- Chose appropriate framing for: qualitative data sources, qualitative analysis, ethics, data collection timeline
- Removed all `[Choose]` / `[If]` / `[State]` / `[describe]` markers

### Verification: 29/29 do-not-submit checklist items passed
- Abstract: 164 words (≤200 ✓), 5 keywords ✓
- Total: ~6,220 words (within 6,000–8,000 ✓)
- All statistical numbers cross-verified against analysis script output

## 2026-09-06: Major Revision Pass (PRAREVISI_CALLEJ)
- Applied all 18 P0 (mandatory) and 9 P1 (recommended) revisions from `notes/PRAREVISI_CALLEJ.md`.
- **P0.1** Title changed from "Enhancing..." to "Evaluating..." + all 4 RQs reworded to remove causal language.
- **P0.2** Methods now explicitly states non-equivalent intact-class design, class-level assignment, and confounding caveat. Placeholders added for instructor info, contact hours, assignment rationale.
- **P0.3** Treatment dosage section added with intervention table (Table 1) and placeholders for weeks/sessions/minutes. Control condition requires author-filled description.
- **P0.4** Contamination section restructured with two conditional framings (log-based vs. unquantifiable).
- **P0.5** Speaking assessment subsection now includes rubric provenance, rater procedure, ICC, blinding, and calibration placeholders.
- **P0.6** Metacognitive instrument explicitly labeled as adapted, NOT MASQ original. 21-item/5-factor discrepancy documented. Reliability/validity placeholders added.
- **P0.7** Timing contradiction fixed: questionnaire explicitly described as administered pre and post intervention.
- **P0.8** All "archived analysis logs", "frozen analysis records", "current drafting pass" language removed. Analysis described as reproducible from dataset.
- **P0.9** Baseline-adjusted ANCOVA is now primary analysis; gain-score Welch test is sensitivity only.
- **P0.10** Normalized gain removed entirely from Results.
- **P0.11** Metacognitive baseline imbalance explicitly documented and addressed analytically.
- **P0.12** "Learning mechanisms" removed from claims; qualitative framing changed to "learner-reported processes".
- **P0.13** Ethical considerations subsection added with two conditional templates (approval vs. no approval).
- **P0.14** Product-development stages moved to Methods as refinement, explicitly stated not used as efficacy evidence.
- **P0.15** Post-use questionnaire now has placeholders for items per dimension, scoring, and reliability.
- **P0.16** "Accessible" removed as positive platform attribute; replaced with "browser-based".
- **P0.17** "Feynman technique" de-emphasized; only appears once in negative framing. Replaced with "explain-back production" throughout.
- **P0.18** All HTML anchors (`<a id="...">`) and Markdown citation links (`](#ref-...)`) removed. Reference years updated: Fiorella & Mayer → 2016, Goh & Liu → 2024, Hafour → 2024, Sabnani & Goh → 2022, Tsang → 2025. Sulistyowati et al. proceedings info expanded.
- **P1.1** Novelty reframed as "CALL practice architecture + dual outcome".
- **P1.2** Gap reframed without overstating what prior literature lacks.
- **P1.3** Merged Results/Discussion into single "Findings and Discussion" section; removed redundant separate Discussion.
- **P1.4** Joint display table (Table 5) added for mixed-method integration.
- **P1.5** Measurement alignment warning added: metacognitive items may reflect intervention-specific awareness.
- **P1.6** Participant reporting expanded with placeholders for demographics, proficiency, attrition, pilot overlap.
- **P1.7** Discussion implication reframed: "pedagogical sequencing may matter as much as technological sophistication."
- **P1.8** Limitations sharpened with 5 specific constraints.
- **P1.9** Conclusion now states strength of evidence accurately; calls for replication across multiple classes/instructors.
- Abstract trimmed to 178 words (≤200 ✓). Keywords updated to 5. Total ~6,700 words.
- Keyword "Feynman technique" replaced with "explain-back production".
- All references cleaned to plain APA 7 format (no HTML, no Markdown links).

## 2026-08-27
- Initialized project `enhancing_speaking_E270826` for a CALL-EJ-style article on Bicaranta.
- Verified CALL-EJ site guidance and extracted local template structure.
- Drafted initial manuscript in `papers/CALLEJ_article_draft.md` using rechecked descriptive statistics from source CSV files.
- Added Mahdi (2022) as the primary benchmark reference and inserted 3 speaking-related CALL-EJ articles for journal-fit positioning.
- Began second pass to add final author metadata and compare manuscript quality more directly against Mahdi (2022).

- Expanded the literature review and references substantially after identifying that the previous draft was under-cited relative to the Mahdi (2022) benchmark and below expected journal quality.
- Rewrote the manuscript structurally using EduLearn-style argument discipline while preserving the current CALL-EJ section template. Tightened the introduction, rebuilt the literature review around explicit theoretical logic, simplified the method into template-compatible subsections, and refocused results/discussion around the strongest findings and design contribution.
- Canonical project root corrected: all further work should be treated as living under `enhancing_speaking_E270826/`, not the parent directory.
- Canonical project root corrected: all further work should be treated as living under `enhancing_speaking_E270826/`, not the parent directory.
- Moved the CALL-EJ template and the Mahdi (2022) benchmark PDF from the parent workspace into the canonical project root so all project artifacts now live under `enhancing_speaking_E270826/`.
- Corrected workspace interpretation: the active directory is `/home/primandhika/artikel/artikel_dist3`, while the article project files remain under `enhancing_speaking_E270826/`.
- Flattened the article workspace by moving all remaining project files out of `enhancing_speaking_E270826/` into the active directory `/home/primandhika/artikel/artikel_dist3`.
- Organized root artifacts to match intent: moved the Mahdi (2022) benchmark PDF into `references/` and moved `CALLEJ-Template.docx` into `template/`.
- Cleaned the working manuscript in `papers/CALLEJ_article_draft.md` to better match journal style: removed in-text emphasis clutter, grounded the qualitative claims with translated participant quotations, added missing supporting citations already present in the reference list, normalized the main results heading, and alphabetized the APA-style references.
- Completed a final prose pass on the manuscript: simplified several high-jargon phrases, aligned the abstract and conclusion more closely with the paper’s actual evidentiary claims, clarified the study purpose in the introduction, tightened the methods-analysis wording, and made the discussion more formal while preserving the CALL-EJ structure.
- Built a `.docx` manuscript from `papers/CALLEJ_article_draft.md` using `template/CALLEJ-Template.docx` as the base shell. The build script is `scripts/build_callej_docx.py`, and the generated output is `papers/CALLEJ_article_final.docx`.
- Smoke-checked the generated `.docx` by reopening it with `python-docx`; the file loaded successfully and contains the expected front matter, 5 tables total (including the abstract panel), and the full manuscript body.
- Rebuilt `papers/CALLEJ_article_final.docx` to follow the template more closely after visual QA findings: replaced the even-page header placeholder `Authors’ names`, restored orange Heading 1 and blue Heading 2 text, removed full justification from body paragraphs, applied hanging indents to references, and changed article tables to thinner horizontal rules closer to the template sample.
