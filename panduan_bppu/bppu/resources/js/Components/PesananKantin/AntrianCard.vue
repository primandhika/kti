<template>
    <div
        class="bg-white rounded-xl shadow-sm border-l-4 transition-all duration-300 relative"
        :class="borderClass"
    >
        <!-- Loading Overlay -->
        <div
            v-if="loading"
            class="absolute inset-0 bg-white/80 backdrop-blur-sm rounded-xl flex items-center justify-center z-10"
        >
            <div class="text-center">
                <svg class="animate-spin h-8 w-8 mx-auto text-[#996600]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <p class="text-xs text-gray-600 mt-2">Memproses...</p>
            </div>
        </div>

        <div class="p-4">
            <!-- Header: nomor + nama + meja + waktu -->
            <div class="flex items-center gap-3 mb-3">
                <div
                    class="text-3xl font-black leading-none w-16 h-16 rounded-xl flex-shrink-0 flex flex-col items-center justify-center"
                    :class="nomorBgClass"
                >
                    <span>{{ pesanan.nomor_antrian }}</span>
                    <span
                        v-if="pesanan.status === 'menunggu' || pesanan.status === 'diproses'"
                        class="w-1.5 h-1.5 rounded-full animate-pulse mt-1"
                        :class="pesanan.status === 'diproses' ? 'bg-blue-400' : 'bg-yellow-500'"
                    ></span>
                </div>
                <div class="min-w-0">
                    <p class="font-semibold text-gray-800 text-sm truncate">{{ pesanan.nama_pemesan }}</p>
                    <p v-if="pesanan.nama_meja" class="text-xs font-medium text-[#996600] mt-0.5 truncate">{{ pesanan.nama_meja }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">{{ pesanan.waktu }}</p>
                </div>
            </div>

            <!-- Item list -->
            <div class="space-y-1 mb-3">
                <div
                    v-for="(item, i) in pesanan.items"
                    :key="i"
                    class="flex justify-between text-xs text-gray-600"
                >
                    <span>
                        {{ item.quantity }}x {{ item.name }}
                        <span v-if="item.varian" class="text-gray-400">({{ item.varian.nama_varian }})</span>
                        <span v-if="item.work_unit_name" class="block text-[10px] text-[#996600] font-medium">{{ item.work_unit_name }}</span>
                    </span>
                    <span class="font-medium">{{ formatRupiah(item.price * item.quantity) }}</span>
                </div>
            </div>

            <!-- Tipe pengiriman -->
            <div class="flex items-center gap-2 mb-3 flex-wrap">
                <span
                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold"
                    :class="pesanan.tipe_pengiriman === 'pickup'
                        ? 'bg-[#f4efe5] text-[#6b4700] border border-[#ccb27f]'
                        : 'bg-blue-50 text-blue-700 border border-blue-200'"
                >
                    <svg v-if="pesanan.tipe_pengiriman === 'pickup'" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    <svg v-else class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    {{ pesanan.tipe_pengiriman === 'pickup' ? 'Pickup' : 'Antar ke meja' }}
                </span>
                <span v-if="pesanan.tipe_pengiriman === 'pickup' && pesanan.estimasi_ambil" class="text-xs text-gray-500 flex items-center gap-1">
                    <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ pesanan.estimasi_ambil }}
                </span>
            </div>

            <!-- Catatan -->
            <div v-if="pesanan.catatan" class="bg-yellow-50 border border-yellow-200 rounded-lg px-3 py-2 mb-3">
                <p class="text-xs text-yellow-800"><span class="font-semibold">Catatan:</span> {{ pesanan.catatan }}</p>
            </div>

            <!-- Total + status bayar -->
            <div class="flex justify-between items-center mb-3 pt-2 border-t border-gray-100">
                <span class="text-xs text-gray-500">Total</span>
                <span class="font-bold text-sm text-[#996600]">{{ formatRupiah(pesanan.total) }}</span>
            </div>

            <!-- Metode bayar -->
            <div v-if="pesanan.metode_bayar" class="mb-2">
                <span
                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-semibold"
                    :class="pesanan.metode_bayar === 'qris' ? 'bg-blue-50 text-blue-700' : 'bg-gray-100 text-gray-600'"
                >
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path v-if="pesanan.metode_bayar === 'tunai'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                    </svg>
                    {{ pesanan.metode_bayar === 'qris' ? 'QRIS' : 'Tunai' }}
                </span>
            </div>

            <!-- Status pembayaran -->
            <div class="mb-3">
                <!-- Sudah bayar QRIS (ada bukti upload) -->
                <a
                    v-if="pesanan.bukti_bayar && pesanan.bukti_bayar !== 'MANUAL'"
                    :href="pesanan.bukti_bayar"
                    target="_blank"
                    class="flex items-center gap-1.5 px-2.5 py-1.5 bg-green-50 border border-green-200 rounded-lg w-full hover:bg-green-100 transition-colors"
                >
                    <svg class="w-3.5 h-3.5 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-xs font-semibold text-green-700">Sudah bayar QRIS</span>
                    <span class="ml-auto text-[10px] text-green-600 underline">Lihat bukti</span>
                </a>
                <!-- Ditandai lunas manual oleh kasir -->
                <div v-else-if="pesanan.bukti_bayar === 'MANUAL'" class="flex items-center gap-1.5 px-2.5 py-1.5 bg-green-50 border border-green-200 rounded-lg">
                    <svg class="w-3.5 h-3.5 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-xs font-semibold text-green-700">Lunas (dikonfirmasi kasir)</span>
                </div>
                <!-- Belum bayar: tampilkan tombol tandai lunas jika pesanan aktif -->
                <div v-else class="flex items-center gap-2">
                    <div class="flex items-center gap-1.5 px-2.5 py-1.5 bg-gray-50 border border-gray-200 rounded-lg flex-1">
                        <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-xs text-gray-500">Belum bayar</span>
                    </div>
                    <button
                        v-if="pesanan.status !== 'selesai' && pesanan.status !== 'dibatalkan'"
                        @click="$emit('tandai-bayar', pesanan.id)"
                        :disabled="loading"
                        class="flex-shrink-0 px-2.5 py-1.5 text-[10px] font-semibold bg-green-600 text-white rounded-lg hover:bg-green-700 disabled:opacity-50 transition-colors"
                    >
                        Tandai Lunas
                    </button>
                </div>
            </div>

            <!-- Action buttons -->
            <div class="flex gap-2" v-if="pesanan.status !== 'selesai' && pesanan.status !== 'dibatalkan'">
                <!-- Tombol edit item (menunggu & diproses) -->
                <button
                    v-if="pesanan.status === 'menunggu' || pesanan.status === 'diproses'"
                    @click="showEditModal = true"
                    :disabled="loading"
                    class="py-2 px-3 text-xs font-semibold bg-white border border-gray-300 text-gray-600 rounded-lg hover:bg-gray-50 disabled:opacity-50 transition-colors flex items-center gap-1"
                >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit
                </button>
                <!-- Tombol aksi utama -->
                <button
                    v-if="pesanan.status === 'menunggu'"
                    @click="$emit('update', pesanan.id, 'diproses')"
                    :disabled="loading"
                    class="flex-1 py-2 text-xs font-semibold bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 transition-colors"
                >
                    Proses
                </button>
                <button
                    v-else-if="pesanan.status === 'diproses'"
                    @click="$emit('update', pesanan.id, 'siap')"
                    :disabled="loading"
                    class="flex-1 py-2 text-xs font-semibold bg-green-600 text-white rounded-lg hover:bg-green-700 disabled:opacity-50 transition-colors"
                >
                    Siap Diambil
                </button>
                <template v-else-if="pesanan.status === 'siap'">
                    <!-- Cetak struk hanya kalau belum bayar -->
                    <button
                        v-if="!pesanan.bukti_bayar"
                        @click="showStrukModal = true"
                        :disabled="loading"
                        class="py-2 px-3 text-xs font-semibold bg-white border border-[#996600] text-[#996600] rounded-lg hover:bg-[#f4efe5] disabled:opacity-50 transition-colors flex items-center gap-1"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        Struk
                    </button>
                    <button
                        @click="$emit('update', pesanan.id, 'selesai')"
                        :disabled="loading"
                        class="flex-1 py-2 text-xs font-semibold bg-[#996600] text-white rounded-lg hover:bg-[#7a5100] disabled:opacity-50 transition-colors"
                    >
                        Selesai
                    </button>
                </template>

                <!-- Tombol batal - hanya tampil jika belum tercatat sebagai penjualan -->
                <button
                    v-if="!pesanan.penjualan_id"
                    @click="showBatalModal = true"
                    :disabled="loading"
                    class="py-2 px-3 text-xs font-semibold bg-white border border-red-300 text-red-600 rounded-lg hover:bg-red-50 disabled:opacity-50 transition-colors"
                >
                    Batal
                </button>
            </div>

            <!-- Badge selesai/batal -->
            <div v-else>
                <div v-if="pesanan.status === 'selesai'" class="flex items-center justify-between gap-2">
                    <div class="text-xs text-gray-400">
                        Pesanan selesai
                        <span v-if="pesanan.nomor_transaksi" class="block mt-0.5 font-semibold text-[#996600]">
                            {{ pesanan.nomor_transaksi }}
                        </span>
                    </div>
                    <button
                        @click="showStrukModal = true"
                        class="flex-shrink-0 py-1.5 px-2.5 text-xs font-semibold bg-white border border-[#996600] text-[#996600] rounded-lg hover:bg-[#f4efe5] transition-colors flex items-center gap-1"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        Struk
                    </button>
                </div>
                <span v-else-if="pesanan.status === 'dibatalkan'" class="text-xs text-red-400">
                    Pesanan dibatalkan
                    <span v-if="pesanan.alasan_batal" class="block mt-0.5">({{ pesanan.alasan_batal }})</span>
                </span>
            </div>
        </div>

        <!-- Modal Alasan Batal -->
        <AlasanBatalModal
            :show="showBatalModal"
            @close="showBatalModal = false"
            @confirm="handleBatal"
        />

        <!-- Modal Cetak Struk -->
        <StruktSelfOrderModal
            :show="showStrukModal"
            :pesanan="pesanan"
            @close="showStrukModal = false"
        />

        <!-- Modal Edit Pesanan -->
        <EditPesananModal
            :show="showEditModal"
            :pesanan="pesanan"
            :katalog-url="katalogUrl"
            :edit-url="editUrl"
            @close="showEditModal = false"
            @saved="handleEditSaved"
        />
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import AlasanBatalModal from './AlasanBatalModal.vue'
import StruktSelfOrderModal from './StruktSelfOrderModal.vue'
import EditPesananModal from './EditPesananModal.vue'

const props = defineProps({
    pesanan:    { type: Object,  required: true },
    loading:    { type: Boolean, default: false },
    katalogUrl: { type: String,  required: true },
    editUrl:    { type: String,  required: true },
})

const emit = defineEmits(['update', 'batal', 'edited', 'tandai-bayar'])

const showBatalModal = ref(false)
const showStrukModal = ref(false)
const showEditModal  = ref(false)

function handleBatal(alasan) {
    showBatalModal.value = false
    emit('batal', props.pesanan.id, alasan)
}

function handleEditSaved(data) {
    emit('edited', props.pesanan.id, data)
}

const borderClass = computed(() => {
    switch (props.pesanan.status) {
        case 'menunggu': return 'border-yellow-400'
        case 'diproses': return 'border-blue-500'
        case 'siap': return 'border-green-500'
        case 'selesai': return 'border-gray-300'
        case 'dibatalkan': return 'border-red-300'
        default: return 'border-gray-200'
    }
})

const nomorBgClass = computed(() => {
    switch (props.pesanan.status) {
        case 'menunggu': return 'bg-yellow-50 text-yellow-700'
        case 'diproses': return 'bg-blue-50 text-blue-700'
        case 'siap': return 'bg-green-50 text-green-700'
        case 'selesai': return 'bg-gray-100 text-gray-500'
        case 'dibatalkan': return 'bg-red-50 text-red-400'
        default: return 'bg-gray-100 text-gray-600'
    }
})


function formatRupiah(value) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(value)
}
</script>
