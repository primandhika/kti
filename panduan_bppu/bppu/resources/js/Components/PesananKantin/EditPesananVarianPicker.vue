<template>
    <div class="absolute inset-0 bg-black/40 z-10 flex items-center justify-center rounded-xl">
        <div class="bg-white rounded-xl shadow-lg w-72 p-5">
            <h3 class="font-semibold text-gray-800 text-sm mb-1">Pilih Varian</h3>
            <p class="text-xs text-gray-500 mb-3">{{ barang.name }}</p>
            <div class="space-y-1.5 mb-4">
                <button
                    v-for="v in barang.varians"
                    :key="v.id"
                    @click="selected = v"
                    class="w-full flex justify-between items-center px-3 py-2 rounded-lg border text-xs font-medium transition-colors"
                    :class="selected?.id === v.id
                        ? 'border-[#996600] bg-[#f4efe5] text-[#7a5100]'
                        : 'border-gray-200 text-gray-700 hover:border-[#c1a366]'"
                >
                    <span>{{ v.nama_varian }}</span>
                    <span v-if="v.harga_tambahan > 0" class="text-gray-400">+{{ formatRupiah(v.harga_tambahan) }}</span>
                </button>
            </div>
            <div class="flex gap-2">
                <button
                    @click="$emit('cancel')"
                    class="flex-1 py-2 text-xs font-semibold text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50"
                >Batal</button>
                <button
                    @click="confirm"
                    :disabled="!selected"
                    class="flex-1 py-2 text-xs font-semibold text-white bg-[#996600] rounded-lg hover:bg-[#7a5100] disabled:opacity-50"
                >Tambah</button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'

const props = defineProps({
    barang: { type: Object, required: true },
})

const emit    = defineEmits(['confirm', 'cancel'])
const selected = ref(null)

function confirm() {
    if (!selected.value) return
    emit('confirm', { barang: props.barang, varian: selected.value })
}

function formatRupiah(value) {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value)
}
</script>
