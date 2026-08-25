# Topik Materi Linguistik Komputasi

Dokumen ini merangkum 14 topik utama dalam linguistik komputasi beserta fokus pembahasannya.

## 1. Pengenalan Konsep Dasar Linguistik Komputasi

**Fokus utama:**

- Linguistik komputasi bertujuan memasangkan bentuk bahasa, termasuk suara atau teks, dengan makna secara efisien pada domain bahasa yang sangat luas.
- Tata bahasa dapat didefinisikan secara eksplisit sebagai sistem formal atau deduktif.
- Dalam pendekatan komputasional klasik, bahasa pemrograman logika seperti **PROLOG** dapat digunakan untuk merepresentasikan aturan tata bahasa dan melakukan inferensi linguistik.

## 2. Pra-pemrosesan Teks, Tokenisasi, dan Segmentasi Kalimat

**Fokus utama:**

- Memecah teks mentah menjadi unit-unit yang dapat diproses, seperti **token**, kata, dan kalimat.
- Tahap ini merupakan langkah awal yang penting dalam *pipeline* Natural Language Processing (NLP).
- Tokenisasi dapat menjadi sulit pada bahasa tanpa pemisah spasi yang jelas, seperti bahasa Mandarin dan Thai.
- Pada bahasa yang menggunakan spasi, seperti bahasa Inggris dan Indonesia, tokenisasi tetap dapat menghadapi ambiguitas akibat tanda baca, singkatan, angka, atau bentuk khusus lainnya.
- **Sentence segmentation** menentukan batas kalimat karena kalimat sering menjadi unit dasar dalam analisis linguistik lanjutan.

## 3. Analisis Leksikal dan Morfologi

**Fokus utama:**

- Mempelajari bagaimana kata dibentuk dari unit pembawa makna terkecil, yaitu **morfem**.
- Menghubungkan berbagai bentuk morfologis suatu kata ke **lemma** atau bentuk kanonis melalui proses *lemmatization*.
- Melakukan **stemming**, yaitu mereduksi kata ke bentuk dasar atau akar, terutama untuk keperluan *Information Retrieval* (IR).
- Menganalisis **morphotactics**, yaitu aturan pengurutan morfem.
- Menganalisis **orthographic rules**, yaitu aturan perubahan bentuk tulisan atau ejaan ketika morfem digabungkan.

## 4. Transduser Berhingga (*Finite-State Transducers* / FST)

**Fokus utama:**

- FST merupakan mesin keadaan berhingga yang memetakan satu representasi bahasa ke representasi lainnya.
- Dalam morfologi, FST dapat menghubungkan **lexical level** dengan **surface level**.
- FST dapat digunakan secara fleksibel untuk **parsing** maupun **generation**.
- Dalam *Two-Level Morphology*, FST digunakan untuk memodelkan hubungan antara bentuk morfologis dan perubahan ortografis.
- FST juga dapat digunakan untuk memodelkan fenomena seperti **allomorphy** dan perubahan bentuk fonologis tertentu.

## 5. Tata Bahasa Formal dan Hirarki Chomsky

**Fokus utama:**

Hirarki Chomsky mengklasifikasikan tata bahasa berdasarkan daya generatif dan kompleksitas komputasinya:

1. **Type 0: Unrestricted Grammar**
2. **Type 1: Context-Sensitive Grammar**
3. **Type 2: Context-Free Grammar**
4. **Type 3: Regular Grammar**

Pemahaman hirarki ini penting untuk menentukan jenis model komputasi yang sesuai dengan fenomena linguistik yang dianalisis.

## 6. Tata Bahasa Bebas Konteks (*Context-Free Grammars* / CFG)

**Fokus utama:**

- CFG merupakan sistem formal yang umum digunakan untuk memodelkan **struktur konstituen** dalam bahasa alami.
- CFG sering menjadi dasar bagi model sintaksis komputasional.
- CFG mendefinisikan sebuah **formal language** melalui seperangkat simbol terminal, nonterminal, aturan produksi, dan simbol awal.
- CFG dapat dikonversi ke **Chomsky Normal Form (CNF)**.
- Dalam CNF, aturan produksi umumnya dibatasi sehingga menghasilkan dua simbol nonterminal atau satu simbol terminal.

## 7. Algoritma Parsing Sintaksis: CKY, Earley, dan *Dynamic Programming*

**Fokus utama:**

- **Parsing** adalah proses mengenali struktur sintaksis suatu kalimat dan menghasilkan representasi seperti *parse tree*.
- Pendekatan *Dynamic Programming* digunakan untuk mengurangi pengulangan komputasi dan menghindari ketidakefisienan *backtracking*.
- Dua algoritma penting adalah:
  - **CKY / CYK (Cocke-Kasami-Younger)**
  - **Earley Parser**
- CKY biasanya memerlukan tata bahasa dalam bentuk **Chomsky Normal Form**.
- Algoritma parsing juga harus mampu menangani **ambiguitas sintaksis**, ketika sebuah kalimat memiliki lebih dari satu struktur yang mungkin.

## 8. Tata Bahasa Probabilistik (*Probabilistic Context-Free Grammars* / PCFG)

**Fokus utama:**

- PCFG merupakan perluasan CFG dengan memberikan **probabilitas** pada setiap aturan produksi.
- Probabilitas digunakan untuk menentukan struktur sintaksis yang paling mungkin.
- PCFG sangat berguna untuk **disambiguasi sintaksis**.
- Jika sebuah kalimat memiliki beberapa *parse tree*, model dapat memilih analisis dengan probabilitas tertinggi.

## 9. Model Bahasa N-gram dan *Perplexity*

**Fokus utama:**

- Model **N-gram** memperkirakan probabilitas sebuah kata berdasarkan sejumlah kata sebelumnya.
- Contoh:
  - **Unigram**: hanya mempertimbangkan satu kata.
  - **Bigram**: mempertimbangkan satu kata sebelumnya.
  - **Trigram**: mempertimbangkan dua kata sebelumnya.
- **Maximum Likelihood Estimation (MLE)** merupakan salah satu metode dasar untuk memperkirakan probabilitas N-gram dari frekuensi data.
- **Perplexity** digunakan sebagai metrik evaluasi intrinsik model bahasa.
- Semakin rendah nilai *perplexity*, semakin baik model dalam memprediksi urutan bahasa pada data evaluasi.

## 10. Semantik Linguistik dan Representasi Makna

**Fokus utama:**

- Analisis semantik berupaya menerjemahkan ekspresi bahasa alami ke dalam representasi formal.
- Representasi tersebut dapat menggunakan **semantic metalanguage**.
- Dalam **procedural theory of meaning**, makna dipandang sebagai prosedur untuk menentukan atau menghitung referen suatu ekspresi.
- **First-Order Logic (FOL)** atau **predicate calculus** merupakan salah satu formalisme utama untuk merepresentasikan makna.
- Representasi formal memungkinkan sistem komputasi melakukan inferensi terhadap makna suatu ujaran atau kalimat.

## 11. Analisis Semantik Leksikal dan Resolusi Ambiguitas Kata (*Word Sense Disambiguation* / WSD)

**Fokus utama:**

- **Word Sense Disambiguation (WSD)** menentukan makna atau *word sense* yang paling sesuai bagi sebuah kata dalam konteks tertentu.
- WSD diperlukan karena satu kata dapat memiliki beberapa makna.
- Analisis semantik juga mencakup **Semantic Role Labeling (SRL)**.
- SRL berupaya mengenali peran peserta dalam suatu peristiwa, misalnya:
  - siapa yang melakukan tindakan,
  - tindakan apa yang dilakukan,
  - kepada siapa atau terhadap apa tindakan dilakukan.

## 12. Tata Bahasa Berbasis Kendala (*Constraint-Based Grammars*) dan Unifikasi

**Fokus utama:**

- Tata bahasa berbasis kendala menggunakan **feature structures** atau **feature terms** untuk merepresentasikan informasi linguistik.
- Representasi biasanya berbentuk pasangan **atribut-nilai**.
- Pendekatan ini lebih kaya daripada kategori atom sederhana seperti `NP`, `VP`, atau `S` pada CFG tradisional.
- Operasi **unifikasi** digunakan untuk menggabungkan informasi linguistik yang kompatibel.
- Unifikasi mendukung pemrosesan yang relatif reversibel sehingga bermanfaat untuk **parsing** dan **generation**.
- Contoh formalisme:
  - **Head-driven Phrase Structure Grammar (HPSG)**
  - **Lexical-Functional Grammar (LFG)**

## 13. Pemrosesan Pragmatik dan Analisis Wacana (*Discourse*)

**Fokus utama:**

- Pragmatik membahas makna bahasa dalam **konteks penggunaan**.
- Analisis wacana memproses hubungan antarkalimat atau antarbagian teks sebagai satu kesatuan.
- Salah satu tugas penting adalah **reference resolution**, yaitu menentukan referen dari ekspresi seperti pronomina atau frasa nominal.
- Pemrosesan wacana juga dapat mencakup:
  - identifikasi **cue phrases**,
  - segmentasi wacana,
  - hubungan antarkalimat,
  - koherensi dan struktur informasi.

## 14. Aplikasi Utama Linguistik Komputasi

**Fokus utama:**

Konsep-konsep linguistik komputasi diterapkan pada berbagai sistem, antara lain:

### a. Terjemahan Mesin (*Machine Translation* / MT)

- Menerjemahkan teks dari satu bahasa ke bahasa lain secara otomatis.
- Dapat melibatkan model bahasa, analisis sintaksis, analisis semantik, dan model probabilistik.

### b. Pengenalan Ucapan (*Speech Recognition*)

- Mengubah sinyal suara menjadi representasi teks.
- Sistem tradisional banyak menggunakan kombinasi **acoustic model** dan **language model**.
- Model N-gram pernah menjadi komponen utama dalam banyak sistem pengenalan ucapan klasik.

### c. Pengambilan Informasi (*Information Retrieval* / IR)

- Menemukan dokumen atau informasi yang relevan berdasarkan kebutuhan pengguna.
- Teknik seperti tokenisasi, stemming, lemmatization, pembobotan istilah, dan pemodelan bahasa dapat digunakan dalam proses pencarian.

---

## Ringkasan Alur Materi

Secara umum, urutan pembelajaran dapat dipahami sebagai berikut:

**Dasar Linguistik Komputasi**  
→ **Pra-pemrosesan Teks**  
→ **Morfologi dan FST**  
→ **Tata Bahasa Formal dan CFG**  
→ **Parsing Sintaksis**  
→ **Model Probabilistik dan N-gram**  
→ **Semantik**  
→ **Pragmatik dan Wacana**  
→ **Aplikasi Linguistik Komputasi**
