<template>
  <AdminLayout pageTitle="Riwayat Pembagian Mitra">
    <div class="min-h-screen bg-gray-50 p-4">
      <div class="max-w-6xl mx-auto space-y-4">

        <!-- Breadcrumbs -->
        <nav class="flex text-xs text-gray-500" aria-label="Breadcrumb">
          <ol class="inline-flex items-center space-x-1">
            <li class="inline-flex items-center">
              <Link :href="route('admin.rekap-penjualan')" class="hover:text-[#996600] transition-colors">
                Rekap Penjualan
              </Link>
            </li>
            <li class="inline-flex items-center">
              <svg class="w-3 h-3 mx-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
              <Link :href="route('pos.pembagianMitra')" class="hover:text-[#996600] transition-colors">
                Pembagian Mitra
              </Link>
            </li>
            <li class="inline-flex items-center">
              <svg class="w-3 h-3 mx-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
              <span class="text-gray-900 font-medium">Riwayat</span>
            </li>
          </ol>
        </nav>

        <!-- Header -->
        <div class="bg-white rounded-lg shadow-md p-5 flex items-center justify-between">
          <div>
            <h1 class="text-xl font-bold text-gray-900">Riwayat Pembagian Mitra</h1>
            <p class="text-sm text-gray-500 mt-0.5">Tandai status pencairan dana per mitra per periode</p>
          </div>
          <Link
            :href="route('pos.pembagianMitra')"
            class="px-4 py-2 bg-[#996600] text-white rounded-lg hover:bg-[#7a5100] text-sm font-medium"
          >
            Kembali
          </Link>
        </div>

        <!-- Summary badges -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
          <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-100">
            <p class="text-xs text-gray-500">Total Riwayat</p>
            <p class="text-2xl font-bold text-gray-800">{{ summary.total }}</p>
          </div>
          <div class="bg-yellow-50 rounded-lg shadow-sm p-4 border border-yellow-200">
            <p class="text-xs text-yellow-700">Mengajukan</p>
            <p class="text-2xl font-bold text-yellow-600">{{ summary.mengajukan }}</p>
          </div>
          <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-100">
            <p class="text-xs text-gray-500">Sudah Cair</p>
            <p class="text-2xl font-bold text-green-600">{{ summary.sudah_cair }}</p>
          </div>
          <div class="bg-white rounded-lg shadow-sm p-4 border border-green-50 border">
            <p class="text-xs text-gray-500">Total Dicairkan</p>
            <p class="text-lg font-bold text-green-700">{{ formatCurrency(summary.total_dicairkan) }}</p>
          </div>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
          <div v-if="riwayat.data.length === 0" class="text-center py-16 text-gray-400">
            Belum ada riwayat pembagian yang disimpan
          </div>

          <div v-else class="overflow-x-auto">
            <table class="w-full text-sm">
              <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                  <th class="px-3 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Mitra</th>
                  <th class="px-3 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Periode</th>
                  <th class="px-3 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Skema</th>
                  <th class="px-3 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Bagian Vendor</th>
                  <th class="px-3 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Status Cair</th>
                  <th class="px-3 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200">
                <tr
                  v-for="item in riwayat.data"
                  :key="item.id"
                  :class="['hover:bg-gray-50', item.status_pencairan === 'dicairkan' ? 'bg-green-50/30' : item.status_pencairan === 'mengajukan' ? 'bg-yellow-50/40' : '']"
                >
                  <!-- Mitra -->
                  <td class="px-3 py-3">
                    <div class="font-medium text-gray-900">{{ item.supplier?.nama || 'Unknown' }}</div>
                    <div class="text-xs text-gray-500">{{ item.work_unit?.name || 'Semua Unit' }} &bull; {{ item.total_items }} item</div>
                  </td>

                  <!-- Periode -->
                  <td class="px-3 py-3 text-xs text-gray-600 whitespace-nowrap">
                    {{ formatDateShort(item.periode_mulai) }}<br>s/d {{ formatDateShort(item.periode_selesai) }}
                  </td>

                  <!-- Skema -->
                  <td class="px-3 py-3">
                    <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded font-medium">
                      {{ item.jenis_skema }}
                    </span>
                  </td>

                  <!-- Bagian Vendor -->
                  <td class="px-3 py-3 text-right font-semibold text-green-700">
                    {{ formatCurrency(item.bagian_vendor) }}
                  </td>

                  <!-- Status Cair -->
                  <td class="px-3 py-3 text-center">
                    <div v-if="item.status_pencairan === 'dicairkan'">
                      <span class="inline-flex items-center gap-1 px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full font-semibold">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        Sudah Cair
                      </span>
                      <div class="text-[10px] text-gray-400 mt-1">
                        {{ formatDateShort(item.dicairkan_at) }}<br>
                        oleh {{ item.dicairkan_oleh?.name || '-' }}<br>
                        <span class="font-medium text-gray-500">{{ metodeLabel(item.metode_pencairan) }}</span>
                        <span v-if="item.detail_transfer"> &bull; {{ item.detail_transfer }}</span>
                      </div>
                    </div>
                    <span v-else-if="item.status_pencairan === 'mengajukan'" class="inline-flex items-center gap-1 px-2 py-1 bg-yellow-100 text-yellow-700 text-xs rounded-full font-semibold">
                      <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                      Mengajukan
                    </span>
                    <span v-else class="inline-flex items-center gap-1 px-2 py-1 bg-gray-100 text-gray-500 text-xs rounded-full font-semibold">
                      Belum Cair
                    </span>
                  </td>

                  <!-- Aksi -->
                  <td class="px-3 py-3 text-center">
                    <div class="flex items-center justify-center gap-1">
                      <!-- Cetak Rincian -->
                      <button
                        @click="bukaPrintModal(item)"
                        class="p-1 text-[#996600] hover:bg-[#f4efe5] rounded transition-colors"
                        title="Cetak Rincian"
                      >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                      </button>

                      <button
                        v-if="item.status_pencairan !== 'dicairkan'"
                        @click="bukaCairModal(item)"
                        :class="[
                          'px-2 py-1 text-xs font-medium rounded transition-colors',
                          item.status_pencairan === 'mengajukan'
                            ? 'text-white bg-[#996600] hover:bg-[#7a5100]'
                            : 'text-white bg-green-600 hover:bg-green-700'
                        ]"
                        :title="item.status_pencairan === 'mengajukan' ? 'Konfirmasi & Cairkan' : 'Tandai Dana Cair'"
                      >
                        {{ item.status_pencairan === 'mengajukan' ? 'Cairkan' : 'Tandai Cair' }}
                      </button>
                      <button
                        v-if="item.status_pencairan === 'dicairkan'"
                        @click="batalkanCair(item)"
                        class="px-2 py-1 text-xs font-medium text-orange-700 bg-orange-50 hover:bg-orange-100 rounded transition-colors"
                        title="Batalkan Tanda Cair"
                      >
                        Batalkan
                      </button>
                      <button
                        @click="deleteRiwayat(item)"
                        class="p-1 text-red-500 hover:bg-red-50 rounded transition-colors"
                        title="Hapus"
                      >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <div v-if="riwayat.data.length > 0" class="px-4 py-3 border-t border-gray-200">
            <PaginationControls :pagination="riwayat" />
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Tandai Cair -->
    <div v-if="showCairModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-xl shadow-xl max-w-md w-full">
        <div class="p-5 border-b border-gray-200">
          <h2 class="text-base font-bold text-gray-900">Tandai Dana Sudah Cair</h2>
          <p class="text-sm text-gray-500 mt-1">
            <span class="font-medium text-gray-700">{{ selectedItem?.supplier?.nama }}</span>
            &bull; {{ formatDateShort(selectedItem?.periode_mulai) }} s/d {{ formatDateShort(selectedItem?.periode_selesai) }}
          </p>
        </div>
        <div class="p-5 space-y-4">
          <div class="bg-green-50 border border-green-200 rounded-lg p-3">
            <p class="text-xs text-green-600 font-medium">Jumlah yang dicairkan</p>
            <p class="text-xl font-bold text-green-700">{{ formatCurrency(selectedItem?.bagian_vendor) }}</p>
          </div>

          <!-- Metode Pencairan -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Metode Pencairan *</label>
            <div class="grid grid-cols-2 gap-2">
              <button
                type="button"
                @click="cairForm.metode_pencairan = 'tunai'"
                :class="[
                  'flex items-center gap-2 px-3 py-2.5 rounded-lg border-2 text-sm font-medium transition-colors',
                  cairForm.metode_pencairan === 'tunai'
                    ? 'border-[#996600] bg-[#f4efe5] text-[#6b4700]'
                    : 'border-gray-200 bg-white text-gray-600 hover:border-gray-300'
                ]"
              >
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                Tunai
              </button>
              <button
                type="button"
                @click="cairForm.metode_pencairan = 'transfer_bank'"
                :class="[
                  'flex items-center gap-2 px-3 py-2.5 rounded-lg border-2 text-sm font-medium transition-colors',
                  cairForm.metode_pencairan === 'transfer_bank'
                    ? 'border-[#996600] bg-[#f4efe5] text-[#6b4700]'
                    : 'border-gray-200 bg-white text-gray-600 hover:border-gray-300'
                ]"
              >
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
                Transfer Bank
              </button>
            </div>
          </div>

          <!-- Detail Transfer -->
          <div v-if="cairForm.metode_pencairan === 'transfer_bank'">
            <label class="block text-sm font-medium text-gray-700 mb-1">Detail Transfer</label>
            <input
              v-model="cairForm.detail_transfer"
              type="text"
              placeholder="Cth: BCA 1234567890 a.n. BPPU"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#996600] focus:border-transparent"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Catatan (opsional)</label>
            <textarea
              v-model="cairForm.catatan_pencairan"
              rows="2"
              placeholder="Catatan tambahan..."
              class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#996600] focus:border-transparent"
            ></textarea>
          </div>
        </div>
        <div class="p-5 border-t border-gray-200 flex gap-3">
          <button
            @click="showCairModal = false"
            class="flex-1 px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 text-sm font-medium"
          >
            Batal
          </button>
          <button
            @click="submitTandaiCair"
            :disabled="processingCair"
            class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm font-medium disabled:opacity-50"
          >
            {{ processingCair ? 'Menyimpan...' : 'Tandai Cair' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Modal Cetak Rincian -->
    <RiwayatPembagianPrintModal
      :show="showPrintModal"
      :item="selectedPrintItem"
      @close="showPrintModal = false"
    />
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import RiwayatPembagianPrintModal from '@/Components/Mitra/RiwayatPembagianPrintModal.vue'
import PaginationControls from '@/Components/StockOpname/PaginationControls.vue'

const props = defineProps({
  riwayat: Object,
  summary: Object,
})

const showCairModal    = ref(false)
const selectedItem     = ref(null)
const processingCair   = ref(false)
const cairForm         = ref({ metode_pencairan: 'tunai', detail_transfer: '', catatan_pencairan: '' })

const showPrintModal   = ref(false)
const selectedPrintItem = ref(null)

const formatDateShort = (dateStr) => {
  if (!dateStr) return '-'
  return new Date(dateStr).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' })
}

const formatCurrency = (value) => {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value || 0)
}

const bukaCairModal = (item) => {
  selectedItem.value = item
  cairForm.value = { metode_pencairan: 'tunai', detail_transfer: '', catatan_pencairan: '' }
  showCairModal.value = true
}

const bukaPrintModal = (item) => {
  selectedPrintItem.value = item
  showPrintModal.value = true
}

const metodeLabel = (metode) => metode === 'transfer_bank' ? 'Transfer Bank' : 'Tunai'

const submitTandaiCair = () => {
  processingCair.value = true
  router.post(route('pos.tandaiCair', selectedItem.value.id), cairForm.value, {
    onSuccess: () => {
      showCairModal.value = false
      processingCair.value = false
    },
    onError: () => { processingCair.value = false },
  })
}

const batalkanCair = (item) => {
  if (!confirm(`Batalkan tanda cair untuk ${item.supplier?.nama}?`)) return
  router.post(route('pos.batalkanCair', item.id), {}, { preserveScroll: true })
}

const deleteRiwayat = (item) => {
  if (!confirm(`Hapus riwayat pembagian ${item.supplier?.nama}?`)) return
  router.delete(route('pos.hapusRiwayatPembagian', item.id))
}
</script>
