<template>
    <Teleport to="body">
        <div v-if="modelValue" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" @click="closeModal"></div>
            <div class="flex min-h-screen items-center justify-center p-4">
                <div class="relative bg-white rounded-lg shadow-xl max-w-2xl w-full p-4 md:p-6 max-h-[90vh] overflow-y-auto">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg md:text-xl font-semibold text-gray-900">
                            {{ isEditing ? 'Edit' : 'Tambah' }} Transaksi
                        </h3>
                        <button @click="closeModal" class="text-gray-400 hover:text-gray-600 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Tabs (only when creating new transaction) -->
                    <div v-if="!isEditing" class="border-b border-gray-200 mb-4">
                        <nav class="flex space-x-1" aria-label="Tabs">
                            <button
                                type="button"
                                @click="switchTab('manual')"
                                :class="[
                                    'px-4 py-2 text-sm font-medium rounded-t-lg transition-colors',
                                    activeTab === 'manual'
                                        ? 'bg-[#996600] text-white'
                                        : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100'
                                ]"
                            >
                                Manual
                            </button>
                            <button
                                type="button"
                                @click="switchTab('kas-lain')"
                                :class="[
                                    'px-4 py-2 text-sm font-medium rounded-t-lg transition-colors',
                                    activeTab === 'kas-lain'
                                        ? 'bg-[#996600] text-white'
                                        : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100'
                                ]"
                            >
                                Dari Kas Lain
                            </button>
                            <button
                                type="button"
                                @click="switchTab('unit-kerja')"
                                :class="[
                                    'px-4 py-2 text-sm font-medium rounded-t-lg transition-colors',
                                    activeTab === 'unit-kerja'
                                        ? 'bg-[#996600] text-white'
                                        : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100'
                                ]"
                            >
                                Dari Penghasilan Unit Kerja
                            </button>
                        </nav>
                    </div>

                    <form @submit.prevent="submitForm">
                        <!-- Tab Content -->
                        <TransactionFormManual
                            v-if="activeTab === 'manual' || isEditing"
                            :form="form"
                            :work-units="workUnits"
                            :kategori-list="kategoriList"
                            :existing-bukti-transaksi="existingBuktiTransaksi"
                            :existing-bukti-transaksi-link="existingBuktiTransaksiLink"
                            :existing-bukti-aktivitas="existingBuktiAktivitas"
                            :existing-bukti-aktivitas-link="existingBuktiAktivitasLink"
                            @bukti-transaksi-change="handleBuktiTransaksi"
                            @bukti-aktivitas-change="handleBuktiAktivitas"
                        />

                        <TransactionFormFromKasLain
                            v-if="activeTab === 'kas-lain' && !isEditing"
                            :form="form"
                            :all-buku-kas="allBukuKas"
                            :current-buku-kas-id="bukuKasId"
                            :work-units="workUnits"
                        />

                        <TransactionFormFromUnitKerja
                            v-if="activeTab === 'unit-kerja' && !isEditing"
                            ref="unitKerjaFormRef"
                            :form="form"
                            :penjualan-per-tanggal="penjualanPerTanggal"
                        />

                        <!-- Action Buttons -->
                        <div class="flex justify-end space-x-3 mt-6">
                            <button
                                type="button"
                                @click="closeModal"
                                class="px-5 py-2 border-2 border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 hover:scale-105"
                            >
                                Batal
                            </button>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="px-5 py-2 bg-[#996600] text-white rounded-lg hover:bg-[#6b4700] disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-300 hover:scale-105 hover:shadow-lg"
                            >
                                {{ form.processing ? 'Menyimpan...' : 'Simpan' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </Teleport>
</template>

<script setup>
import TransactionFormManual from './TransactionFormManual.vue';
import TransactionFormFromKasLain from './TransactionFormFromKasLain.vue';
import TransactionFormFromUnitKerja from './TransactionFormFromUnitKerja.vue';
import { ref, watch } from 'vue';

const props = defineProps({
    modelValue: Boolean,
    form: Object,
    isEditing: Boolean,
    editingId: Number,
    bukuKasId: Number,
    workUnits: Array,
    kategoriList: Array,
    allBukuKas: Array,
    penjualanPerTanggal: Array,
    existingBuktiTransaksi: String,
    existingBuktiTransaksiLink: String,
    existingBuktiAktivitas: String,
    existingBuktiAktivitasLink: String,
});

const emit = defineEmits([
    'update:modelValue',
    'submit',
    'submitUnitKerja',
    'bukti-transaksi-change',
    'bukti-aktivitas-change'
]);

const activeTab = ref('manual');
const unitKerjaFormRef = ref(null);

// Watch for modal open/close to reset tab
watch(() => props.modelValue, (newVal) => {
    if (newVal && !props.isEditing) {
        activeTab.value = 'manual';
    }
});

const closeModal = () => {
    emit('update:modelValue', false);
};

const switchTab = (tab) => {
    activeTab.value = tab;
    props.form.source_type = tab;

    // Clear form when switching tabs
    if (tab === 'manual') {
        props.form.source_buku_kas_id = null;
        props.form.unit_kerja_id = null;
    } else if (tab === 'kas-lain') {
        props.form.unit_kerja_id = null;
    } else if (tab === 'unit-kerja') {
        props.form.source_buku_kas_id = null;
    }
};

const handleBuktiTransaksi = (file) => {
    emit('bukti-transaksi-change', file);
};

const handleBuktiAktivitas = (file) => {
    emit('bukti-aktivitas-change', file);
};

const submitForm = () => {
    // If using unit-kerja tab, use different submit handler
    if (activeTab.value === 'unit-kerja' && !props.isEditing) {
        // Validate that dates are selected and confirmed
        if (!unitKerjaFormRef.value?.selectedDates?.length) {
            alert('Pilih minimal 1 tanggal penjualan');
            return;
        }
        if (!unitKerjaFormRef.value?.confirmRecording) {
            alert('Harap centang konfirmasi terlebih dahulu');
            return;
        }
        emit('submitUnitKerja');
    } else {
        emit('submit');
    }
};
</script>
