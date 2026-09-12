# PRAREVISI_CALLEJ

## Status reviewer

**Keputusan simulasi reviewer: MAJOR REVISION / REVISE AND RESUBMIT.**

Secara scope, manuskrip ini cocok dengan CALL-EJ. Fokusnya adalah computer-assisted language learning untuk EFL speaking melalui platform web, microlearning, self-recording, replay, dan reflection. Itu selaras langsung dengan cakupan CALL-EJ pada CALL/CAL/CMC, second language acquisition and computers, language assessment and computers, serta applied linguistics and computers.

Masalah utama naskah ini bukan scope. Masalah utama adalah **validitas desain dan transparansi metode**. Dalam kondisi sekarang, saya belum akan merekomendasikan accept karena reviewer dapat dengan mudah mempertanyakan: (1) treatment yang tampaknya hanya dibedakan pada dua intact classes, (2) reliabilitas speaking score yang tidak dilaporkan, (3) provenance dan validitas instrumen metakognitif yang tidak cocok dengan MASQ yang dikutip, (4) analisis inferensial yang masih berasal dari "archived analysis logs", (5) contamination pada control group yang belum dikuantifikasi, (6) metode kualitatif yang belum cukup untuk mendukung klaim "learning mechanisms", dan (7) tidak adanya ethical approval/informed consent statement.

**Target sebelum submit:** ubah naskah dari "promising classroom evaluation" menjadi **reproducible, baseline-adjusted, instrument-validated mixed-methods quasi-experimental evaluation**.

Tidak ada reviewer yang dapat menjamin acceptance. Namun, apabila seluruh revisi P0 di bawah diselesaikan dengan data asli yang mendukung, naskah ini menjadi jauh lebih defensible untuk external review CALL-EJ.

---

## 1. Aturan CALL-EJ yang saya jadikan benchmark

Sumber resmi CALL-EJ:

- Scope: https://callej.org/index.php/journal/about
- Author guidelines: https://callej.org/index.php/journal/guides
- Submission requirements: https://callej.org/index.php/journal/about/submissions
- Peer review process: https://callej.org/index.php/journal/review
- Recent comparable quasi-experimental article: https://callej.org/index.php/journal/article/view/824

Hal yang relevan langsung untuk manuskrip ini:

1. Full-length article sekitar **6,000-8,000 words**.
2. Abstract maksimal **200 words**.
3. Keywords maksimal **5**.
4. Semua submission harus mengikuti **APA 7** dan template CALL-EJ.
5. Manuskrip harus dikirim bersama similarity report, dengan similarity **<15%**.
6. Editorial screening memeriksa scope, template, similarity, AI-writing signal, dan basic quality.
7. External review menilai validity, innovation, significance, dan originality.
8. Untuk human-participant research, CALL-EJ meminta disclosure tentang ethics approval, informed consent, funding/conflicts, dan dokumen terkait.

Naskah sekarang memiliki abstract sekitar **191 words** dan tepat **5 keywords**, jadi dua syarat itu sudah aman. Total visible Word text sekitar **6.3k words termasuk references**, sehingga tidak ada alasan untuk menambah teori hanya demi panjang. Ruang tambahan sebaiknya dialihkan ke Methods dan validation, lalu pengulangan Discussion dipangkas.

Benchmark penting: artikel quasi-experimental CALL-EJ yang terbit pada 2026 melaporkan durasi intervensi, rater calibration, inter-rater ICC, content validity dan reliability questionnaire, interview sampling/duration/translation, thematic analysis, mixed-method integration, dan ethical approval. Manuskrip ini perlu mendekati standar pelaporan tersebut.

---

# P0: REVISI WAJIB SEBELUM SUBMISSION

## P0.1. Turunkan causal wording pada title dan research questions

### BEFORE (verbatim)

> Enhancing Speaking Skills and Metacognitive Awareness through a Microlearning-Based Multimedia Platform

### REVISI YANG DISARANKAN

> **Evaluating a Microlearning-Based Multimedia Platform for Speaking Performance and Metacognitive Awareness: A Mixed-Methods Quasi-Experimental Study**

Alasan: "Enhancing" terdengar seperti causal effect sudah mapan, sedangkan desain menggunakan intact classes tanpa random assignment dan tampaknya hanya satu class per condition. "Evaluating" lebih sesuai dengan kekuatan desain.

### BEFORE (verbatim)

> 1. To what extent does Bicaranta improve students’ speaking skills compared with regular instruction?

> 2. To what extent does Bicaranta improve students’ metacognitive awareness related to speaking?

### REVISI YANG DISARANKAN

> 1. **To what extent did pre-to-post speaking performance differ between students in the Bicaranta class and those receiving regular instruction after accounting for baseline performance?**

> 2. **To what extent did pre-to-post metacognitive-awareness scores differ between the two classes after accounting for baseline scores?**

Untuk RQ3 dan RQ4:

> 3. **How did students in the Bicaranta class perceive the platform’s pedagogical usefulness and usability?**

> 4. **What learner-reported processes and implementation challenges help interpret the quantitative outcome patterns?**

Gunakan "learner-reported processes", bukan "learning mechanisms", kecuali data kualitatif benar-benar mampu menguji mekanisme.

---

## P0.2. Akui desain sebenarnya: non-equivalent intact classes dan kemungkinan class-treatment confounding

### BEFORE (verbatim)

> The study used a mixed-methods, quasi-experimental design embedded in an iterative product-development process. The quantitative strand examined comparative changes in speaking performance and metacognitive awareness, while the qualitative strand was used to explain why those changes may have occurred and what implementation constraints were visible in use. The design is therefore best understood as a comparative classroom evaluation informed by developmental logic rather than as a tightly controlled experiment.

### REVISI YANG DISARANKAN

> **The study employed a mixed-methods, non-equivalent-groups quasi-experimental design. The field evaluation involved two intact classes, one receiving Bicaranta-supported instruction and one receiving regular instruction, without individual random assignment. Because treatment condition was assigned at the class level, any between-group difference may also reflect unmeasured class-level characteristics. The quantitative strand therefore estimates an adjusted between-class difference rather than an unrestricted causal treatment effect. Qualitative evidence was used to interpret learner experiences and implementation conditions.**

Jika ternyata ada lebih dari satu class per arm, tulis jumlah class secara eksplisit dan analisis clustering. Jika memang hanya dua class, jangan mencoba menyembunyikan confounding ini. Reviewer akan menangkapnya.

Tambahkan informasi berikut di Methods:

- siapa instructor tiap class;
- apakah instructor sama atau berbeda;
- jadwal dan jumlah contact hours;
- materi/course objectives yang sama;
- perbedaan yang sengaja dibuat antara experimental dan control;
- bagaimana class dipilih menjadi experimental atau control.

Jika instructor berbeda, treatment effect praktis tidak dapat dipisahkan dari instructor effect. Itu harus disebut limitation utama.

---

## P0.3. Methods masih terlalu tipis mengenai treatment dosage dan control condition

### BEFORE (verbatim)

> Bicaranta itself was designed around a short-cycle practice architecture. Based on the archived data mapping and development notes, the platform included modular microlearning pages, embedded video input, interactive tasks, Feynman-style explain-back prompts, recording tools, playback opportunities, reflection prompts, and lecturer-facing monitoring features. The instructional assumption was that students would learn more effectively if they encountered content in small segments and then had to restate, record, review, and revise that content as speaking output.

### REVISI YANG DISARANKAN

Ganti deskripsi generik dengan deskripsi yang dapat direplikasi:

> **The intervention lasted [X weeks] and comprised [X sessions/modules], with approximately [X minutes] of Bicaranta-supported practice per [week/session]. Each cycle contained four core activities: (1) segmented multimedia input of approximately [X minutes/items], (2) an explain-back task requiring learners to restate the target concept without copying the source, (3) a recorded speaking attempt followed by self-playback, and (4) a reflection/revision prompt before a subsequent attempt. The same course topics and contact hours were used in both classes. The control class completed [describe the actual speaking activities], but did not receive the required Bicaranta recording, replay, and reflection cycle.**

Buat satu intervention table:

| Component | Operationalization | Frequency/dosage | Theoretical function |
|---|---|---|---|
| Segmentation | actual module length | actual frequency | cognitive load/selection |
| Explain-back | actual prompt | actual frequency | generative processing |
| Recording | actual speaking task | actual frequency | externalization |
| Playback | actual requirement | actual frequency | self-monitoring |
| Revision | actual retry rule | actual frequency | self-regulation |

**Penting:** "microlearning" harus didefinisikan secara operasional. Kalau unit ternyata tidak benar-benar pendek atau tidak ada duration records, gunakan "modular multimedia learning" daripada menjadikan microlearning sebagai klaim utama.

---

## P0.4. Gunakan platform logs sebagai fidelity evidence dan untuk mengukur contamination

### BEFORE (verbatim)

> However, lecturer records indicate that some control-group students may have accessed the platform voluntarily as preparation for testing. This point is treated explicitly as a limitation because it weakens treatment purity.

### REVISI YANG DISARANKAN

Jangan berhenti pada "may have". Karena platform memiliki monitoring features, coba recover usage logs.

Jika log tersedia:

> **Platform logs showed that [n] of 37 control-group students accessed Bicaranta at least once during the field period, with a median of [X] completed activities. The primary analysis retained students in their original class condition, while a sensitivity analysis [describe] was conducted to evaluate whether the between-group estimate changed after accounting for documented cross-over exposure.**

Jika log tidak tersedia:

> **Unscheduled Bicaranta access by some control-class students was reported by the lecturer but could not be quantified from the available records. The resulting treatment contrast should therefore be interpreted as partially contaminated and likely conservative if cross-over exposure provided any instructional benefit.**

Jangan melakukan post-hoc exclusion semata-mata untuk memperbesar effect. Primary analysis harus mempertahankan original classroom assignment. Sensitivity analysis boleh ditambahkan transparan.

---

## P0.5. Speaking outcome belum punya measurement validity yang cukup

### BEFORE (verbatim)

> Three main forms of quantitative evidence were used. First, speaking performance was measured through pretest-posttest tasks scored with a five-aspect rubric covering organization, clarity, language accuracy, strategy, and delivery impact. Each aspect was scored on a 0-15 scale and then converted to a 0-100 final score. The archived files also preserved finer scoring indicators such as diction, grammar, rhetoric, articulation, intonation, speed, transitions, formality, and audience effect, suggesting that the rubric was intended to capture structured academic speaking rather than fluency alone.

Masalah reviewer:

- siapa yang membuat/adaptasi rubric?
- construct validity-nya apa?
- pretest dan posttest task setara atau tidak?
- berapa rater?
- apakah rater blind terhadap group/time?
- bagaimana calibration dilakukan?
- berapa inter-rater reliability?
- jika hanya satu rater, bagaimana scoring error dikendalikan?
- kenapa Methods mengatakan "suggesting that the rubric was intended", seolah penulis sendiri tidak pasti dengan konstruk instrumen?

### REVISI YANG DISARANKAN

> **Speaking performance was assessed using [parallel/same] pretest and posttest tasks designed to elicit [academic explanatory speaking / presentation speaking / actual construct]. Responses were scored using a five-domain analytic rubric covering organization, clarity, language accuracy, strategy, and delivery impact. The rubric was [adapted from/source or researcher-developed], and its content was reviewed by [number and expertise] before use. [Two] trained raters independently scored [all/a randomly selected X% of] recordings after calibration using [number] practice samples. Raters were [blinded/not blinded] to group and time point. Inter-rater reliability was estimated using ICC ([model]), yielding [value and 95% CI] for the total score and [values] for the domains. Disagreements were resolved by [procedure].**

Jika rekaman asli masih ada, **rescore secara blind sekarang**. Ini salah satu revisi dengan return terbesar untuk peluang accept.

Jika tidak ada rekaman/rater data, jangan membuat angka. Nyatakan limitation secara eksplisit, tetapi ketahuilah bahwa speaking score sebagai primary outcome akan tetap lebih lemah.

---

## P0.6. Instrumen metakognitif adalah titik paling berbahaya

### BEFORE (verbatim)

> Second, metacognitive awareness was measured using a 16-item questionnaire on a 4-point Likert scale with a total score range of 16-64. According to the project’s instrument mapping, the questionnaire covered four dimensions: planning, monitoring, evaluation, and an integrative/Feynman dimension reflecting the ability to simplify, re-articulate, and diagnose explanation difficulty.

### MASALAH SUBSTANSIAL

Sulistyowati et al. (2022), yang dikutip di literature review sebagai speaking-specific instrument, mempublikasikan MASQ sebagai **21-item questionnaire**, **1-5 Likert**, dengan **lima faktor**: Problem-Solving, Planning and Evaluation, Mental Translation, Person Knowledge, dan Directed Attention. Paper itu tidak memvalidasi "integrative/Feynman dimension".

Sumber:
https://www.atlantis-press.com/proceedings/teflin-icoelt-21/125970102

Artinya, naskah sekarang tidak boleh memberi kesan bahwa 16-item/4-point/four-dimension scale adalah MASQ asli.

### REVISI YANG DISARANKAN

Jika 16-item scale memang **adaptasi**:

> **Metacognitive awareness was assessed using a 16-item, four-point adapted questionnaire developed from speaking-metacognition constructs in [sources]. The instrument was not treated as the original MASQ because the published MASQ contains 21 items across five factors. For the present study, items were adapted to represent planning, monitoring, evaluation, and explain-back regulation in the Bicaranta task cycle. The adaptation procedure involved [translation/back-translation, expert review, pilot testing]. Content validity was evaluated by [n] experts, and internal consistency in the field sample was [McDonald’s omega/Cronbach’s alpha] = [value] at pretest and [value] at posttest. The complete item mapping and scoring procedure are provided in Appendix [X].**

Jika adaptasi/validation record **tidak ada**, pilih salah satu:

1. downgrade metacognitive outcome menjadi **exploratory self-report outcome**, atau
2. keluarkan metacognitive awareness dari title dan klaim kontribusi utama.

Jangan mempertahankan klaim kuat terhadap konstruk yang instrument provenance-nya tidak dapat dipertanggungjawabkan.

---

## P0.7. Ada kontradiksi timing pengukuran metakognitif

### BEFORE (verbatim)

> The metacognitive questionnaire and post-use response data were collected after the implementation sequence, and the archived interview material was used as explanatory evidence.

Tetapi Results melaporkan:

> Experimental Pretest 40 26.40 8.30 16.00 64.00

> Experimental Posttest 40 50.95 9.43 16.00 64.00

> Control Pretest 37 31.89 11.99 20.00 62.00

> Control Posttest 37 48.35 7.45 27.00 59.00

### REVISI YANG DISARANKAN

Jika memang ada metacognitive pretest dan posttest:

> **The metacognitive-awareness questionnaire was administered to both classes immediately before the intervention and again after the final intervention session. The post-use perception questionnaire was administered only to the experimental class after the intervention.**

Ini harus diperbaiki karena reviewer akan menganggap Methods dan Results tidak konsisten.

---

## P0.8. Hentikan ketergantungan pada "archived inferential analysis logs"

### BEFORE (verbatim)

> For the current drafting pass, the descriptive statistics reported in this article were rechecked directly from the source CSV files. The project archive also contained inferential analysis logs for speaking and metacognitive outcomes, including gain comparisons and supplementary robustness checks. Because these values come from the project’s frozen analysis records, they are reported cautiously.

### REVISI YANG DISARANKAN

Kalimat seperti "current drafting pass", "archived analysis logs", dan "frozen analysis records" tidak boleh ada dalam submission final. Mereka memberi sinyal bahwa Methods direkonstruksi dari file lama dan inferential results belum diverifikasi.

Setelah semua analisis benar-benar direrun dari dataset final, ganti dengan:

> **All quantitative analyses were reproduced from the de-identified field-test dataset using a prespecified analysis script. Descriptive statistics were calculated for each outcome at pretest and posttest. Primary between-group estimates were obtained using baseline-adjusted linear regression/ANCOVA, with posttest score as the dependent variable, group as the focal predictor, and the corresponding pretest score as a covariate. Heteroskedasticity-robust standard errors were used where appropriate. Adjusted mean differences, 95% confidence intervals, exact p values, and standardized effect sizes are reported. Gain-score comparisons were retained only as sensitivity analyses.**

Simpan analysis script dan output. Angka di abstract, Results, tables, Discussion, dan Conclusion harus semuanya berasal dari rerun yang sama.

---

## P0.9. Primary statistics harus baseline-adjusted, terutama untuk metacognition

### BEFORE (verbatim)

> According to the archived speaking-analysis log, the gain difference between groups was 3.61 points, with a 95% confidence interval of 1.17 to 6.06, a Welch-adjusted p = .00443, and Hedges’ g = 0.653. A supplementary baseline-adjusted robustness analysis in the same archive reported an adjusted group effect of 3.32 points with p = .00169.

### REVISI YANG DISARANKAN

Balik hierarki analisis. Baseline-adjusted estimate harus menjadi **primary**, bukan supplementary robustness check.

Format Results yang lebih defensible:

> **After adjustment for baseline speaking score, students in the Bicaranta class had an estimated posttest score [X] points higher than students in the regular-instruction class (adjusted mean difference = [X], 95% CI [X, X], p = [X], standardized effect = [X]). A gain-score Welch test produced a consistent direction of effect (mean gain difference = 3.61 points, 95% CI [1.17, 6.06], p = .004).**

Catatan penting: speaking pretest means hampir sama, tetapi SD sangat berbeda (5.55 vs. 1.84) dan ranges sangat berbeda. Jangan sekadar menyatakan "baseline was similar". Tampilkan distribution plot atau minimal SMD dan jelaskan heterogeneity.

---

## P0.10. Jangan gunakan normalized gain sebagai hasil utama metakognitif

### BEFORE (verbatim)

> The archived metacognitive analysis note also reported a normalized-gain advantage for the experimental group, with mean normalized gains of 0.648 and 0.400, respectively, and a between-group comparison of t(53.91) = 2.832, p = .00649, with Hedges’ g = 0.653.

### MASALAH

Scale maksimum dinyatakan 64, dan Table 3 menunjukkan **experimental pretest max = 64**. Jika normalized gain memakai formula konvensional `(post-pre)/(max-pre)`, participant dengan pretest 64 menghasilkan denominator zero. Selain itu, group-level normalized gain dari reported means juga tidak menghasilkan 0.648 vs. 0.400 secara langsung.

### REVISI YANG DISARANKAN

Hapus normalized gain dari primary result. Gunakan baseline-adjusted posttest analysis.

> **Because the metacognitive score was bounded and the groups differed at baseline, the primary analysis estimated the posttest group difference while adjusting for pretest score. The experimental and control classes differed by [adjusted estimate], 95% CI [X, X], p = [X], with [effect size]. Gain-score and robust/nonparametric analyses were used only as sensitivity checks.**

Jika normalized gain tetap dipertahankan, definisikan rumus, unit analisis, handling untuk perfect pretest score, dan alasan metodologis. Namun untuk reviewer, pilihan paling bersih adalah membuangnya.

---

## P0.11. Metacognitive baseline imbalance harus diperlakukan serius

### BEFORE (verbatim)

> The control group began with a higher mean pretest score, which means that the comparison cannot be treated as perfectly balanced at baseline. The result therefore supports a stronger improvement pattern, but not an unrestricted causal claim.

### REVISI YANG DISARANKAN

Kalimat ini benar arahnya, tetapi tindakan analitisnya harus nyata:

> **The classes differed descriptively at baseline on metacognitive awareness (experimental M = 26.40, SD = 8.30; control M = 31.89, SD = 11.99). Consequently, raw gain scores were not treated as the sole basis for the between-group inference. The primary model adjusted posttest scores for baseline metacognitive awareness and reported the adjusted group difference with 95% confidence intervals.**

Jangan hanya "acknowledge" imbalance di Discussion tetapi tetap menjadikan raw/normalized gain sebagai evidence utama.

---

## P0.12. Qualitative strand belum cukup untuk klaim "mechanisms"

### BEFORE (verbatim)

> The qualitative record used in this article came from two archived sources: 12 student interview excerpts and 17 lecturer-coded field statements representing 2 lecturers, all preserved in wawancara.csv, plus open-ended student comments from the post-use files and trial notes from the limited-test stages. These materials were already stored as project artifacts, so the present article treats them as an explanatory qualitative corpus linked to the quantitative record.

### MASALAH

"12 interview excerpts" tidak sama dengan "12 full interviews". "17 lecturer-coded field statements" juga belum menjelaskan bagaimana coding dilakukan. Reviewer akan bertanya:

- bagaimana interviewees dipilih?
- berapa lama wawancara?
- audio-recorded atau tidak?
- full transcript atau excerpts saja?
- siapa coder?
- codebook atau reflexive thematic analysis?
- bagaimana translation quality dikendalikan?
- bagaimana quantitative dan qualitative strands diintegrasikan?

### REVISI YANG DISARANKAN

**Jika full transcripts/recordings masih ada:**

> **Twelve experimental-group students were selected for follow-up interviews using [sampling rationale]. Interviews lasted approximately [X-X] minutes, were conducted in Indonesian, audio-recorded with consent, and transcribed verbatim. The transcripts were analyzed using [thematic analysis/content analysis], following [actual steps]. [One/two] researchers coded the material, maintained analytic memos, and resolved coding differences through [procedure]. Quotations were translated into English and checked by [procedure]. Qualitative themes were integrated with the quantitative findings at the interpretation stage through a joint display linking outcome patterns, learner-reported processes, and implementation barriers.**

**Jika yang tersisa memang hanya excerpts/coded statements:**

Jangan sebut ini full mixed-method explanatory mechanism analysis. Ubah framing menjadi:

> **Archived qualitative excerpts and open-ended comments were used as contextual evidence to illustrate learner experiences and implementation barriers. Because full interview transcripts were unavailable for reanalysis, these materials were not used to make claims about causal learning mechanisms or thematic saturation.**

Dalam skenario kedua, ubah RQ4 dan kurangi bahasa "mechanisms".

---

## P0.13. Tambahkan ethical approval dan informed consent statement

### BEFORE

**[Tidak ada ethics/informed-consent statement di manuscript saat ini.]**

CALL-EJ secara eksplisit meminta disclosure dan form untuk human participants.

### REVISI YANG DISARANKAN

Tambahkan subsection **Ethical considerations** sebelum Results:

> **Ethical approval for the study was obtained from [name of ethics committee/institution] (Approval No. [XXXX]) before data collection. Participants received information about the study procedures, confidentiality, voluntary participation, and their right to withdraw without academic penalty. Written informed consent was obtained from all participants. Data used for analysis were de-identified, and platform/interview records were stored and analyzed using participant codes.**

Jika penelitian pada saat itu **tidak** memiliki formal ethics approval, jangan mengarang approval retrospectively. Jelaskan kondisi sebenarnya dan cek dengan editor/institutional ethics office mengenai permissible retrospective use of the dataset.

Tambahkan juga funding dan competing interests statement sesuai kondisi nyata.

---

## P0.14. Product-development stages jangan dicampur sebagai efficacy evidence

### BEFORE (verbatim)

> Table 1. Summary of limited-trial results

> Stage 1, small group | 6 | readability, usability, navigation, face validity | 79.63% | 62.22 | 69.32

> Stage 2, extended trial | 10 | product stability, practicality, full-flow simulation | 96.25% | 71.60 | 87.47

### REVISI YANG DISARANKAN

n=6 dan n=10 adalah pilot/development stages. Jangan memberi kesan bahwa pre-post gain dari pilot memperkuat efficacy.

Lebih baik pindahkan ke Methods sebagai **Product refinement prior to field evaluation**, dan fokus pada perubahan desain:

> **Two pre-field usability trials were conducted to identify implementation problems rather than to estimate intervention effectiveness. Stage 1 (n = 6) identified problems in login flow, video loading, and navigation. After revision, Stage 2 (n = 10) focused on full-flow usability and identified remaining issues in mobile navigation and recording stability. Because these stages were developmental and involved very small samples, their pre/post scores were not used as efficacy evidence in the field-test analysis.**

Jika pilot participants berbeda dari field-test 77 students, tulis eksplisit. Jika ada overlap, laporkan karena pre-exposure dapat memengaruhi field outcome.

---

## P0.15. Post-use questionnaire perlu konstruk, scoring, dan reliability yang jelas

### BEFORE (verbatim)

> The structured response profile captured perceptions of content quality, ease of use, engagement, speaking support, metacognitive support, microlearning design, accessibility, Feynman integration, and reflection.

dan:

> A second archived response note, based on a separate 15-item experimental-group questionnaire, described overall platform use as moderately high, with mean values of 3.31 for planning-related use, 3.08 for monitoring-related use, 3.17 for evaluation-related use, and 3.18 overall on a four-point scale. However, because that same note reported weak internal consistency for some scales, these values are best treated as descriptive context rather than as strong inferential evidence.

### REVISI YANG DISARANKAN

Saat ini terlalu banyak instruments dengan provenance tidak jelas. Pilih satu post-use instrument yang paling defensible.

Untuk Table 4, jelaskan:

- berapa items per dimension;
- response scale per item;
- kenapa denominator per dimension = 8;
- source/development;
- content validity;
- reliability total/subscale.

Jika tiap dimension hanya dua items, pertimbangkan reporting item means pada skala asli 1-4, bukan "7.00/8", agar interpretasi lebih transparan.

**Saran kuat:** hapus paragraf "second archived response note" dari main text jika reliability lemah. Memasukkan scale yang diketahui psychometrically weak justru menambah attack surface.

---

## P0.16. Hapus klaim "accessible" jika data usability justru menunjukkan friction tinggi

### BEFORE (verbatim)

> The findings indicate that an accessible multimedia platform can support speaking development when it is organized as a reflective practice cycle rather than as content delivery alone.

Tetapi Results menunjukkan:

- Ease of use: 5.03/8
- Accessibility: 4.95/8
- Internet dependence: 31 mentions
- Navigation problems: 26 mentions
- Feature stability problems: 10 mentions

### REVISI YANG DISARANKAN

> **The findings suggest that a browser-based multimedia platform can organize speaking practice as a reflective cycle of segmented input, explanation, replay, and revision, although usability and connectivity problems constrained implementation.**

Gunakan "browser-based", "comparatively low-compute", atau "non-immersive" jika memang benar. Jangan menyebut "accessible" sebagai atribut positif tanpa evidence yang mendukung.

---

## P0.17. De-emphasize "Feynman technique" sebagai teori ilmiah mandiri

### BEFORE (verbatim)

> The Feynman technique is closely aligned with these principles because it asks learners to explain a concept in simple language, detect what remains unclear, return to the source material, and explain again. In pedagogical terms, it creates a feedback loop between understanding and expression.

### REVISI YANG DISARANKAN

> **Operationally, Bicaranta used explain-back prompts requiring learners to restate target content in accessible language, identify gaps, revisit the source material, and record a revised explanation. The study interprets this sequence through established work on self-explanation, teaching-to-learn, and generative processing rather than treating the "Feynman technique" as an independently validated theoretical construct.**

Platform branding boleh tetap memakai "Feynman-style", tetapi theoretical contribution sebaiknya berdiri pada generative learning dan self-regulated learning.

---

## P0.18. Perbaiki reference accuracy dan bersihkan artefak Markdown/HTML

Naskah Word mengandung banyak citation strings seperti:

### BEFORE (verbatim)

> [Mahdi (2022)](#ref-mahdi-2022)

dan reference entries seperti:

> <a id="ref-mahdi-2022"></a>Mahdi, D. A. (2022). Improving speaking and presentation skills through interactive multimedia environment for non-native speakers of English. SAGE Open, 12(1), 1-12. https://doi.org/10.1177/21582440221079811

### REVISI YANG DISARANKAN

Dalam body:

> **Mahdi (2022)**

Dalam references, hapus seluruh `<a id="..."></a>`.

Ada sedikitnya puluhan artefak `](#ref-...)` dan HTML anchor di file. Ini tidak boleh tersisa pada submission final APA 7.

Lakukan full DOI/reference audit. Beberapa entries yang perlu diverifikasi khusus:

- **Fiorella & Mayer**: volume 28(4), 717-741 adalah final issue 2016, bukan 2015.
- **Goh & Liu, Confident Speaking**: publisher page menampilkan copyright 2024, bukan 2023.
- **Hafour**: final bibliographic issue 37(4), 986-1018 adalah 2024; DOI memang mengandung 2022 karena online-first.
- **Sabnani & Goh**: final issue TESOL Quarterly 56(1), 336-346 adalah 2022, meskipun first online 2021.
- **Tsang**: final Language Teaching Research 29(4), 1639-1659 terbit issue pada 2025, meskipun first online 2022.
- **Sulistyowati et al.**: lengkapi proceedings title/volume/pages dan pastikan citation sesuai publisher metadata.

CALL-EJ meminta references dicek accuracy dan completeness. Jangan campur tahun online-first dengan final volume/issue metadata secara tidak konsisten.

---

# P1: REVISI YANG AKAN MENAIKKAN PELUANG ACCEPT

## P1.1. Tajamkan novelty, jangan menjadikan "non-AI" sebagai novelty

### BEFORE (verbatim)

> The novelty of the present study lies in testing a more accessible reflective design in a comparative classroom setting while examining metacognitive awareness alongside speaking performance.

### REVISI YANG DISARANKAN

> **The contribution of the study lies not in multimedia use itself, but in evaluating a specific CALL practice architecture that combines segmentation, explain-back production, self-recording, replay, and revision while examining both speaking performance and speaking-related self-regulation. This mechanism-oriented framing distinguishes the intervention from studies that evaluate digital access or final oral performance alone.**

Novelty harus berupa **design architecture + dual outcome + classroom implementation evidence**, bukan "technology yang lebih sederhana daripada AI".

---

## P1.2. Jangan overstate gap literature

### BEFORE (verbatim)

> Much of it shows that technology can help, but less of it specifies how a manageable, classroom-deployable platform can be designed so that learners repeatedly explain, monitor, and revise their speaking rather than merely consume digital materials.

### REVISI YANG DISARANKAN

> **Existing studies provide substantial evidence that technology-mediated speaking tasks can support oral performance, but the present study focuses more narrowly on a repeatable practice sequence in which segmented input is followed by explain-back production, self-playback, and revision. The empirical question is whether this sequence is associated with stronger classroom outcome patterns and whether learners report the regulatory processes the sequence is designed to elicit.**

Ini lebih defensible daripada klaim luas bahwa previous studies tidak menjelaskan design.

---

## P1.3. Rapikan struktur Results dan Discussion

Saat ini ada heading:

> Results/Findings and Discussion

kemudian beberapa halaman kemudian:

> Discussion

Akibatnya interpretasi muncul dua kali.

Pilih salah satu struktur:

### Opsi yang saya rekomendasikan untuk CALL-EJ

1. **Findings and Discussion**
   - RQ1 Speaking performance
   - RQ2 Metacognitive awareness
   - RQ3 Perceptions/usability
   - RQ4 Learner-reported processes and implementation barriers
   - Integrative synthesis
2. **Implications**
3. **Limitations**
4. **Conclusion**

Atau pakai separate Results dan Discussion secara konsisten.

Gunakan ruang yang dihemat untuk Methods, instrument validity, fidelity, dan ethics.

---

## P1.4. Buat joint display untuk mixed-method integration

Saat ini integration sebagian besar berupa narasi "this is compatible with...".

Tambahkan satu table:

| Quantitative pattern | Qualitative evidence | Interpretation | Alternative explanation |
|---|---|---|---|
| Speaking organization improved more | planning/outline comments | explain-back may support structuring | class/instructor effect |
| Metacognitive score increased | monitoring/replay comments | self-playback may support self-monitoring | self-report alignment |
| Positive engagement | engagement ratings | repeated low-stakes practice acceptable | novelty effect |
| Technical friction | navigation/internet comments | fidelity reduced | contamination/nonuse |

Ini akan membuat "mixed methods" terlihat sebagai desain analitis, bukan sekadar kombinasi dua jenis data.

---

## P1.5. Waspadai measurement alignment yang terlalu dekat dengan treatment

### BEFORE (verbatim)

> It was also associated with stronger growth in planning, monitoring, evaluation, and integrative explain-back awareness.

Jika metacognitive questionnaire memasukkan item "Feynman/explain-back" yang secara langsung diajarkan oleh intervention, outcome dapat sebagian menangkap **familiarity with treatment language**, bukan general metacognitive awareness.

### REVISI YANG DISARANKAN

> **The increase should be interpreted as growth in self-reported speaking-related metacognitive awareness as operationalized by the adapted questionnaire. Because several items were closely aligned with practices explicitly taught in Bicaranta, the measure may partly reflect intervention-specific awareness rather than broad metacognitive transfer.**

Ini bukan melemahkan paper. Ini justru menunjukkan measurement sophistication.

---

## P1.6. Perkuat participant reporting dan attrition flow

### BEFORE (verbatim)

> The field-test dataset contained 77 students with complete paired data, comprising 40 students in the experimental group and 37 students in the control group.

Tambahkan:

- jumlah awal yang enrolled;
- jumlah yang punya complete speaking pre/post;
- jumlah yang punya complete metacognitive pre/post;
- reason for exclusions/missing data;
- age range/mean;
- semester/year;
- proficiency level dan cara menentukannya;
- relevant language background;
- apakah small-trial participants excluded from field test.

Jika 77 adalah seluruh cohort tanpa missing data, tulis eksplisit.

---

## P1.7. Reframe Discussion implication

### BEFORE (verbatim)

> For CALL-EJ readers, an important implication is that speaking-oriented technology does not need to be AI-reliant or immersion-intensive to be pedagogically meaningful.

### REVISI YANG DISARANKAN

> **For CALL implementation, the practical implication is that pedagogical sequencing may matter as much as technological sophistication. A browser-based system can create repeated opportunities for explanation, self-observation, and revision, but the present findings also show that navigation, connectivity, and recording reliability are part of the intervention rather than peripheral technical details.**

Ini lebih relevan dan tidak membuat strawman "AI versus simple platform".

---

## P1.8. Limitations harus lebih tajam

### BEFORE (verbatim)

> Several boundaries should therefore remain visible. The study used intact classes, not random assignment, so unmeasured class-level differences may have contributed to the result. Some control-group students may have accessed the platform voluntarily, which weakens treatment purity. Some post-use questionnaire scales were psychometrically weak in the archived notes, so those numbers should remain supportive rather than central. Finally, the inferential results reported here come from archived project analyses and should be rerun line by line against the frozen dataset in the final submission pass.

### REVISI YANG DISARANKAN SETELAH ANALYSIS SUDAH DIRERUN

> **Several limitations constrain interpretation. First, the study compared intact classes, so treatment condition was confounded with class-level characteristics that could not be fully controlled statistically. Second, some control-class students reportedly accessed Bicaranta, reducing treatment separation. Third, speaking-score inference depends on the reliability and equivalence of the assessment tasks and rater process; these sources of measurement error should be considered when interpreting the effect estimate. Fourth, the metacognitive outcome was based on an adapted self-report instrument and may partly reflect intervention-specific awareness. Fifth, qualitative evidence was used to interpret learner experiences rather than to establish causal mediation. These limitations support interpreting the findings as an adjusted comparative classroom effect rather than definitive evidence of causality.**

Jangan mengirim paper yang masih mengatakan "the inferential results ... should be rerun". Rerun dulu, lalu hapus kalimat itu.

---

## P1.9. Conclusion harus menyatakan strength of evidence yang benar

### BEFORE (verbatim)

> Taken together, the findings position Bicaranta as a promising CALL-oriented approach for speaking courses that aim to connect performance practice with metacognitive growth.

### REVISI YANG DISARANKAN

> **Taken together, the findings support Bicaranta as a promising CALL design for organizing repeated speaking, self-playback, and reflective revision. In this two-class quasi-experimental evaluation, the Bicaranta class showed stronger adjusted outcome patterns than the regular-instruction class, while learner reports described planning, monitoring, replay, and technical friction as salient features of implementation. Replication across multiple classes and instructors is needed before stronger causal or generalizable claims are warranted.**

Gunakan "adjusted outcome patterns" hanya setelah analysis baseline-adjusted benar-benar selesai.

---

# 2. ABSTRACT: BEFORE DAN TARGET REWRITE

## BEFORE (verbatim)

> This study examined whether Bicaranta, a microlearning-based multimedia platform that integrates explain-to-learn activities, could improve university students’ speaking performance and metacognitive awareness. The study used a mixed-methods, quasi-experimental design embedded in an iterative product-development process. After expert review and two limited trials, the field test involved 77 Indonesian undergraduates, with 40 students in the experimental group and 37 in the control group. Quantitative evidence came from pretest-posttest speaking scores, a metacognitive awareness questionnaire, and post-use response profiles. Qualitative evidence came from archived student and lecturer interviews, open-ended feedback, and trial notes. Rechecked descriptive statistics showed that the experimental group’s speaking mean rose from 70.84 to 82.70, whereas the control group improved from 71.21 to 79.46. Metacognitive awareness also increased more strongly in the experimental group, from 26.40 to 50.95, compared with 31.89 to 48.35 in the control group. The qualitative record suggests that segmented content, self-explanation, replay, and repeated low-stakes rehearsal supported planning, self-monitoring, and confidence. The findings indicate that an accessible multimedia platform can support speaking development when it is organized as a reflective practice cycle rather than as content delivery alone.

## TARGET REWRITE SETELAH ANALYSIS FINAL

> **This mixed-methods quasi-experimental study evaluated Bicaranta, a browser-based platform that organizes speaking practice through segmented input, explain-back production, self-recording, replay, and revision. Two intact Indonesian university classes participated (Bicaranta, n = 40; regular instruction, n = 37) over [X weeks]. Speaking performance and speaking-related metacognitive awareness were measured before and after the intervention, with posttest outcomes analyzed using baseline-adjusted models. Students in the Bicaranta class showed higher adjusted posttest speaking performance (adjusted difference = [X], 95% CI [X, X], p = [X]) and [state the adjusted metacognitive result accurately]. Post-use responses and qualitative evidence indicated that learners valued repeated explanation and self-playback, while navigation, connectivity, and recording stability constrained implementation. Learners also described greater attention to planning, monitoring, and revision. Because treatment was assigned by intact class and some control-class exposure may have occurred, the findings should be interpreted as a comparative classroom effect rather than a definitive causal estimate. The study highlights a reflective CALL practice cycle whose pedagogical value depends on both task design and implementation reliability.**

Keep this under 200 words after filling the placeholders.

---

# 3. METHODS: URUTAN BARU YANG SAYA SARANKAN

Gunakan struktur ini:

## Methods

### Research design
- non-equivalent groups quasi-experimental;
- mixed-method design type and priority;
- unit of assignment = class;
- point of QUAN-qual integration.

### Context and participants
- university/course/context;
- demographics/proficiency;
- recruitment;
- attrition;
- pilot participants versus field participants.

### Intervention and control condition
- weeks;
- sessions;
- dosage;
- content equivalence;
- instructor;
- Bicaranta cycle;
- fidelity;
- control activities;
- contamination.

### Instruments
1. Speaking tasks and rubric
2. Adapted metacognitive questionnaire
3. Post-use perception questionnaire
4. Interviews/open-ended materials

### Data collection procedure
- exact timeline;
- pretest;
- intervention;
- posttest;
- interviews;
- platform logs.

### Data analysis
- primary outcome models;
- baseline adjustment;
- effect sizes and CIs;
- assumptions/robust SE;
- sensitivity analyses;
- qualitative analytic method;
- integration/joint display.

### Ethical considerations
- approval;
- consent;
- privacy/anonymization.

---

# 4. ANALYSIS PLAN YANG PALING DEFENSIBLE UNTUK DATA INI

Saya merekomendasikan:

### Speaking primary model

`Speaking_Post = β0 + β1(Group) + β2(Speaking_Pre) + error`

Report:
- adjusted group difference;
- 95% CI;
- p;
- standardized effect;
- model diagnostics;
- HC3 robust SE jika heteroskedasticity terlihat.

Sensitivity:
- Welch test pada gain;
- bootstrap CI bila residual bermasalah.

### Metacognitive primary model

`Meta_Post = β0 + β1(Group) + β2(Meta_Pre) + error`

Report sama.

Jangan jadikan normalized gain sebagai hasil utama.

### Baseline reporting

Jangan hanya mengandalkan "pretest p > .05". Tampilkan:
- means/SD;
- ranges;
- standardized mean difference;
- distribution figure jika memungkinkan.

### Class-level limitation

Jika benar hanya satu class per treatment, **tidak ada statistical adjustment yang dapat sepenuhnya memisahkan treatment dari class effect**. ANCOVA memperbaiki individual baseline imbalance, tetapi tidak menyelesaikan treatment-class confounding. Ini harus tetap disebut secara eksplisit.

---

# 5. TIGA KEPUTUSAN BESAR YANG HARUS DIBUAT BERDASARKAN ARSIP DATA

## A. Apakah full speaking recordings masih ada?

**Jika ya:** rescore blind dengan minimal dua raters pada seluruh atau subset yang layak, hitung ICC, dan jadikan itu evidence utama measurement reliability.

**Jika tidak:** speaking outcome tetap dapat dipakai, tetapi limitation harus jauh lebih eksplisit dan peluang reviewer mempertanyakan validity lebih tinggi.

## B. Apakah provenance dan item-level data 16-item metacognitive scale masih ada?

**Jika ya:** dokumentasikan adaptation, item mapping, scoring, content validation, dan internal consistency. Jangan sebut sebagai MASQ asli.

**Jika tidak:** downgrade menjadi exploratory outcome atau keluarkan dari title.

## C. Apakah full interview transcripts/recordings masih ada?

**Jika ya:** lakukan qualitative analysis yang reproducible dan pertahankan mixed-method claim.

**Jika tidak:** gunakan excerpts hanya sebagai contextual qualitative evidence dan hentikan klaim tentang "mechanisms".

---

# 6. REFERENSI DAN THEORY CLEAN-UP

Prioritas:

1. Ganti semua citation-link Markdown dengan APA 7 biasa.
2. Hapus semua HTML anchors.
3. Audit year/volume/pages dari publisher/Crossref.
4. Bedakan theoretical constructs:
   - Multimedia learning: segmentation/input design.
   - Generative learning: self-explanation/teaching-to-learn.
   - Self-regulated learning: planning, monitoring, evaluation, reflection.
5. Jadikan "Feynman-style" sebagai instructional label, bukan theoretical construct utama.
6. Jangan memakai source yang hanya mendukung speaking secara umum untuk menyimpulkan mekanisme tertentu.
7. Tambahkan literature yang lebih langsung tentang self-recording, self-assessment/self-monitoring, dan repeated oral rehearsal jika memang akan menjadi mechanism claim.

---

# 7. HAL YANG SUDAH KUAT DAN SEBAIKNYA DIPERTAHANKAN

- Scope fit dengan CALL-EJ kuat.
- Intervention bukan sekadar "platform", tetapi sudah memiliki cycle yang dapat dirumuskan dengan baik: segmented input -> explain-back -> recording -> replay -> revision.
- Kedua groups sama-sama meningkat, jadi narrative tidak memaksakan "control gagal".
- Manuskrip sudah mengakui contamination dan baseline imbalance, yang menunjukkan awareness terhadap limitations.
- Speaking effect dari reported gain summary tidak trivial: mean gain difference 3.61 points dan reported Hedges' g sekitar .65. Namun hierarki analysis harus dipindah ke baseline-adjusted result.
- Data usability yang negatif justru berharga. Navigation, internet dependence, dan recording stability membuat paper lebih realistis jika diperlakukan sebagai implementation findings.
- Abstract saat ini sudah memenuhi batas 200 words dan keywords sudah tepat 5.

---

# 8. DO-NOT-SUBMIT CHECKLIST

Jangan submit sebelum semua item ini selesai:

- [ ] Title dan RQs tidak lagi overstating causality.
- [ ] Methods menyebut dengan jelas jumlah intact classes dan unit assignment.
- [ ] Instructor, duration, dosage, control activities, dan treatment fidelity dilaporkan.
- [ ] Control contamination dikuantifikasi dari logs, atau dinyatakan unquantifiable secara eksplisit.
- [ ] Speaking task/rubric provenance dijelaskan.
- [ ] Rater procedure dan ICC/reliability dilaporkan, jika data memungkinkan.
- [ ] 16-item metacognitive questionnaire diklasifikasikan sebagai adapted/custom instrument, bukan MASQ asli.
- [ ] Item mapping, scoring, adaptation, dan reliability metacognitive scale dilaporkan.
- [ ] Contradiction tentang metacognitive pretest/posttest diperbaiki.
- [ ] Semua inferential analyses direrun dari final dataset/script.
- [ ] Baseline-adjusted model menjadi primary analysis.
- [ ] Normalized gain metacognitive dihapus atau diberi definisi/handling yang sah.
- [ ] Full qualitative method ditulis, atau klaim mixed-method/mechanism diturunkan.
- [ ] Ethics approval, informed consent, funding, dan competing interests statements ditambahkan.
- [ ] Product pilot tidak dipakai sebagai efficacy evidence.
- [ ] Weak second questionnaire dihapus dari main text kecuali ada alasan kuat.
- [ ] "Accessible" diganti jika tidak didukung usability data.
- [ ] "Feynman technique" direframe sebagai explain-back/self-explanation within generative learning.
- [ ] Heading Results/Discussion tidak redundan.
- [ ] Semua Markdown/HTML citation artefacts dihapus.
- [ ] Semua references diaudit sesuai APA 7 dan publisher metadata.
- [ ] Abstract diupdate dengan adjusted estimates final dan tetap <=200 words.
- [ ] Manuscript final berada sekitar 6,000-8,000 words sesuai CALL-EJ.
- [ ] Turnitin/similarity report <15% disiapkan.
- [ ] CALL-EJ human-participant forms dan required submission forms disiapkan.

---

# 9. SIMULASI FINAL REVIEWER COMMENT

**Recommendation: Major Revision**

The manuscript addresses a topic that fits CALL-EJ well and presents a potentially useful CALL practice architecture combining segmented multimedia input, explain-back production, self-recording, replay, and revision. The dual focus on speaking performance and speaking-related metacognitive awareness is potentially valuable. However, the current manuscript does not yet provide sufficient methodological transparency to support its main claims. The most important concerns involve the non-equivalent intact-class design, incomplete description of the intervention and control condition, uncertain reliability of the speaking assessment, insufficient documentation of the adapted metacognitive instrument, reliance on archived inferential outputs, unquantified treatment contamination, and limited qualitative analytic detail. The metacognitive instrument requires particular attention because the 16-item four-point version described in the manuscript does not correspond directly to the published 21-item MASQ. The statistical analysis should be fully reproduced from the final dataset, with baseline-adjusted models treated as primary. Ethical approval and informed consent reporting should also be added. If these issues are resolved transparently and the reanalysis supports the reported pattern, the manuscript would be substantially stronger and suitable for reconsideration.

---

## Bottom line

**Scope: kuat.**
**Idea/intervention architecture: kuat.**
**Current methodological defensibility: belum cukup untuk accept.**
**Most likely current decision: Major Revision, dengan desk-review risk jika ethics, template/APA artefacts, dan basic reporting tidak dibersihkan.**
**Most important acceptance lever: rebuild Methods + instrument validity + rerun baseline-adjusted statistics + rater reliability.**

Kalau hanya memperbaiki bahasa, paper ini masih rentan ditolak. Kalau empat area itu diperbaiki dengan data asli, paper ini punya cerita yang jauh lebih kuat untuk CALL-EJ.
