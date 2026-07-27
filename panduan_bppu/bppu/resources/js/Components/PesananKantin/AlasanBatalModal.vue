<template>
    <Transition
        enter-active-class="transition ease-out duration-200"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition ease-in duration-150"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div
            v-if="show"
            class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4"
            @click.self="$emit('close')"
        >
            <Transition
                enter-active-class="transition ease-out duration-200"
                enter-from-class="opacity-0 scale-95"
                enter-to-class="opacity-100 scale-100"
                leave-active-class="transition ease-in duration-150"
                leave-from-class="opacity-100 scale-100"
                leave-to-class="opacity-0 scale-95"
            >
                <div
                    v-if="show"
                    class="bg-white rounded-xl shadow-xl max-w-md w-full"
                >
                    <!-- Header -->
                    <div class="px-5 py-4 border-b border-gray-200">
                        <h3 class="text-base font-bold text-gray-900">Alasan Pembatalan Pesanan</h3>
                        <p class="text-xs text-gray-500 mt-1">Pilih alasan pembatalan untuk diinformasikan ke pemesan</p>
                    </div>

                    <!-- Body -->
                    <div class="p-5 space-y-3">
                        <!-- Pilihan alasan -->
                        <label
                            v-for="option in alasanOptions"
                            :key="option.value"
                            class="flex items-start gap-3 p-3 border-2 rounded-lg cursor-pointer transition-all"
                            :class="selectedAlasan === option.value
                                ? 'border-[#996600] bg-[#f4efe5]'
                                : 'border-gray-200 hover:border-gray-300 bg-white'"
                        >
                            <input
                                type="radio"
                                :value="option.value"
                                v-model="selectedAlasan"
                                class="mt-0.5 text-[#996600] focus:ring-[#996600]"
                            />
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-gray-800">{{ option.label }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">{{ option.description }}</p>
                            </div>
                        </label>

                        <!-- Input custom alasan jika pilih "Lainnya" -->
                        <div v-if="selectedAlasan === 'lainnya'" class="mt-3">
                            <label class="block text-xs font-medium text-gray-700 mb-1">Alasan lainnya:</label>
                            <textarea
                                v-model="customAlasan"
                                rows="3"
                                class="w-full text-sm px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-[#996600]"
                                placeholder="Jelaskan alasan pembatalan..."
                            ></textarea>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="px-5 py-4 bg-gray-50 rounded-b-xl flex gap-2">
                        <button
                            @click="$emit('close')"
                            class="flex-1 py-2.5 px-4 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
                        >
                            Batal
                        </button>
                        <button
                            @click="handleSubmit"
                            :disabled="isSubmitDisabled"
                            class="flex-1 py-2.5 px-4 text-sm font-semibold text-white bg-red-600 rounded-lg hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                        >
                            Batalkan Pesanan
                        </button>
                    </div>
                </div>
            </Transition>
        </div>
    </Transition>
</template>

<script setup>
import { ref, watch, computed } from 'vue'

const props = defineProps({
    show: { type: Boolean, default: false },
})

const emit = defineEmits(['close', 'confirm'])

const selectedAlasan = ref('')
const customAlasan = ref('')

const alasanOptions = [
    {
        value: 'stok_habis',
        label: 'Stok Habis',
        description: 'Beberapa atau semua item pesanan stoknya habis'
    },
    {
        value: 'kantin_sibuk',
        label: 'Kantin Sedang Sibuk',
        description: 'Kantin sedang sibuk atau kurang personil'
    },
    {
        value: 'penyesuaian_harga',
        label: 'Perlu Penyesuaian Harga',
        description: 'Ada perbedaan harga atau perlu penyesuaian'
    },
    {
        value: 'kesalahan_pesanan',
        label: 'Kesalahan Pesanan',
        description: 'Ada kesalahan dalam pesanan yang tidak bisa diproses'
    },
    {
        value: 'lainnya',
        label: 'Alasan Lainnya',
        description: 'Jelaskan alasan pembatalan lainnya'
    }
]

const isSubmitDisabled = computed(() => {
    if (!selectedAlasan.value) return true
    if (selectedAlasan.value === 'lainnya' && !customAlasan.value.trim()) return true
    return false
})

// Reset when modal closed
watch(() => props.show, (newVal) => {
    if (!newVal) {
        selectedAlasan.value = ''
        customAlasan.value = ''
    }
})

function handleSubmit() {
    if (isSubmitDisabled.value) {
        console.warn('Submit disabled - validation failed', {
            selectedAlasan: selectedAlasan.value,
            customAlasan: customAlasan.value
        })
        return
    }

    const alasan = selectedAlasan.value === 'lainnya'
        ? customAlasan.value.trim()
        : alasanOptions.find(o => o.value === selectedAlasan.value)?.label || ''

    if (!alasan) {
        console.error('Alasan kosong setelah processing!')
        return
    }

    console.log('Submitting pembatalan:', {
        kode: selectedAlasan.value,
        teks: alasan
    })

    emit('confirm', {
        kode: selectedAlasan.value,
        teks: alasan
    })

    emit('close')
}
</script>
