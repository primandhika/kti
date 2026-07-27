<template>
    <div
        class="group bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden flex relative transition-all duration-200"
        :class="menu.stok <= 0 ? 'opacity-60' : 'hover:shadow-md hover:border-[#ccb27f]/60'"
    >
        <!-- Out of Stock Overlay -->
        <div v-if="menu.stok <= 0" class="absolute inset-0 bg-gray-900/5 z-10 pointer-events-none rounded-xl"></div>

        <!-- Image Container - Left Square 1:1 -->
        <div class="relative w-24 sm:w-28 flex-shrink-0 overflow-hidden bg-[#eae0cc] rounded-l-xl" style="aspect-ratio:1/1">
            <img
                v-if="menu.image"
                :src="menu.image"
                :alt="menu.name"
                :class="menu.stok <= 0 ? 'grayscale' : 'group-hover:scale-105'"
                class="w-full h-full object-cover transition-transform duration-500"
            />
            <div v-else class="w-full h-full flex items-center justify-center text-[#ccb27f]">
                <svg class="w-10 h-10" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
                </svg>
            </div>

            <!-- Badges overlay -->
            <div class="absolute top-1.5 left-1.5 flex flex-col gap-1">
                <span v-if="menu.diskon_aktif" class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-green-500 text-white leading-none">
                    <template v-if="menu.diskon_tipe === 'persen'">{{ menu.diskon_nilai }}%</template>
                    <template v-else>OFF</template>
                </span>
            </div>
            <div v-if="menu.is_best_seller" class="absolute top-1.5 right-1.5">
                <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-blue-600 text-white leading-none">Top</span>
            </div>
            <div v-else-if="menu.stok > 0 && menu.stok < 10" class="absolute top-1.5 right-1.5">
                <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-yellow-500 text-white leading-none">Sisa</span>
            </div>
        </div>

        <!-- Content -->
        <div class="flex-1 p-3 flex flex-col min-w-0 min-h-[88px]">
            <!-- Top row: name + varian badge -->
            <div class="flex items-start justify-between gap-2 mb-0.5">
                <h3 class="text-sm font-bold text-gray-900 line-clamp-2 leading-snug flex-1 min-w-0">
                    {{ menu.name }}
                </h3>
                <span
                    v-if="hasVarians"
                    class="flex-shrink-0 mt-0.5 px-1.5 py-0.5 text-[10px] font-semibold rounded bg-[#f4efe5] text-[#7a5100] border border-[#ccb27f]/50 leading-none"
                >
                    {{ menu.varians.length }} pilihan
                </span>
            </div>

            <!-- Category / Sub Kategori -->
            <span class="text-[11px] text-gray-400 mb-1.5 truncate">
                {{ menu.sub_kategori || menu.category?.name }}
                <span v-if="menu.work_unit?.name" class="text-[#c1a366]"> &middot; {{ menu.work_unit.name }}</span>
            </span>

            <!-- Spacer -->
            <div class="flex-1"></div>

            <!-- Bottom: price + button -->
            <div class="flex items-center justify-between gap-2 mt-1">
                <div class="min-w-0">
                    <p v-if="menu.stok <= 0" class="text-xs font-semibold text-red-500">Habis</p>
                    <template v-else>
                        <p v-if="!hasVarians && menu.diskon_aktif" class="text-[10px] text-gray-400 line-through leading-none mb-0.5">
                            Rp {{ formatPrice(menu.price) }}
                        </p>
                        <p v-if="hasVarians && priceRange" class="text-[10px] text-gray-400 leading-none mb-0.5">Mulai dari</p>
                        <p class="text-sm font-bold leading-none" :class="menu.diskon_aktif && !hasVarians ? 'text-green-700' : 'text-[#996600]'">
                            {{ displayPrice }}
                        </p>
                    </template>
                </div>

                <button
                    @click="$emit('order', menu)"
                    :disabled="menu.stok <= 0"
                    class="flex-shrink-0 px-3 py-1.5 rounded-lg text-xs font-bold transition-all duration-150 disabled:opacity-40 disabled:cursor-not-allowed flex items-center gap-1 shadow-sm"
                    :class="menu.stok <= 0
                        ? 'bg-gray-200 text-gray-500'
                        : 'bg-[#996600] text-white hover:bg-[#7a5100] active:scale-95'"
                >
                    {{ buttonText }}
                    <svg v-if="hasVarians && menu.stok > 0" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    menu: { type: Object, required: true }
});

defineEmits(['order']);

const formatPrice = (price) => new Intl.NumberFormat('id-ID').format(price);

const hasVarians = computed(() => props.menu.varians?.length > 0);

const priceRange = computed(() => {
    if (!hasVarians.value) return null;
    const prices = props.menu.varians.map(v => v.harga_jual);
    const min = Math.min(...prices), max = Math.max(...prices);
    return min !== max ? { min, max } : null;
});

const effectivePrice = computed(() => {
    if (props.menu.diskon_aktif && !hasVarians.value) {
        return Math.max(0, props.menu.price - (props.menu.nominal_diskon || 0));
    }
    return props.menu.price;
});

const displayPrice = computed(() => {
    if (!hasVarians.value) return 'Rp ' + formatPrice(effectivePrice.value);
    if (priceRange.value) return 'Rp ' + formatPrice(priceRange.value.min);
    return 'Rp ' + formatPrice(props.menu.varians[0].harga_jual);
});

const buttonText = computed(() => {
    if (props.menu.stok <= 0) return 'Habis';
    return hasVarians.value ? 'Pilih' : 'Pesan';
});
</script>
