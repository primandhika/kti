<template>
    <div class="space-y-3">
        <!-- Catatan untuk kantin -->
        <div class="bg-white rounded-lg p-4 border border-gray-200">
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Catatan untuk Kantin (Opsional)
            </label>
            <textarea
                v-model="localCatatan"
                @input="$emit('update:catatan', localCatatan)"
                placeholder="Contoh: Tidak pakai cabe, kurangi gula, dll."
                rows="2"
                maxlength="255"
                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-900 focus:border-transparent resize-none"
            ></textarea>
            <p class="text-xs text-gray-400 mt-1">{{ localCatatan?.length || 0 }}/255 karakter</p>
        </div>

        <!-- Minimal order warning -->
        <div
            v-if="minimalOrder > 0 && totalPrice < minimalOrder"
            class="flex items-center gap-2 px-3 py-2 bg-amber-50 border border-amber-200 rounded-lg text-xs text-amber-700"
        >
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
            </svg>
            <span>Minimal order Rp {{ formatPrice(minimalOrder) }} (kurang Rp {{ formatPrice(minimalOrder - totalPrice) }})</span>
        </div>

        <!-- Summary -->
        <div class="bg-white rounded-lg p-4 space-y-3 border border-gray-200">
            <div class="flex items-center justify-between text-sm">
                <span class="text-gray-600">Subtotal</span>
                <span class="font-semibold text-gray-900">Rp {{ formatPrice(totalPrice) }}</span>
            </div>

            <div v-if="biayaLayananAktif && biayaLayanan > 0" class="flex items-center justify-between text-sm">
                <span class="text-gray-600">Biaya layanan</span>
                <span class="font-semibold text-gray-900">Rp {{ formatPrice(biayaLayanan) }}</span>
            </div>

            <div class="border-t border-gray-200 pt-3">
                <div class="flex items-center justify-between">
                    <span class="text-base font-bold text-gray-900">Total</span>
                    <span class="text-lg font-bold text-primary-900">Rp {{ formatPrice(grandTotal) }}</span>
                </div>
            </div>
        </div>

        <!-- Pilihan metode bayar -->
        <div class="bg-white rounded-lg p-4 border border-gray-200">
            <p class="text-sm font-medium text-gray-700 mb-2">Metode Pembayaran</p>
            <div class="grid grid-cols-2 gap-2">
                <button
                    @click="localMetodeBayar = 'tunai'; $emit('update:metodeBayar', 'tunai')"
                    class="flex items-center gap-2 px-3 py-2.5 rounded-lg border-2 transition-all text-sm font-semibold"
                    :class="localMetodeBayar === 'tunai' ? 'border-[#996600] bg-[#f4efe5] text-[#996600]' : 'border-gray-200 text-gray-600 hover:border-gray-300'"
                >
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    Tunai
                </button>
                <button
                    @click="localMetodeBayar = 'qris'; $emit('update:metodeBayar', 'qris')"
                    class="flex items-center gap-2 px-3 py-2.5 rounded-lg border-2 transition-all text-sm font-semibold"
                    :class="localMetodeBayar === 'qris' ? 'border-[#996600] bg-[#f4efe5] text-[#996600]' : 'border-gray-200 text-gray-600 hover:border-gray-300'"
                >
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                    </svg>
                    QRIS
                </button>
            </div>
        </div>

        <div class="space-y-2">
            <button
                @click="$emit('checkout')"
                :disabled="totalItems === 0 || isCheckingOut || !localMetodeBayar || (minimalOrder > 0 && totalPrice < minimalOrder)"
                class="w-full py-4 px-4 bg-primary-900 text-white font-bold text-base rounded-lg hover:bg-primary-800 active:bg-primary-900 active:scale-[0.98] transition-all duration-200 disabled:bg-gray-300 disabled:cursor-not-allowed shadow-lg hover:shadow-xl active:shadow-md flex items-center justify-center gap-2"
            >
                <svg v-if="isCheckingOut" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z" />
                </svg>
                <span>{{ isCheckingOut ? 'Memproses...' : (localMetodeBayar ? `Checkout (${totalItems} item)` : 'Pilih metode bayar dulu') }}</span>
            </button>

            <button
                v-if="totalItems > 0"
                @click="$emit('clear')"
                class="w-full py-2.5 px-4 text-sm font-medium text-red-600 hover:text-red-700 bg-white hover:bg-red-50 border border-red-200 rounded-lg transition-colors"
            >
                Kosongkan Keranjang
            </button>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';

const props = defineProps({
    totalPrice: { type: Number, required: true },
    totalItems: { type: Number, required: true },
    isCheckingOut: { type: Boolean, default: false },
    catatan: { type: String, default: '' },
    minimalOrder: { type: Number, default: 0 },
    biayaLayananAktif: { type: Boolean, default: false },
    biayaLayanan: { type: Number, default: 0 },
    metodeBayar: { type: String, default: null },
});

defineEmits(['checkout', 'clear', 'update:catatan', 'update:metodeBayar']);

const localCatatan = ref(props.catatan);
const localMetodeBayar = ref(props.metodeBayar);

watch(() => props.catatan, (newVal) => { localCatatan.value = newVal; });
watch(() => props.metodeBayar, (newVal) => { localMetodeBayar.value = newVal; });

const grandTotal = computed(() => {
    if (props.biayaLayananAktif && props.biayaLayanan > 0) {
        return props.totalPrice + props.biayaLayanan;
    }
    return props.totalPrice;
});

const formatPrice = (price) => {
    return new Intl.NumberFormat('id-ID').format(price);
};
</script>
