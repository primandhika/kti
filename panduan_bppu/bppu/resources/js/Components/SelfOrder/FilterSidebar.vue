<template>
    <div class="bg-white rounded-xl shadow-md p-6 sticky top-24">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Filter Menu</h3>

        <!-- Search -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Cari Menu</label>
            <input
                type="text"
                v-model="localFilters.search"
                @input="updateFilters"
                placeholder="Nama menu atau varian..."
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-900 focus:border-transparent"
            />
        </div>

        <!-- Kantin Filter -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Kantin</label>
            <select
                v-model="localFilters.work_unit"
                @change="updateFilters"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-900 focus:border-transparent"
            >
                <option value="">Semua Kantin</option>
                <option v-for="workUnit in workUnits" :key="workUnit.id" :value="workUnit.id">
                    {{ workUnit.name }}
                </option>
            </select>
        </div>

        <!-- Category Filter -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
            <select
                v-model="localFilters.kategori"
                @change="updateFilters"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-900 focus:border-transparent"
            >
                <option value="">Semua Kategori</option>
                <option v-for="category in categories" :key="category.id" :value="category.id">
                    {{ category.name }}
                </option>
            </select>
        </div>

        <!-- Price Range -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Rentang Harga</label>
            <div class="flex gap-2">
                <input
                    type="number"
                    v-model.number="localFilters.min_price"
                    @input="updateFilters"
                    placeholder="Min"
                    min="0"
                    class="w-1/2 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-900 focus:border-transparent"
                />
                <input
                    type="number"
                    v-model.number="localFilters.max_price"
                    @input="updateFilters"
                    placeholder="Max"
                    min="0"
                    class="w-1/2 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-900 focus:border-transparent"
                />
            </div>
        </div>

        <!-- Sort Options -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Urutkan</label>
            <select
                v-model="localFilters.sort_by"
                @change="updateFilters"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-900 focus:border-transparent mb-2"
            >
                <option value="display_order">Urutan Default</option>
                <option value="name">Nama</option>
                <option value="price">Harga</option>
            </select>
            <select
                v-model="localFilters.sort_order"
                @change="updateFilters"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-900 focus:border-transparent"
            >
                <option value="asc">A-Z / Murah-Mahal</option>
                <option value="desc">Z-A / Mahal-Murah</option>
            </select>
        </div>

        <!-- Items per page -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Tampilkan</label>
            <select
                v-model.number="localFilters.per_page"
                @change="updateFilters"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-900 focus:border-transparent"
            >
                <option :value="12">12 per halaman</option>
                <option :value="24">24 per halaman</option>
                <option :value="48">48 per halaman</option>
            </select>
        </div>

        <!-- Reset Button -->
        <button
            @click="resetFilters"
            class="w-full px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold rounded-lg transition-colors duration-300"
        >
            Reset Filter
        </button>
    </div>
</template>

<script setup>
import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    filters: {
        type: Object,
        required: true
    },
    categories: {
        type: Array,
        default: () => []
    },
    workUnits: {
        type: Array,
        default: () => []
    }
});

const localFilters = ref({ ...props.filters });

let debounceTimer = null;

const updateFilters = () => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        router.get('/kantin/self-order', localFilters.value, {
            preserveState: true,
            preserveScroll: true,
        });
    }, 500);
};

const resetFilters = () => {
    localFilters.value = {
        search: '',
        kategori: '',
        work_unit: '',
        min_price: '',
        max_price: '',
        sort_by: 'display_order',
        sort_order: 'asc',
        per_page: 12
    };
    router.get('/kantin/self-order', localFilters.value);
};

watch(() => props.filters, (newFilters) => {
    localFilters.value = { ...newFilters };
}, { deep: true });
</script>
