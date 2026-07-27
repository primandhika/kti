<template>
    <AdminLayout
        :page-title="`Detail ${bukuKas.nama}`"
        :page-subtitle="bukuKas.work_unit ? bukuKas.work_unit.name : ''"
    >
        <div class="p-4 md:p-6">
            <!-- Header Section -->
            <BukuKasHeader
                :buku-kas="bukuKas"
                :summary-by-unit="summaryByUnit"
                :is-super-admin="isSuperAdmin"
                :is-owner="isOwner"
                @back="goBack"
            />

            <!-- Transactions Section -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden border-2 border-[#d6c199]">
                <!-- Filter Section -->
                <FilterSection
                    :filters="localFilters"
                    :kategori-list="kategoriList"
                    :jenis-transaksi-list="jenisTransaksiList"
                    :work-units="workUnits"
                    :has-transactions="transaksi.data.length > 0"
                    @update:filters="handleFilterUpdate"
                    @reset-filters="resetFilters"
                    @add-transaction="openCreateModal"
                    @export="exportTransaksi"
                />

                <!-- Transaction List -->
                <TransactionList
                    :transaksi="transaksi"
                    :has-active-filters="hasActiveFilters"
                    :sort-field="localFilters.sort_field"
                    :sort-direction="localFilters.sort_direction"
                    @edit="openEditModal"
                    @delete="confirmDelete"
                    @sort="sortBy"
                    @page-change="changePage"
                />
            </div>
        </div>

        <!-- Transaction Modal -->
        <TransactionModal
            v-model="showModal"
            :form="form"
            :is-editing="isEditing"
            :editing-id="editingId"
            :buku-kas-id="bukuKas.id"
            :work-units="workUnits"
            :kategori-list="kategoriList"
            :all-buku-kas="allBukuKas"
            :penjualan-per-tanggal="penjualanPerTanggal"
            :existing-bukti-transaksi="existingBuktiTransaksi"
            :existing-bukti-transaksi-link="existingBuktiTransaksiLink"
            :existing-bukti-aktivitas="existingBuktiAktivitas"
            :existing-bukti-aktivitas-link="existingBuktiAktivitasLink"
            @submit="submitForm"
            @submitUnitKerja="submitFromUnitKerja"
            @bukti-transaksi-change="handleBuktiTransaksi"
            @bukti-aktivitas-change="handleBuktiAktivitas"
        />
    </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import BukuKasHeader from '@/Components/BukuKas/BukuKasHeader.vue';
import FilterSection from '@/Components/BukuKas/FilterSection.vue';
import TransactionList from '@/Components/BukuKas/TransactionList.vue';
import TransactionModal from '@/Components/BukuKas/TransactionModal.vue';
import { router, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { useToast } from 'vue-toastification';

const toast = useToast();

const props = defineProps({
    bukuKas: Object,
    transaksi: Object,
    summaryByUnit: Array,
    kategoriList: Array,
    jenisTransaksiList: Array,
    workUnits: Array,
    filters: Object,
    isSuperAdmin: Boolean,
    isOwner: Boolean,
    allBukuKas: Array,
    penjualanPerTanggal: Array,
});

// Modal state
const showModal = ref(false);
const isEditing = ref(false);
const editingId = ref(null);
const existingBuktiTransaksi = ref(null);
const existingBuktiTransaksiLink = ref(null);
const existingBuktiAktivitas = ref(null);
const existingBuktiAktivitasLink = ref(null);

// Filter state
const localFilters = ref({
    search: props.filters.search || '',
    type: props.filters.type || '',
    kategori: props.filters.kategori || '',
    jenis_transaksi: props.filters.jenis_transaksi || '',
    unit_kerja_id: props.filters.unit_kerja_id || '',
    date_from: props.filters.date_from || '',
    date_to: props.filters.date_to || '',
    sort_field: props.filters.sort_field || 'tanggal',
    sort_direction: props.filters.sort_direction || 'desc',
});

const hasActiveFilters = computed(() => {
    return localFilters.value.search ||
           localFilters.value.type ||
           localFilters.value.kategori ||
           localFilters.value.jenis_transaksi ||
           localFilters.value.unit_kerja_id ||
           localFilters.value.date_from ||
           localFilters.value.date_to;
});

// Form state
const form = useForm({
    source_type: 'manual',
    tanggal: '',
    kategori: '',
    jenis_transaksi: '',
    unit_kerja_id: null,
    deskripsi: '',
    pemasukan: '',
    pengeluaran: '',
    bukti_transaksi_type: 'upload',
    bukti_transaksi: null,
    bukti_transaksi_link: '',
    bukti_aktivitas_type: 'upload',
    bukti_aktivitas: null,
    bukti_aktivitas_link: '',
    source_buku_kas_id: null,
    selected_dates: [],
});

// Debounce timer for search
let debounceTimer = null;

// Filter methods
const handleFilterUpdate = (updatedFilters) => {
    localFilters.value = { ...localFilters.value, ...updatedFilters };

    // Debounce only for search field
    if ('search' in updatedFilters) {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            applyFilters();
        }, 500);
    } else {
        applyFilters();
    }
};

const applyFilters = () => {
    router.get(`/pengelola/buku-kas/${props.bukuKas.id}`, localFilters.value, {
        preserveState: true,
        preserveScroll: true,
    });
};

const resetFilters = () => {
    localFilters.value = {
        search: '',
        type: '',
        kategori: '',
        jenis_transaksi: '',
        unit_kerja_id: '',
        date_from: '',
        date_to: '',
        sort_field: 'tanggal',
        sort_direction: 'desc',
    };
    applyFilters();
};

const sortBy = (field) => {
    if (localFilters.value.sort_field === field) {
        localFilters.value.sort_direction = localFilters.value.sort_direction === 'asc' ? 'desc' : 'asc';
    } else {
        localFilters.value.sort_field = field;
        localFilters.value.sort_direction = 'asc';
    }
    applyFilters();
};

const changePage = (url) => {
    if (!url) return;
    router.get(url, {}, {
        preserveState: true,
        preserveScroll: true,
    });
};

// Navigation
const goBack = () => {
    router.visit('/pengelola/buku-kas');
};

// Modal methods
const openCreateModal = () => {
    isEditing.value = false;
    editingId.value = null;
    form.reset();
    form.clearErrors();
    existingBuktiTransaksi.value = null;
    existingBuktiTransaksiLink.value = null;
    existingBuktiAktivitas.value = null;
    existingBuktiAktivitasLink.value = null;

    // Set default values
    const today = new Date().toISOString().split('T')[0];
    form.tanggal = today;
    form.source_type = 'manual';
    form.bukti_transaksi_type = 'upload';
    form.bukti_aktivitas_type = 'upload';
    form.unit_kerja_id = null;

    showModal.value = true;
};

const openEditModal = (transaksi) => {
    isEditing.value = true;
    editingId.value = transaksi.id;

    // Set form values with proper null handling
    form.tanggal = transaksi.tanggal_raw || '';
    form.kategori = transaksi.kategori || '';
    form.jenis_transaksi = transaksi.jenis_transaksi || '';
    form.unit_kerja_id = transaksi.unit_kerja_id || null;
    form.deskripsi = transaksi.deskripsi || '';

    // Handle pemasukan/pengeluaran - convert to empty string if 0 or null
    form.pemasukan = (transaksi.pemasukan && transaksi.pemasukan > 0) ? transaksi.pemasukan : '';
    form.pengeluaran = (transaksi.pengeluaran && transaksi.pengeluaran > 0) ? transaksi.pengeluaran : '';

    // Reset file inputs
    form.bukti_transaksi = null;
    form.bukti_transaksi_type = transaksi.bukti_transaksi_type || 'upload';
    form.bukti_transaksi_link = transaksi.bukti_transaksi_link || '';

    form.bukti_aktivitas = null;
    form.bukti_aktivitas_type = transaksi.bukti_aktivitas_type || 'upload';
    form.bukti_aktivitas_link = transaksi.bukti_aktivitas_link || '';

    // Set existing file paths
    existingBuktiTransaksi.value = transaksi.bukti_transaksi || null;
    existingBuktiTransaksiLink.value = transaksi.bukti_transaksi_link || null;
    existingBuktiAktivitas.value = transaksi.bukti_aktivitas || null;
    existingBuktiAktivitasLink.value = transaksi.bukti_aktivitas_link || null;

    form.clearErrors();
    showModal.value = true;
};

const handleBuktiTransaksi = (file) => {
    form.bukti_transaksi = file;
};

const handleBuktiAktivitas = (file) => {
    form.bukti_aktivitas = file;
};

const submitForm = () => {
    if (isEditing.value) {
        form.post(`/pengelola/buku-kas/${props.bukuKas.id}/transaksi/${editingId.value}?_method=PUT`, {
            onSuccess: () => {
                toast.success('Transaksi berhasil diupdate');
                showModal.value = false;
                form.reset();
            },
            onError: () => {
                toast.error('Gagal mengupdate transaksi');
            },
            forceFormData: true,
        });
    } else {
        form.post(`/pengelola/buku-kas/${props.bukuKas.id}/transaksi`, {
            onSuccess: () => {
                toast.success('Transaksi berhasil ditambahkan');
                showModal.value = false;
                form.reset();
            },
            onError: () => {
                toast.error('Gagal menambahkan transaksi');
            },
            forceFormData: true,
        });
    }
};

const submitFromUnitKerja = () => {
    // Use the new endpoint for recording by date
    form.post(`/pengelola/buku-kas/${props.bukuKas.id}/record-penjualan-by-date`, {
        forceFormData: false,
        onSuccess: () => {
            toast.success('Penjualan berhasil dicatat ke buku kas');
            showModal.value = false;
            form.reset();
        },
        onError: (errors) => {
            console.error('Error mencatat penjualan:', errors);
            const errorMsg = errors?.selected_dates
                ? 'Gagal mencatat penjualan: ' + errors.selected_dates
                : 'Gagal mencatat penjualan. Silakan cek data dan coba lagi.';
            toast.error(errorMsg);
        },
    });
};

const confirmDelete = (transaksi) => {
    let message = `Yakin ingin menghapus transaksi "${transaksi.deskripsi}"?`;

    // Special warning for imported transactions
    if (transaksi.source_type === 'unit-kerja') {
        message += `\n\nPERHATIAN: Transaksi ini diimpor dari "Penghasilan Unit Kerja".\nJika dihapus, penjualan yang sudah tercatat akan dikembalikan ke status "belum tercatat" dan bisa dipilih lagi untuk dicatat ulang.`;
    } else if (transaksi.source_type === 'kas-lain') {
        message += `\n\nPERHATIAN: Transaksi ini ditransfer dari buku kas lain.`;
    }

    if (confirm(message)) {
        router.delete(`/pengelola/buku-kas/${props.bukuKas.id}/transaksi/${transaksi.id}`, {
            onSuccess: () => {
                toast.success('Transaksi berhasil dihapus' + (transaksi.source_type === 'unit-kerja' ? ' dan penjualan dikembalikan ke status belum tercatat' : ''));
            },
            onError: () => {
                toast.error('Gagal menghapus transaksi');
            }
        });
    }
};

// Export methods
const buildExportUrl = (format) => {
    const baseUrl = `/pengelola/buku-kas/${props.bukuKas.id}/export/${format}`;
    const params = new URLSearchParams();

    // Add filters to the URL
    if (localFilters.value.date_from) params.append('date_from', localFilters.value.date_from);
    if (localFilters.value.date_to) params.append('date_to', localFilters.value.date_to);
    if (localFilters.value.kategori) params.append('kategori', localFilters.value.kategori);
    if (localFilters.value.jenis_transaksi) params.append('jenis_transaksi', localFilters.value.jenis_transaksi);
    if (localFilters.value.unit_kerja_id) params.append('unit_kerja_id', localFilters.value.unit_kerja_id);
    if (localFilters.value.type) params.append('type', localFilters.value.type);
    if (localFilters.value.search) params.append('search', localFilters.value.search);
    if (localFilters.value.sort_field) params.append('sort_field', localFilters.value.sort_field);
    if (localFilters.value.sort_direction) params.append('sort_direction', localFilters.value.sort_direction);

    const queryString = params.toString();
    return queryString ? `${baseUrl}?${queryString}` : baseUrl;
};

const exportTransaksi = (format) => {
    window.location.href = buildExportUrl(format);
};
</script>
