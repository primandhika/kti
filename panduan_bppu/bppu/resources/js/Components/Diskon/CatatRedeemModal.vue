<template>
    <Teleport to="body">
        <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" @click="$emit('close')"></div>
            <div class="flex min-h-screen items-center justify-center p-4">
                <div class="relative bg-white rounded-lg shadow-xl max-w-3xl w-full p-6 max-h-[90vh] overflow-y-auto">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">
                            {{ isCampaign ? 'Catat Campaign ke Buku Kas' : 'Catat Potongan ke Buku Kas' }}
                        </h3>
                        <button @click="$emit('close')" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Info Redeem -->
                    <div v-if="redeem" class="mb-5 p-4 bg-[#f4efe5] rounded-lg">
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3 text-sm">
                            <div>
                                <p class="text-gray-500 text-xs">Sumber</p>
                                <p class="font-medium">{{ sourceLabel }}</p>
                            </div>
                            <div v-if="isCampaign">
                                <p class="text-gray-500 text-xs">Campaign</p>
                                <p class="font-medium">{{ redeem.nama_potongan }}</p>
                            </div>
                            <div v-if="isCampaign">
                                <p class="text-gray-500 text-xs">Jumlah Voucher</p>
                                <p class="font-medium">{{ redeem.jumlah_voucher }} voucher</p>
                            </div>
                            <div v-if="!isCampaign && redeem.kode_voucher">
                                <p class="text-gray-500 text-xs">Kode Voucher</p>
                                <p class="font-mono font-medium">{{ redeem.kode_voucher }}</p>
                            </div>
                            <div v-if="!isCampaign && redeem.poin">
                                <p class="text-gray-500 text-xs">Poin</p>
                                <p class="font-medium">{{ Number(redeem.poin).toLocaleString('id-ID') }} poin</p>
                            </div>
                            <div>
                                <p class="text-gray-500 text-xs">Nilai Potongan</p>
                                <p class="font-medium text-[#996600]">Rp {{ formatRupiah(redeem.nilai_potongan) }}</p>
                            </div>
                            <div v-if="!isCampaign">
                                <p class="text-gray-500 text-xs">Member</p>
                                <p class="font-medium">{{ redeem.member_name || '-' }}</p>
                            </div>
                            <div v-if="!isCampaign && redeem.nomor_transaksi">
                                <p class="text-gray-500 text-xs">No. Transaksi</p>
                                <p class="font-mono text-xs font-medium">{{ redeem.nomor_transaksi }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Pilih Buku Kas (search + suggestion dropdown) -->
                    <div class="mb-5 relative" ref="bukuKasRef">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Pilih Buku Kas <span class="text-red-500">*</span>
                        </label>

                        <!-- Buku kas terpilih -->
                        <div
                            v-if="selectedBukuKas"
                            class="flex items-center justify-between gap-3 p-3 rounded-lg border-2 border-[#996600] bg-[#99660010]"
                        >
                            <div class="flex items-center gap-3 min-w-0">
                                <div
                                    :style="selectedBukuKas.jenis_buku_kas ? { backgroundColor: selectedBukuKas.jenis_buku_kas.warna } : {}"
                                    :class="selectedBukuKas.jenis_buku_kas ? '' : 'bg-gray-400'"
                                    class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-white font-bold text-xs flex-shrink-0"
                                >
                                    {{ selectedBukuKas.jenis_buku_kas?.kode || 'BK' }}
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ selectedBukuKas.nama }}</p>
                                    <p class="text-xs text-gray-500 truncate">
                                        {{ selectedBukuKas.user_name }}
                                        <span v-if="selectedBukuKas.jenis_buku_kas"> &middot; {{ selectedBukuKas.jenis_buku_kas.nama }}</span>
                                    </p>
                                </div>
                            </div>
                            <button type="button" @click="clearBukuKas" class="text-gray-400 hover:text-red-500 flex-shrink-0" title="Ganti buku kas">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Search input + dropdown -->
                        <div v-else>
                            <div class="relative">
                                <svg class="absolute left-3 top-3 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                <input
                                    v-model="bukuKasSearch"
                                    @focus="showBukuKasDropdown = true"
                                    type="text"
                                    placeholder="Cari buku kas..."
                                    class="w-full pl-9 pr-3 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-[#996600]"
                                >
                            </div>
                            <div
                                v-if="showBukuKasDropdown"
                                class="absolute z-10 left-0 right-0 mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-56 overflow-y-auto"
                            >
                                <button
                                    v-for="buku in filteredBukuKas"
                                    :key="buku.id"
                                    type="button"
                                    @click="selectBukuKas(buku)"
                                    class="w-full text-left p-3 hover:bg-[#f4efe5] transition-colors flex items-center gap-3 border-b border-gray-50 last:border-0"
                                >
                                    <div
                                        :style="buku.jenis_buku_kas ? { backgroundColor: buku.jenis_buku_kas.warna } : {}"
                                        :class="buku.jenis_buku_kas ? '' : 'bg-gray-400'"
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-white font-bold text-xs flex-shrink-0"
                                    >
                                        {{ buku.jenis_buku_kas?.kode || 'BK' }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">{{ buku.nama }}</p>
                                        <p class="text-xs text-gray-500 truncate">
                                            {{ buku.user_name }}
                                            <span v-if="buku.jenis_buku_kas"> &middot; {{ buku.jenis_buku_kas.nama }}</span>
                                        </p>
                                    </div>
                                </button>
                                <div v-if="filteredBukuKas.length === 0" class="p-3 text-sm text-gray-400 text-center">
                                    Buku kas tidak ditemukan
                                </div>
                            </div>
                        </div>

                        <p v-if="bukuKasList.length === 0" class="text-sm text-red-500 mt-2">
                            Tidak ada buku kas tersedia. Buat buku kas terlebih dahulu.
                        </p>
                        <p v-if="form.errors.buku_kas_id" class="text-red-500 text-sm mt-1">{{ form.errors.buku_kas_id }}</p>
                    </div>

                    <!-- Detail transaksi (sama seperti form buku kas) -->
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal <span class="text-red-500">*</span></label>
                                <input v-model="form.tanggal" type="date" class="w-full px-3 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-[#996600]">
                                <p v-if="form.errors.tanggal" class="text-red-500 text-sm mt-1">{{ form.errors.tanggal }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Kategori <span class="text-red-500">*</span></label>
                                <select v-model="form.kategori" class="w-full px-3 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-[#996600] bg-white">
                                    <option value="">Pilih Kategori</option>
                                    <option v-for="kat in kategoriList" :key="kat.id" :value="kat.nama">
                                        {{ kat.kode_akun ? `[${kat.kode_akun}] ${kat.nama}` : kat.nama }}
                                    </option>
                                </select>
                                <p v-if="form.errors.kategori" class="text-red-500 text-sm mt-1">{{ form.errors.kategori }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Transaksi</label>
                                <select v-model="form.jenis_transaksi" class="w-full px-3 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-[#996600] bg-white">
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
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Unit Kerja</label>
                            <select v-model="form.unit_kerja_id" class="w-full px-3 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-[#996600] bg-white">
                                <option :value="null">Pilih Unit Kerja</option>
                                <option v-for="unit in workUnits" :key="unit.id" :value="unit.id">
                                    {{ unit.name }}<span v-if="unit.unit_id"> (#{{ unit.unit_id }})</span>
                                </option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi <span class="text-red-500">*</span></label>
                            <textarea v-model="form.deskripsi" rows="2" class="w-full px-3 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-[#996600]" placeholder="Deskripsi transaksi..."></textarea>
                            <p v-if="form.errors.deskripsi" class="text-red-500 text-sm mt-1">{{ form.errors.deskripsi }}</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Pengeluaran</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-3 text-gray-500">Rp</span>
                                    <input v-model="form.pengeluaran" type="number" step="0.01" min="0" class="w-full pl-10 pr-3 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-[#996600]" placeholder="0">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Pemasukan</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-3 text-gray-500">Rp</span>
                                    <input v-model="form.pemasukan" type="number" step="0.01" min="0" class="w-full pl-10 pr-3 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-[#996600]" placeholder="0">
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Bukti Transaksi</label>
                                <div class="flex gap-4 mb-2">
                                    <label class="flex items-center"><input type="radio" v-model="form.bukti_transaksi_type" value="upload" class="mr-2"><span class="text-sm">Upload</span></label>
                                    <label class="flex items-center"><input type="radio" v-model="form.bukti_transaksi_type" value="link" class="mr-2"><span class="text-sm">Link URL</span></label>
                                </div>
                                <input v-if="form.bukti_transaksi_type === 'upload'" @change="e => form.bukti_transaksi = e.target.files[0]" type="file" accept="image/*" class="w-full px-3 py-2 border-2 border-gray-300 rounded-lg">
                                <input v-else v-model="form.bukti_transaksi_link" type="url" placeholder="https://..." class="w-full px-3 py-2 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600]">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Bukti Aktivitas</label>
                                <div class="flex gap-4 mb-2">
                                    <label class="flex items-center"><input type="radio" v-model="form.bukti_aktivitas_type" value="upload" class="mr-2"><span class="text-sm">Upload</span></label>
                                    <label class="flex items-center"><input type="radio" v-model="form.bukti_aktivitas_type" value="link" class="mr-2"><span class="text-sm">Link URL</span></label>
                                </div>
                                <input v-if="form.bukti_aktivitas_type === 'upload'" @change="e => form.bukti_aktivitas = e.target.files[0]" type="file" accept="image/*" class="w-full px-3 py-2 border-2 border-gray-300 rounded-lg">
                                <input v-else v-model="form.bukti_aktivitas_link" type="url" placeholder="https://..." class="w-full px-3 py-2 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600]">
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-end gap-3 mt-6">
                        <button @click="$emit('close')" type="button" class="px-5 py-2 border-2 border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Batal</button>
                        <button
                            @click="submit"
                            type="button"
                            :disabled="!form.buku_kas_id || form.processing"
                            :class="[
                                'px-5 py-2 rounded-lg transition-all',
                                form.buku_kas_id && !form.processing
                                    ? 'bg-[#996600] text-white hover:bg-[#6b4700]'
                                    : 'bg-gray-300 text-gray-500 cursor-not-allowed'
                            ]"
                        >
                            {{ form.processing ? 'Menyimpan...' : 'Catat ke Buku Kas' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </Teleport>
</template>

<script setup>
import { ref, computed, watch, onMounted, onBeforeUnmount } from 'vue'
import { useForm } from '@inertiajs/vue3'

const props = defineProps({
    show: Boolean,
    redeem: Object,
    bukuKasList: { type: Array, default: () => [] },
    workUnits: { type: Array, default: () => [] },
    kategoriList: { type: Array, default: () => [] },
})

const emit = defineEmits(['close', 'saved'])

const isCampaign = computed(() => props.redeem?.source === 'campaign')
const sourceLabel = computed(() => {
    if (isCampaign.value) return 'Campaign Voucher'
    return props.redeem?.source === 'voucher' ? 'Voucher' : 'Redeem Poin'
})

const form = useForm({
    // single redeem
    source: null,
    redeem_id: null,
    // campaign
    potongan_id: null,
    // shared
    buku_kas_id: null,
    tanggal: new Date().toISOString().slice(0, 10),
    kategori: '',
    jenis_transaksi: '',
    unit_kerja_id: null,
    deskripsi: '',
    pemasukan: 0,
    pengeluaran: 0,
    bukti_transaksi_type: 'upload',
    bukti_transaksi: null,
    bukti_transaksi_link: '',
    bukti_aktivitas_type: 'upload',
    bukti_aktivitas: null,
    bukti_aktivitas_link: '',
})

// ---- Buku kas combobox ----
const bukuKasRef = ref(null)
const bukuKasSearch = ref('')
const showBukuKasDropdown = ref(false)

const selectedBukuKas = computed(() =>
    props.bukuKasList.find((b) => b.id === form.buku_kas_id) || null
)

const filteredBukuKas = computed(() => {
    const q = bukuKasSearch.value.trim().toLowerCase()
    if (!q) return props.bukuKasList
    return props.bukuKasList.filter((b) =>
        (b.nama || '').toLowerCase().includes(q) ||
        (b.user_name || '').toLowerCase().includes(q) ||
        (b.jenis_buku_kas?.nama || '').toLowerCase().includes(q)
    )
})

function selectBukuKas(buku) {
    form.buku_kas_id = buku.id
    showBukuKasDropdown.value = false
    bukuKasSearch.value = ''
}

function clearBukuKas() {
    form.buku_kas_id = null
    bukuKasSearch.value = ''
    showBukuKasDropdown.value = true
}

function handleClickOutside(e) {
    if (bukuKasRef.value && !bukuKasRef.value.contains(e.target)) {
        showBukuKasDropdown.value = false
    }
}
onMounted(() => document.addEventListener('mousedown', handleClickOutside))
onBeforeUnmount(() => document.removeEventListener('mousedown', handleClickOutside))

// Isi ulang form tiap modal dibuka: default pengeluaran = nilai potongan
watch(() => props.show, (val) => {
    if (val && props.redeem) {
        form.reset()
        form.clearErrors()
        bukuKasSearch.value = ''
        showBukuKasDropdown.value = false
        form.buku_kas_id = null
        form.tanggal = props.redeem.tanggal
            ? new Date(props.redeem.tanggal).toISOString().slice(0, 10)
            : new Date().toISOString().slice(0, 10)
        form.unit_kerja_id = props.redeem.work_unit_id || null
        form.pengeluaran = props.redeem.nilai_potongan || 0
        form.pemasukan = 0

        if (isCampaign.value) {
            form.potongan_id = props.redeem.potongan_id
            form.deskripsi = `Potongan campaign ${props.redeem.nama_potongan || ''} (${props.redeem.jumlah_voucher} voucher)`
        } else {
            form.source = props.redeem.source
            form.redeem_id = props.redeem.id
            form.deskripsi = props.redeem.kode_voucher
                ? `Potongan voucher ${props.redeem.kode_voucher} (${props.redeem.nama_potongan || ''})`
                : `Potongan redeem poin - ${props.redeem.member_name || 'member'}`
            if (props.redeem.nomor_transaksi) {
                form.deskripsi += ` - ${props.redeem.nomor_transaksi}`
            }
        }
    }
})

function submit() {
    const url = isCampaign.value
        ? '/pengelola/diskon/redeem-poin/catat-campaign'
        : '/pengelola/diskon/redeem-poin/catat-buku-kas'
    form.post(url, {
        preserveScroll: true,
        onSuccess: () => {
            emit('saved')
            emit('close')
        },
    })
}

function formatRupiah(val) {
    return Number(val || 0).toLocaleString('id-ID')
}
</script>
