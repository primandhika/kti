<template>
    <div class="bg-white border-b border-[#ccb27f]/40 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center gap-2 overflow-x-auto scrollbar-hide py-2">
                <!-- Toggle Advanced Filter -->
                <button
                    @click="showAdvanced = !showAdvanced"
                    class="flex-shrink-0 flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold border transition-colors"
                    :class="hasActiveFilters
                        ? 'bg-[#996600] text-white border-[#996600]'
                        : 'bg-white text-gray-700 border-gray-300 hover:border-[#996600] hover:text-[#996600]'"
                >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    Filter
                    <span v-if="activeFilterCount > 0" class="bg-white text-[#996600] rounded-full w-4 h-4 flex items-center justify-center text-[10px] font-bold leading-none">
                        {{ activeFilterCount }}
                    </span>
                </button>

                <div v-if="subKategories && subKategories.length > 0" class="w-px h-5 bg-gray-200 flex-shrink-0"></div>

                <!-- Sub Kategori Pills -->
                <button
                    v-if="subKategories && subKategories.length > 0"
                    @click="applySubKategori(null)"
                    class="flex-shrink-0 px-3 py-1.5 rounded-full text-xs font-semibold border transition-colors whitespace-nowrap"
                    :class="!localFilters.sub_kategori
                        ? 'bg-[#b7934c] text-white border-[#b7934c]'
                        : 'bg-white text-gray-700 border-gray-300 hover:border-[#b7934c] hover:text-[#b7934c]'"
                >
                    Semua
                </button>
                <button
                    v-for="subKat in subKategories"
                    :key="subKat"
                    @click="applySubKategori(subKat)"
                    class="flex-shrink-0 px-3 py-1.5 rounded-full text-xs font-semibold border transition-colors whitespace-nowrap"
                    :class="localFilters.sub_kategori === subKat
                        ? 'bg-[#b7934c] text-white border-[#b7934c]'
                        : 'bg-white text-gray-700 border-gray-300 hover:border-[#b7934c] hover:text-[#b7934c]'"
                >
                    {{ subKat }}
                </button>

                <!-- Reset Filter -->
                <button
                    v-if="hasActiveFilters"
                    @click="resetFilters"
                    class="flex-shrink-0 flex items-center gap-1 px-3 py-1.5 rounded-full text-xs font-semibold border border-red-300 text-red-600 hover:bg-red-50 transition-colors whitespace-nowrap ml-1"
                >
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Reset
                </button>
            </div>
        </div>

        <!-- Advanced Filter Panel -->
        <transition
            enter-active-class="transition-all duration-200 ease-out"
            enter-from-class="opacity-0 max-h-0"
            enter-to-class="opacity-100 max-h-96"
            leave-active-class="transition-all duration-150 ease-in"
            leave-from-class="opacity-100 max-h-96"
            leave-to-class="opacity-0 max-h-0"
        >
            <div v-show="showAdvanced" class="overflow-hidden border-t border-[#ccb27f]/30 bg-[#f4efe5]/60">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
                    <div class="flex flex-wrap gap-3 items-end">
                        <!-- Harga Min -->
                        <div class="flex flex-col gap-1">
                            <label class="text-xs font-semibold text-gray-600">Harga Min</label>
                            <input
                                type="number"
                                v-model.number="localFilters.min_price"
                                @input="applyFilters"
                                placeholder="0"
                                min="0"
                                class="px-3 py-1.5 text-xs border border-gray-300 rounded-lg focus:ring-1 focus:ring-[#996600] focus:border-[#996600] bg-white w-28"
                            />
                        </div>

                        <!-- Harga Max -->
                        <div class="flex flex-col gap-1">
                            <label class="text-xs font-semibold text-gray-600">Harga Max</label>
                            <input
                                type="number"
                                v-model.number="localFilters.max_price"
                                @input="applyFilters"
                                placeholder="Max"
                                min="0"
                                class="px-3 py-1.5 text-xs border border-gray-300 rounded-lg focus:ring-1 focus:ring-[#996600] focus:border-[#996600] bg-white w-28"
                            />
                        </div>

                        <!-- Urutkan -->
                        <div class="flex flex-col gap-1">
                            <label class="text-xs font-semibold text-gray-600">Urutkan</label>
                            <select
                                v-model="localFilters.sort_by"
                                @change="applyFilters"
                                class="px-3 py-1.5 text-xs border border-gray-300 rounded-lg focus:ring-1 focus:ring-[#996600] focus:border-[#996600] bg-white min-w-[150px]"
                            >
                                <option value="display_order">Urutan Default</option>
                                <option value="name">Nama A-Z</option>
                                <option value="price">Harga</option>
                            </select>
                        </div>

                        <!-- Tampilkan per halaman -->
                        <div class="flex flex-col gap-1">
                            <label class="text-xs font-semibold text-gray-600">Tampilkan</label>
                            <select
                                v-model.number="localFilters.per_page"
                                @change="applyFilters"
                                class="px-3 py-1.5 text-xs border border-gray-300 rounded-lg focus:ring-1 focus:ring-[#996600] focus:border-[#996600] bg-white min-w-[130px]"
                            >
                                <option :value="12">12 per halaman</option>
                                <option :value="24">24 per halaman</option>
                                <option :value="48">48 per halaman</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </transition>
    </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    filters: { type: Object, required: true },
    subKategories: { type: Array, default: () => [] },
    mejaQrToken: { type: String, default: null },
});

const localFilters = ref({ ...props.filters });
const showAdvanced = ref(false);
let debounceTimer = null;

watch(() => props.filters, (newFilters) => {
    localFilters.value = { ...newFilters };
}, { deep: true });

const hasActiveFilters = computed(() => {
    const f = localFilters.value;
    return !!(f.min_price || f.max_price || (f.sort_by && f.sort_by !== 'display_order'));
});

const activeFilterCount = computed(() => {
    const f = localFilters.value;
    let count = 0;
    if (f.min_price) count++;
    if (f.max_price) count++;
    if (f.sort_by && f.sort_by !== 'display_order') count++;
    return count;
});

const buildCleanParams = (overrides = {}) => {
    const f = { ...localFilters.value, ...overrides };
    const params = {};
    if (f.search)                                       params.search       = f.search;
    if (f.sub_kategori)                                 params.sub_kategori = f.sub_kategori;
    if (f.work_unit)                                    params.work_unit    = f.work_unit;
    if (f.min_price)                                    params.min_price    = f.min_price;
    if (f.max_price)                                    params.max_price    = f.max_price;
    if (f.sort_by && f.sort_by !== 'display_order')     params.sort_by      = f.sort_by;
    if (f.per_page && f.per_page !== 12)                params.per_page     = f.per_page;
    if (overrides.page && overrides.page > 1)           params.page         = overrides.page;
    if (props.mejaQrToken)                              params.meja         = props.mejaQrToken;
    return params;
};

const applyFilters = () => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        router.get('/belanja/self-order', buildCleanParams({ page: 1 }), {
            preserveState: true,
            preserveScroll: true,
        });
    }, 400);
};

const applySubKategori = (subKat) => {
    localFilters.value.sub_kategori = subKat;
    router.get('/belanja/self-order', buildCleanParams({ sub_kategori: subKat, page: 1 }), {
        preserveState: true,
        preserveScroll: false,
    });
};

const resetFilters = () => {
    localFilters.value = {
        search:       localFilters.value.search    || '',
        work_unit:    localFilters.value.work_unit || '',
        sub_kategori: '',
        min_price:    '',
        max_price:    '',
        sort_by:      'display_order',
        per_page:     12,
    };
    router.get('/belanja/self-order', buildCleanParams({ page: 1 }), {
        preserveState: true,
        preserveScroll: true,
    });
};
</script>

<style scoped>
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
</style>
