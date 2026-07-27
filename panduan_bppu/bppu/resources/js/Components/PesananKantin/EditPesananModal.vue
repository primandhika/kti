<template>
    <Teleport to="body">
        <Transition
            enter-active-class="transition duration-150"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition duration-100"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center p-3 bg-black/50" @click.self="$emit('close')">
                <div class="bg-white rounded-xl shadow-xl w-full max-w-3xl max-h-[90vh] flex flex-col">

                    <!-- Header -->
                    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                        <div>
                            <h2 class="font-bold text-gray-800 text-base">Edit Pesanan #{{ pesanan.nomor_antrian }}</h2>
                            <p class="text-xs text-gray-400 mt-0.5">{{ pesanan.nama_pemesan }}</p>
                        </div>
                        <button @click="$emit('close')" class="text-gray-400 hover:text-gray-600 p-1">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <div class="flex flex-col md:flex-row flex-1 min-h-0 overflow-hidden">

                        <!-- KIRI: Item yang sudah dipesan -->
                        <div class="md:w-1/2 flex flex-col border-r border-gray-100">
                            <div class="px-4 pt-3 pb-2 border-b border-gray-50">
                                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Item Pesanan</h3>
                            </div>
                            <div class="flex-1 overflow-y-auto px-4 py-2 space-y-2">
                                <div
                                    v-for="(item, i) in editedItems"
                                    :key="i"
                                    class="flex items-center gap-3 py-2 border-b border-gray-50 last:border-0"
                                >
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-800 truncate">{{ item.name }}</p>
                                        <p v-if="item.varian" class="text-xs text-gray-400">{{ item.varian.nama_varian }}</p>
                                        <p class="text-xs text-[#996600] font-semibold">{{ formatRupiah(item.price) }}</p>
                                    </div>
                                    <div class="flex items-center gap-1.5 flex-shrink-0">
                                        <button
                                            @click="decreaseQty(i)"
                                            class="w-6 h-6 rounded-full border border-gray-300 text-gray-600 flex items-center justify-center text-sm hover:bg-gray-100"
                                        >-</button>
                                        <span class="w-6 text-center text-sm font-semibold text-gray-800">{{ item.quantity }}</span>
                                        <button
                                            @click="increaseQty(i)"
                                            class="w-6 h-6 rounded-full border border-gray-300 text-gray-600 flex items-center justify-center text-sm hover:bg-gray-100"
                                        >+</button>
                                        <button
                                            @click="removeItem(i)"
                                            class="w-6 h-6 ml-1 rounded-full text-red-400 flex items-center justify-center hover:bg-red-50"
                                        >
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                                <p v-if="editedItems.length === 0" class="text-xs text-gray-400 text-center py-6">Tidak ada item. Tambah dari katalog.</p>
                            </div>

                            <!-- Catatan -->
                            <div class="px-4 py-3 border-t border-gray-100">
                                <label class="block text-xs font-semibold text-gray-500 mb-1">Catatan</label>
                                <input
                                    v-model="editedCatatan"
                                    type="text"
                                    maxlength="255"
                                    placeholder="Catatan pesanan..."
                                    class="w-full text-xs border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:border-[#996600] focus:ring-1 focus:ring-[#996600]/20"
                                />
                            </div>

                            <!-- Total -->
                            <div class="px-4 py-3 border-t border-gray-100 flex items-center justify-between">
                                <span class="text-sm text-gray-500">Total</span>
                                <span class="text-base font-bold text-[#996600]">{{ formatRupiah(totalBaru) }}</span>
                            </div>
                        </div>

                        <!-- KANAN: Katalog tambah item -->
                        <div class="md:w-1/2 flex flex-col">
                            <div class="px-4 pt-3 pb-2 border-b border-gray-50 flex items-center gap-2">
                                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide flex-1">Tambah dari Katalog</h3>
                                <input
                                    v-model="searchKatalog"
                                    type="text"
                                    placeholder="Cari..."
                                    class="text-xs border border-gray-200 rounded-lg px-2.5 py-1.5 w-28 focus:outline-none focus:border-[#996600]"
                                />
                            </div>
                            <div class="flex-1 overflow-y-auto px-4 py-2">
                                <div v-if="loadingKatalog" class="flex items-center justify-center py-8">
                                    <svg class="animate-spin w-5 h-5 text-[#996600]" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                    </svg>
                                </div>
                                <div v-else class="space-y-1.5">
                                    <button
                                        v-for="barang in filteredKatalog"
                                        :key="barang.id"
                                        @click="addFromKatalog(barang)"
                                        class="w-full flex items-center gap-3 px-3 py-2 rounded-lg border border-gray-100 hover:border-[#c1a366] hover:bg-[#f4efe5] transition-colors text-left"
                                    >
                                        <img
                                            v-if="barang.gambar"
                                            :src="barang.gambar"
                                            class="w-8 h-8 rounded object-cover flex-shrink-0"
                                        />
                                        <div v-else class="w-8 h-8 rounded bg-gray-100 flex-shrink-0 flex items-center justify-center">
                                            <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14"/>
                                            </svg>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs font-medium text-gray-800 truncate">{{ barang.name }}</p>
                                            <p class="text-xs text-[#996600]">{{ formatRupiah(barang.price) }}</p>
                                        </div>
                                        <span class="text-xs text-gray-400 flex-shrink-0">stok {{ barang.stok }}</span>
                                        <svg class="w-4 h-4 text-[#996600] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                    </button>
                                    <p v-if="filteredKatalog.length === 0 && !loadingKatalog" class="text-xs text-gray-400 text-center py-6">
                                        Tidak ada barang ditemukan.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer actions -->
                    <div class="flex items-center justify-end gap-2 px-5 py-4 border-t border-gray-100">
                        <button
                            @click="$emit('close')"
                            :disabled="saving"
                            class="px-4 py-2 text-xs font-semibold text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 disabled:opacity-50"
                        >
                            Batal
                        </button>
                        <button
                            @click="simpan"
                            :disabled="saving || editedItems.length === 0"
                            class="px-5 py-2 text-xs font-semibold text-white bg-[#996600] rounded-lg hover:bg-[#7a5100] disabled:opacity-50 flex items-center gap-2"
                        >
                            <svg v-if="saving" class="animate-spin w-3.5 h-3.5" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                            </svg>
                            Simpan Perubahan
                        </button>
                    </div>

                    <!-- Varian picker modal -->
                    <VarianPicker
                        v-if="varianTarget"
                        :barang="varianTarget"
                        @confirm="onVarianConfirm"
                        @cancel="varianTarget = null"
                    />
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import VarianPicker from './EditPesananVarianPicker.vue'

const props = defineProps({
    show:        { type: Boolean, required: true },
    pesanan:     { type: Object,  required: true },
    katalogUrl:  { type: String,  required: true },
    editUrl:     { type: String,  required: true },
})

const emit = defineEmits(['close', 'saved'])

const editedItems   = ref([])
const editedCatatan = ref('')
const katalog       = ref([])
const loadingKatalog = ref(false)
const saving        = ref(false)
const searchKatalog = ref('')
const varianTarget  = ref(null)

watch(() => props.show, async (val) => {
    if (!val) return
    editedItems.value   = JSON.parse(JSON.stringify(props.pesanan.items || []))
    editedCatatan.value = props.pesanan.catatan || ''
    searchKatalog.value = ''
    await fetchKatalog()
})

async function fetchKatalog() {
    if (katalog.value.length > 0) return
    loadingKatalog.value = true
    try {
        const res  = await fetch(props.katalogUrl, { headers: { Accept: 'application/json' } })
        const data = await res.json()
        katalog.value = data.items || []
    } catch {
        // silent
    } finally {
        loadingKatalog.value = false
    }
}

const filteredKatalog = computed(() => {
    const q = searchKatalog.value.toLowerCase().trim()
    return q ? katalog.value.filter(b => b.name.toLowerCase().includes(q)) : katalog.value
})

const totalBaru = computed(() =>
    editedItems.value.reduce((s, item) => s + item.price * item.quantity, 0)
)

function decreaseQty(i) {
    if (editedItems.value[i].quantity > 1) {
        editedItems.value[i].quantity--
    } else {
        editedItems.value.splice(i, 1)
    }
}

function increaseQty(i) {
    editedItems.value[i].quantity++
}

function removeItem(i) {
    editedItems.value.splice(i, 1)
}

function addFromKatalog(barang) {
    if (barang.varians && barang.varians.length > 0) {
        varianTarget.value = barang
        return
    }
    pushItem(barang, null)
}

function onVarianConfirm({ barang, varian }) {
    varianTarget.value = null
    const price = barang.price + (varian ? varian.harga_tambahan : 0)
    pushItem(barang, varian, price)
}

function pushItem(barang, varian, price = null) {
    const finalPrice = price ?? barang.price
    const existing = editedItems.value.findIndex(
        i => i.id === barang.id && (varian ? i.varian?.id === varian.id : !i.varian)
    )
    if (existing !== -1) {
        editedItems.value[existing].quantity++
    } else {
        editedItems.value.push({
            id:       barang.id,
            name:     barang.name,
            price:    finalPrice,
            quantity: 1,
            varian:   varian ? { id: varian.id, nama_varian: varian.nama_varian } : null,
        })
    }
}

async function simpan() {
    if (saving.value) return
    saving.value = true
    try {
        const token = document.querySelector('meta[name="csrf-token"]')?.content || ''
        const res   = await fetch(props.editUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                Accept: 'application/json',
            },
            body: JSON.stringify({
                items:   editedItems.value,
                catatan: editedCatatan.value,
            }),
        })
        const data = await res.json()
        if (res.ok && data.success) {
            emit('saved', { items: data.items, total: data.total, catatan: data.catatan })
            emit('close')
        } else {
            alert(data.error || 'Gagal menyimpan perubahan')
        }
    } catch {
        alert('Terjadi kesalahan saat menyimpan')
    } finally {
        saving.value = false
    }
}

function formatRupiah(value) {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value)
}
</script>
