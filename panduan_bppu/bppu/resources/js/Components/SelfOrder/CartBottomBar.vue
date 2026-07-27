<template>
    <!-- Mobile only: Sticky bottom checkout bar -->
    <div class="lg:hidden fixed bottom-0 left-0 right-0 z-[60] pointer-events-none">
        <!-- Collapsed: Floating pill saat keranjang kosong -->
        <div v-if="isEmpty" class="pointer-events-auto flex justify-end px-4 pb-4">
            <div class="w-12 h-12 bg-[#996600] rounded-full shadow-lg flex items-center justify-center opacity-50">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
        </div>

        <!-- Active Cart Panel -->
        <transition
            enter-active-class="transition-transform duration-300"
            enter-from-class="translate-y-full"
            enter-to-class="translate-y-0"
            leave-active-class="transition-transform duration-200"
            leave-from-class="translate-y-0"
            leave-to-class="translate-y-full"
        >
            <div v-if="!isEmpty" class="pointer-events-auto bg-white border-t-2 border-[#996600] shadow-2xl">
                <!-- Toggle handle -->
                <button
                    @click="expanded = !expanded"
                    class="w-full flex items-center justify-between px-4 py-3 bg-gradient-to-r from-[#996600] to-[#7a5100]"
                >
                    <div class="flex items-center gap-2 min-w-0">
                        <!-- Ikon keranjang + badge -->
                        <div class="relative flex-shrink-0">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <span class="absolute -top-1.5 -right-1.5 bg-white text-[#996600] text-[9px] font-bold rounded-full w-4 h-4 flex items-center justify-center leading-none">
                                {{ totalItems > 99 ? '99+' : totalItems }}
                            </span>
                        </div>
                        <span class="text-xs text-white/80 truncate max-w-[160px]">{{ previewText }}</span>
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <span class="text-sm font-bold text-white">Rp {{ formatPrice(grandTotal) }}</span>
                        <svg
                            class="w-4 h-4 text-white transition-transform duration-200"
                            :class="expanded ? 'rotate-180' : ''"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                        </svg>
                    </div>
                </button>

                <!-- Expanded: item list + catatan + checkout -->
                <transition
                    enter-active-class="transition-all duration-200 ease-out"
                    enter-from-class="opacity-0 max-h-0"
                    enter-to-class="opacity-100 max-h-[60vh]"
                    leave-active-class="transition-all duration-150 ease-in"
                    leave-from-class="opacity-100 max-h-[60vh]"
                    leave-to-class="opacity-0 max-h-0"
                >
                    <div v-show="expanded" class="overflow-hidden">
                        <!-- Items scrollable -->
                        <div class="overflow-y-auto max-h-[35vh] px-3 py-2 space-y-1.5 bg-gray-50">
                            <div
                                v-for="(item, index) in cartItems"
                                :key="index"
                                class="flex items-center gap-2 bg-white rounded-lg p-2"
                            >
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-semibold text-gray-800 truncate">{{ item.name }}</p>
                                    <p v-if="item.varian" class="text-[10px] text-gray-500 truncate">{{ item.varian.nama_varian }}</p>
                                    <p v-if="item.work_unit_name" class="text-[10px] text-[#996600] truncate font-medium">{{ item.work_unit_name }}</p>
                                    <p class="text-xs font-bold text-[#996600]">Rp {{ formatPrice(item.price * item.quantity) }}</p>
                                </div>
                                <div class="flex items-center gap-1 flex-shrink-0">
                                    <button
                                        @click="$emit('decrease', index)"
                                        class="w-6 h-6 rounded-full flex items-center justify-center"
                                        :class="item.quantity === 1 ? 'bg-red-100 text-red-600' : 'bg-gray-100 text-gray-600'"
                                    >
                                        <svg v-if="item.quantity === 1" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        <svg v-else class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                        </svg>
                                    </button>
                                    <span class="w-5 text-center text-xs font-bold">{{ item.quantity }}</span>
                                    <button
                                        @click="$emit('increase', index)"
                                        class="w-6 h-6 rounded-full bg-[#996600] text-white flex items-center justify-center"
                                    >
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Catatan + Meja + Checkout -->
                        <div class="px-3 pt-2 pb-3 space-y-2 bg-white border-t border-gray-100">
                            <textarea
                                v-model="localCatatan"
                                @input="$emit('update:catatan', localCatatan)"
                                placeholder="Catatan untuk kantin (opsional)..."
                                rows="2"
                                maxlength="255"
                                class="w-full px-2.5 py-2 text-xs border border-gray-200 rounded-lg focus:ring-1 focus:ring-[#996600] focus:border-[#996600] resize-none"
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
                                class="flex items-center gap-2 px-2.5 py-1.5 bg-amber-50 border border-amber-200 rounded-lg text-xs text-amber-700"
                            >
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                                </svg>
                                <span>Minimal order Rp {{ formatPrice(minimalOrder) }} &mdash; kurang Rp {{ formatPrice(minimalOrder - totalPrice) }}</span>
                            </div>

                            <!-- Biaya layanan -->
                            <div v-if="biayaLayananAktif && biayaLayanan > 0" class="flex items-center justify-between text-xs text-gray-500 px-1">
                                <span>Biaya layanan</span>
                                <span>+ Rp {{ formatPrice(biayaLayanan) }}</span>
                            </div>

                            <!-- Metode bayar -->
                            <div class="grid grid-cols-2 gap-2">
                                <button
                                    @click="localMetodeBayar = 'tunai'; $emit('update:metodeBayar', 'tunai')"
                                    class="flex items-center justify-center gap-1.5 py-2 rounded-lg border-2 transition-all text-xs font-semibold"
                                    :class="localMetodeBayar === 'tunai' ? 'border-[#996600] bg-[#f4efe5] text-[#996600]' : 'border-gray-200 text-gray-600'"
                                >
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    Tunai
                                </button>
                                <button
                                    @click="localMetodeBayar = 'qris'; $emit('update:metodeBayar', 'qris')"
                                    class="flex items-center justify-center gap-1.5 py-2 rounded-lg border-2 transition-all text-xs font-semibold"
                                    :class="localMetodeBayar === 'qris' ? 'border-[#996600] bg-[#f4efe5] text-[#996600]' : 'border-gray-200 text-gray-600'"
                                >
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                                    </svg>
                                    QRIS
                                </button>
                            </div>

                            <div class="flex gap-2">
                                <button
                                    @click="$emit('clear')"
                                    class="px-3 py-2.5 text-xs font-semibold text-red-600 border border-red-200 rounded-lg hover:bg-red-50 transition-colors flex-shrink-0"
                                >
                                    Hapus
                                </button>
                                <button
                                    @click="$emit('checkout')"
                                    :disabled="isCheckingOut || !localMetodeBayar || (minimalOrder > 0 && totalPrice < minimalOrder)"
                                    class="flex-1 py-2.5 bg-[#996600] hover:bg-[#7a5100] text-white font-bold text-sm rounded-lg transition-all disabled:opacity-60 flex items-center justify-center gap-2"
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
                        </div>
                    </div>
                </transition>

                <!-- Collapsed: Quick checkout bar -->
                <div v-if="!expanded" class="px-4 py-2.5 flex flex-col gap-1.5 bg-white">
                    <div
                        v-if="minimalOrder > 0 && totalPrice < minimalOrder"
                        class="flex items-center gap-1.5 text-xs text-amber-700 bg-amber-50 border border-amber-200 rounded-lg px-2.5 py-1.5"
                    >
                        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                        </svg>
                        <span>Minimal order Rp {{ formatPrice(minimalOrder) }} &mdash; kurang Rp {{ formatPrice(minimalOrder - totalPrice) }}</span>
                    </div>
                    <button
                        @click="$emit('checkout')"
                        :disabled="isCheckingOut || (minimalOrder > 0 && totalPrice < minimalOrder)"
                        class="flex-1 py-2.5 bg-[#996600] hover:bg-[#7a5100] text-white font-bold text-sm rounded-lg transition-all disabled:opacity-60 flex items-center justify-center gap-2 shadow-md"
                    >
                        <svg v-if="isCheckingOut" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z" />
                        </svg>
                        <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                        {{ isCheckingOut ? 'Memproses...' : 'Pesan Sekarang' }}
                    </button>
                </div>
            </div>
        </transition>
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

const expanded = ref(false);
const localCatatan = ref(props.catatan);
const localMetodeBayar = ref(props.metodeBayar);

watch(() => props.catatan, (v) => { localCatatan.value = v; });
watch(() => props.metodeBayar, (v) => { localMetodeBayar.value = v; });
watch(() => props.totalItems, (newVal, oldVal) => {
    // Auto-expand saat baru add item
    if (newVal > oldVal) expanded.value = true;
});

const previewText = computed(() => {
    if (!props.cartItems.length) return '';
    const first = props.cartItems[0];
    const name = first.varian ? `${first.name} (${first.varian.nama_varian})` : first.name;
    return props.cartItems.length > 1 ? `${name} +${props.cartItems.length - 1} lainnya` : name;
});

const grandTotal = computed(() => {
    if (props.biayaLayananAktif && props.biayaLayanan > 0) {
        return props.totalPrice + props.biayaLayanan;
    }
    return props.totalPrice;
});

const formatPrice = (price) => new Intl.NumberFormat('id-ID').format(price);
</script>
