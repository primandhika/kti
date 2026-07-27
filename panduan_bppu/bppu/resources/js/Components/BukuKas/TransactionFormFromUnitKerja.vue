<template>
    <div class="space-y-4">
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
            <p class="text-sm text-blue-800">
                <strong>Info:</strong> Pilih tanggal-tanggal penjualan yang ingin dicatat ke buku kas. Setiap tanggal yang dicentang akan dicatat sebagai satu transaksi pemasukan.
            </p>
        </div>

        <!-- Filter Metode Pembayaran -->
        <div v-if="penjualanPerTanggal && penjualanPerTanggal.length > 0" class="bg-gray-50 border border-gray-200 rounded-lg p-3">
            <div class="flex items-center gap-3 flex-wrap">
                <label class="text-xs font-medium text-gray-600 whitespace-nowrap">Filter:</label>
                <div class="flex gap-1 flex-wrap">
                    <label
                        v-for="metode in availableMetodes"
                        :key="metode"
                        class="inline-flex items-center px-3 py-1 border rounded cursor-pointer transition-all text-xs font-medium"
                        :class="selectedMetodePembayaran === metode
                            ? 'bg-[#996600] text-white border-[#996600]'
                            : 'bg-white text-gray-700 border-gray-300 hover:border-[#996600]'"
                    >
                        <input
                            type="radio"
                            :value="metode"
                            v-model="selectedMetodePembayaran"
                            class="sr-only"
                        >
                        <span>{{ metode }}</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- List Penjualan Per Tanggal -->
        <div v-if="filteredPenjualanPerTanggal && filteredPenjualanPerTanggal.length > 0" class="space-y-4">
            <div class="bg-white border border-gray-300 rounded-lg p-4 max-h-96 overflow-y-auto">
                <h4 class="text-sm font-semibold text-gray-700 mb-3">Pilih Tanggal Penjualan yang Akan Dicatat</h4>
                <div class="space-y-2">
                    <label
                        v-for="(item, index) in filteredPenjualanPerTanggal"
                        :key="index"
                        class="flex items-start p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors"
                        :class="{ 'bg-green-50 border-green-300': selectedDates.includes(index) }"
                    >
                        <input
                            type="checkbox"
                            :value="index"
                            v-model="selectedDates"
                            class="mt-1 mr-3 h-4 w-4 text-[#996600] focus:ring-[#996600] border-gray-300 rounded"
                        >
                        <div class="flex-1">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ item.tanggal_display }}</p>
                                    <p class="text-xs text-gray-600">{{ item.unit_name }}</p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ item.jumlah_transaksi }} transaksi
                                        <span class="ml-2 px-2 py-0.5 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">
                                            {{ item.metode_pembayaran }}
                                        </span>
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-bold text-green-700">Rp {{ formatNumber(item.total_penghasilan) }}</p>
                                </div>
                            </div>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Summary -->
            <div v-if="selectedDates.length > 0" class="bg-gray-50 border border-gray-300 rounded-lg p-4">
                <h4 class="text-sm font-semibold text-gray-700 mb-3">Ringkasan Pencatatan</h4>
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div>
                        <span class="text-gray-600">Jumlah Tanggal Dipilih:</span>
                        <p class="font-semibold">{{ selectedDates.length }} tanggal</p>
                    </div>
                    <div>
                        <span class="text-gray-600">Total Transaksi:</span>
                        <p class="font-semibold">{{ totalTransaksi }} transaksi</p>
                    </div>
                    <div class="col-span-2">
                        <span class="text-gray-600">Total Pemasukan:</span>
                        <p class="font-bold text-green-700 text-lg">Rp {{ formatNumber(totalPemasukan) }}</p>
                    </div>
                </div>
            </div>

            <!-- Checkbox konfirmasi -->
            <div v-if="selectedDates.length > 0" class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <label class="flex items-start">
                    <input type="checkbox" v-model="confirmRecording" class="mt-1 mr-3 h-4 w-4 text-[#996600] focus:ring-[#996600] border-gray-300 rounded">
                    <span class="text-sm text-gray-700">
                        <strong>Saya memahami bahwa:</strong><br>
                        Dengan mencatat transaksi ini, {{ totalTransaksi }} transaksi penjualan dari {{ selectedDates.length }} tanggal yang dipilih akan ditandai sebagai "sudah tercatat" dan tidak dapat diubah lagi.
                    </span>
                </label>
            </div>
        </div>

        <!-- Empty State -->
        <div v-else class="text-center py-8">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada transaksi</h3>
            <p class="mt-1 text-sm text-gray-500">Belum ada transaksi penjualan yang sudah diapprove dan belum tercatat.</p>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';

const props = defineProps({
    form: Object,
    penjualanPerTanggal: Array,
});

const selectedDates = ref([]);
const confirmRecording = ref(false);
const selectedMetodePembayaran = ref('Semua');

// Get available payment methods from the data
const availableMetodes = computed(() => {
    if (!props.penjualanPerTanggal || props.penjualanPerTanggal.length === 0) {
        return ['Semua'];
    }

    const metodes = new Set(['Semua']);
    props.penjualanPerTanggal.forEach(item => {
        if (item.metode_pembayaran) {
            metodes.add(item.metode_pembayaran);
        }
    });

    return Array.from(metodes);
});

// Filtered penjualan based on selected payment method
const filteredPenjualanPerTanggal = computed(() => {
    if (!props.penjualanPerTanggal) return [];

    if (selectedMetodePembayaran.value === 'Semua') {
        return props.penjualanPerTanggal;
    }

    const filtered = props.penjualanPerTanggal.filter(item =>
        item.metode_pembayaran === selectedMetodePembayaran.value
    );

    return filtered;
});

// Reset selected dates when filter changes
watch(selectedMetodePembayaran, () => {
    selectedDates.value = [];
    confirmRecording.value = false;
});

const totalTransaksi = computed(() => {
    return selectedDates.value.reduce((sum, index) => {
        return sum + (filteredPenjualanPerTanggal.value[index]?.jumlah_transaksi || 0);
    }, 0);
});

const totalPemasukan = computed(() => {
    return selectedDates.value.reduce((sum, index) => {
        return sum + (filteredPenjualanPerTanggal.value[index]?.total_penghasilan || 0);
    }, 0);
});

// Watch for changes and update form
watch([selectedDates, confirmRecording], () => {
    if (selectedDates.value.length > 0 && confirmRecording.value) {
        // Build the data to send
        const selectedData = selectedDates.value
            .map(index => {
                const item = filteredPenjualanPerTanggal.value[index];
                if (!item) return null;

                return {
                    unit_id: item.unit_id,
                    tanggal: item.tanggal,
                    total: item.total_penghasilan,
                    penjualan_ids: item.penjualan_ids,
                    metode_pembayaran: item.metode_pembayaran,
                };
            })
            .filter(item => item !== null);

        props.form.selected_dates = selectedData;
    } else {
        props.form.selected_dates = [];
    }
}, { deep: true });

const formatNumber = (number) => {
    return new Intl.NumberFormat('id-ID').format(number || 0);
};

// Expose confirmRecording for parent component validation
defineExpose({
    confirmRecording,
    selectedDates,
});
</script>
