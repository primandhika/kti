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
            class="fixed inset-0 z-[80] flex items-end sm:items-center justify-center p-0 sm:p-4"
            @click.self="$emit('close')"
        >
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>

            <transition
                enter-active-class="transition-transform duration-300"
                enter-from-class="translate-y-full sm:translate-y-0 sm:scale-95"
                enter-to-class="translate-y-0 sm:scale-100"
                leave-active-class="transition-transform duration-200"
                leave-from-class="translate-y-0 sm:scale-100"
                leave-to-class="translate-y-full sm:translate-y-0 sm:scale-95"
            >
                <div
                    v-if="show"
                    class="relative bg-white rounded-t-2xl sm:rounded-2xl shadow-2xl w-full sm:max-w-md max-h-[90vh] overflow-hidden flex flex-col"
                >
                    <div class="p-6 border-b border-gray-200 bg-[#f4efe5]">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex-1">
                                <h3 class="text-lg font-bold text-gray-900 mb-1">{{ menu?.name }}</h3>
                                <p class="text-sm text-gray-600">Pilih varian</p>
                            </div>
                            <button
                                @click="$emit('close')"
                                class="p-2 hover:bg-white/50 rounded-lg transition-colors"
                            >
                                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="flex-1 overflow-y-auto p-6">
                        <div v-if="menu?.image" class="mb-6 rounded-lg overflow-hidden">
                            <img
                                :src="menu.image"
                                :alt="menu.name"
                                class="w-full h-48 object-cover"
                            />
                        </div>

                        <div v-if="menu?.description" class="mb-6">
                            <p class="text-sm text-gray-600">{{ menu.description }}</p>
                        </div>

                        <div class="space-y-3">
                            <h4 class="text-sm font-semibold text-gray-900 mb-3">Varian Tersedia</h4>
                            <button
                                v-for="varian in menu?.varians"
                                :key="varian.id"
                                @click="selectVarian(varian)"
                                class="w-full p-4 border-2 rounded-lg hover:border-primary-900 hover:bg-gray-50 transition-all text-left group"
                                :class="selectedVarian?.id === varian.id ? 'border-primary-900 bg-gray-50' : 'border-gray-200'"
                            >
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <p class="font-semibold text-gray-900 mb-1">{{ varian.nama_varian }}</p>
                                        <p v-if="varian.deskripsi" class="text-xs text-gray-600 mb-2">{{ varian.deskripsi }}</p>
                                        <p class="text-sm font-bold text-primary-900">Rp {{ formatPrice(varian.harga_jual) }}</p>
                                    </div>
                                    <div
                                        class="w-5 h-5 rounded-full border-2 flex items-center justify-center transition-colors"
                                        :class="selectedVarian?.id === varian.id
                                            ? 'border-primary-900 bg-primary-900'
                                            : 'border-gray-300 group-hover:border-primary-900'"
                                    >
                                        <svg v-if="selectedVarian?.id === varian.id" class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                </div>
                            </button>
                        </div>
                    </div>

                    <div class="p-6 border-t border-gray-200 bg-gray-50">
                        <div class="flex items-center gap-3">
                            <div class="flex items-center gap-2 bg-white rounded-lg border border-gray-300">
                                <button
                                    @click="quantity > 1 && quantity--"
                                    class="w-10 h-10 flex items-center justify-center text-gray-600 hover:text-primary-900 hover:bg-gray-50 rounded-l-lg transition-colors"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                    </svg>
                                </button>
                                <span class="w-12 text-center text-base font-semibold text-gray-900">
                                    {{ quantity }}
                                </span>
                                <button
                                    @click="quantity < (menu?.stok || 999) && quantity++"
                                    class="w-10 h-10 flex items-center justify-center text-gray-600 hover:text-primary-900 hover:bg-gray-50 rounded-r-lg transition-colors"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                </button>
                            </div>
                            <button
                                @click="addToCart"
                                :disabled="!selectedVarian"
                                class="flex-1 py-3 px-4 bg-primary-900 text-white font-semibold rounded-lg hover:bg-primary-800 transition-colors disabled:bg-gray-300 disabled:cursor-not-allowed"
                            >
                                Tambah ke Keranjang
                            </button>
                        </div>
                    </div>
                </div>
            </transition>
        </div>
    </transition>
</template>

<script setup>
import { ref, watch, onMounted, onUnmounted } from 'vue';

const props = defineProps({
    show: {
        type: Boolean,
        default: false
    },
    menu: {
        type: Object,
        default: null
    }
});

const emit = defineEmits(['close', 'add']);

const selectedVarian = ref(null);
const quantity = ref(1);

const onKeydown = (e) => {
    if (e.key === 'Escape' && props.show) emit('close');
};

onMounted(() => window.addEventListener('keydown', onKeydown));
onUnmounted(() => window.removeEventListener('keydown', onKeydown));

watch(() => props.show, (newVal) => {
    if (newVal) {
        selectedVarian.value = props.menu?.varians?.[0] || null;
        quantity.value = 1;
    }
});

const selectVarian = (varian) => {
    selectedVarian.value = varian;
};

const addToCart = () => {
    if (selectedVarian.value) {
        emit('add', props.menu, quantity.value, selectedVarian.value);
        emit('close');
    }
};

const formatPrice = (price) => {
    return new Intl.NumberFormat('id-ID').format(price);
};
</script>
