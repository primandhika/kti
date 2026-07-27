<template>
    <div class="p-4 max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-4">
            <h1 class="text-lg font-bold text-gray-900">{{ pageTitle }}</h1>
            <p class="text-xs text-gray-500">{{ todayLabel }} &mdash; refresh {{ lastUpdated || '-' }}</p>
        </div>

        <!-- Filter Pills dengan Stats -->
        <div class="mb-4 flex gap-2 overflow-x-auto scrollbar-hide pb-2">
            <button
                v-for="tab in tabs"
                :key="tab.value"
                @click="activeTab = tab.value"
                class="flex-shrink-0 px-3 py-2 rounded-lg text-xs font-semibold transition-all border-2 flex items-center justify-between gap-3"
                :class="activeTab === tab.value ? tab.activeClass : tab.inactiveClass"
            >
                <span class="leading-tight">{{ tab.label }}</span>
                <span
                    v-if="tab.value !== 'semua'"
                    class="text-xl font-black leading-tight"
                >{{ statsByKey[tab.value] || 0 }}</span>
            </button>
        </div>

        <!-- Toast notif internal -->
        <Transition
            enter-active-class="transition ease-out duration-200"
            enter-from-class="opacity-0 -translate-y-2"
            enter-to-class="opacity-100 translate-y-0"
            leave-active-class="transition ease-in duration-150"
            leave-from-class="opacity-100 translate-y-0"
            leave-to-class="opacity-0 -translate-y-2"
        >
            <div v-if="toast.show" class="mb-3 flex items-center gap-2 px-4 py-2.5 rounded-lg text-sm font-medium"
                :class="toast.type === 'success' ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-red-100 text-red-800 border border-red-200'">
                {{ toast.message }}
            </div>
        </Transition>

        <!-- Pesanan list -->
        <div v-if="filteredPesanan.length === 0" class="text-center py-16 text-gray-400">
            <svg class="w-16 h-16 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
            <p class="text-sm">Belum ada pesanan {{ activeTab !== 'semua' ? `dengan status "${activeTabLabel}"` : 'hari ini' }}</p>
        </div>

        <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3">
            <AntrianCard
                v-for="p in filteredPesanan"
                :key="p.id"
                :pesanan="p"
                :loading="loadingId === p.id"
                :katalog-url="katalogUrl"
                :edit-url="editItemsUrl.replace('{id}', p.id)"
                @update="handleUpdate"
                @batal="handleBatal"
                @edited="handleEdited"
                @tandai-bayar="handleTandaiBayar"
            />
        </div>

        <!-- Audio indicator (optional visual feedback) -->
        <div
            v-if="audioNotif.isPlaying.value"
            :class="audioPositionClass"
        >
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"/>
            </svg>
            <span class="text-sm font-semibold">{{ audioNotifText }}</span>
        </div>

    </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, onUnmounted } from 'vue'
import AntrianCard from './AntrianCard.vue'
import { usePesananAudio } from '@/composables/usePesananAudio'

const props = defineProps({
    pesanan: { type: Array, default: () => [] },
    stats: { type: Object, default: () => ({}) },
    pageTitle: { type: String, default: 'Pesanan Self-Order' },
    apiUrl: { type: String, required: true },
    updateStatusUrl: { type: String, required: true },
    tandaiBayarUrl: { type: String, default: '/api/self-order/tandai-bayar/{id}' },
    editItemsUrl: { type: String, required: true },
    katalogUrl: { type: String, required: true },
    audioNotifText: { type: String, default: 'Pesanan baru masuk!' },
    audioPositionClass: { type: String, default: 'fixed bottom-4 right-4 bg-yellow-500 text-white px-4 py-2 rounded-lg shadow-lg flex items-center gap-2 animate-pulse' },
    notifTitle: { type: String, default: 'Pesanan Baru' },
    notifIcon: { type: String, default: '/logo-BPPU-flat.png' },
})

const pesananList = ref([...props.pesanan])
const currentStats = ref({ ...props.stats })
const activeTab = ref('menunggu')
const loadingId = ref(null)
const lastUpdated = ref(null)
const toast = ref({ show: false, message: '', type: 'success' })
const audioNotif = usePesananAudio()
let pollInterval = null
let toastTimeout = null

watch(
    () => pesananList.value.filter(p => p.status === 'menunggu').length,
    (count) => {
        audioNotif.hasPendingOrders.value = count > 0
    },
    { immediate: true }
)

const todayLabel = new Date().toLocaleDateString('id-ID', {
    weekday: 'long', day: 'numeric', month: 'long', year: 'numeric'
})

const tabs = [
    {
        value: 'semua',
        label: 'Semua',
        activeClass: 'bg-gray-700 text-white border-gray-700',
        inactiveClass: 'bg-white text-gray-600 border-gray-200 hover:border-gray-400'
    },
    {
        value: 'menunggu',
        label: 'Menunggu',
        activeClass: 'bg-yellow-500 text-white border-yellow-500',
        inactiveClass: 'bg-yellow-50 text-yellow-700 border-yellow-200 hover:border-yellow-400'
    },
    {
        value: 'diproses',
        label: 'Diproses',
        activeClass: 'bg-blue-600 text-white border-blue-600',
        inactiveClass: 'bg-blue-50 text-blue-700 border-blue-200 hover:border-blue-400'
    },
    {
        value: 'siap',
        label: 'Siap',
        activeClass: 'bg-green-600 text-white border-green-600',
        inactiveClass: 'bg-green-50 text-green-700 border-green-200 hover:border-green-400'
    },
    {
        value: 'selesai',
        label: 'Selesai',
        activeClass: 'bg-[#996600] text-white border-[#996600]',
        inactiveClass: 'bg-[#f4efe5] text-[#996600] border-[#c1a366] hover:border-[#996600]'
    },
]

const statsByKey = computed(() => currentStats.value)

const activeTabLabel = computed(() => tabs.find(t => t.value === activeTab.value)?.label || '')

const filteredPesanan = computed(() => {
    if (activeTab.value === 'semua') return pesananList.value
    return pesananList.value.filter(p => p.status === activeTab.value)
})

async function getCsrfToken() {
    const meta = document.querySelector('meta[name="csrf-token"]')
    if (meta?.content) return meta.content
    // Fallback: ambil token segar dari server
    try {
        const res = await fetch('/api/self-order/ping', { headers: { 'Accept': 'application/json' } })
        const data = await res.json()
        if (data.csrf_token && meta) meta.setAttribute('content', data.csrf_token)
        return data.csrf_token || ''
    } catch {
        return ''
    }
}

async function postWithCsrf(url, body) {
    const token = await getCsrfToken()
    const res = await fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json',
        },
        body: JSON.stringify(body),
    })
    // Jika 419 (CSRF expired), refresh token dan coba sekali lagi
    if (res.status === 419) {
        try {
            const pingRes = await fetch('/api/self-order/ping', { headers: { 'Accept': 'application/json' } })
            const pingData = await pingRes.json()
            const newToken = pingData.csrf_token || ''
            const meta = document.querySelector('meta[name="csrf-token"]')
            if (meta && newToken) meta.setAttribute('content', newToken)
            return await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': newToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify(body),
            })
        } catch {
            return res
        }
    }
    return res
}

function showToast(message, type = 'success') {
    clearTimeout(toastTimeout)
    toast.value = { show: true, message, type }
    toastTimeout = setTimeout(() => { toast.value.show = false }, 3000)
}

async function handleUpdate(id, status) {
    if (loadingId.value) return
    loadingId.value = id

    try {
        const url = props.updateStatusUrl.replace('{id}', id)
        const res = await postWithCsrf(url, { status })
        const data = await res.json()

        if (res.ok && data.success) {
            const idx = pesananList.value.findIndex(p => p.id === id)
            if (idx !== -1) {
                pesananList.value[idx] = {
                    ...pesananList.value[idx],
                    status: data.status,
                    label_status: data.label_status,
                    penjualan_id: data.penjualan?.penjualan_id || pesananList.value[idx].penjualan_id,
                    nomor_transaksi: data.penjualan?.nomor_transaksi || pesananList.value[idx].nomor_transaksi
                }
            }
            recalcStats()

            if (data.penjualan?.nomor_transaksi) {
                showToast(`Pesanan selesai! Tercatat sebagai ${data.penjualan.nomor_transaksi}`)
            } else {
                showToast(`Pesanan diperbarui: ${data.label_status}`)
            }
        } else {
            const errorMsg = data.error || data.message || 'Gagal memperbarui status'
            showToast(errorMsg, 'error')
        }
    } catch (error) {
        showToast('Terjadi kesalahan saat memperbarui status', 'error')
    } finally {
        loadingId.value = null
    }
}

function handleEdited(id, data) {
    const idx = pesananList.value.findIndex(p => p.id === id)
    if (idx !== -1) {
        pesananList.value[idx] = {
            ...pesananList.value[idx],
            items:  data.items,
            total:  data.total,
            catatan: data.catatan,
        }
    }
    showToast('Pesanan berhasil diperbarui')
}

async function handleTandaiBayar(id) {
    if (loadingId.value) return
    loadingId.value = id

    try {
        const url = props.tandaiBayarUrl.replace('{id}', id)
        const res = await postWithCsrf(url, {})
        const data = await res.json()

        if (res.ok && data.success) {
            const idx = pesananList.value.findIndex(p => p.id === id)
            if (idx !== -1) {
                pesananList.value[idx] = { ...pesananList.value[idx], bukti_bayar: 'MANUAL' }
            }
            showToast('Pembayaran berhasil dikonfirmasi')
        } else {
            showToast(data.error || 'Gagal mengkonfirmasi pembayaran', 'error')
        }
    } catch {
        showToast('Terjadi kesalahan', 'error')
    } finally {
        loadingId.value = null
    }
}

async function handleBatal(id, alasan) {
    if (loadingId.value) return
    loadingId.value = id

    try {
        const payload = {
            status: 'dibatalkan',
            alasan_batal: alasan.teks,
            kode_alasan_batal: alasan.kode
        }

        const url = props.updateStatusUrl.replace('{id}', id)
        const res = await postWithCsrf(url, payload)
        const data = await res.json()

        if (res.ok && data.success) {
            const idx = pesananList.value.findIndex(p => p.id === id)
            if (idx !== -1) {
                pesananList.value[idx] = {
                    ...pesananList.value[idx],
                    status: data.status,
                    label_status: data.label_status,
                    alasan_batal: alasan.teks
                }
            }
            recalcStats()
            showToast(`Pesanan dibatalkan: ${alasan.teks}`)
        } else {
            const errorMsg = data.error || data.message || 'Gagal membatalkan pesanan'
            showToast(errorMsg, 'error')
        }
    } catch (error) {
        showToast('Terjadi kesalahan saat membatalkan pesanan: ' + error.message, 'error')
    } finally {
        loadingId.value = null
    }
}

function recalcStats() {
    const stats = { menunggu: 0, diproses: 0, siap: 0, selesai: 0, dibatalkan: 0 }
    pesananList.value.forEach(p => {
        if (stats[p.status] !== undefined) stats[p.status]++
    })
    currentStats.value = stats
}

async function pollPesanan() {
    try {
        const res = await fetch(props.apiUrl, {
            headers: { 'Accept': 'application/json' }
        })
        if (!res.ok) return
        const data = await res.json()

        const prevCount = pesananList.value.filter(p => p.status === 'menunggu').length
        const newCount = (data.stats?.menunggu ?? 0)

        pesananList.value = data.pesanan
        currentStats.value = data.stats

        if (newCount > prevCount) {
            const diff = newCount - prevCount
            showToast(`${diff} pesanan baru masuk!`, 'success')
            triggerBrowserNotif(`${diff} pesanan baru!`)
        }

        const now = new Date()
        lastUpdated.value = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })
    } catch {
        // silent
    }
}

function triggerBrowserNotif(message) {
    if (!('Notification' in window)) return
    if (Notification.permission === 'granted') {
        new Notification(props.notifTitle, { body: message, icon: props.notifIcon })
    }
}

onMounted(() => {
    if ('Notification' in window && Notification.permission === 'default') {
        Notification.requestPermission()
    }
    pollInterval = setInterval(pollPesanan, 8000)
})

onUnmounted(() => {
    clearInterval(pollInterval)
    clearTimeout(toastTimeout)
})
</script>

<style scoped>
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
</style>
