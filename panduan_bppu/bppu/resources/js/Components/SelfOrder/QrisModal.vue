<template>
    <Teleport to="body">
        <Transition
            enter-active-class="transition ease-out duration-200"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition ease-in duration-150"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="show" class="fixed inset-0 z-50 flex items-end sm:items-center justify-center">
                <!-- Backdrop -->
                <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="$emit('close')" />

                <!-- Sheet -->
                <Transition
                    enter-active-class="transition ease-out duration-300"
                    enter-from-class="translate-y-full sm:translate-y-0 sm:scale-95 sm:opacity-0"
                    enter-to-class="translate-y-0 sm:scale-100 sm:opacity-100"
                    leave-active-class="transition ease-in duration-200"
                    leave-from-class="translate-y-0 sm:scale-100 sm:opacity-100"
                    leave-to-class="translate-y-full sm:translate-y-0 sm:scale-95 sm:opacity-0"
                >
                    <div v-if="show" class="relative w-full sm:max-w-sm bg-white rounded-t-2xl sm:rounded-2xl shadow-2xl z-10 overflow-hidden">

                        <!-- Handle bar (mobile) -->
                        <div class="sm:hidden flex justify-center pt-3 pb-1">
                            <div class="w-10 h-1 rounded-full bg-gray-300" />
                        </div>

                        <!-- Header -->
                        <div class="px-5 py-3 flex items-center justify-between border-b border-gray-100">
                            <div class="flex items-center gap-2">
                                <p class="text-sm font-bold text-gray-800">Bayar via QRIS</p>
                                <span class="px-1.5 py-0.5 text-[10px] font-semibold bg-gray-100 text-gray-500 rounded">Opsional</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <img src="/images/gpn.svg" alt="GPN" class="h-5 object-contain" onerror="this.style.display='none'" />
                                <button @click="$emit('close')" class="text-gray-400 hover:text-gray-600 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Nominal -->
                        <div class="px-5 pt-4 flex items-center justify-between">
                            <p class="text-xs text-gray-500">Nominal pembayaran</p>
                            <p class="text-base font-black text-[#996600]">{{ formatRupiah(total) }}</p>
                        </div>

                        <!-- QR Image -->
                        <div class="px-5 py-4 flex justify-center">
                            <div class="border-2 border-[#ccb27f] rounded-xl overflow-hidden">
                                <img :src="qrisImage" alt="QRIS Kantin BPPU" class="w-56 h-56 object-contain" />
                            </div>
                        </div>

                        <!-- Info -->
                        <p class="text-xs text-gray-400 text-center px-5 pb-4">
                            Bisa juga bayar langsung di kasir saat pesanan selesai
                        </p>

                        <!-- Upload section -->
                        <div class="px-5 pb-5 border-t border-gray-100 pt-4">
                            <p class="text-xs text-gray-400 text-center mb-3">Upload bukti bayar untuk mempercepat konfirmasi kasir</p>

                            <!-- Sudah upload -->
                            <div v-if="buktiBayarUrl" class="space-y-2">
                                <div class="rounded-lg overflow-hidden border border-green-200">
                                    <img :src="buktiBayarUrl" alt="Bukti bayar" class="w-full max-h-40 object-contain bg-gray-50" />
                                </div>
                                <div class="flex items-center gap-2 bg-green-50 border border-green-200 rounded-lg px-3 py-2">
                                    <svg class="w-4 h-4 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <p class="text-xs text-green-700 font-semibold">Bukti pembayaran berhasil dikirim</p>
                                </div>
                                <button @click="$emit('reset-bukti')" class="w-full text-xs text-gray-500 underline">Ganti bukti</button>
                            </div>

                            <!-- Form upload -->
                            <div v-else>
                                <label
                                    class="flex flex-col items-center gap-2 px-4 py-4 border-2 border-dashed border-[#ccb27f] rounded-xl cursor-pointer hover:bg-[#f4efe5] transition-colors"
                                    :class="isUploading ? 'opacity-60 pointer-events-none' : ''"
                                >
                                    <input type="file" accept="image/*" capture="environment" class="hidden" @change="handleUpload" />
                                    <svg v-if="!isUploading" class="w-8 h-8 text-[#ccb27f]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <svg v-else class="w-8 h-8 text-[#996600] animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                                    </svg>
                                    <span class="text-xs font-semibold text-[#996600]">{{ isUploading ? 'Mengupload...' : 'Foto / Pilih Bukti Bayar' }}</span>
                                    <span v-if="isUploading" class="text-[10px] text-gray-400">Mengompresi gambar...</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </Transition>
            </div>
        </Transition>
    </Teleport>
</template>

<script setup>
import { ref } from 'vue'
import { useImageCompress } from '@/composables/useImageCompress'

const props = defineProps({
    show: { type: Boolean, default: false },
    qrisImage: { type: String, required: true },
    total: { type: Number, required: true },
    pesananId: { type: Number, required: true },
    buktiBayarUrl: { type: String, default: null },
})

const emit = defineEmits(['close', 'uploaded', 'reset-bukti'])

const { compress } = useImageCompress()
const isUploading = ref(false)

const formatRupiah = (v) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(v)

async function handleUpload(e) {
    const file = e.target.files?.[0]
    if (!file) return
    isUploading.value = true
    try {
        const compressed = await compress(file, { maxWidth: 1200, quality: 0.75 })
        const formData = new FormData()
        formData.append('bukti', compressed, file.name)
        formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.content || '')
        const res = await fetch(`/api/self-order/upload-bukti/${props.pesananId}`, { method: 'POST', body: formData })
        const data = await res.json()
        if (data.success) {
            emit('uploaded', data.bukti_bayar)
        }
    } catch {
        // silent
    } finally {
        isUploading.value = false
    }
}
</script>
