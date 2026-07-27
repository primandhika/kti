<template>
    <div class="root">

        <!-- HEADER -->
        <header class="header">
            <div class="h-left">
                <img src="/logo-BPPU-flat.png" alt="BPPU" class="logo" />
                <div class="h-brand">
                    <span class="h-name">Kantin BPPU</span>
                    <span class="h-sub">IKIP Siliwangi</span>
                </div>
                <button class="h-kantin" @click="showKantinModal = true">
                    {{ namaKantin }}
                    <span class="h-kantin-icon">&#9660;</span>
                </button>
            </div>

            <div class="h-center">
                <span class="h-title">ANTRIAN PESANAN</span>
            </div>

            <div class="h-right">
                <span class="h-clock">
                    {{ clockHM }}<span class="clock-sec">:{{ clockSec }}</span>
                    <span class="clock-wib">WIB</span>
                </span>
                <span class="h-date">{{ currentDate }}</span>
            </div>
        </header>

        <!-- BODY — 2 KOLOM -->
        <div class="body">

            <!-- KOLOM KIRI — pesanan aktif -->
            <section class="col col-left">
                <div class="col-head">
                    <span class="col-title">PESANAN MASUK</span>
                    <span class="col-count">{{ pesananAktifList.length }} antrian</span>
                </div>

                <div class="row-legend">
                    <span class="leg-no">No.</span>
                    <span class="leg-name">Pemesan</span>
                    <span class="leg-meja">Lokasi / Meja</span>
                    <span class="leg-item">Item</span>
                    <span class="leg-time">Waktu</span>
                    <span class="leg-status">Status</span>
                </div>

                <div v-if="!pesananAktifList.length" class="empty">
                    Belum ada pesanan aktif
                </div>

                <div class="row-list-wrap">
                    <TransitionGroup name="row" tag="div" class="row-list">
                        <ActiveRow
                            v-for="p in pesananAktifList"
                            :key="p.id"
                            :pesanan="p"
                        />
                    </TransitionGroup>
                    <div v-if="overflowCount > 0" class="row-fade-overlay" />
                </div>

                <Transition name="toast">
                    <div v-if="overflowCount > 0" class="overflow-toast">
                        {{ overflowCount }} pesanan lainnya dalam antrean
                    </div>
                </Transition>
            </section>

            <!-- KOLOM KANAN — selesai -->
            <SelesaiPanel
                :list="pesananSelesaiList"
                :visible="showSelesai"
                @toggle="showSelesai = !showSelesai"
            />
        </div>

        <!-- FOOTER -->
        <footer class="footer">
            <span class="foot-tag">INFO</span>
            <span class="foot-text">
                Mohon periksa nomor antrian Anda secara berkala &bull;
                Pesanan siap segera diambil &bull;
                Terima kasih telah menggunakan layanan Kantin BPPU IKIP Siliwangi
            </span>
            <span class="foot-refresh">Diperbarui: {{ lastUpdated || '...' }}</span>
        </footer>

        <!-- MODAL PILIH KANTIN -->
        <Transition name="modal">
            <div v-if="showKantinModal" class="modal-backdrop" @click.self="showKantinModal = false">
                <div class="modal-box">
                    <div class="modal-head">
                        <span class="modal-title">Pilih Tampilan Kantin</span>
                        <button class="modal-close" @click="showKantinModal = false">&#10005;</button>
                    </div>
                    <div class="modal-list">
                        <button
                            class="modal-item"
                            :class="{ 'modal-item-active': kantin === null }"
                            @click="pilihKantin(null)"
                        >
                            Semua Kantin
                        </button>
                        <button
                            v-for="k in kantinList"
                            :key="k.id"
                            class="modal-item"
                            :class="{ 'modal-item-active': kantin === k.id }"
                            @click="pilihKantin(k.id)"
                        >
                            {{ k.name }}
                        </button>
                    </div>
                </div>
            </div>
        </Transition>

        <!-- MODAL PASSCODE -->
        <Transition name="passcode-modal">
            <div v-if="!isAuthenticated" class="passcode-backdrop">
                <div class="passcode-box">
                    <img src="/logo-BPPU-flat.png" alt="BPPU" class="passcode-logo" />
                    <span class="passcode-title">Live Order Display</span>
                    <span class="passcode-hint">Masukkan passcode</span>
                    <div class="passcode-input-wrap">
                        <input
                            ref="passcodeInputRef"
                            v-model="passcodeValue"
                            type="password"
                            inputmode="numeric"
                            autocomplete="off"
                            placeholder="••••••"
                            class="passcode-input"
                            :class="{ 'passcode-input-error': passcodeError }"
                            maxlength="20"
                            @keydown.enter="submitPasscode"
                        />
                        <span v-if="passcodeError" class="passcode-error">{{ passcodeError }}</span>
                    </div>
                    <button class="passcode-btn" :disabled="passcodeLoading" @click="submitPasscode">
                        <span v-if="passcodeLoading" class="passcode-spinner" />
                        <span v-else>Masuk</span>
                    </button>
                </div>
            </div>
        </Transition>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, nextTick } from 'vue'
import ActiveRow    from '@/Components/LiveOrder/ActiveRow.vue'
import SelesaiPanel from '@/Components/LiveOrder/SelesaiPanel.vue'

const SESSION_KEY  = 'lo_auth'
const VISIBLE_ROWS = 5

const props = defineProps({
    pesananAktif:   { type: Array,  default: () => [] },
    pesananSelesai: { type: Array,  default: () => [] },
    namaKantin:     { type: String, default: 'Semua Kantin' },
    kantin:         { type: Number, default: null },
    kantinList:     { type: Array,  default: () => [] },
})

const pesananAktifList   = ref([...props.pesananAktif])
const pesananSelesaiList = ref([...props.pesananSelesai])
const lastUpdated        = ref(null)
const clockHM            = ref('00:00')
const clockSec           = ref('00')
const currentDate        = ref('')
const showKantinModal    = ref(false)
const showSelesai        = ref(true)

const isAuthenticated  = ref(false)
const passcodeValue    = ref('')
const passcodeError    = ref('')
const passcodeLoading  = ref(false)
const passcodeInputRef = ref(null)

let pollInterval  = null
let clockInterval = null

const overflowCount = computed(() =>
    Math.max(0, pesananAktifList.value.length - VISIBLE_ROWS)
)

function pad2(n) { return String(n).padStart(2, '0') }

function updateClock() {
    const now = new Date()
    clockHM.value  = `${pad2(now.getHours())}:${pad2(now.getMinutes())}`
    clockSec.value = pad2(now.getSeconds())
    currentDate.value = now.toLocaleDateString('id-ID', {
        weekday: 'long', day: 'numeric', month: 'long', year: 'numeric',
    })
}

function pilihKantin(id) {
    showKantinModal.value = false
    window.location.href = id ? `/live-order/${id}` : '/live-order'
}

function enterFullscreen() {
    const el = document.documentElement
    if (el.requestFullscreen)            el.requestFullscreen()
    else if (el.webkitRequestFullscreen) el.webkitRequestFullscreen()
    else if (el.mozRequestFullScreen)    el.mozRequestFullScreen()
    else if (el.msRequestFullscreen)     el.msRequestFullscreen()
}

async function submitPasscode() {
    if (!passcodeValue.value || passcodeLoading.value) return
    passcodeLoading.value = true
    passcodeError.value   = ''
    try {
        const res = await fetch('/api/live-order/verify-passcode', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
            },
            body: JSON.stringify({ passcode: passcodeValue.value }),
        })
        if (res.ok) {
            sessionStorage.setItem(SESSION_KEY, '1')
            isAuthenticated.value = true
            enterFullscreen()
        } else {
            passcodeError.value = 'Passcode salah.'
            passcodeValue.value = ''
            await nextTick()
            passcodeInputRef.value?.focus()
        }
    } catch {
        passcodeError.value = 'Terjadi kesalahan, coba lagi.'
    } finally {
        passcodeLoading.value = false
    }
}

async function poll() {
    try {
        const url = props.kantin ? `/api/live-order/${props.kantin}` : '/api/live-order'
        const res = await fetch(url, { headers: { Accept: 'application/json' } })
        if (!res.ok) return
        const data = await res.json()
        pesananAktifList.value   = data.pesananAktif
        pesananSelesaiList.value = data.pesananSelesai
        lastUpdated.value = new Date().toLocaleTimeString('id-ID', {
            hour: '2-digit', minute: '2-digit', second: '2-digit',
        })
    } catch {}
}

onMounted(() => {
    isAuthenticated.value = sessionStorage.getItem(SESSION_KEY) === '1'
    if (!isAuthenticated.value) {
        nextTick(() => passcodeInputRef.value?.focus())
    } else {
        const onFirstInteraction = () => {
            enterFullscreen()
            document.removeEventListener('click', onFirstInteraction)
            document.removeEventListener('keydown', onFirstInteraction)
        }
        document.addEventListener('click', onFirstInteraction, { once: true })
        document.addEventListener('keydown', onFirstInteraction, { once: true })
    }
    updateClock()
    clockInterval = setInterval(updateClock, 1000)
    pollInterval  = setInterval(poll, 6000)
})

onUnmounted(() => {
    clearInterval(clockInterval)
    clearInterval(pollInterval)
})
</script>

<style scoped>
* { box-sizing: border-box; }

.root {
    min-height: 100vh;
    height: 100vh;
    display: flex;
    flex-direction: column;
    background: #1e1400;
    color: #f4efe5;
    font-family: 'Inter', 'Segoe UI', system-ui, sans-serif;
    overflow: hidden;
}

/* HEADER */
.header {
    display: grid;
    grid-template-columns: 1fr auto 1fr;
    align-items: center;
    padding: 0.5rem 2rem;
    background: #0f0a00;
    border-bottom: 2px solid #3d2800;
    gap: 1rem;
    flex-shrink: 0;
}

.h-left  { display: flex; align-items: center; gap: 0.7rem; }
.h-center{ display: flex; justify-content: center; }
.h-right { display: flex; flex-direction: column; align-items: flex-end; }

.logo {
    height: 2.4rem;
    width: 2.4rem;
    object-fit: contain;
    filter: brightness(0) invert(1);
    flex-shrink: 0;
}

.h-brand { display: flex; flex-direction: column; line-height: 1.2; }
.h-name  { font-size: 1rem; font-weight: 700; color: #f4efe5; }
.h-sub   { font-size: 0.7rem; color: #ccb27f; }

.h-kantin {
    font-size: 0.8rem;
    font-weight: 600;
    color: #0f0a00;
    background: #ccb27f;
    padding: 0.15rem 0.7rem;
    border-radius: 3px;
    white-space: nowrap;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 0.3rem;
    transition: background 0.2s;
}
.h-kantin:hover { background: #b7934c; }
.h-kantin-icon  { font-size: 0.6rem; opacity: 0.7; }

.h-title {
    font-size: 1.2rem;
    font-weight: 800;
    letter-spacing: 0.2em;
    color: #ccb27f;
    text-transform: uppercase;
}

.h-clock {
    font-size: 2rem;
    font-weight: 700;
    font-variant-numeric: tabular-nums;
    color: #f4efe5;
    line-height: 1;
    display: flex;
    align-items: baseline;
    gap: 0;
}

.clock-sec {
    font-size: 1.1rem;
    font-weight: 500;
    color: #b7934c;
}

.clock-wib {
    font-size: 0.75rem;
    font-weight: 600;
    color: #7a5100;
    margin-left: 0.35rem;
    letter-spacing: 0.05em;
}

.h-date {
    font-size: 0.75rem;
    color: #7a5100;
    margin-top: 0.1rem;
}

/* BODY */
.body {
    flex: 1;
    display: grid;
    grid-template-columns: 1fr 360px;
    gap: 0;
    overflow: hidden;
    min-height: 0;
}

/* KOLOM KIRI */
.col {
    display: flex;
    flex-direction: column;
    overflow: hidden;
    min-height: 0;
}

.col-left { border-right: 1px solid #3d2800; }

.col-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.75rem 2rem;
    background: #2d1e00;
    border-bottom: 1px solid #3d2800;
    flex-shrink: 0;
}

.col-title {
    font-size: 1rem;
    font-weight: 800;
    letter-spacing: 0.18em;
    color: #ccb27f;
    text-transform: uppercase;
}

.col-count {
    font-size: 0.9rem;
    color: #4c3300;
}

/* LEGEND */
.row-legend {
    display: grid;
    grid-template-columns: 6rem 1fr 1fr 5.5rem 6rem 9rem;
    gap: 0.75rem;
    padding: 0.5rem 2rem;
    border-bottom: 1px solid #2d1e00;
    flex-shrink: 0;
}

.row-legend span {
    font-size: 0.85rem;
    font-weight: 700;
    letter-spacing: 0.1em;
    color: #4c3300;
    text-transform: uppercase;
}

/* ROW LIST WRAP */
.row-list-wrap {
    flex-shrink: 0;
    position: relative;
    overflow: hidden;
    height: calc(5 * 6.5rem);
    display: flex;
    flex-direction: column;
}

.row-list {
    flex: 1;
    overflow-y: auto;
    scrollbar-width: none;
}

.row-list::-webkit-scrollbar { display: none; }

.row-fade-overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 5rem;
    pointer-events: none;
    background: linear-gradient(to bottom, transparent 0%, rgba(30,20,0,0.7) 55%, rgba(30,20,0,0.98) 100%);
    z-index: 2;
}

.overflow-toast {
    margin: 0 2rem 1rem;
    padding: 0.65rem 1.25rem;
    background: rgba(153,102,0,0.18);
    border: 1px solid rgba(153,102,0,0.35);
    border-radius: 6px;
    font-size: 1rem;
    font-weight: 700;
    color: #ccb27f;
    letter-spacing: 0.03em;
    text-align: center;
    flex-shrink: 0;
}

.toast-enter-active, .toast-leave-active { transition: all 0.3s ease; }
.toast-enter-from, .toast-leave-to       { opacity: 0; transform: translateY(6px); }

.empty {
    padding: 3rem 2rem;
    font-size: 1rem;
    color: #3d2800;
    text-align: center;
}

.row-enter-active, .row-leave-active { transition: all 0.35s ease; }
.row-enter-from   { opacity: 0; transform: translateX(-8px); }
.row-leave-to     { opacity: 0; transform: translateX(8px); }

/* FOOTER */
.footer {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 0.6rem 2.5rem;
    background: #0f0a00;
    border-top: 1px solid #2d1e00;
    flex-shrink: 0;
    overflow: hidden;
}

.foot-tag {
    font-size: 0.8rem;
    font-weight: 800;
    letter-spacing: 0.12em;
    color: #0f0a00;
    background: #ccb27f;
    padding: 0.15rem 0.6rem;
    border-radius: 2px;
    flex-shrink: 0;
}

.foot-text {
    font-size: 0.9rem;
    color: #4c3300;
    flex: 1;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.foot-refresh {
    font-size: 0.85rem;
    color: #3d2800;
    white-space: nowrap;
    flex-shrink: 0;
}

/* PASSCODE */
.passcode-backdrop {
    position: fixed;
    inset: 0;
    z-index: 200;
    display: flex;
    align-items: center;
    justify-content: center;
    backdrop-filter: blur(18px);
    -webkit-backdrop-filter: blur(18px);
    background: rgba(15,10,0,0.55);
}

.passcode-box {
    background: #0f0a00;
    border: 1px solid #3d2800;
    border-radius: 14px;
    padding: 2.5rem 2rem;
    width: 320px;
    max-width: 90vw;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.55rem;
}

.passcode-logo {
    height: 3.5rem;
    width: 3.5rem;
    object-fit: contain;
    filter: brightness(0) invert(1);
    margin-bottom: 0.25rem;
}

.passcode-title {
    font-size: 1.05rem;
    font-weight: 800;
    color: #ccb27f;
    letter-spacing: 0.05em;
}

.passcode-hint {
    font-size: 0.78rem;
    color: #4c3300;
    margin-bottom: 0.5rem;
}

.passcode-input-wrap {
    width: 100%;
    display: flex;
    flex-direction: column;
    gap: 0.3rem;
}

.passcode-input {
    width: 100%;
    background: #2d1e00;
    border: 1px solid #3d2800;
    border-radius: 8px;
    padding: 0.75rem 1rem;
    font-size: 1.4rem;
    font-family: inherit;
    color: #f4efe5;
    letter-spacing: 0.4em;
    text-align: center;
    outline: none;
    transition: border-color 0.2s;
}
.passcode-input::placeholder { letter-spacing: 0.15em; color: #3d2800; font-size: 1rem; }
.passcode-input:focus         { border-color: #996600; }
.passcode-input-error         { border-color: #ef4444 !important; animation: shake 0.3s ease; }

.passcode-error {
    font-size: 0.78rem;
    color: #f87171;
    text-align: center;
}

.passcode-btn {
    width: 100%;
    margin-top: 0.5rem;
    background: #996600;
    border: none;
    border-radius: 8px;
    padding: 0.75rem;
    font-size: 0.95rem;
    font-weight: 700;
    color: #0f0a00;
    cursor: pointer;
    transition: background 0.2s;
    min-height: 2.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
}
.passcode-btn:hover:not(:disabled) { background: #b7934c; }
.passcode-btn:disabled             { opacity: 0.55; cursor: not-allowed; }

.passcode-spinner {
    width: 1rem;
    height: 1rem;
    border: 2px solid rgba(15,10,0,0.3);
    border-top-color: #0f0a00;
    border-radius: 50%;
    animation: spin 0.7s linear infinite;
}

.passcode-modal-enter-active, .passcode-modal-leave-active { transition: opacity 0.3s ease; }
.passcode-modal-enter-from, .passcode-modal-leave-to       { opacity: 0; }

/* MODAL KANTIN */
.modal-backdrop {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.65);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 100;
}

.modal-box {
    background: #1e1400;
    border: 1px solid #3d2800;
    border-radius: 10px;
    width: 380px;
    max-width: 90vw;
    overflow: hidden;
}

.modal-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem 1.5rem;
    background: #0f0a00;
    border-bottom: 1px solid #3d2800;
}

.modal-title {
    font-size: 0.85rem;
    font-weight: 800;
    letter-spacing: 0.1em;
    color: #ccb27f;
    text-transform: uppercase;
}

.modal-close {
    background: none;
    border: none;
    color: #4c3300;
    font-size: 1rem;
    cursor: pointer;
    line-height: 1;
    padding: 0.2rem 0.4rem;
    border-radius: 4px;
    transition: color 0.2s;
}
.modal-close:hover { color: #ccb27f; }

.modal-list {
    padding: 0.75rem;
    display: flex;
    flex-direction: column;
    gap: 0.4rem;
    max-height: 60vh;
    overflow-y: auto;
}

.modal-item {
    background: none;
    border: 1px solid #2d1e00;
    border-radius: 6px;
    padding: 0.75rem 1rem;
    text-align: left;
    font-size: 0.9rem;
    font-weight: 600;
    color: #b7934c;
    cursor: pointer;
    transition: background 0.15s, border-color 0.15s, color 0.15s;
}
.modal-item:hover       { background: #2d1e00; border-color: #4c3300; color: #f4efe5; }
.modal-item-active      { background: #3d2800; border-color: #996600; color: #f4efe5; }

.modal-enter-active, .modal-leave-active { transition: opacity 0.2s ease; }
.modal-enter-from, .modal-leave-to       { opacity: 0; }

/* 1920px+ */
@media (min-width: 1920px) {
    .body          { grid-template-columns: 1fr 480px; }
    .h-clock       { font-size: 2.4rem; }
    .clock-sec     { font-size: 1.4rem; }
    .h-title       { font-size: 1.5rem; }
    .h-name        { font-size: 1.2rem; }
    .h-sub         { font-size: 0.85rem; }
    .h-kantin      { font-size: 0.95rem; }
    .h-date        { font-size: 0.9rem; }
    .col-title     { font-size: 1.3rem; }
    .col-count     { font-size: 1.1rem; }
    .logo          { height: 3rem; width: 3rem; }
    .foot-text     { font-size: 1.1rem; }
    .foot-tag      { font-size: 1rem; }
    .foot-refresh  { font-size: 1rem; }
    .row-legend    { grid-template-columns: 8rem 1fr 1fr 7rem 8rem 11rem; padding: 0.65rem 2.5rem; gap: 1rem; }
    .row-legend span { font-size: 1.1rem; }
    .row-list-wrap { height: calc(5 * 8rem); }
    .overflow-toast { font-size: 1.3rem; }
}

/* Mobile landscape */
@media (max-height: 520px) and (orientation: landscape) {
    .header        { padding: 0.3rem 1rem; gap: 0.5rem; }
    .logo          { height: 1.8rem; width: 1.8rem; }
    .h-name        { font-size: 0.75rem; }
    .h-sub         { font-size: 0.6rem; }
    .h-kantin      { font-size: 0.65rem; padding: 0.1rem 0.5rem; }
    .h-title       { font-size: 0.85rem; letter-spacing: 0.1em; }
    .h-clock       { font-size: 1.3rem; }
    .clock-sec     { font-size: 0.75rem; }
    .clock-wib     { font-size: 0.6rem; }
    .h-date        { font-size: 0.6rem; }
    .col-head      { padding: 0.4rem 1rem; }
    .col-title     { font-size: 0.75rem; letter-spacing: 0.1em; }
    .col-count     { font-size: 0.7rem; }
    .row-legend    { padding: 0.3rem 1rem; gap: 0.5rem;
                     grid-template-columns: 4.5rem 1fr 1fr 4rem 5rem 7rem; }
    .row-legend span { font-size: 0.65rem; }
    .row-list-wrap { height: calc(3 * 4.5rem); }
    .footer        { padding: 0.3rem 1rem; gap: 0.5rem; }
    .foot-text     { font-size: 0.7rem; }
    .foot-tag      { font-size: 0.65rem; }
    .foot-refresh  { font-size: 0.65rem; }
    .overflow-toast { font-size: 0.75rem; margin: 0 1rem 0.5rem; padding: 0.4rem 0.75rem; }
}

@keyframes spin {
    to { transform: rotate(360deg); }
}
@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25%       { transform: translateX(-6px); }
    75%       { transform: translateX(6px); }
}
</style>
