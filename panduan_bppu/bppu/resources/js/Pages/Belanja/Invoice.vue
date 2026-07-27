<template>
    <div class="min-h-screen bg-[#f4efe5] print:bg-white">
        <!-- Notif banner -->
        <Transition
            enter-active-class="transition ease-out duration-300"
            enter-from-class="opacity-0 -translate-y-4"
            enter-to-class="opacity-100 translate-y-0"
            leave-active-class="transition ease-in duration-200"
            leave-from-class="opacity-100 translate-y-0"
            leave-to-class="opacity-0 -translate-y-4"
        >
            <div
                v-if="showNotifBanner"
                class="print:hidden fixed top-0 left-0 right-0 z-50 bg-[#996600] text-white text-sm font-semibold text-center py-3 px-4 shadow-lg"
            >
                {{ notifMessage }}
            </div>
        </Transition>

        <!-- Toolbar -->
        <div class="print:hidden bg-white border-b border-gray-200 px-4 py-3 flex items-center justify-between">
            <a href="/belanja/self-order" class="flex items-center gap-2 text-gray-600 hover:text-gray-900 text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Kembali ke toko
            </a>
            <button
                @click="handlePrint"
                class="flex items-center gap-2 bg-[#996600] text-white px-3 py-1.5 rounded-lg text-sm font-medium hover:bg-[#7a5100] transition-colors"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Cetak
            </button>
        </div>

        <div class="max-w-sm mx-auto py-4 px-4 print:py-0 print:px-0 print:max-w-none space-y-3">

            <!-- Card utama: antrian + status + detail ringkas -->
            <div class="bg-white rounded-2xl shadow-lg print:shadow-none overflow-hidden">

                <!-- Header compact -->
                <div class="bg-[#996600] text-white px-4 py-3 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <img src="/logo-BPPU-flat.png" alt="Logo BPPU" class="w-7 h-7 object-contain brightness-0 invert" />
                        <div>
                            <p class="text-xs font-bold leading-none">Toko BPPU</p>
                            <p class="text-[10px] text-yellow-200 leading-none mt-0.5">IKIP Siliwangi</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] text-yellow-200">{{ pesanan.tanggal_antrian }}</p>
                        <p class="text-[10px] text-yellow-200">{{ pesanan.created_at }}</p>
                    </div>
                </div>

                <!-- Nomor antrian BESAR -->
                <div class="px-4 py-4 text-center border-b border-dashed border-gray-200">
                    <p class="text-[10px] text-gray-400 uppercase tracking-widest mb-1">Nomor Antrian</p>
                    <div class="text-8xl font-black text-[#996600] leading-none tracking-tight">
                        {{ pesanan.nomor_antrian }}
                    </div>

                    <!-- Status pill -->
                    <div class="mt-3 flex items-center justify-center gap-2">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-semibold" :class="statusClass">
                            <span
                                v-if="currentStatus === 'menunggu' || currentStatus === 'diproses'"
                                class="w-2 h-2 rounded-full animate-pulse"
                                :class="currentStatus === 'diproses' ? 'bg-blue-500' : 'bg-yellow-500'"
                            ></span>
                            {{ currentLabelStatus }}
                        </span>
                    </div>

                    <!-- Info antrian -->
                    <div v-if="currentStatus === 'menunggu' || currentStatus === 'diproses'" class="mt-2">
                        <p class="text-xs text-gray-500">
                            Antrian sebelum kamu:
                            <span class="font-bold text-gray-700">{{ currentPosisi > 1 ? currentPosisi - 1 : 0 }}</span>
                        </p>
                    </div>
                    <div v-if="currentStatus === 'siap'" class="mt-2">
                        <p class="text-sm font-semibold text-green-700">Pesananmu sudah siap, silakan ambil!</p>
                    </div>
                    <div v-if="currentStatus === 'dibatalkan' && alasanBatal" class="mt-2 bg-red-50 border border-red-200 rounded-lg px-3 py-2">
                        <p class="text-xs text-red-700"><span class="font-semibold">Alasan:</span> {{ alasanBatal }}</p>
                    </div>
                </div>

                <!-- Detail ringkas: pemesan + items + total -->
                <div class="px-4 py-3 space-y-2.5">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500 text-xs">Pemesan</span>
                        <span class="font-semibold text-gray-800">{{ pesanan.nama_pemesan }}</span>
                    </div>

                    <!-- Items compact -->
                    <div class="border-t border-dashed border-gray-200 pt-2.5 space-y-1.5">
                        <div
                            v-for="(item, i) in pesanan.items"
                            :key="i"
                            class="flex justify-between items-baseline text-sm"
                        >
                            <div class="flex-1 min-w-0 pr-2">
                                <span class="font-medium text-gray-800 text-xs">{{ item.name }}</span>
                                <span v-if="item.varian" class="text-gray-400 text-[10px] ml-1">{{ item.varian.nama_varian }}</span>
                                <span class="text-gray-400 text-[10px] ml-1">x{{ item.quantity }}</span>
                            </div>
                            <span class="text-xs font-semibold text-gray-700 flex-shrink-0">{{ formatRupiah(item.price * item.quantity) }}</span>
                        </div>
                    </div>

                    <!-- Total -->
                    <div class="border-t border-gray-200 pt-2 flex justify-between items-center">
                        <span class="text-sm font-bold text-gray-800">Total</span>
                        <span class="text-base font-black text-[#996600]">{{ formatRupiah(pesanan.total) }}</span>
                    </div>

                    <div v-if="pesanan.catatan" class="text-xs text-gray-500 italic">
                        Catatan: {{ pesanan.catatan }}
                    </div>

                    <p class="text-[10px] text-gray-400 text-center pt-1">Pembayaran dilakukan di kasir</p>
                </div>
            </div>

            <!-- Info refresh live -->
            <div class="print:hidden text-center pb-4">
                <p class="text-xs text-gray-500">
                    Status diperbarui otomatis setiap
                    <span class="font-medium text-[#996600]">5 detik</span>
                </p>
                <p v-if="lastUpdated" class="text-xs text-gray-400 mt-0.5">Terakhir: {{ lastUpdated }}</p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'

const props = defineProps({
    pesanan: { type: Object, required: true },
    posisi_antrian: { type: Number, default: 0 },
})

const currentStatus = ref(props.pesanan.status)
const currentLabelStatus = ref(props.pesanan.label_status)
const currentPosisi = ref(props.posisi_antrian)
const alasanBatal = ref(props.pesanan.alasan_batal || null)
const lastUpdated = ref(null)
let pollInterval = null

const statusClass = computed(() => {
    const map = {
        menunggu:   'bg-yellow-100 text-yellow-800',
        diproses:   'bg-blue-100 text-blue-800',
        siap:       'bg-green-100 text-green-800',
        selesai:    'bg-gray-100 text-gray-600',
        dibatalkan: 'bg-red-100 text-red-700',
    }
    return map[currentStatus.value] || 'bg-gray-100 text-gray-600'
})

const formatRupiah = (v) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(v)
const handlePrint = () => window.print()

// Notif
const notifMessage = ref('')
const showNotifBanner = ref(false)
let notifTimeout = null

const notifSound = typeof window !== 'undefined' ? new Audio('/sfx/notif_user.mp3') : null
if (notifSound) notifSound.volume = 0.7

function triggerNotif(message) {
    if (notifSound) notifSound.play().catch(() => {})
    if ('Notification' in window && Notification.permission === 'granted') {
        new Notification('Update Pesanan Toko', { body: message, icon: '/logo-BPPU-flat.png' })
    }
    clearTimeout(notifTimeout)
    notifMessage.value = message
    showNotifBanner.value = true
    notifTimeout = setTimeout(() => { showNotifBanner.value = false }, 6000)
}

// Auto-cancel
const AUTO_CANCEL = 5 * 60 * 1000
const WARN_BEFORE = 30 * 1000
let autoCancelTimer = null, warningTimer = null

async function autoCancelPesanan() {
    try {
        const res = await fetch(`/api/belanja/cancel/${props.pesanan.id}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '' },
        })
        if (res.ok) {
            const data = await res.json()
            if (data.success) {
                currentStatus.value = data.status
                currentLabelStatus.value = data.label_status
                triggerNotif('Pesanan dibatalkan otomatis karena tidak ada respons.')
            }
        }
    } catch {}
}

function setupAutoCancelTimer() {
    if (currentStatus.value !== 'menunggu') return
    warningTimer = setTimeout(() => {
        if (currentStatus.value === 'menunggu')
            triggerNotif('Pesananmu akan dibatalkan otomatis dalam 30 detik jika tidak ada respons.')
    }, AUTO_CANCEL - WARN_BEFORE)
    autoCancelTimer = setTimeout(() => {
        if (currentStatus.value === 'menunggu') autoCancelPesanan()
    }, AUTO_CANCEL)
}

async function pollStatus() {
    if (currentStatus.value === 'selesai' || currentStatus.value === 'dibatalkan') {
        clearInterval(pollInterval)
        return
    }
    try {
        const res = await fetch(`/api/belanja/status/${props.pesanan.id}`)
        if (!res.ok) return
        const data = await res.json()
        const prev = currentStatus.value
        currentStatus.value = data.status
        currentLabelStatus.value = data.label_status
        currentPosisi.value = data.posisi_antrian
        if (data.alasan_batal) alasanBatal.value = data.alasan_batal

        const now = new Date()
        lastUpdated.value = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' })

        if (prev !== data.status) {
            if (prev === 'menunggu' && data.status !== 'menunggu') {
                clearTimeout(warningTimer)
                clearTimeout(autoCancelTimer)
            }
            const msgs = {
                diproses:   'Pesananmu sedang diproses!',
                siap:       'Pesananmu sudah siap! Silakan ambil di kasir.',
                selesai:    'Pesananmu selesai. Terima kasih!',
                dibatalkan: data.alasan_batal ? `Maaf, pesananmu dibatalkan. Alasan: ${data.alasan_batal}` : 'Maaf, pesananmu dibatalkan.',
            }
            if (msgs[data.status]) triggerNotif(msgs[data.status])
        }

        if (data.status === 'selesai' || data.status === 'dibatalkan') clearInterval(pollInterval)
    } catch {}
}

onMounted(() => {
    if ('Notification' in window && Notification.permission === 'default') Notification.requestPermission()
    setupAutoCancelTimer()
    pollInterval = setInterval(pollStatus, 5000)
})

onUnmounted(() => {
    clearInterval(pollInterval)
    clearTimeout(warningTimer)
    clearTimeout(autoCancelTimer)
    clearTimeout(notifTimeout)
})
</script>

<style>
@media print {
    @page { size: 58mm auto; margin: 0; }
    body { margin: 0; padding: 0; font-size: 11px; }
    .print\:hidden { display: none !important; }
}
</style>
