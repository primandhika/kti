<template>
    <AdminLayout
        page-title="Buku Kas"
        :page-subtitle="isSuperAdmin ? 'Kelola semua buku kas' : 'Kelola buku kas Anda'"
    >
        <div class="p-6">
            <!-- All Controls in One Container -->
            <div class="mb-4 bg-white rounded-lg shadow-sm border border-gray-200 p-3 sm:p-4">
                <!-- Filter Fields Row -->
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-2 mb-2">
                    <select
                        v-model="localFilters.jenis_buku_kas_id"
                        @change="applyFilter('jenis_buku_kas_id')"
                        class="w-full px-2.5 py-1.5 sm:px-3 sm:py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-[#996600] transition-all text-sm"
                    >
                        <option value="">Semua Jenis</option>
                        <option v-for="jenis in jenisBukuKasList" :key="jenis.id" :value="jenis.id">
                            {{ jenis.nama }}
                        </option>
                    </select>

                    <select
                        v-model="localFilters.month"
                        @change="applyFilter('month')"
                        class="w-full px-2.5 py-1.5 sm:px-3 sm:py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-[#996600] transition-all text-sm"
                    >
                        <option value="">Semua Bulan</option>
                        <option v-for="month in months" :key="month.value" :value="month.value">
                            {{ month.label }}
                        </option>
                    </select>

                    <select
                        v-model="localFilters.year"
                        @change="applyFilter('year')"
                        class="w-full px-2.5 py-1.5 sm:px-3 sm:py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-[#996600] transition-all text-sm"
                    >
                        <option value="">Semua Tahun</option>
                        <option v-for="year in years" :key="year" :value="year">
                            {{ year }}
                        </option>
                    </select>

                    <input
                        v-model="localFilters.search"
                        @input="debounceSearch"
                        type="text"
                        placeholder="Cari..."
                        class="w-full px-2.5 py-1.5 sm:px-3 sm:py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-[#996600] transition-all text-sm"
                    />
                </div>

                <!-- Controls Row: Reset | Sort & Group | Actions -->
                <div class="flex flex-col gap-2">
                    <!-- Row 1: Sort & Group -->
                    <div v-if="bukuKas.data && bukuKas.data.length > 0" class="flex flex-wrap items-center gap-2">
                        <button v-if="hasActiveFilters" @click="resetFilters" class="text-xs sm:text-sm text-[#996600] hover:text-[#6b4700] font-medium transition-colors whitespace-nowrap">
                            Reset
                        </button>
                        <SortControl
                            :sortField="filters.sort_field || 'created_at'"
                            :sortDirection="filters.sort_direction || 'desc'"
                            :filters="filters"
                        />
                        <GroupControl v-model="groupBy" />
                    </div>
                    <!-- Row 2: Action Buttons -->
                    <div class="flex items-center gap-1.5 sm:gap-2 flex-wrap">
                        <a v-if="isSuperAdmin" :href="route('admin.bukukas.recycle-bin')" class="bg-gray-600 text-white px-2 py-1.5 sm:px-3 sm:py-2 rounded-lg hover:bg-gray-700 transition-all duration-300 flex items-center gap-1.5 shadow-sm text-xs sm:text-sm">
                            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            <span class="font-semibold hidden sm:inline">Sampah</span>
                        </a>
                        <div v-if="bukuKas.data && bukuKas.data.length > 0" class="relative">
                            <button @click="toggleExportDropdown" class="bg-green-600 text-white px-2 py-1.5 sm:px-3 sm:py-2 rounded-lg hover:bg-green-700 transition-all duration-300 flex items-center gap-1.5 shadow-sm text-xs sm:text-sm">
                                <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <span class="font-semibold">Export</span>
                            </button>
                            <div v-if="showExportDropdown" @click.stop class="export-dropdown-menu absolute right-0 mt-2 w-40 bg-white rounded-lg shadow-xl border border-gray-200 z-10">
                                <button @click="exportData('csv')" class="w-full text-left block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-t-lg transition-colors">
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <span>Export CSV</span>
                                    </div>
                                </button>
                                <button @click="exportData('xlsx')" class="w-full text-left block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-b-lg transition-colors">
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <span>Export XLSX</span>
                                    </div>
                                </button>
                            </div>
                        </div>
                        <button v-if="!isReadOnly" @click="openCreateModal" class="bg-[#996600] text-white px-2 py-1.5 sm:px-3 sm:py-2 rounded-lg hover:bg-[#6b4700] transition-all duration-300 flex items-center gap-1.5 shadow-sm text-xs sm:text-sm">
                            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            <span class="font-semibold">Tambah</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-if="!bukuKas.data || bukuKas.data.length === 0" class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada buku kas</h3>
                <p class="mt-1 text-sm text-gray-500">Mulai dengan membuat buku kas baru</p>
            </div>

            <!-- Cards Grid -->
            <div v-else>
                <div v-for="group in groupedBukuKas" :key="group.key" class="mb-4">
                    <!-- Group Header -->
                    <div v-if="group.label" class="mb-2 pb-1.5 border-b-2 border-[#996600]">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1.5">
                            <div>
                                <h2 class="text-base font-bold text-[#6b4700]">{{ group.label }}</h2>
                                <p class="text-xs text-gray-600">{{ group.items.length }} buku kas</p>
                            </div>
                            <div class="flex items-center gap-2 sm:gap-3 text-xs flex-wrap">
                                <span class="px-2 py-1 rounded bg-green-50 border border-green-200 text-green-700 font-semibold whitespace-nowrap">+{{ formatNumber(group.totalPemasukan) }}</span>
                                <span class="px-2 py-1 rounded bg-red-50 border border-red-200 text-red-700 font-semibold whitespace-nowrap">-{{ formatNumber(group.totalPengeluaran) }}</span>
                                <span :class="[
                                    'px-2 py-1 rounded font-bold whitespace-nowrap',
                                    group.totalSaldo >= 0
                                        ? 'bg-[#eae0cc] border border-[#996600] text-[#6b4700]'
                                        : 'bg-red-50 border border-red-300 text-red-700'
                                ]">
                                    {{ formatNumber(group.totalSaldo) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Cards -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2 sm:gap-2.5">
                        <BukuKasCard
                            v-for="buku in group.items"
                            :key="buku.id"
                            :buku="buku"
                            :jenisBukuKasList="jenisBukuKasList"
                            :isSuperAdmin="isSuperAdmin"
                            :isReadOnly="isReadOnly"
                            @click="openBukuKas(buku.id)"
                            @edit="openEditModal"
                            @delete="confirmDelete"
                        />
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="bukuKas.data && bukuKas.data.length > 0">
                <div v-if="bukuKas.last_page > 1" class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-3 rounded-lg shadow-sm mt-6">
                    <div class="flex flex-1 justify-between sm:hidden">
                        <a
                            v-if="bukuKas.prev_page_url"
                            :href="bukuKas.prev_page_url"
                            class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                        >
                            Previous
                        </a>
                        <a
                            v-if="bukuKas.next_page_url"
                            :href="bukuKas.next_page_url"
                            class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                        >
                            Next
                        </a>
                    </div>
                    <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700">
                                Showing
                                <span class="font-medium">{{ bukuKas.from }}</span>
                                to
                                <span class="font-medium">{{ bukuKas.to }}</span>
                                of
                                <span class="font-medium">{{ bukuKas.total }}</span>
                                results
                            </p>
                        </div>
                        <div>
                            <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                                <a
                                    v-if="bukuKas.prev_page_url"
                                    :href="bukuKas.prev_page_url"
                                    class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0"
                                >
                                    <span class="sr-only">Previous</span>
                                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd" />
                                    </svg>
                                </a>
                                <a
                                    v-for="page in bukuKas.links.slice(1, -1)"
                                    :key="page.label"
                                    :href="page.url"
                                    :class="[
                                        page.active
                                            ? 'z-10 bg-[#996600] text-white focus:z-20 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#996600]'
                                            : 'text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0',
                                        'relative inline-flex items-center px-4 py-2 text-sm font-semibold'
                                    ]"
                                >
                                    {{ page.label }}
                                </a>
                                <a
                                    v-if="bukuKas.next_page_url"
                                    :href="bukuKas.next_page_url"
                                    class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0"
                                >
                                    <span class="sr-only">Next</span>
                                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                                    </svg>
                                </a>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Create/Edit Modal -->
        <Teleport to="body">
            <div v-if="showModal" class="fixed inset-0 z-50 overflow-y-auto">
                <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" @click="closeModal"></div>
                <div class="flex min-h-screen items-center justify-center p-4">
                    <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-xl font-semibold text-gray-900">{{ isEditing ? 'Edit' : 'Tambah' }} Buku Kas</h3>
                            <button @click="closeModal" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <form @submit.prevent="submitForm">
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Buku Kas <span class="text-red-500">*</span></label>
                                    <input v-model="form.nama" type="text" required class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-[#996600] transition-all duration-200" placeholder="Contoh: Buku Kas Januari 2026">
                                    <p v-if="form.errors.nama" class="text-red-500 text-sm mt-1">{{ form.errors.nama }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Buku Kas</label>
                                    <select v-model="form.jenis_buku_kas_id" class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-[#996600] transition-all duration-200">
                                        <option value="">Pilih Jenis Buku Kas</option>
                                        <option v-for="jenis in jenisBukuKasList" :key="jenis.id" :value="jenis.id">
                                            {{ jenis.nama }} ({{ jenis.kode }})
                                        </option>
                                    </select>
                                    <p v-if="form.errors.jenis_buku_kas_id" class="text-red-500 text-sm mt-1">{{ form.errors.jenis_buku_kas_id }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Keterangan</label>
                                    <textarea v-model="form.keterangan" rows="3" class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-[#996600] transition-all duration-200" placeholder="Deskripsi atau catatan..."></textarea>
                                    <p v-if="form.errors.keterangan" class="text-red-500 text-sm mt-1">{{ form.errors.keterangan }}</p>
                                </div>
                            </div>

                            <div class="flex justify-end space-x-3 mt-6">
                                <button type="button" @click="closeModal" class="px-5 py-2 border-2 border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 hover:scale-105">Batal</button>
                                <button type="submit" :disabled="form.processing" class="px-5 py-2 bg-[#996600] text-white rounded-lg hover:bg-[#6b4700] disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-300 hover:scale-105 hover:shadow-lg">
                                    {{ form.processing ? 'Menyimpan...' : 'Simpan' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </Teleport>

        <!-- Delete Confirmation Modal (Sysadmin) -->
        <Teleport to="body">
            <div v-if="showDeleteModal && deletingBuku" class="fixed inset-0 z-50 overflow-y-auto">
                <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" @click="closeDeleteModal"></div>
                <div class="flex min-h-screen items-center justify-center p-4">
                    <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-xl font-semibold text-gray-900">Hapus Buku Kas</h3>
                            <button @click="closeDeleteModal" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <div class="mb-6">
                            <p class="text-gray-700 mb-2">Anda akan menghapus buku kas:</p>
                            <p class="font-semibold text-gray-900 mb-4">{{ deletingBuku.nama }}</p>
                            <p class="text-sm text-gray-600">Pilih jenis penghapusan:</p>
                        </div>

                        <div class="space-y-3">
                            <button
                                @click="doSoftDelete"
                                class="w-full px-4 py-3 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-all duration-300 flex items-center justify-center gap-2"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                <div class="text-left">
                                    <div class="font-semibold">Pindahkan ke Tempat Sampah</div>
                                    <div class="text-xs text-yellow-100">Data bisa dipulihkan kembali</div>
                                </div>
                            </button>

                            <button
                                @click="doPermanentDelete"
                                class="w-full px-4 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-all duration-300 flex items-center justify-center gap-2"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <div class="text-left">
                                    <div class="font-semibold">Hapus Permanen</div>
                                    <div class="text-xs text-red-100">Data tidak bisa dikembalikan</div>
                                </div>
                            </button>

                            <button
                                @click="closeDeleteModal"
                                class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 hover:border-gray-400 transition-all duration-200"
                            >
                                Batal
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </Teleport>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import BukuKasCard from '@/Components/BukuKas/BukuKasCard.vue';
import SortControl from '@/Components/BukuKas/SortControl.vue';
import GroupControl from '@/Components/BukuKas/GroupControl.vue';
import { router, useForm, usePage } from '@inertiajs/vue3';
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { useToast } from 'vue-toastification';

const toast = useToast();
const $page = usePage();

const props = defineProps({
    bukuKas: Object,
    jenisBukuKasList: Array,
    filters: Object,
    isSuperAdmin: Boolean,
});

const isReadOnly = computed(() => {
    return $page.props.auth.user.roles.some(role => role.name === 'head');
});

const showModal = ref(false);
const isEditing = ref(false);
const editingId = ref(null);
const showExportDropdown = ref(false);
const groupBy = ref('month');
const showDeleteModal = ref(false);
const deletingBuku = ref(null);

// Local filters
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

const form = useForm({
    nama: '',
    jenis_buku_kas_id: '',
    keterangan: '',
});

// Filter methods
let debounceTimer = null;
const debounceSearch = () => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        applyFilters();
    }, 500);
};

const applyFilter = (field) => {
    applyFilters();
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

// Group data based on selected groupBy option
const groupedBukuKas = computed(() => {
    if (!props.bukuKas.data || props.bukuKas.data.length === 0) {
        return [];
    }

    const data = props.bukuKas.data;
    const groups = {};

    data.forEach(buku => {
        let groupKey, groupLabel;

        if (groupBy.value === 'month') {
            const date = new Date(buku.created_at);
            const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            const month = date.getMonth();
            const year = date.getFullYear();
            groupKey = `${year}-${String(month + 1).padStart(2, '0')}`;
            groupLabel = `${monthNames[month]} ${year}`;
        } else if (groupBy.value === 'year') {
            const date = new Date(buku.created_at);
            const year = date.getFullYear();
            groupKey = String(year);
            groupLabel = `Tahun ${year}`;
        } else if (groupBy.value === 'jenis') {
            if (buku.jenis_buku_kas) {
                groupKey = buku.jenis_buku_kas.id;
                groupLabel = buku.jenis_buku_kas.nama;
            } else {
                groupKey = 'tanpa-jenis';
                groupLabel = 'Tanpa Jenis';
            }
        }

        if (!groups[groupKey]) {
            groups[groupKey] = {
                key: groupKey,
                label: groupLabel,
                items: [],
                totalPemasukan: 0,
                totalPengeluaran: 0,
                totalSaldo: 0,
            };
        }

        groups[groupKey].items.push(buku);
        groups[groupKey].totalPemasukan += parseFloat(buku.total_pemasukan || 0);
        groups[groupKey].totalPengeluaran += parseFloat(buku.total_pengeluaran || 0);
        groups[groupKey].totalSaldo += parseFloat(buku.saldo || 0);
    });

    return Object.values(groups).sort((a, b) => {
        if (groupBy.value === 'month' || groupBy.value === 'year') {
            return b.key.localeCompare(a.key);
        }
        return a.label.localeCompare(b.label);
    });
});

const openBukuKas = (id) => {
    router.visit(`/pengelola/buku-kas/${id}`);
};

const openCreateModal = () => {
    isEditing.value = false;
    form.reset();
    form.clearErrors();
    showModal.value = true;
};

const openEditModal = (buku) => {
    isEditing.value = true;
    editingId.value = buku.id;
    form.nama = buku.nama;
    form.jenis_buku_kas_id = buku.jenis_buku_kas?.id || '';
    form.keterangan = buku.keterangan || '';
    form.clearErrors();
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    form.reset();
    form.clearErrors();
};

const submitForm = () => {
    if (isEditing.value) {
        form.put(`/pengelola/buku-kas/${editingId.value}`, {
            onSuccess: () => {
                toast.success('Buku kas berhasil diupdate');
                closeModal();
            },
            onError: () => {
                toast.error('Gagal mengupdate buku kas');
            }
        });
    } else {
        form.post('/pengelola/buku-kas', {
            onSuccess: () => {
                toast.success('Buku kas berhasil ditambahkan');
                closeModal();
            },
            onError: () => {
                toast.error('Gagal menambahkan buku kas');
            }
        });
    }
};

const confirmDelete = (buku) => {
    if (props.isSuperAdmin) {
        // Sysadmin gets modal with soft delete and permanent delete options
        deletingBuku.value = buku;
        showDeleteModal.value = true;
    } else {
        // Regular users get simple confirmation for soft delete
        if (confirm(`Yakin ingin menghapus buku kas "${buku.nama}"?`)) {
            router.delete(`/pengelola/buku-kas/${buku.id}`, {
                onSuccess: () => {
                    toast.success('Buku kas berhasil dihapus');
                },
                onError: () => {
                    toast.error('Gagal menghapus buku kas');
                }
            });
        }
    }
};

const closeDeleteModal = () => {
    showDeleteModal.value = false;
    deletingBuku.value = null;
};

const doSoftDelete = () => {
    router.delete(`/pengelola/buku-kas/${deletingBuku.value.id}`, {
        onSuccess: () => {
            toast.success('Buku kas berhasil dipindahkan ke tempat sampah');
            closeDeleteModal();
        },
        onError: () => {
            toast.error('Gagal menghapus buku kas');
        }
    });
};

const doPermanentDelete = () => {
    const userInput = prompt(`PERINGATAN: Hapus permanen buku kas "${deletingBuku.value.nama}"?\n\nSemua data transaksi akan ikut terhapus PERMANEN dan TIDAK BISA dikembalikan!\n\nKetik nama buku kas untuk konfirmasi: "${deletingBuku.value.nama}"`);
    if (userInput === deletingBuku.value.nama) {
        router.delete(route('admin.bukukas.permanent-delete', deletingBuku.value.id), {
            onSuccess: () => {
                toast.success('Buku kas berhasil dihapus permanen');
                closeDeleteModal();
            },
            onError: () => {
                toast.error('Gagal menghapus buku kas');
            }
        });
    } else {
        toast.error('Nama buku kas tidak cocok. Penghapusan dibatalkan.');
    }
};

const toggleExportDropdown = (event) => {
    event.stopPropagation();
    showExportDropdown.value = !showExportDropdown.value;
};

const closeExportDropdown = () => {
    showExportDropdown.value = false;
};

const exportData = (format) => {
    const url = format === 'csv'
        ? route('admin.bukukas.export.csv')
        : route('admin.bukukas.export.xlsx');
    window.location.href = url;
    showExportDropdown.value = false;
};

const formatNumber = (number) => {
    return new Intl.NumberFormat('id-ID').format(number);
};


onMounted(() => {
    document.addEventListener('click', closeExportDropdown);
});

onUnmounted(() => {
    document.removeEventListener('click', closeExportDropdown);
});
</script>
