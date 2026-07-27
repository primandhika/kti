<template>
    <div class="space-y-4">
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-4">
            <p class="text-sm text-blue-800">
                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Pilih buku kas lain untuk dicatat sebagai transaksi. Akumulasi total pemasukan/pengeluaran dari buku kas tersebut akan otomatis terisi.
            </p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Buku Kas <span class="text-red-500">*</span></label>
            <select
                v-model="selectedBukuKas"
                @change="onSelectBukuKas"
                class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-[#996600] transition-all duration-200 bg-white"
            >
                <option :value="null">-- Pilih Buku Kas --</option>
                <option
                    v-for="buku in availableBukuKas"
                    :key="buku.id"
                    :value="buku"
                >
                    {{ buku.nama }} ({{ buku.user_name }}) - Saldo: Rp {{ formatNumber(buku.saldo) }}
                </option>
            </select>
            <p v-if="form.errors.source_buku_kas_id" class="text-red-500 text-sm mt-1">{{ form.errors.source_buku_kas_id }}</p>
        </div>

        <!-- Preview & Edit Form -->
        <div v-if="selectedBukuKas" class="space-y-4">
            <!-- Info Buku Kas -->
            <div class="bg-gray-50 border border-gray-300 rounded-lg p-4">
                <h4 class="text-sm font-semibold text-gray-700 mb-3">Informasi Buku Kas</h4>
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div>
                        <span class="text-gray-600">Nama:</span>
                        <p class="font-semibold">{{ selectedBukuKas.nama }}</p>
                    </div>
                    <div>
                        <span class="text-gray-600">Pemilik:</span>
                        <p class="font-semibold">{{ selectedBukuKas.user_name }}</p>
                    </div>
                    <div>
                        <span class="text-gray-600">Total Pemasukan:</span>
                        <p class="font-bold text-green-700">Rp {{ formatNumber(selectedBukuKas.total_pemasukan) }}</p>
                    </div>
                    <div>
                        <span class="text-gray-600">Total Pengeluaran:</span>
                        <p class="font-bold text-red-700">Rp {{ formatNumber(selectedBukuKas.total_pengeluaran) }}</p>
                    </div>
                    <div>
                        <span class="text-gray-600">Saldo:</span>
                        <p class="font-bold" :class="selectedBukuKas.saldo >= 0 ? 'text-[#6b4700]' : 'text-red-700'">
                            Rp {{ formatNumber(selectedBukuKas.saldo) }}
                        </p>
                    </div>
                    <div>
                        <span class="text-gray-600">Dibuat:</span>
                        <p class="font-semibold">{{ selectedBukuKas.created_at }}</p>
                    </div>
                </div>
            </div>

            <!-- Form Transaksi -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal <span class="text-red-500">*</span></label>
                    <input v-model="form.tanggal" type="date" class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-[#996600] transition-all duration-200">
                    <p v-if="form.errors.tanggal" class="text-red-500 text-sm mt-1">{{ form.errors.tanggal }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kategori <span class="text-red-500">*</span></label>
                    <input v-model="form.kategori" type="text" class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-[#996600] transition-all duration-200">
                    <p v-if="form.errors.kategori" class="text-red-500 text-sm mt-1">{{ form.errors.kategori }}</p>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi <span class="text-red-500">*</span></label>
                <textarea v-model="form.deskripsi" rows="2" class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-[#996600] transition-all duration-200"></textarea>
                <p v-if="form.errors.deskripsi" class="text-red-500 text-sm mt-1">{{ form.errors.deskripsi }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Unit Kerja</label>
                <select v-model="form.unit_kerja_id" class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-[#996600] transition-all duration-200 bg-white">
                    <option :value="null">Pilih Unit Kerja</option>
                    <option v-for="unit in workUnits" :key="unit.id" :value="unit.id">
                        {{ unit.name }} (#{{ unit.unit_id }})
                    </option>
                </select>
                <p v-if="form.errors.unit_kerja_id" class="text-red-500 text-sm mt-1">{{ form.errors.unit_kerja_id }}</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pemasukan</label>
                    <div class="relative">
                        <span class="absolute left-3 top-3 text-gray-500">Rp</span>
                        <input v-model="form.pemasukan" type="number" step="0.01" min="0" class="w-full pl-10 pr-3 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-[#996600] transition-all duration-200" placeholder="0">
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Default: Total pemasukan buku kas ({{ formatNumber(selectedBukuKas.total_pemasukan) }})</p>
                    <p v-if="form.errors.pemasukan" class="text-red-500 text-sm mt-1">{{ form.errors.pemasukan }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pengeluaran</label>
                    <div class="relative">
                        <span class="absolute left-3 top-3 text-gray-500">Rp</span>
                        <input v-model="form.pengeluaran" type="number" step="0.01" min="0" class="w-full pl-10 pr-3 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-[#996600] transition-all duration-200" placeholder="0">
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Default: Total pengeluaran buku kas ({{ formatNumber(selectedBukuKas.total_pengeluaran) }})</p>
                    <p v-if="form.errors.pengeluaran" class="text-red-500 text-sm mt-1">{{ form.errors.pengeluaran }}</p>
                </div>
            </div>

            <div class="flex space-x-2">
                <button
                    type="button"
                    @click="useDefaultPemasukan"
                    class="px-3 py-1.5 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 text-sm transition-colors"
                >
                    Gunakan Total Pemasukan
                </button>
                <button
                    type="button"
                    @click="useDefaultPengeluaran"
                    class="px-3 py-1.5 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 text-sm transition-colors"
                >
                    Gunakan Total Pengeluaran
                </button>
                <button
                    type="button"
                    @click="useSaldo"
                    class="px-3 py-1.5 bg-[#eae0cc] text-[#6b4700] rounded-lg hover:bg-[#d6c199] text-sm transition-colors"
                >
                    Gunakan Saldo
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
    form: Object,
    allBukuKas: Array,
    currentBukuKasId: Number,
    workUnits: Array,
});

const selectedBukuKas = ref(null);

const availableBukuKas = computed(() => {
    return (props.allBukuKas || []).filter(buku => buku.id !== props.currentBukuKasId);
});

const onSelectBukuKas = () => {
    if (!selectedBukuKas.value) return;

    props.form.source_buku_kas_id = selectedBukuKas.value.id;

    // Set default values
    const today = new Date().toISOString().split('T')[0];
    props.form.tanggal = today;
    props.form.kategori = 'Transfer Kas';
    props.form.deskripsi = `Pencatatan dari Buku Kas: ${selectedBukuKas.value.nama}`;

    // Auto-fill both pemasukan and pengeluaran using total amounts
    useSaldo();
};

const useDefaultPemasukan = () => {
    props.form.pemasukan = selectedBukuKas.value.total_pemasukan;
    props.form.pengeluaran = '';
};

const useDefaultPengeluaran = () => {
    props.form.pengeluaran = selectedBukuKas.value.total_pengeluaran;
    props.form.pemasukan = '';
};

const useSaldo = () => {
    // Fill both fields with total pemasukan and pengeluaran
    props.form.pemasukan = selectedBukuKas.value.total_pemasukan;
    props.form.pengeluaran = selectedBukuKas.value.total_pengeluaran;
};

const formatNumber = (number) => {
    return new Intl.NumberFormat('id-ID').format(number || 0);
};
</script>
