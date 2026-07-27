<template>
    <div class="root">
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
                <span class="h-title">LIVE TRANSACTION</span>
            </div>

            <div class="h-right">
                <span class="h-clock">
                    {{ clockHM }}<span class="clock-sec">:{{ clockSec }}</span>
                    <span class="clock-wib">WIB</span>
                </span>
                <span class="h-date">{{ currentDate }}</span>
            </div>
        </header>

        <div class="body">
            <section class="col col-left">
                <div class="col-head">
                    <span class="col-title">TRANSAKSI HARI INI</span>
                    <span class="col-count">{{ transaksiList.length }} transaksi</span>
                </div>

                <div class="row-legend trx-legend">
                    <span>No. Transaksi</span>
                    <span>Kantin</span>
                    <span>Item</span>
                    <span>Waktu</span>
                    <span>Total</span>
                </div>

                <div v-if="!transaksiList.length" class="empty">Belum ada transaksi hari ini</div>

                <div class="row-list-wrap">
                    <div class="row-list">
                        <div v-for="trx in transaksiList" :key="trx.id" class="row trx-row">
                            <span>{{ trx.nomor_transaksi }}</span>
                            <span>{{ trx.kantin }}</span>
                            <span :title="trx.ringkas_item">{{ trx.ringkas_item || '-' }} ({{ trx.jumlah_item }})</span>
                            <span>{{ shortTime(trx.waktu) }}</span>
                            <span>Rp {{ money(trx.total) }}</span>
                        </div>
                    </div>
                </div>
            </section>

            <section class="col col-right">
                <div class="col-head">
                    <span class="col-title">ITEM TERJUAL</span>
                    <span class="col-count">{{ soldItemsList.length }} item</span>
                </div>

                <div class="row-legend sold-legend">
                    <span>Nama Item</span>
                    <span>Kantin</span>
                    <span>Qty</span>
                </div>

                <div v-if="!soldItemsList.length" class="empty">Belum ada item terjual hari ini</div>

                <div class="row-list-wrap">
                    <div class="row-list">
                        <div v-for="(item, idx) in soldItemsList" :key="`${item.nama_barang}-${item.kantin}-${idx}`" class="row sold-row">
                            <span>{{ item.nama_barang }}</span>
                            <span>{{ item.kantin }}</span>
                            <span>{{ item.qty }}</span>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <footer class="footer">
            <span class="foot-tag">INFO</span>
            <span class="foot-text">Total transaksi: {{ totalTransaksi }} • Total omzet: Rp {{ money(totalOmzet) }}</span>
            <span class="foot-refresh">Diperbarui: {{ lastUpdated || '...' }}</span>
        </footer>

        <Transition name="modal">
            <div v-if="showKantinModal" class="modal-backdrop" @click.self="showKantinModal = false">
                <div class="modal-box">
                    <div class="modal-head">
                        <span class="modal-title">Pilih Kantin</span>
                        <button class="modal-close" @click="showKantinModal = false">&#10005;</button>
                    </div>
                    <div class="modal-list">
                        <button class="modal-item" :class="{ 'modal-item-active': kantin === null }" @click="pilihKantin(null)">Semua Kantin</button>
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
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'

const props = defineProps({
    transaksi: { type: Array, default: () => [] },
    soldItems: { type: Array, default: () => [] },
    totalTransaksi: { type: Number, default: 0 },
    totalOmzet: { type: Number, default: 0 },
    namaKantin: { type: String, default: 'Semua Kantin' },
    kantin: { type: Number, default: null },
    kantinList: { type: Array, default: () => [] },
})

const transaksiList = ref([...props.transaksi])
const soldItemsList = ref([...props.soldItems])
const totalTransaksi = ref(props.totalTransaksi)
const totalOmzet = ref(props.totalOmzet)
const previousTotalTransaksi = ref(props.totalTransaksi)
const lastUpdated = ref(null)
const clockHM = ref('00:00')
const clockSec = ref('00')
const currentDate = ref('')
const showKantinModal = ref(false)
const hasPolledOnce = ref(false)

const transactionNotifSound = typeof window !== 'undefined' ? new Audio('/sfx/pesanan_diterima.mp3') : null
if (transactionNotifSound) {
    transactionNotifSound.preload = 'auto'
    transactionNotifSound.volume = 0.8
}

let pollInterval = null
let clockInterval = null

function pad2(n) { return String(n).padStart(2, '0') }
function shortTime(time) {
    if (!time) return '--:--'
    const parts = time.split(':')
    return parts.length >= 2 ? `${parts[0]}:${parts[1]}` : time
}
function money(v) {
    return Number(v || 0).toLocaleString('id-ID')
}

function updateClock() {
    const now = new Date()
    clockHM.value = `${pad2(now.getHours())}:${pad2(now.getMinutes())}`
    clockSec.value = pad2(now.getSeconds())
    currentDate.value = now.toLocaleDateString('id-ID', {
        weekday: 'long', day: 'numeric', month: 'long', year: 'numeric',
    })
}

function pilihKantin(id) {
    showKantinModal.value = false
    window.location.href = id ? `/live-transaction/${id}` : '/live-transaction'
}

async function poll() {
    try {
        const url = props.kantin ? `/api/live-transaction/${props.kantin}` : '/api/live-transaction'
        const res = await fetch(url, { headers: { Accept: 'application/json' } })
        if (res.status === 401 || res.status === 419) {
            clearInterval(pollInterval)
            clearInterval(clockInterval)
            window.location.href = '/pengelola/login'
            return
        }
        if (!res.ok) return
        const data = await res.json()
        const nextTotalTransaksi = data.totalTransaksi || 0
        const hasNewTransaction = hasPolledOnce.value && nextTotalTransaksi > previousTotalTransaksi.value

        transaksiList.value = data.transaksi || []
        soldItemsList.value = data.soldItems || []
        totalTransaksi.value = nextTotalTransaksi
        totalOmzet.value = data.totalOmzet || 0
        previousTotalTransaksi.value = nextTotalTransaksi
        hasPolledOnce.value = true

        if (hasNewTransaction && transactionNotifSound) {
            transactionNotifSound.currentTime = 0
            transactionNotifSound.play().catch(() => {})
        }

        lastUpdated.value = new Date().toLocaleTimeString('id-ID', {
            hour: '2-digit', minute: '2-digit', second: '2-digit',
        })
    } catch {}
}

onMounted(() => {
    updateClock()
    clockInterval = setInterval(updateClock, 1000)
    pollInterval = setInterval(poll, 6000)
})

onUnmounted(() => {
    clearInterval(clockInterval)
    clearInterval(pollInterval)
})
</script>

<style scoped>
* { box-sizing: border-box; }
.root { min-height: 100vh; height: 100vh; display: flex; flex-direction: column; background: #1e1400; color: #f4efe5; font-family: 'Inter', 'Segoe UI', system-ui, sans-serif; overflow: hidden; }
.header { display: grid; grid-template-columns: 1fr auto 1fr; align-items: center; padding: 0.5rem 2rem; background: #0f0a00; border-bottom: 2px solid #3d2800; gap: 1rem; flex-shrink: 0; }
.h-left { display: flex; align-items: center; gap: 0.7rem; }
.h-center { display: flex; justify-content: center; }
.h-right { display: flex; flex-direction: column; align-items: flex-end; }
.logo { height: 2.4rem; width: 2.4rem; object-fit: contain; filter: brightness(0) invert(1); flex-shrink: 0; }
.h-brand { display: flex; flex-direction: column; line-height: 1.2; }
.h-name { font-size: 1rem; font-weight: 700; color: #f4efe5; }
.h-sub { font-size: 0.7rem; color: #ccb27f; }
.h-kantin { font-size: 0.8rem; font-weight: 600; color: #0f0a00; background: #ccb27f; padding: 0.15rem 0.7rem; border-radius: 3px; white-space: nowrap; border: none; cursor: pointer; display: flex; align-items: center; gap: 0.3rem; }
.h-title { font-size: 1.3rem; letter-spacing: 0.08em; color: #ffcf70; font-weight: 800; }
.h-clock { font-variant-numeric: tabular-nums; font-size: 1.1rem; font-weight: 700; }
.clock-sec { color: #ffcf70; }
.clock-wib { margin-left: 0.3rem; font-size: 0.72rem; color: #ccb27f; font-weight: 700; }
.h-date { font-size: 0.78rem; color: #ccb27f; }
.body { flex: 1; min-height: 0; display: grid; grid-template-columns: 2fr 1.3fr; gap: 0.9rem; padding: 0.8rem; }
.col { min-height: 0; background: #2a1d03; border: 1px solid #4a3407; border-radius: 10px; display: flex; flex-direction: column; overflow: hidden; }
.col-head { display: flex; align-items: center; justify-content: space-between; padding: 0.7rem 0.9rem; background: #1a1202; border-bottom: 1px solid #4a3407; }
.col-title { font-size: 0.95rem; font-weight: 800; color: #ffcf70; letter-spacing: 0.08em; }
.col-count { font-size: 0.75rem; background: #3d2800; color: #ffd88f; padding: 0.2rem 0.5rem; border-radius: 999px; }
.row-legend { display: grid; gap: 0.5rem; padding: 0.45rem 0.8rem; font-size: 0.68rem; color: #ccb27f; border-bottom: 1px solid #3d2800; text-transform: uppercase; letter-spacing: 0.06em; }
.trx-legend { grid-template-columns: 1.5fr 1.1fr 1.2fr 0.7fr 0.8fr; }
.sold-legend { grid-template-columns: 1.8fr 1fr 0.5fr; }
.empty { padding: 1rem; text-align: center; color: #ccb27f; font-size: 0.85rem; }
.row-list-wrap { flex: 1; min-height: 0; overflow-y: auto; overflow-x: hidden; scrollbar-width: thin; scrollbar-color: #4a3407 transparent; }
.row-list-wrap::-webkit-scrollbar { width: 4px; }
.row-list-wrap::-webkit-scrollbar-track { background: transparent; }
.row-list-wrap::-webkit-scrollbar-thumb { background: #4a3407; border-radius: 999px; }
.row-list-wrap::-webkit-scrollbar-thumb:hover { background: #7a5100; }
.row-list { display: flex; flex-direction: column; }
.row { display: grid; gap: 0.5rem; align-items: center; padding: 0.55rem 0.8rem; border-bottom: 1px solid rgba(255, 216, 143, 0.08); font-size: 0.8rem; }
.trx-row { grid-template-columns: 1.5fr 1.1fr 1.2fr 0.7fr 0.8fr; }
.sold-row { grid-template-columns: 1.8fr 1fr 0.5fr; }
.footer { height: 2.3rem; display: flex; align-items: center; gap: 0.55rem; padding: 0 0.8rem; background: #0f0a00; border-top: 1px solid #3d2800; font-size: 0.74rem; color: #ccb27f; }
.foot-tag { font-weight: 800; color: #ffcf70; }
.foot-text { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.foot-refresh { margin-left: auto; font-variant-numeric: tabular-nums; color: #ffcf70; }
.modal-backdrop { position: fixed; inset: 0; background: rgba(0, 0, 0, 0.55); display: flex; align-items: center; justify-content: center; z-index: 40; }
.modal-box { width: min(30rem, calc(100vw - 2rem)); background: #1a1202; border: 1px solid #4a3407; border-radius: 12px; overflow: hidden; }
.modal-head { display: flex; align-items: center; justify-content: space-between; padding: 0.75rem 0.9rem; border-bottom: 1px solid #3d2800; }
.modal-title { font-size: 0.85rem; font-weight: 700; color: #ffcf70; }
.modal-close { border: 0; background: transparent; color: #ccb27f; font-size: 1rem; cursor: pointer; }
.modal-list { display: grid; gap: 0.45rem; padding: 0.8rem; }
.modal-item { border: 1px solid #4a3407; background: #241903; color: #f4efe5; border-radius: 8px; padding: 0.55rem 0.65rem; text-align: left; cursor: pointer; }
.modal-item-active { border-color: #ffcf70; color: #ffcf70; }
@media (max-width: 1024px) {
    .body { grid-template-columns: 1fr; grid-template-rows: 1fr 1fr; padding: 0.5rem; gap: 0.5rem; }
    .col { min-height: 0; }
    .header { padding: 0.4rem 1rem; gap: 0.5rem; }
    .h-name { font-size: 0.85rem; }
    .h-title { font-size: 1rem; }
    .h-clock { font-size: 0.95rem; }
    .h-date { font-size: 0.68rem; }
    .footer { flex-wrap: wrap; height: auto; padding: 0.4rem 0.8rem; gap: 0.3rem; }
    .foot-refresh { width: 100%; margin-left: 0; }
}
@media (max-width: 640px) {
    .header { grid-template-columns: 1fr 1fr; grid-template-rows: auto auto; }
    .h-center { grid-column: 1 / -1; order: -1; padding: 0.25rem 0; border-bottom: 1px solid #3d2800; }
    .h-right { align-items: flex-end; }
    .logo { height: 1.8rem; width: 1.8rem; }
    .h-sub { display: none; }
    .h-kantin { font-size: 0.72rem; padding: 0.1rem 0.5rem; }
    .trx-legend { grid-template-columns: 1.4fr 0.9fr 1fr 0.6fr 0.8fr; font-size: 0.6rem; }
    .trx-row { grid-template-columns: 1.4fr 0.9fr 1fr 0.6fr 0.8fr; font-size: 0.72rem; }
    .sold-legend { grid-template-columns: 2fr 1fr 0.5fr; }
    .sold-row { grid-template-columns: 2fr 1fr 0.5fr; font-size: 0.72rem; }
    .row { padding: 0.45rem 0.6rem; }
    .col-title { font-size: 0.8rem; }
}
</style>
