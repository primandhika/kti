<template>
    <div class="group relative h-96 rounded-2xl overflow-hidden shadow-2xl hover:shadow-3xl transition-all duration-500 transform hover:scale-[1.02]">
        <!-- Background Image -->
        <div class="absolute inset-0">
            <img
                v-if="menu.image"
                :src="menu.image"
                :alt="menu.name"
                class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
            />
            <div v-else class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-800 to-gray-900">
                <svg class="w-32 h-32 text-white/20" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
                </svg>
            </div>
        </div>

        <!-- Dark Gradient Overlay -->
        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/50 to-black/30"></div>

        <!-- Content Overlay -->
        <div class="absolute inset-0 p-6 flex flex-col justify-between">
            <!-- Top Badges -->
            <div class="flex items-start gap-2 flex-wrap">
                <span v-if="menu.work_unit?.name" class="px-3 py-1.5 rounded-full text-xs font-bold bg-[#996600]/90 backdrop-blur-md text-white border border-[#7a5100]/50 flex items-center gap-1">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                    </svg>
                    {{ menu.work_unit.name }}
                </span>
                <span class="px-3 py-1.5 rounded-full text-xs font-medium bg-white/20 backdrop-blur-md text-white border border-white/30">
                    {{ menu.sub_kategori || menu.category.name }}
                </span>
                <span v-if="hasVarians" class="px-3 py-1.5 rounded-full text-xs font-bold bg-primary-900/90 backdrop-blur-md text-white border border-primary-700/50 flex items-center gap-1">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z" />
                    </svg>
                    {{ menu.varians.length }} Varian
                </span>
            </div>

            <!-- Bottom Info -->
            <div class="space-y-2 transform transition-all duration-300 group-hover:translate-y-0 translate-y-2">
                <h3 class="text-lg font-bold text-white drop-shadow-lg leading-tight truncate">
                    {{ menu.name }}
                </h3>

                <!-- Varian Preview Chips -->
                <div v-if="hasVarians" class="flex flex-wrap gap-1 mb-2">
                    <span
                        v-for="varian in menu.varians.slice(0, 3)"
                        :key="varian.id"
                        class="px-2 py-1 text-xs font-medium rounded-md bg-white/20 backdrop-blur-md text-white border border-white/40"
                    >
                        {{ varian.nama_varian }}
                    </span>
                    <span v-if="menu.varians.length > 3" class="px-2 py-1 text-xs font-medium rounded-md bg-white/10 backdrop-blur-md text-white/80">
                        +{{ menu.varians.length - 3 }}
                    </span>
                </div>

                <p
                    class="text-xs text-white/90 line-clamp-3 drop-shadow"
                    :title="menu.description"
                >
                    {{ menu.description }}
                </p>

                <div class="flex items-end justify-between pt-2">
                    <div>
                        <p class="text-xs text-white/80 mb-0.5">
                            {{ hasVarians && priceRange ? 'Mulai dari' : 'Harga' }}
                        </p>
                        <p class="text-xl font-bold text-white drop-shadow-lg">
                            {{ displayPrice }}
                        </p>
                    </div>
                    <button
                        @click="$emit('order', menu)"
                        class="px-4 py-2 rounded-xl text-sm font-bold bg-white text-primary-900 hover:bg-primary-50 active:bg-primary-100 transition-all duration-200 transform hover:scale-105 active:scale-95 shadow-xl hover:shadow-2xl active:shadow-lg flex items-center gap-1"
                    >
                        {{ buttonText }}
                        <svg v-if="hasVarians" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    menu: {
        type: Object,
        required: true
    }
});

defineEmits(['order']);

const formatPrice = (price) => {
    return new Intl.NumberFormat('id-ID').format(price);
};

const hasVarians = computed(() => {
    return props.menu.varians && props.menu.varians.length > 0;
});

const priceRange = computed(() => {
    if (!hasVarians.value) return null;

    const prices = props.menu.varians.map(v => v.harga_jual);
    const minPrice = Math.min(...prices);
    const maxPrice = Math.max(...prices);

    return minPrice !== maxPrice ? { min: minPrice, max: maxPrice } : null;
});

const displayPrice = computed(() => {
    if (!hasVarians.value) {
        return 'Rp ' + formatPrice(props.menu.price);
    }

    if (priceRange.value) {
        return `Rp ${formatPrice(priceRange.value.min)} - ${formatPrice(priceRange.value.max)}`;
    }

    // All variants have same price
    return 'Rp ' + formatPrice(props.menu.varians[0].harga_jual);
});

const buttonText = computed(() => {
    return hasVarians.value ? 'Pilih Varian' : 'Pesan Sekarang';
});
</script>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
