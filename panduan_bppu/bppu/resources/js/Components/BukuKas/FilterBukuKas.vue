<template>
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Filter Jenis Buku Kas -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Buku Kas</label>
                <select
                    v-model="localFilters.jenis_buku_kas_id"
                    @change="applyFilters"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-[#996600] transition-all text-sm"
                >
                    <option value="">Semua Jenis</option>
                    <option v-for="jenis in jenisBukuKasList" :key="jenis.id" :value="jenis.id">
                        {{ jenis.nama }}
                    </option>
                </select>
            </div>

            <!-- Filter Bulan -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Bulan</label>
                <select
                    v-model="localFilters.month"
                    @change="applyFilters"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-[#996600] transition-all text-sm"
                >
                    <option value="">Semua Bulan</option>
                    <option v-for="month in months" :key="month.value" :value="month.value">
                        {{ month.label }}
                    </option>
                </select>
            </div>

            <!-- Filter Tahun -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tahun</label>
                <select
                    v-model="localFilters.year"
                    @change="applyFilters"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-[#996600] transition-all text-sm"
                >
                    <option value="">Semua Tahun</option>
                    <option v-for="year in years" :key="year" :value="year">
                        {{ year }}
                    </option>
                </select>
            </div>

            <!-- Search -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Cari</label>
                <input
                    v-model="localFilters.search"
                    @input="debounceSearch"
                    type="text"
                    placeholder="Cari nama atau keterangan..."
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-[#996600] transition-all text-sm"
                />
            </div>
        </div>

        <!-- Reset Filter -->
        <div class="mt-4 flex justify-end" v-if="hasActiveFilters">
            <button
                @click="resetFilters"
                class="text-sm text-[#996600] hover:text-[#6b4700] font-medium transition-colors"
            >
                Reset Filter
            </button>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    filters: {
        type: Object,
        default: () => ({}),
    },
    jenisBukuKasList: {
        type: Array,
        default: () => [],
    },
});

const localFilters = ref({
    jenis_buku_kas_id: props.filters.jenis_buku_kas_id || '',
    month: props.filters.month || '',
    year: props.filters.year || '',
    search: props.filters.search || '',
});

const months = [
    { value: '1', label: 'Januari' },
    { value: '2', label: 'Februari' },
    { value: '3', label: 'Maret' },
    { value: '4', label: 'April' },
    { value: '5', label: 'Mei' },
    { value: '6', label: 'Juni' },
    { value: '7', label: 'Juli' },
    { value: '8', label: 'Agustus' },
    { value: '9', label: 'September' },
    { value: '10', label: 'Oktober' },
    { value: '11', label: 'November' },
    { value: '12', label: 'Desember' },
];

const currentYear = new Date().getFullYear();
const years = Array.from({ length: 10 }, (_, i) => currentYear - i);

const hasActiveFilters = computed(() => {
    return Object.values(localFilters.value).some(val => val !== '');
});

let debounceTimer = null;
const debounceSearch = () => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        applyFilters();
    }, 500);
};

const applyFilters = () => {
    const params = {};
    if (localFilters.value.jenis_buku_kas_id) params.jenis_buku_kas_id = localFilters.value.jenis_buku_kas_id;
    if (localFilters.value.month) params.month = localFilters.value.month;
    if (localFilters.value.year) params.year = localFilters.value.year;
    if (localFilters.value.search) params.search = localFilters.value.search;

    router.get(route('admin.bukukas.index'), params, {
        preserveState: true,
        preserveScroll: true,
    });
};

const resetFilters = () => {
    localFilters.value = {
        jenis_buku_kas_id: '',
        month: '',
        year: '',
        search: '',
    };
    applyFilters();
};
</script>
