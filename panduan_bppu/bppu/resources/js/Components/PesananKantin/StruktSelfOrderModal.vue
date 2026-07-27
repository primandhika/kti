<template>
    <div
        v-if="show && pesanan"
        class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4"
        @click.self="$emit('close')"
    >
        <div class="bg-white rounded-lg shadow-2xl w-full max-w-xs">
            <div class="flex items-center justify-between px-4 py-3 border-b bg-gray-50 rounded-t-lg">
                <div>
                    <h3 class="font-semibold text-gray-800 text-sm">Cetak Struk</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Antrian #{{ pesanan.nomor_antrian }} &mdash; {{ pesanan.nama_pemesan }}</p>
                </div>
                <button @click="$emit('close')" class="p-1.5 hover:bg-gray-200 rounded transition-colors">
                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="p-4 space-y-3">
                <!-- Info singkat -->
                <div class="bg-gray-50 rounded-lg px-3 py-2 text-xs text-gray-600 space-y-1">
                    <div class="flex justify-between">
                        <span class="text-gray-400">Total</span>
                        <span class="font-semibold text-[#996600]">{{ formatRupiah(pesanan.total) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Item</span>
                        <span>{{ pesanan.items?.length || 0 }} item</span>
                    </div>
                    <div v-if="pesanan.nama_meja" class="flex justify-between">
                        <span class="text-gray-400">Meja</span>
                        <span>{{ pesanan.nama_meja }}</span>
                    </div>
                </div>

                <p class="text-xs text-gray-500 text-center">
                    Struk akan dicetak ke printer thermal (58mm)
                </p>

                <div class="flex gap-2">
                    <button
                        @click="$emit('close')"
                        class="flex-1 py-2 text-xs font-semibold bg-white border border-gray-200 text-gray-600 rounded-lg hover:bg-gray-50 transition-colors"
                    >
                        Batal
                    </button>
                    <button
                        @click="doCetak"
                        :disabled="printing"
                        class="flex-1 py-2 text-xs font-semibold text-white rounded-lg transition-colors disabled:opacity-50 flex items-center justify-center gap-1.5 bg-[#996600] hover:bg-[#7a5100]"
                    >
                        <svg v-if="!printing" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        <svg v-else class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                        </svg>
                        {{ printing ? 'Mencetak...' : 'Cetak Struk' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import { useThermalPrinter } from '@/composables/useThermalPrinter'

const props = defineProps({
    show: Boolean,
    pesanan: Object,
})

const emit = defineEmits(['close'])

const printing = ref(false)
const { printReceipt } = useThermalPrinter()

function formatRupiah(value) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency', currency: 'IDR', minimumFractionDigits: 0,
    }).format(value)
}

async function doCetak() {
    if (!props.pesanan) return
    printing.value = true

    try {
        const p = props.pesanan
        const sudahSelesai = p.status === 'selesai'
        const transaction = {
            nomor_transaksi: p.nomor_transaksi || `Antrian #${p.nomor_antrian}`,
            tanggal: new Date(),
            work_unit_name: p.nama_kantin || 'Kantin BPPU',
            items: (p.items || []).map(item => ({
                nama_barang: item.name + (item.varian ? ` (${item.varian.nama_varian})` : ''),
                qty: item.quantity,
                harga_satuan: item.price,
                subtotal: item.price * item.quantity,
            })),
            subtotal: p.total,
            total: p.total,
            metode_pembayaran: sudahSelesai ? (p.bukti_bayar ? 'QRIS' : 'kasir') : 'belum lunas',
            catatan: p.catatan || null,
        }

        await printReceipt(transaction)
        emit('close')
    } finally {
        printing.value = false
    }
}
</script>
