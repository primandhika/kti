from pathlib import Path
import re

src = Path('outputs/base-article-fostering-metacognitive-regulation.md')
text = src.read_text().replace('\u00a0', ' ')
lines = [ln.rstrip() for ln in text.splitlines()]

start = next(i for i,l in enumerate(lines) if l.strip() == 'Abstract')
end = next(i for i,l in enumerate(lines) if l.strip() == 'References')
core = lines[start:end]

headings = {
    'Abstract': '# Abstract',
    'Introduction': '# Introduction',
    'Literature Review': '# Literature Review',
    'Academic Speaking and Metacognitive Regulation in Higher Education': '## Academic Speaking and Metacognitive Regulation in Higher Education',
    'Microlearning as Instructional Design for Deep Processing': '## Microlearning as Instructional Design for Deep Processing',
    'The Feynman Technique as an Explain-to-Learn Strategy': '## The Feynman Technique as an Explain-to-Learn Strategy',
    'Theoretical Framework': '# Theoretical Framework',
    'Cognitive Theory of Multimedia Learning (CTML)': '## Cognitive Theory of Multimedia Learning (CTML)',
    'Self-Regulated Learning (SRL) Theory': '## Self-Regulated Learning (SRL) Theory',
    'Integration of CTML and SRL in Research Design': '## Integration of CTML and SRL in Research Design',
    'Research Method': '# Research Method',
    'Participants': '## Participants',
    'Research Design': '## Research Design',
    'Instructional Intervention': '## Instructional Intervention',
    'Instruments and Data Collection': '## Instruments and Data Collection',
    'Academic Speaking Performance Test': '### Academic Speaking Performance Test',
    'Metacognitive Regulation Questionnaire': '### Metacognitive Regulation Questionnaire',
    'Learner Perception Survey': '### Learner Perception Survey',
    'Validity and Reliability': '## Validity and Reliability',
    'Data Analysis': '## Data Analysis',
    'Results': '# Results',
    'Descriptive Statistics': '## Descriptive Statistics',
    'RQ1: Effects on Metacognitive Regulation': '## RQ1: Effects on Metacognitive Regulation',
    'RQ2: Effects on Academic Speaking Performance': '## RQ2: Effects on Academic Speaking Performance',
    'RQ3: Student Perceptions of the Instructional Approach': '## RQ3: Student Perceptions of the Instructional Approach',
    'Discussion': '# Discussion',
    'Interpretation of Findings on Metacognitive Regulation (RQ1)': '## Interpretation of Findings on Metacognitive Regulation (RQ1)',
    'Interpretation of Findings on Academic Speaking Performance (RQ2)': '## Interpretation of Findings on Academic Speaking Performance (RQ2)',
    'Interpretation of Findings on Student Perceptions (RQ3)': '## Interpretation of Findings on Student Perceptions (RQ3)',
    'Comparison with Prior Research': '## Comparison with Prior Research',
    'Limitations': '# Limitations',
    'Recommendations for Future Research': '# Recommendations for Future Research',
    'Practical Implications': '# Practical Implications',
    'Conclusion': '# Conclusion',
}

list_item_re = re.compile(r'^[1-9]\.')
out = []
paragraph = []

def flush_paragraph():
    if paragraph:
        txt = ' '.join(x.strip() for x in paragraph)
        txt = re.sub(r'\s+', ' ', txt).strip()
        if txt:
            out.append(txt)
        paragraph.clear()

for raw in core:
    s = raw.strip()
    if not s:
        flush_paragraph()
        continue
    if s in headings:
        flush_paragraph()
        out += ['', headings[s], '']
        continue
    if s.startswith('Keywords:') or list_item_re.match(s) or s.startswith('The authors declare'):
        flush_paragraph()
        out += [s, '']
        continue
    paragraph.append(s)

flush_paragraph()

clean = []
prev_blank = False
for line in out:
    blank = line == ''
    if blank and prev_blank:
        continue
    clean.append(line)
    prev_blank = blank

Path('outputs/base-article-sections-raw.md').write_text('\n'.join(clean).strip() + '\n')
print('Wrote outputs/base-article-sections-raw.md')
