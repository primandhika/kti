<template>
    <div class="flex items-start gap-3 p-3 bg-white rounded-lg border border-gray-200 hover:border-primary-900 transition-colors">
        <div class="flex-shrink-0 w-16 h-16 rounded-lg overflow-hidden bg-gray-100">
            <img
                v-if="item.image"
                :src="item.image"
                :alt="item.name"
                class="w-full h-full object-cover"
            />
            <div v-else class="w-full h-full flex items-center justify-center text-gray-400">
                <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
                </svg>
            </div>
        </div>

        <div class="flex-1 min-w-0">
            <h4 class="font-semibold text-gray-900 text-sm mb-1 truncate">
                {{ item.name }}
            </h4>
            <p v-if="item.varian" class="text-xs text-gray-600 mb-1">
                {{ item.varian.nama_varian }}
            </p>
            <div class="flex items-center gap-2 flex-wrap">
                <p v-if="item.diskon_nominal > 0" class="text-xs text-gray-400 line-through">
                    Rp {{ formatPrice(item.original_price) }}
                </p>
                <p class="text-sm font-bold text-primary-900">
                    Rp {{ formatPrice(item.price) }}
                </p>
                <span v-if="item.diskon_nominal > 0" class="text-[9px] px-1 py-0.5 bg-green-100 text-green-700 rounded font-semibold">
                    DISKON
                </span>
            </div>
        </div>

        <div class="flex flex-col items-end gap-2">
            <button
                @click="$emit('remove')"
                class="p-1 text-gray-400 hover:text-red-600 transition-colors"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>

            <div class="flex items-center gap-2 bg-gray-100 rounded-lg">
                <button
                    @click="$emit('decrease')"
                    class="w-7 h-7 flex items-center justify-center text-gray-600 hover:text-primary-900 hover:bg-gray-200 rounded-l-lg transition-colors"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                    </svg>
                </button>
                <span class="w-8 text-center text-sm font-semibold text-gray-900">
                    {{ item.quantity }}
                </span>
                <button
                    @click="$emit('increase')"
                    :disabled="item.maxStock && item.quantity >= item.maxStock"
                    class="w-7 h-7 flex items-center justify-center text-gray-600 hover:text-primary-900 hover:bg-gray-200 rounded-r-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    item: {
        type: Object,
        required: true
    }
});

defineEmits(['remove', 'increase', 'decrease']);

const formatPrice = (price) => {
    return new Intl.NumberFormat('id-ID').format(price);
};
</script>
