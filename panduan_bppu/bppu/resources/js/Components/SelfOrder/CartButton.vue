<template>
    <!-- Empty Cart: Circle Button -->
    <button
        v-if="totalItems === 0"
        @click="$emit('click')"
        class="fixed bottom-6 right-6 z-[60] w-14 h-14 bg-primary-900 text-white rounded-full shadow-lg hover:shadow-xl hover:bg-primary-800 transition-all duration-300 flex items-center justify-center pointer-events-auto"
    >
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
        </svg>
    </button>

    <!-- Cart with Items: Elongated Button -->
    <button
        v-else
        @click="$emit('click')"
        class="fixed bottom-6 right-4 left-4 md:left-auto md:right-6 md:max-w-md z-[60] bg-primary-900 text-white rounded-full shadow-lg hover:shadow-xl hover:bg-primary-800 transition-all duration-300 flex items-center overflow-hidden pointer-events-auto"
        style="height: 56px;"
    >
        <!-- Left: Preview Text (75%) -->
        <div class="flex-1 px-4 min-w-0 overflow-hidden">
            <div class="flex items-center gap-2 overflow-hidden">
                <!-- Badge Total Items -->
                <div class="flex-shrink-0 w-6 h-6 bg-white text-primary-900 text-xs font-bold rounded-full flex items-center justify-center">
                    {{ totalItems > 99 ? '99+' : totalItems }}
                </div>

                <!-- Scrolling Preview Text -->
                <div class="flex-1 overflow-hidden relative">
                    <div
                        class="whitespace-nowrap text-sm font-medium"
                        :class="{'animate-marquee': previewText.length > 40}"
                    >
                        {{ previewText }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Cart Icon (25%) -->
        <div class="flex-shrink-0 w-14 h-full bg-primary-800 flex items-center justify-center border-l-2 border-primary-700">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
        </div>
    </button>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    totalItems: {
        type: Number,
        default: 0
    },
    cartItems: {
        type: Array,
        default: () => []
    }
});

defineEmits(['click']);

const previewText = computed(() => {
    if (props.cartItems.length === 0) return '';

    // Create preview of items in cart
    const items = props.cartItems.map(item => {
        const name = item.varian ? `${item.name} (${item.varian.nama_varian})` : item.name;
        return `${name} x${item.quantity}`;
    });

    return items.join(' • ');
});
</script>

<style scoped>
@keyframes marquee {
    0% {
        transform: translateX(0%);
    }
    100% {
        transform: translateX(-50%);
    }
}

.animate-marquee {
    animation: marquee 15s linear infinite;
}

.animate-marquee::after {
    content: attr(data-text);
    position: absolute;
    left: 100%;
    padding-left: 2rem;
}
</style>
