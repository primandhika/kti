<template>
    <transition
        enter-active-class="transition-opacity duration-300"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition-opacity duration-200"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div
            v-if="show"
            class="fixed inset-0 z-[70] overflow-hidden pointer-events-auto"
            @click.self="$emit('close')"
        >
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>

            <transition
                enter-active-class="transition-transform duration-300"
                enter-from-class="translate-x-full"
                enter-to-class="translate-x-0"
                leave-active-class="transition-transform duration-200"
                leave-from-class="translate-x-0"
                leave-to-class="translate-x-full"
            >
                <div
                    v-if="show"
                    class="absolute right-0 top-0 bottom-0 w-full sm:w-[480px] bg-white shadow-2xl flex flex-col"
                >
                    <div class="flex items-center justify-between p-4 sm:p-6 border-b border-gray-200 bg-[#f4efe5]">
                        <div class="flex items-center gap-3">
                            <svg class="w-6 h-6 text-primary-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            <h2 class="text-xl font-bold text-gray-900">Keranjang</h2>
                            <span
                                v-if="totalItems > 0"
                                class="px-2 py-1 text-xs font-bold bg-primary-900 text-white rounded-full"
                            >
                                {{ totalItems }}
                            </span>
                        </div>
                        <button
                            @click="$emit('close')"
                            class="p-2 hover:bg-white/50 rounded-lg transition-colors"
                        >
                            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <div v-if="isEmpty" class="flex-1 flex items-center justify-center p-8">
                        <div class="text-center">
                            <svg class="w-24 h-24 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            <h3 class="text-lg font-bold text-gray-900 mb-2">Keranjang Kosong</h3>
                            <p class="text-sm text-gray-600 mb-4">Belum ada menu yang ditambahkan</p>
                            <button
                                @click="$emit('close')"
                                class="px-6 py-2 bg-primary-900 text-white font-semibold rounded-lg hover:bg-primary-800 transition-colors"
                            >
                                Mulai Belanja
                            </button>
                        </div>
                    </div>

                    <template v-else>
                        <div class="flex-1 overflow-y-auto p-4 sm:p-6 space-y-3 pb-safe">
                            <CartItem
                                v-for="(item, index) in cartItems"
                                :key="index"
                                :item="item"
                                @remove="$emit('remove', index)"
                                @increase="$emit('increase', index)"
                                @decrease="$emit('decrease', index)"
                            />
                        </div>

                        <div class="flex-shrink-0 p-4 sm:p-6 border-t border-gray-200 bg-gray-50 pb-safe">
                            <CartSummary
                                :total-price="totalPrice"
                                :total-items="totalItems"
                                :is-checking-out="isCheckingOut"
                                :catatan="catatan"
                                :minimal-order="minimalOrder"
                                :biaya-layanan-aktif="biayaLayananAktif"
                                :biaya-layanan="biayaLayanan"
                                :metode-bayar="metodeBayar"
                                @checkout="$emit('checkout')"
                                @clear="$emit('clear')"
                                @update:catatan="$emit('update:catatan', $event)"
                                @update:metodeBayar="$emit('update:metodeBayar', $event)"
                            />
                        </div>
                    </template>
                </div>
            </transition>
        </div>
    </transition>
</template>

<script setup>
import CartItem from './CartItem.vue';
import CartSummary from './CartSummary.vue';

defineProps({
    show: {
        type: Boolean,
        default: false
    },
    cartItems: {
        type: Array,
        default: () => []
    },
    totalItems: {
        type: Number,
        default: 0
    },
    totalPrice: {
        type: Number,
        default: 0
    },
    isEmpty: {
        type: Boolean,
        default: true
    },
    isCheckingOut: {
        type: Boolean,
        default: false
    },
    catatan: {
        type: String,
        default: ''
    },
    minimalOrder: { type: Number, default: 0 },
    biayaLayananAktif: { type: Boolean, default: false },
    biayaLayanan: { type: Number, default: 0 },
    metodeBayar: { type: String, default: null },
});

defineEmits(['close', 'remove', 'increase', 'decrease', 'checkout', 'clear', 'update:catatan', 'update:metodeBayar']);
</script>
