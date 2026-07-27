<template>
    <Teleport to="body">
        <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" @click="$emit('close')"></div>
            <div class="flex min-h-screen items-center justify-center p-4">
                <div class="relative bg-white rounded-lg shadow-xl max-w-2xl w-full p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Catat ke Buku Kas</h3>
                        <button @click="$emit('close')" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Penjualan Info -->
                    <div v-if="penjualan" class="mb-6 p-4 bg-gray-50 rounded-lg">
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div>
                                <p class="text-gray-500">Kode Transaksi</p>
                                <p class="font-medium">{{ penjualan.nomor_transaksi }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Total</p>
                                <p class="font-medium text-[#996600]">{{ formatCurrency(penjualan.total) }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Unit Kerja</p>
                                <p class="font-medium">{{ penjualan.workUnit?.name || '-' }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Metode Pembayaran</p>
                                <p class="font-medium capitalize">{{ penjualan.metode_pembayaran }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Pilih Buku Kas -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Pilih Buku Kas <span class="text-red-500">*</span>
                        </label>
                        <div class="space-y-2 max-h-64 overflow-y-auto">
                            <button
                                v-for="buku in bukuKasList"
                                :key="buku.id"
                                @click="selectedBukuKasId = buku.id"
                                type="button"
                                :class="[
                                    'w-full text-left p-3 rounded-lg border-2 transition-all duration-200',
                                    selectedBukuKasId === buku.id
                                        ? 'border-[#996600] bg-[#99660010]'
                                        : 'border-gray-200 hover:border-gray-300 hover:bg-gray-50'
                                ]"
                            >
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3 flex-1">
                                        <div
                                            v-if="buku.jenis_buku_kas"
                                            :style="{ backgroundColor: buku.jenis_buku_kas.warna }"
                                            class="inline-flex items-center justify-center w-10 h-10 rounded-lg text-white font-bold text-sm flex-shrink-0"
                                        >
                                            {{ buku.jenis_buku_kas.kode }}
                                        </div>
                                        <div
                                            v-else
                                            class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-gray-400 text-white font-bold text-sm flex-shrink-0"
                                        >
                                            BK
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900">{{ buku.nama }}</p>
                                            <p class="text-xs text-gray-500 truncate">
                                                {{ buku.user_name }}
                                                <span v-if="buku.jenis_buku_kas"> &middot; {{ buku.jenis_buku_kas.nama }}</span>
                                            </p>
                                            <p v-if="buku.keterangan" class="text-xs text-gray-400 truncate">{{ buku.keterangan }}</p>
                                        </div>
                                    </div>
                                    <svg v-if="selectedBukuKasId === buku.id" class="w-5 h-5 text-[#996600] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </div>
                        <p v-if="bukuKasList.length === 0" class="text-sm text-red-500 mt-2">
                            Tidak ada buku kas tersedia. Silakan buat buku kas terlebih dahulu.
                        </p>
                    </div>

                    <!-- Keterangan -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Keterangan (Opsional)
                        </label>
                        <textarea
                            v-model="keterangan"
                            rows="3"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
                            placeholder="Tambahkan keterangan jika diperlukan..."
                        ></textarea>
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-end gap-3">
                        <button
                            @click="$emit('close')"
                            type="button"
                            class="px-5 py-2 border-2 border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-all"
                        >
                            Batal
                        </button>
                        <button
                            @click="handleConfirm"
                            type="button"
                            :disabled="!selectedBukuKasId"
                            :class="[
                                'px-5 py-2 rounded-lg transition-all',
                                selectedBukuKasId
                                    ? 'bg-[#996600] text-white hover:bg-[#6b4700]'
                                    : 'bg-gray-300 text-gray-500 cursor-not-allowed'
                            ]"
                        >
                            Catat ke Buku Kas
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </Teleport>
</template>

<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
    show: Boolean,
    penjualan: Object,
    bukuKasList: Array,
});

const emit = defineEmits(['close', 'confirm']);

const selectedBukuKasId = ref(null);
const keterangan = ref('');

const formatCurrency = (value) => {
    if (!value) return 'Rp 0';
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(value);
};

watch(() => props.show, (newVal) => {
    if (newVal) {
        selectedBukuKasId.value = null;
        keterangan.value = '';
    }
});

const handleConfirm = () => {
    if (!selectedBukuKasId.value) return;

    emit('confirm', {
        buku_kas_id: selectedBukuKasId.value,
        keterangan: keterangan.value,
    });
};
</script>
