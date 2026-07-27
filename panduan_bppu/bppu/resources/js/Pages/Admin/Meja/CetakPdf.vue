<template>
  <div>
    <div class="no-print ctrl-bar">
      <button @click="window.print()" class="btn-print">
        <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
        </svg>
        Cetak / Simpan PDF
      </button>
      <a href="/pengelola/meja" class="btn-back">&larr; Kembali</a>
      <span class="ctrl-badge" :class="type === 'belanja' ? 'badge-toko' : 'badge-kantin'">
        {{ type === 'belanja' ? 'QR Toko' : 'QR Kantin' }}
      </span>
      <span class="ctrl-info">{{ totalMeja }} meja &bull; {{ totalLokasi }} lokasi</span>
    </div>

    <div class="pages-wrap">
      <div v-for="(chunk, pi) in mejaChunks" :key="pi" class="page">
        <div class="cards">
          <div
            v-for="meja in chunk" :key="meja.id"
            class="card"
            :class="type === 'belanja' ? 'card-toko' : 'card-kantin'"
          >
            <div class="card-inner">

              <!-- KIRI: info panel KANTIN (tidak berubah) -->
              <template v-if="type !== 'belanja'">
                <div class="left left-kantin">
                  <div class="brand">
                    <img src="/images/logo-bppu-round.png" alt="BPPU" class="brand-logo" />
                    <div class="brand-txt">
                      <b>BPPU</b>
                      <em>IKIP Siliwangi</em>
                    </div>
                  </div>
                  <div class="meja-block">
                    <span class="meja-lokasi">{{ meja.lokasi_nama }}</span>
                    <span class="meja-nama">{{ meja.nama || meja.kode_meja }}</span>
                  </div>
                  <div class="steps">
                    <div class="step">
                      <svg class="sico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0118.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                        <circle cx="12" cy="13" r="3"/>
                      </svg>
                      <span>Scan QR Code</span>
                    </div>
                    <div class="step">
                      <svg class="sico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                      </svg>
                      <span>Pilih Menu</span>
                    </div>
                    <div class="step">
                      <svg class="sico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                      </svg>
                      <span>Bayar</span>
                    </div>
                  </div>
                  <div class="footer-row">
                    <img src="/images/qris.png" alt="QRIS" class="p-qris" />
                    <img src="/images/gpn.svg"  alt="GPN"  class="p-gpn"  />
                  </div>
                </div>
              </template>

              <!-- KIRI: info panel TOKO — 2 section: atas merah, bawah biru -->
              <template v-else>
                <div class="left left-toko">

                  <!-- section atas: merah -->
                  <div class="toko-top">
                    <div class="brand">
                      <img src="/images/logo-bppu-round.png" alt="BPPU" class="brand-logo" />
                      <div class="brand-txt-toko">
                        <span class="toko-title">Siliwangi Mandiri</span>
                        <span class="toko-sub">SHOP</span>
                      </div>
                    </div>
                    <div class="meja-block-toko">
                      <span class="meja-lokasi-toko">{{ meja.lokasi_nama }}</span>
                      <span class="meja-nama-toko">{{ meja.nama || meja.kode_meja }}</span>
                    </div>
                    <div class="unit-label">
                      <span class="unit-badge unit-toko">Belanja</span>
                    </div>
                  </div>

                  <!-- section bawah: biru -->
                  <div class="toko-bot">
                    <div class="steps">
                      <div class="step">
                        <svg class="sico sico-toko" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0118.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                          <circle cx="12" cy="13" r="3"/>
                        </svg>
                        <span>Scan QR Code</span>
                      </div>
                      <div class="step">
                        <svg class="sico sico-toko" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                        <span>Belanja</span>
                      </div>
                      <div class="step">
                        <svg class="sico sico-toko" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <span>Bayar</span>
                      </div>
                    </div>
                    <div class="footer-row footer-row-toko">
                      <img src="/images/qris.png" alt="QRIS" class="p-qris" />
                      <img src="/images/gpn.svg"  alt="GPN"  class="p-gpn"  />
                    </div>
                  </div>

                </div>
              </template>

              <!-- KANAN: QR panel -->
              <div class="right" :class="type === 'belanja' ? 'right-toko' : ''">
                <p class="qr-hint" :class="type === 'belanja' ? 'qr-hint-toko' : ''">
                  {{ type === 'belanja' ? 'Scan untuk belanja' : 'Pesan dari Kantin' }}
                </p>
                <div class="qr-frame" :class="type === 'belanja' ? 'qr-frame-toko' : ''">
                  <canvas :ref="el => setRef(el, meja.id)" />
                </div>
                <p class="qr-url" :class="type === 'belanja' ? 'qr-url-toko' : ''">{{ shortUrl }}</p>
                <p class="qr-tagline" :class="type === 'belanja' ? 'qr-tagline-toko' : 'qr-tagline-kantin'">Langsung diantar<br>ke ruangan/mejamu!</p>
              </div>

            </div>
          </div>
          <div v-for="i in (9 - chunk.length)" :key="'e'+i" class="card card-empty"></div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import QRCode from 'qrcode'

const props = defineProps({
  lokasis: { type: Array,  default: () => [] },
  type:    { type: String, default: 'kantin' },
  baseUrl: { type: String, default: '' },
})

const allMejas = computed(() => {
  const list = []
  props.lokasis.forEach(l => (l.mejas || []).forEach(m => list.push({ ...m, lokasi_nama: l.nama })))
  return list
})

const mejaChunks = computed(() => {
  const out = []
  for (let i = 0; i < allMejas.value.length; i += 9) out.push(allMejas.value.slice(i, i + 9))
  return out
})

const shortUrl    = computed(() => { try { return new URL(props.baseUrl).hostname } catch { return props.baseUrl } })
const totalMeja   = computed(() => allMejas.value.length)
const totalLokasi = computed(() => props.lokasis.length)
const window      = globalThis
const refs        = ref({})
const setRef      = (el, id) => { if (el) refs.value[id] = el }

onMounted(async () => {
  for (const m of allMejas.value) {
    const c = refs.value[m.id]
    if (!c || !m.qr_url) continue
    await QRCode.toCanvas(c, m.qr_url, {
      width: 114, margin: 1, color: { dark: '#000000', light: '#ffffff' }
    })
  }
})
</script>

<style>
@import url('https://fonts.googleapis.com/css2?family=Dancing+Script:wght@600&display=swap');
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
body { background: #cbd5e1; }

/* ── controls ── */
.ctrl-bar {
  display: flex; align-items: center; gap: 12px;
  padding: 10px 16px; background: #fff; border-bottom: 1px solid #e5e7eb;
}
.btn-print {
  display: flex; align-items: center; gap: 6px;
  padding: 7px 16px; background: #996600; color: #fff;
  border: none; border-radius: 8px; font-size: 13px; font-weight: 600;
  cursor: pointer; font-family: Arial, sans-serif;
}
.btn-print:hover { background: #7a5100; }
.btn-back  { font-size: 13px; color: #6b7280; text-decoration: none; font-family: Arial, sans-serif; }
.btn-back:hover { color: #996600; }
.ctrl-badge {
  font-size: 11px; font-weight: 700; padding: 3px 10px;
  border-radius: 20px; font-family: Arial, sans-serif;
}
.badge-kantin { background: #fef3c7; color: #92400e; }
.badge-toko   { background: #dbeafe; color: #1e40af; }
.ctrl-info { font-size: 12px; color: #9ca3af; margin-left: auto; font-family: Arial, sans-serif; }

/* ── pages ── */
.pages-wrap { padding: 16px; display: flex; flex-direction: column; gap: 16px; background: #cbd5e1; }
.page {
  width: 1090px; height: 762px;
  background: #fff; border-radius: 4px; overflow: hidden;
}
.cards {
  display: grid;
  grid-template-columns: repeat(3, 359px);
  grid-template-rows: repeat(3, 248px);
  gap: 5px; padding: 5px;
  width: 100%; height: 100%;
}

/* ── card base ── */
.card-kantin {
  font-family: Cambria, 'Times New Roman', serif;
  background: #fff; border: 1.5px solid #d6c199;
  border-radius: 8px; overflow: hidden;
  display: flex; flex-direction: column;
}
.card-toko {
  font-family: Arial, Helvetica, sans-serif;
  background: #fff; border: 1.5px solid #bfdbfe;
  border-radius: 8px; overflow: hidden;
  display: flex; flex-direction: column;
}
.card-empty { background: transparent; border: 1.5px dashed #e8dcc8; border-radius: 8px; }

/* ── card-inner ── */
.card-inner {
  flex: 1; display: flex; flex-direction: row;
  overflow: hidden; min-height: 0;
}

/* ── LEFT PANEL base ── */
.left {
  flex: 1; display: flex; flex-direction: column;
  justify-content: space-between; min-width: 0;
  padding: 7px 9px 6px;
}
.left-kantin { background: #fff; }

/* toko: tidak ada padding di sini, diatur per section */
.left-toko {
  padding: 0;
  flex-direction: column;
}

/* section atas MERAH */
.toko-top {
  background: #c01515;
  padding: 7px 9px 7px;
  display: flex; flex-direction: column; gap: 5px;
  flex: 1;
}

/* section bawah BIRU */
.toko-bot {
  background: #1e3a8a;
  padding: 6px 9px 6px;
  display: flex; flex-direction: column;
  justify-content: space-between;
  flex: 1;
}

/* brand kantin */
.brand { display: flex; align-items: center; gap: 6px; }
.brand-logo { width: 28px; height: 28px; object-fit: contain; flex-shrink: 0; }
.brand-txt { display: flex; flex-direction: column; line-height: 1.15; }
.brand-txt b  { font-size: 12px; font-weight: 700; color: #996600; letter-spacing: .04em; }
.brand-txt em { font-size: 7.5px; font-style: italic; color: #7a5100; }

/* brand toko */
.brand-txt-toko { display: flex; flex-direction: column; line-height: 1.2; }
.toko-title { font-size: 10.5px; font-weight: 800; color: #ffffff; letter-spacing: .01em; }
.toko-sub   { font-size: 7px; font-weight: 700; color: rgba(255,255,255,0.75); letter-spacing: .18em; }

/* meja block kantin */
.meja-block {
  display: flex; flex-direction: column;
  background: linear-gradient(135deg, #f4efe5, #e8d9bc);
  border-left: 3px solid #996600; border-radius: 5px;
  padding: 5px 9px; gap: 2px;
}
.meja-lokasi {
  font-size: 7px; font-weight: 700;
  color: #b7934c; text-transform: uppercase; letter-spacing: .08em;
}
.meja-nama {
  font-family: Cambria, serif;
  font-size: 16px; font-weight: 700;
  color: #1e1400; line-height: 1; letter-spacing: -.02em;
}

/* meja block toko: putih transparan di atas merah */
.meja-block-toko {
  display: flex; flex-direction: column;
  background: rgba(255,255,255,0.18);
  border-left: 3px solid #fff;
  border-radius: 5px;
  padding: 5px 9px; gap: 2px;
}
.meja-lokasi-toko {
  font-size: 7px; font-weight: 700; font-family: Arial, sans-serif;
  color: rgba(255,255,255,0.8); text-transform: uppercase; letter-spacing: .08em;
}
.meja-nama-toko {
  font-size: 16px; font-weight: 700; font-family: Arial, sans-serif;
  color: #ffffff; line-height: 1;
}

/* badge jenis */
.unit-label { display: flex; }
.unit-badge {
  font-size: 8px; font-weight: 700; padding: 2px 8px;
  border-radius: 20px; text-transform: uppercase; letter-spacing: .06em;
  font-family: Arial, sans-serif;
}
.unit-kantin { background: #fef3c7; color: #92400e; }
.unit-toko   { background: rgba(255,255,255,0.22); color: #ffffff; border: 1px solid rgba(255,255,255,0.6); }

/* steps */
.steps { display: flex; flex-direction: column; gap: 3px; }
.step  { display: flex; align-items: center; gap: 6px; }
.sico  {
  width: 17px; height: 17px; flex-shrink: 0;
  stroke: #fff; background: #996600;
  border-radius: 50%; padding: 2.5px; display: block;
}
.sico-toko { background: rgba(255,255,255,0.2); stroke: #ffffff; }
.step span { font-size: 9.5px; font-weight: 700; color: #1e1400; font-family: Arial, sans-serif; }
.toko-bot .step span { color: #ffffff; }

/* footer */
.footer-row { display: flex; align-items: center; gap: 7px; padding-top: 5px; border-top: 1px solid #e0d1b2; }
.footer-row-toko { border-top: 1px solid rgba(255,255,255,0.25); }
.p-qris { height: 16px; width: auto; object-fit: contain; }
.p-gpn  { height: 14px; width: auto; object-fit: contain; }
.toko-bot .p-qris { filter: brightness(0) invert(1); }
.toko-bot .p-gpn  { filter: brightness(0) invert(1); }

/* ── RIGHT PANEL ── */
.right {
  display: flex; flex-direction: column;
  align-items: center; justify-content: center;
  gap: 5px; flex-shrink: 0; width: 130px;
  background: #fff;
}
.right-toko {
  background: #f0f4ff;
}

.qr-hint { font-size: 7px; font-weight: 700; color: #996600; text-transform: uppercase; letter-spacing: .07em; font-family: Arial, sans-serif; }
.qr-hint-toko { color: #1e3a8a; }
.qr-frame { padding: 4px; border: 1.5px solid #d6c199; border-radius: 6px; background: #fff; line-height: 0; }
.qr-frame-toko { border: 2px solid #1e3a8a; }
.qr-frame canvas { display: block; border-radius: 3px; }
.qr-url { font-size: 6.5px; font-style: italic; color: #b7934c; text-align: center; font-family: Arial, sans-serif; }
.qr-url-toko { color: #1e3a8a; }
.qr-tagline {
  font-family: 'Dancing Script', 'Segoe Script', 'Brush Script MT', cursive;
  font-size: 10px; font-weight: 700;
  text-align: center; line-height: 1.1;
}
.qr-tagline-toko    { color: #c01515; }
.qr-tagline-kantin  { color: #996600; }

/* ── print ── */
@media print {
  @page { size: A4 landscape; margin: 16px; }
  .no-print { display: none !important; }
  body { background: white; }
  .pages-wrap { padding: 0; background: white; gap: 0; }
  .page {
    width: 100%; height: 100%; box-shadow: none; border-radius: 0;
    page-break-after: always; break-after: page;
  }
  .page:last-child { page-break-after: avoid; break-after: avoid; }
  .cards { grid-template-columns: repeat(3, 1fr); grid-template-rows: repeat(3, 1fr); padding: 4px; gap: 4px; }
  * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
}
</style>
