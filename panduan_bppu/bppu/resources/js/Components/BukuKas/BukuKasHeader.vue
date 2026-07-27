<template>
    <div class="mb-6">
        <button @click="$emit('back')" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900 mb-4 transition-colors duration-200">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Kembali
        </button>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-6">
            <!-- Kolom 1: Judul Buku Kas + Ringkasan -->
            <div class="bg-white rounded-xl shadow-lg p-3 border border-[#d6c199]">
                <div class="flex items-start justify-between mb-2">
                    <div class="flex-1">
                        <h1 class="text-base md:text-lg font-bold text-gray-900">{{ bukuKas.nama }}</h1>
                        <p v-if="bukuKas.keterangan" class="text-gray-600 mt-1 text-xs line-clamp-2">
                            {{ bukuKas.keterangan }}
                        </p>
                    </div>
                    <div v-if="isSuperAdmin && !isOwner" class="ml-2 px-2 py-1 bg-yellow-100 border border-yellow-400 rounded text-xs flex-shrink-0">
                        <p class="font-semibold text-yellow-800 uppercase text-xs">Admin</p>
                        <p class="text-xs text-yellow-900 truncate">{{ bukuKas.user_name }}</p>
                    </div>
                </div>

                <!-- Stats dalam kolom -->
                <div class="grid grid-cols-2 gap-1.5 md:gap-2 mt-2">
                    <div class="bg-gray-50 rounded p-1.5 md:p-2 border border-gray-200">
                        <p class="text-xs text-gray-500">Dibuat</p>
                        <p class="text-xs font-semibold text-gray-900">{{ bukuKas.created_at }}</p>
                    </div>
                    <div :class="[
                        'rounded p-1.5 md:p-2 border',
                        bukuKas.saldo >= 0 ? 'bg-[#eae0cc] border-[#996600]' : 'bg-red-50 border-red-400'
                    ]">
                        <p class="text-xs text-gray-700">Saldo</p>
                        <p class="text-xs font-bold truncate" :class="bukuKas.saldo >= 0 ? 'text-[#6b4700]' : 'text-red-700'">
                            Rp {{ formatNumber(bukuKas.saldo) }}
                        </p>
                    </div>
                    <div class="bg-green-50 rounded p-1.5 md:p-2 border border-green-300">
                        <p class="text-xs text-gray-500">Pemasukan</p>
                        <p class="text-xs font-bold text-green-700 truncate">Rp {{ formatNumber(bukuKas.total_pemasukan) }}</p>
                    </div>
                    <div class="bg-red-50 rounded p-1.5 md:p-2 border border-red-300">
                        <p class="text-xs text-gray-500">Pengeluaran</p>
                        <p class="text-xs font-bold text-red-700 truncate">Rp {{ formatNumber(bukuKas.total_pengeluaran) }}</p>
                    </div>
                </div>
            </div>

            <!-- Kolom 2: Ringkasan Per Unit Kerja -->
            <div class="bg-white rounded-xl shadow-lg p-3 border border-[#d6c199]">
                <h2 class="text-sm font-bold text-gray-900 mb-2 flex items-center">
                    <svg class="w-4 h-4 mr-1 text-[#996600]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    Ringkasan Per Unit Kerja
                </h2>

                <div v-if="summaryByUnit.length === 0" class="text-center py-4 text-xs text-gray-500">
                    Tidak ada data unit kerja
                </div>

                <div v-else class="space-y-1.5 md:space-y-2 max-h-[150px] md:max-h-[180px] overflow-y-auto">
                    <div
                        v-for="unit in summaryByUnit"
                        :key="unit.unit_kerja_id"
                        class="bg-gray-50 rounded p-1.5 md:p-2 border border-gray-200 hover:border-[#996600] transition-all"
                    >
                        <h3 class="font-bold text-[#996600] mb-1 text-xs flex items-center truncate">
                            <div class="w-4 h-4 bg-[#996600] rounded flex items-center justify-center mr-1 flex-shrink-0">
                                <svg class="w-2 h-2 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <span class="truncate">{{ unit.unit_kerja_name }}</span>
                        </h3>
                        <div class="grid grid-cols-3 gap-1 text-xs">
                            <div class="text-center">
                                <p class="text-gray-500 text-xs">Masuk</p>
                                <p class="font-bold text-green-700 text-xs truncate">{{ formatNumber(unit.total_pemasukan) }}</p>
                            </div>
                            <div class="text-center">
                                <p class="text-gray-500 text-xs">Keluar</p>
                                <p class="font-bold text-red-700 text-xs truncate">{{ formatNumber(unit.total_pengeluaran) }}</p>
                            </div>
                            <div class="text-center">
                                <p class="text-gray-500 text-xs">Saldo</p>
                                <p class="font-bold text-xs truncate" :class="unit.saldo >= 0 ? 'text-[#6b4700]' : 'text-red-700'">
                                    {{ formatNumber(unit.saldo) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    bukuKas: {
        type: Object,
        required: true
    },
    summaryByUnit: {
        type: Array,
        default: () => []
    },
    isSuperAdmin: Boolean,
    isOwner: Boolean,
});

defineEmits(['back']);

const formatNumber = (number) => {
    return new Intl.NumberFormat('id-ID').format(number || 0);
};
</script>
