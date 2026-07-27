<template>
    <div class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal <span class="text-red-500">*</span></label>
                <input v-model="form.tanggal" type="date" required class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-[#996600] transition-all duration-200">
                <p v-if="form.errors.tanggal" class="text-red-500 text-sm mt-1">{{ form.errors.tanggal }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Kategori <span class="text-red-500">*</span></label>
                <select v-model="form.kategori" required class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-[#996600] transition-all duration-200 bg-white">
                    <option value="">Pilih Kategori</option>
                    <option v-for="kat in kategoriList" :key="kat.id" :value="kat.nama">
                        {{ kat.kode_akun ? `[${kat.kode_akun}] ${kat.nama}` : kat.nama }}
                    </option>
                </select>
                <p v-if="form.errors.kategori" class="text-red-500 text-sm mt-1">{{ form.errors.kategori }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Transaksi</label>
                <select v-model="form.jenis_transaksi" class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-[#996600] transition-all duration-200 bg-white">
                    <option value="">Pilih Jenis Transaksi</option>
                    <option value="Tunai">Tunai</option>
                    <option value="Transfer Bank">Transfer Bank</option>
                    <option value="QRIS">QRIS</option>
                    <option value="Debit Card">Debit Card</option>
                    <option value="Credit Card">Credit Card</option>
                    <option value="E-Wallet">E-Wallet (GoPay/OVO/Dana)</option>
                    <option value="Virtual Account">Virtual Account</option>
                    <option value="Cek/Giro">Cek/Giro</option>
                </select>
                <p v-if="form.errors.jenis_transaksi" class="text-red-500 text-sm mt-1">{{ form.errors.jenis_transaksi }}</p>
            </div>
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

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi <span class="text-red-500">*</span></label>
            <textarea v-model="form.deskripsi" rows="3" required class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-[#996600] transition-all duration-200" placeholder="Deskripsi transaksi..."></textarea>
            <p v-if="form.errors.deskripsi" class="text-red-500 text-sm mt-1">{{ form.errors.deskripsi }}</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Pemasukan</label>
                <div class="relative">
                    <span class="absolute left-3 top-3 text-gray-500">Rp</span>
                    <input v-model="form.pemasukan" type="number" step="0.01" min="0" class="w-full pl-10 pr-3 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-[#996600] transition-all duration-200" placeholder="0">
                </div>
                <p v-if="form.errors.pemasukan" class="text-red-500 text-sm mt-1">{{ form.errors.pemasukan }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Pengeluaran</label>
                <div class="relative">
                    <span class="absolute left-3 top-3 text-gray-500">Rp</span>
                    <input v-model="form.pengeluaran" type="number" step="0.01" min="0" class="w-full pl-10 pr-3 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-[#996600] transition-all duration-200" placeholder="0">
                </div>
                <p v-if="form.errors.pengeluaran" class="text-red-500 text-sm mt-1">{{ form.errors.pengeluaran }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Bukti Transaksi</label>
                <div class="flex gap-4 mb-2">
                    <label class="flex items-center">
                        <input type="radio" v-model="form.bukti_transaksi_type" value="upload" class="mr-2">
                        <span class="text-sm">Upload File</span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" v-model="form.bukti_transaksi_type" value="link" class="mr-2">
                        <span class="text-sm">Link URL</span>
                    </label>
                </div>
                <input
                    v-if="form.bukti_transaksi_type === 'upload'"
                    @change="handleBuktiTransaksi"
                    type="file"
                    accept="image/*"
                    class="w-full px-3 py-2 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-[#996600] transition-all duration-200"
                >
                <input
                    v-else
                    v-model="form.bukti_transaksi_link"
                    type="url"
                    placeholder="https://example.com/bukti.jpg"
                    class="w-full px-3 py-2 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-[#996600] transition-all duration-200"
                >
                <p v-if="form.errors.bukti_transaksi" class="text-red-500 text-sm mt-1">{{ form.errors.bukti_transaksi }}</p>
                <p v-if="form.errors.bukti_transaksi_link" class="text-red-500 text-sm mt-1">{{ form.errors.bukti_transaksi_link }}</p>
                <p v-if="existingBuktiTransaksi && !form.bukti_transaksi" class="text-sm text-blue-600 mt-1">
                    <a :href="`/storage/${existingBuktiTransaksi}`" target="_blank" class="hover:underline">Lihat bukti saat ini</a>
                </p>
                <p v-if="existingBuktiTransaksiLink && form.bukti_transaksi_type === 'link'" class="text-sm text-blue-600 mt-1">
                    <a :href="existingBuktiTransaksiLink" target="_blank" class="hover:underline">Lihat link saat ini</a>
                </p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Bukti Aktivitas</label>
                <div class="flex gap-4 mb-2">
                    <label class="flex items-center">
                        <input type="radio" v-model="form.bukti_aktivitas_type" value="upload" class="mr-2">
                        <span class="text-sm">Upload File</span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" v-model="form.bukti_aktivitas_type" value="link" class="mr-2">
                        <span class="text-sm">Link URL</span>
                    </label>
                </div>
                <input
                    v-if="form.bukti_aktivitas_type === 'upload'"
                    @change="handleBuktiAktivitas"
                    type="file"
                    accept="image/*"
                    class="w-full px-3 py-2 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-[#996600] transition-all duration-200"
                >
                <input
                    v-else
                    v-model="form.bukti_aktivitas_link"
                    type="url"
                    placeholder="https://example.com/bukti.jpg"
                    class="w-full px-3 py-2 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-[#996600] transition-all duration-200"
                >
                <p v-if="form.errors.bukti_aktivitas" class="text-red-500 text-sm mt-1">{{ form.errors.bukti_aktivitas }}</p>
                <p v-if="form.errors.bukti_aktivitas_link" class="text-red-500 text-sm mt-1">{{ form.errors.bukti_aktivitas_link }}</p>
                <p v-if="existingBuktiAktivitas && !form.bukti_aktivitas" class="text-sm text-blue-600 mt-1">
                    <a :href="`/storage/${existingBuktiAktivitas}`" target="_blank" class="hover:underline">Lihat bukti saat ini</a>
                </p>
                <p v-if="existingBuktiAktivitasLink && form.bukti_aktivitas_type === 'link'" class="text-sm text-blue-600 mt-1">
                    <a :href="existingBuktiAktivitasLink" target="_blank" class="hover:underline">Lihat link saat ini</a>
                </p>
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    form: Object,
    workUnits: Array,
    kategoriList: Array,
    existingBuktiTransaksi: String,
    existingBuktiTransaksiLink: String,
    existingBuktiAktivitas: String,
    existingBuktiAktivitasLink: String,
});

const emit = defineEmits(['bukti-transaksi-change', 'bukti-aktivitas-change']);

const handleBuktiTransaksi = (event) => {
    emit('bukti-transaksi-change', event.target.files[0]);
};

const handleBuktiAktivitas = (event) => {
    emit('bukti-aktivitas-change', event.target.files[0]);
};
</script>
