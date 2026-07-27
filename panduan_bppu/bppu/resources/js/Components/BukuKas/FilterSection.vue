<template>
    <div class="p-4 md:p-6 border-b-2 border-[#eae0cc] bg-[#f4efe5]">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <h2 class="text-lg md:text-xl font-bold text-gray-900 flex items-center">
                <svg class="w-5 h-5 md:w-6 md:h-6 mr-2 text-[#996600]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Daftar Transaksi
            </h2>
            <button
                @click="$emit('add-transaction')"
                class="bg-[#996600] text-white px-4 md:px-6 py-2 md:py-2.5 rounded-lg hover:bg-[#6b4700] transition-all duration-300 flex items-center justify-center space-x-2 shadow-md hover:shadow-xl hover:scale-105 active:scale-95"
            >
                <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span class="text-sm md:text-base font-semibold">Tambah Transaksi</span>
            </button>
        </div>

        <!-- Filters - Super Compact One Row -->
        <div class="mt-3 flex flex-wrap items-center gap-1.5">
            <!-- Search -->
            <div class="relative flex-shrink-0">
                <input
                    :value="filters.search"
                    @input="$emit('update:filters', { ...filters, search: $event.target.value })"
                    type="text"
                    placeholder="Cari..."
                    class="w-32 md:w-40 pl-7 pr-2 py-1.5 border border-gray-300 rounded focus:ring-1 focus:ring-[#996600] focus:border-[#996600] text-xs"
                >
                <svg class="w-3.5 h-3.5 absolute left-2 top-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>

            <!-- Date From -->
            <input
                :value="filters.date_from"
                @change="$emit('update:filters', { ...filters, date_from: $event.target.value })"
                type="date"
                placeholder="Dari"
                class="w-28 md:w-32 px-2 py-1.5 border border-gray-300 rounded focus:ring-1 focus:ring-[#996600] focus:border-[#996600] text-xs"
            >

            <!-- Date To -->
            <input
                :value="filters.date_to"
                @change="$emit('update:filters', { ...filters, date_to: $event.target.value })"
                type="date"
                placeholder="Sampai"
                class="w-28 md:w-32 px-2 py-1.5 border border-gray-300 rounded focus:ring-1 focus:ring-[#996600] focus:border-[#996600] text-xs"
            >

            <!-- Type Filter -->
            <select
                :value="filters.type"
                @change="$emit('update:filters', { ...filters, type: $event.target.value })"
                class="px-2 py-1.5 border border-gray-300 rounded focus:ring-1 focus:ring-[#996600] focus:border-[#996600] bg-white text-xs"
            >
                <option value="">Tipe</option>
                <option value="pemasukan">Masuk</option>
                <option value="pengeluaran">Keluar</option>
            </select>

            <!-- Kategori Filter -->
            <select
                :value="filters.kategori"
                @change="$emit('update:filters', { ...filters, kategori: $event.target.value })"
                class="px-2 py-1.5 border border-gray-300 rounded focus:ring-1 focus:ring-[#996600] focus:border-[#996600] bg-white text-xs max-w-[100px] md:max-w-none"
            >
                <option value="">Kategori</option>
                <option v-for="kat in kategoriList" :key="kat.id" :value="kat.nama">{{ kat.nama }}</option>
            </select>

            <!-- Jenis Transaksi Filter -->
            <select
                :value="filters.jenis_transaksi"
                @change="$emit('update:filters', { ...filters, jenis_transaksi: $event.target.value })"
                class="px-2 py-1.5 border border-gray-300 rounded focus:ring-1 focus:ring-[#996600] focus:border-[#996600] bg-white text-xs max-w-[100px] md:max-w-none"
            >
                <option value="">Jenis</option>
                <option v-for="jenis in jenisTransaksiList" :key="jenis" :value="jenis">{{ jenis }}</option>
            </select>

            <!-- Unit Kerja Filter -->
            <select
                :value="filters.unit_kerja_id"
                @change="$emit('update:filters', { ...filters, unit_kerja_id: $event.target.value })"
                class="px-2 py-1.5 border border-gray-300 rounded focus:ring-1 focus:ring-[#996600] focus:border-[#996600] bg-white text-xs max-w-[100px] md:max-w-none"
            >
                <option value="">Unit</option>
                <option v-for="unit in workUnits" :key="unit.id" :value="unit.id">{{ unit.name }}</option>
            </select>

            <!-- Reset Button -->
            <button
                @click="$emit('reset-filters')"
                class="px-2 py-1.5 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 transition-colors text-xs font-medium flex items-center"
                title="Reset Filter"
            >
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                <span class="ml-1 hidden sm:inline">Reset</span>
            </button>

            <!-- Export Dropdown -->
            <div class="relative ml-auto" v-if="hasTransactions">
                <button
                    @click="toggleExportDropdown"
                    class="px-2 py-1.5 bg-green-600 text-white rounded hover:bg-green-700 transition-colors text-xs font-medium flex items-center"
                >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span class="ml-1 hidden sm:inline">Export</span>
                </button>
                <!-- Dropdown Menu -->
                <div
                    v-if="showExportDropdown"
                    @click.stop
                    class="export-dropdown-menu absolute right-0 mt-2 w-36 bg-white rounded-lg shadow-xl border border-gray-200 z-10"
                >
                    <button
                        @click="$emit('export', 'csv')"
                        class="w-full text-left block px-3 py-2 text-xs text-gray-700 hover:bg-gray-100 rounded-t-lg transition-colors"
                    >
                        <div class="flex items-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span>Export CSV</span>
                        </div>
                    </button>
                    <button
                        @click="$emit('export', 'xlsx')"
                        class="w-full text-left block px-3 py-2 text-xs text-gray-700 hover:bg-gray-100 rounded-b-lg transition-colors"
                    >
                        <div class="flex items-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span>Export XLSX</span>
                        </div>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';

defineProps({
    filters: {
        type: Object,
        required: true
    },
    kategoriList: {
        type: Array,
        default: () => []
    },
    jenisTransaksiList: {
        type: Array,
        default: () => []
    },
    workUnits: {
        type: Array,
        default: () => []
    },
    hasTransactions: Boolean,
});

defineEmits(['update:filters', 'reset-filters', 'add-transaction', 'export']);

const showExportDropdown = ref(false);

const toggleExportDropdown = (event) => {
    event.stopPropagation();
    showExportDropdown.value = !showExportDropdown.value;
};

const closeExportDropdown = () => {
    showExportDropdown.value = false;
};

onMounted(() => {
    document.addEventListener('click', closeExportDropdown);
});

onUnmounted(() => {
    document.removeEventListener('click', closeExportDropdown);
});
</script>
