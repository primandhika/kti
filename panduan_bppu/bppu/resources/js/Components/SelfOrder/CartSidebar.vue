<template>
    <!-- Desktop: Sticky Sidebar -->
    <div class="hidden lg:flex flex-col bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden h-full">
        <!-- Header -->
        <div class="flex items-center justify-between px-4 py-3 bg-gradient-to-r from-[#996600] to-[#7a5100] flex-shrink-0">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <h2 class="text-sm font-bold text-white">Keranjang</h2>
                <span v-if="totalItems > 0" class="bg-white text-[#996600] text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center leading-none">
                    {{ totalItems > 99 ? '99+' : totalItems }}
                </span>
            </div>
            <button
                v-if="totalItems > 0"
                @click="$emit('clear')"
                class="text-white/70 hover:text-white p-1 hover:bg-white/20 rounded transition-colors"
                title="Kosongkan keranjang"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </button>
        </div>

        <!-- Empty State -->
        <div v-if="isEmpty" class="flex-1 flex flex-col items-center justify-center p-6 text-center">
            <svg class="w-14 h-14 text-gray-200 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <p class="text-sm font-semibold text-gray-400">Keranjang kosong</p>
            <p class="text-xs text-gray-400 mt-1">Pilih menu untuk memulai</p>
        </div>

        <template v-else>
            <!-- Cart Items -->
            <div class="flex-1 overflow-y-auto px-3 py-2 space-y-1.5 min-h-0">
                <div
                    v-for="(item, index) in cartItems"
                    :key="index"
                    class="flex items-center gap-2 p-2 bg-gray-50 rounded-lg group"
                >
                    <!-- Thumbnail -->
                    <div class="w-10 h-10 rounded-lg overflow-hidden bg-gray-200 flex-shrink-0">
                        <img v-if="item.image" :src="item.image" :alt="item.name" class="w-full h-full object-cover" />
                        <div v-else class="w-full h-full flex items-center justify-center text-gray-400">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>

                    <!-- Info -->
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-semibold text-gray-800 truncate leading-tight">{{ item.name }}</p>
                        <p v-if="item.varian" class="text-[10px] text-gray-500 truncate">{{ item.varian.nama_varian }}</p>
                        <p v-if="item.work_unit_name" class="text-[10px] text-[#996600] truncate font-medium">{{ item.work_unit_name }}</p>
                        <p class="text-xs font-bold text-[#996600]">Rp {{ formatPrice(item.price * item.quantity) }}</p>
                    </div>

                    <!-- Qty Controls -->
                    <div class="flex items-center gap-1 flex-shrink-0">
                        <button
                            @click="$emit('decrease', index)"
                            class="w-6 h-6 rounded-full flex items-center justify-center transition-colors"
                            :class="item.quantity === 1 ? 'bg-red-100 hover:bg-red-200 text-red-600' : 'bg-gray-200 hover:bg-gray-300 text-gray-700'"
                        >
                            <svg v-if="item.quantity === 1" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            <svg v-else class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                            </svg>
                        </button>
                        <span class="w-5 text-center text-xs font-bold text-gray-800">{{ item.quantity }}</span>
                        <button
                            @click="$emit('increase', index)"
                            class="w-6 h-6 rounded-full bg-[#996600] hover:bg-[#7a5100] text-white flex items-center justify-center transition-colors"
                        >
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Footer: Catatan + Meja + Total + Checkout -->
            <div class="flex-shrink-0 border-t border-gray-100 p-3 space-y-2.5 bg-white">
                <!-- Catatan -->
                <textarea
                    v-model="localCatatan"
                    @input="$emit('update:catatan', localCatatan)"
                    placeholder="Catatan (opsional)..."
                    rows="2"
                    maxlength="255"
                    class="w-full px-2.5 py-2 text-xs border border-gray-200 rounded-lg focus:ring-1 focus:ring-[#996600] focus:border-[#996600] resize-none placeholder-gray-400"
                ></textarea>

                <!-- Meja Picker -->
                <MejaPicker
                    :selected-meja="selectedMeja"
                    :is-locked="isMejaLocked"
                    @open="$emit('open-meja-picker')"
                    @clear="$emit('clear-meja')"
                />

                <!-- Minimal order warning -->
                <div
                    v-if="minimalOrder > 0 && totalPrice < minimalOrder"
                    class="flex items-center gap-2 px-3 py-2 bg-amber-50 border border-amber-200 rounded-lg text-xs text-amber-700"
                >
                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                    </svg>
                    <span>Minimal order Rp {{ formatPrice(minimalOrder) }} &mdash; kurang Rp {{ formatPrice(minimalOrder - totalPrice) }}</span>
                </div>

                <!-- Total -->
                <div class="bg-gradient-to-r from-[#f4efe5] to-[#eae0cc] rounded-lg px-3 py-2.5 border border-[#ccb27f]/50 space-y-1">
                    <div v-if="biayaLayananAktif && biayaLayanan > 0" class="flex items-center justify-between text-xs text-[#6b4700]">
                        <span>Subtotal ({{ totalItems }} item)</span>
                        <span>Rp {{ formatPrice(totalPrice) }}</span>
                    </div>
                    <div v-if="biayaLayananAktif && biayaLayanan > 0" class="flex items-center justify-between text-xs text-[#6b4700]">
                        <span>Biaya layanan</span>
                        <span>Rp {{ formatPrice(biayaLayanan) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-[#6b4700] font-semibold">
                            {{ biayaLayananAktif && biayaLayanan > 0 ? 'Total' : 'Total (' + totalItems + ' item)' }}
                        </span>
                        <span class="text-base font-bold text-[#996600]">Rp {{ formatPrice(grandTotal) }}</span>
                    </div>
                </div>

                <!-- Metode bayar -->
                <div class="grid grid-cols-2 gap-2">
                    <button
                        @click="localMetodeBayar = 'tunai'; $emit('update:metodeBayar', 'tunai')"
                        class="flex items-center justify-center gap-1.5 py-2 rounded-lg border-2 transition-all text-xs font-semibold"
                        :class="localMetodeBayar === 'tunai' ? 'border-[#996600] bg-[#f4efe5] text-[#996600]' : 'border-gray-200 text-gray-600 hover:border-gray-300'"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Tunai
                    </button>
                    <button
                        @click="localMetodeBayar = 'qris'; $emit('update:metodeBayar', 'qris')"
                        class="flex items-center justify-center gap-1.5 py-2 rounded-lg border-2 transition-all text-xs font-semibold"
                        :class="localMetodeBayar === 'qris' ? 'border-[#996600] bg-[#f4efe5] text-[#996600]' : 'border-gray-200 text-gray-600 hover:border-gray-300'"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                        </svg>
                        QRIS
                    </button>
                </div>

                <!-- Checkout Button -->
                <button
                    @click="$emit('checkout')"
                    :disabled="isCheckingOut || !localMetodeBayar || (minimalOrder > 0 && totalPrice < minimalOrder)"
                    class="w-full py-3 bg-[#996600] hover:bg-[#7a5100] active:scale-[0.98] text-white font-bold text-sm rounded-lg transition-all shadow-md hover:shadow-lg disabled:opacity-60 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                >
                    <svg v-if="isCheckingOut" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z" />
                    </svg>
                    <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                    </svg>
                    {{ isCheckingOut ? 'Memproses...' : (localMetodeBayar ? 'Pesan Sekarang' : 'Pilih metode bayar') }}
                </button>
            </div>
        </template>
    </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import MejaPicker from '@/Components/SelfOrder/MejaPicker.vue';

const props = defineProps({
    cartItems: { type: Array, default: () => [] },
    totalItems: { type: Number, default: 0 },
    totalPrice: { type: Number, default: 0 },
    isEmpty: { type: Boolean, default: true },
    isCheckingOut: { type: Boolean, default: false },
    catatan: { type: String, default: '' },
    selectedMeja: { type: Object, default: null },
    isMejaLocked: { type: Boolean, default: false },
    minimalOrder: { type: Number, default: 0 },
    biayaLayananAktif: { type: Boolean, default: false },
    biayaLayanan: { type: Number, default: 0 },
    metodeBayar: { type: String, default: null },
});

defineEmits(['increase', 'decrease', 'clear', 'checkout', 'update:catatan', 'open-meja-picker', 'clear-meja', 'update:metodeBayar']);

const localCatatan = ref(props.catatan);
const localMetodeBayar = ref(props.metodeBayar);
watch(() => props.catatan, (v) => { localCatatan.value = v; });
watch(() => props.metodeBayar, (v) => { localMetodeBayar.value = v; });

const grandTotal = computed(() => {
    if (props.biayaLayananAktif && props.biayaLayanan > 0) {
        return props.totalPrice + props.biayaLayanan;
    }
    return props.totalPrice;
});

const formatPrice = (price) => new Intl.NumberFormat('id-ID').format(price);
</script>
