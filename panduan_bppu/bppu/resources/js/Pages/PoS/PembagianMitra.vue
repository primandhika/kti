<template>
  <component :is="layoutComponent" pageTitle="Pembagian Mitra">
    <div class="min-h-screen bg-gray-50">
      <!-- Header -->
      <div class="bg-white border-b sticky top-0 z-10 shadow-sm">
        <div class="px-3 py-2">
          <!-- Breadcrumbs -->
          <div class="flex items-center gap-1.5 mb-2 text-xs">
            <Link
              :href="route('admin.rekap-penjualan')"
              class="text-gray-500 hover:text-[#996600] transition-colors"
            >
              Rekap Penjualan
            </Link>
            <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            <span class="text-gray-900 font-medium">Pembagian Mitra</span>
            <span
              v-if="totalPengajuan > 0"
              class="ml-1 min-w-[18px] h-[18px] flex items-center justify-center text-[9px] font-bold bg-red-500 text-white rounded-full px-1"
            >{{ totalPengajuan }}</span>
          </div>

          <!-- Filters -->
          <div class="flex items-center gap-2 mb-2">
            <select
              v-model="selectedWorkUnitId"
              @change="applyFilters"
              class="flex-1 px-2 py-1.5 text-xs border border-gray-300 rounded focus:ring-2 focus:ring-[#996600] focus:border-transparent"
            >
              <option :value="null">Semua Unit</option>
              <option v-for="unit in workUnits" :key="unit.id" :value="unit.id">
                {{ unit.name }}
              </option>
            </select>
          </div>

          <div class="flex items-center gap-2">
            <input
              v-model="startDate"
              @change="applyFilters"
              type="date"
              class="flex-1 px-2 py-1.5 text-xs border border-gray-300 rounded focus:ring-2 focus:ring-[#996600] focus:border-transparent"
            />
            <input
              v-model="endDate"
              @change="applyFilters"
              type="date"
              class="flex-1 px-2 py-1.5 text-xs border border-gray-300 rounded focus:ring-2 focus:ring-[#996600] focus:border-transparent"
            />
          </div>

          <!-- Summary Stats (responsive: 2 rows on mobile, 1 row on desktop) -->
          <div class="mt-2 grid grid-cols-1 md:grid-cols-2 gap-1.5">
            <!-- Row 1 (mobile) / Left side (desktop) -->
            <div class="flex gap-1.5">
              <button
                @click="toggleVerifiedFilter"
                class="flex-1 bg-green-50 rounded px-2 py-1.5 hover:bg-green-100 transition-colors text-left"
                :class="showVerifiedOnly ? 'ring-2 ring-green-600' : ''"
              >
                <div class="text-[10px] md:text-xs text-gray-600 font-medium flex items-center gap-1">
                  Total
                  <svg v-if="showVerifiedOnly" class="w-3 h-3 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                  </svg>
                </div>
                <div class="text-xs md:text-sm font-bold text-green-900 mt-0.5">{{ formatCurrency(filteredSummary.total_penjualan) }}</div>
                <div class="text-[9px] md:text-[10px] mt-0.5" :class="showVerifiedOnly ? 'text-green-600 font-medium' : 'text-gray-500'">
                  <span v-if="showVerifiedOnly">Verified Only</span>
                  <span v-else>Mitra: {{ filteredSummary.total_mitra }} • Item: {{ filteredSummary.total_items }}</span>
                </div>
              </button>
              <button
                v-if="summary.tunai > 0"
                @click="togglePaymentFilter('tunai')"
                class="flex-1 rounded px-2 py-1.5 transition-colors text-left"
                :class="activePaymentFilters.includes('tunai') ? 'bg-gray-200 opacity-60' : 'bg-blue-50 hover:bg-blue-100'"
              >
                <div class="text-[10px] md:text-xs text-gray-600 font-medium">Tunai</div>
                <div class="text-xs md:text-sm font-bold text-blue-900 mt-0.5">{{ formatCurrency(summary.tunai) }}</div>
                <div class="text-[9px] text-gray-500">{{ summary.tunai_count }} trx</div>
              </button>
              <button
                v-if="summary.qris > 0"
                @click="togglePaymentFilter('qris')"
                class="flex-1 rounded px-2 py-1.5 transition-colors text-left"
                :class="activePaymentFilters.includes('qris') ? 'bg-gray-200 opacity-60' : 'bg-purple-50 hover:bg-purple-100'"
              >
                <div class="text-[10px] md:text-xs text-gray-600 font-medium">QRIS</div>
                <div class="text-xs md:text-sm font-bold text-purple-900 mt-0.5">{{ formatCurrency(summary.qris) }}</div>
                <div class="text-[9px] text-gray-500">{{ summary.qris_count }} trx</div>
              </button>
              <button
                v-if="summary.transfer > 0"
                @click="togglePaymentFilter('transfer')"
                class="flex-1 rounded px-2 py-1.5 transition-colors text-left"
                :class="activePaymentFilters.includes('transfer') ? 'bg-gray-200 opacity-60' : 'bg-indigo-50 hover:bg-indigo-100'"
              >
                <div class="text-[10px] md:text-xs text-gray-600 font-medium">Transfer</div>
                <div class="text-xs md:text-sm font-bold text-indigo-900 mt-0.5">{{ formatCurrency(summary.transfer) }}</div>
                <div class="text-[9px] text-gray-500">{{ summary.transfer_count }} trx</div>
              </button>
              <button
                v-if="summary.debit > 0"
                @click="togglePaymentFilter('debit')"
                class="flex-1 rounded px-2 py-1.5 transition-colors text-left"
                :class="activePaymentFilters.includes('debit') ? 'bg-gray-200 opacity-60' : 'bg-teal-50 hover:bg-teal-100'"
              >
                <div class="text-[10px] md:text-xs text-gray-600 font-medium">Debit</div>
                <div class="text-xs md:text-sm font-bold text-teal-900 mt-0.5">{{ formatCurrency(summary.debit) }}</div>
                <div class="text-[9px] text-gray-500">{{ summary.debit_count }} trx</div>
              </button>
              <button
                v-if="summary.kredit > 0"
                @click="togglePaymentFilter('kredit')"
                class="flex-1 rounded px-2 py-1.5 transition-colors text-left"
                :class="activePaymentFilters.includes('kredit') ? 'bg-gray-200 opacity-60' : 'bg-pink-50 hover:bg-pink-100'"
              >
                <div class="text-[10px] md:text-xs text-gray-600 font-medium">Kredit</div>
                <div class="text-xs md:text-sm font-bold text-pink-900 mt-0.5">{{ formatCurrency(summary.kredit) }}</div>
                <div class="text-[9px] text-gray-500">{{ summary.kredit_count }} trx</div>
              </button>
            </div>

            <!-- Row 2 (mobile) / Right side (desktop) -->
            <div class="flex gap-1.5">
              <div class="flex-1 bg-orange-50 rounded px-2 py-1.5">
                <div class="text-[10px] md:text-xs text-orange-700 font-medium">Mitra</div>
                <div class="text-xs md:text-sm font-bold text-orange-900 mt-0.5">{{ formatCurrency(filteredSummary.total_vendor || 0) }}</div>
              </div>
              <div class="flex-1 bg-amber-50 rounded px-2 py-1.5">
                <div class="text-[10px] md:text-xs text-amber-700 font-medium mb-0.5">Laba</div>
                <div class="text-xs md:text-sm font-bold text-amber-900">{{ formatCurrency(filteredTotalLaba) }}</div>
                <div class="text-[9px] md:text-[10px] text-amber-600 mt-0.5">
                  (Badan Usaha: {{ formatCurrency(filteredSummary.total_badan_usaha || 0) }} - Lainnya: {{ formatCurrency(filteredLabaLainnya) }})
                </div>
              </div>

              <!-- Action Buttons (Icon only) -->
              <div class="flex gap-1">
                <button
                  @click="downloadCSV"
                  title="Export CSV"
                  class="px-2 py-1 bg-[#6b4700] text-white rounded hover:bg-[#5b3d00] flex items-center justify-center"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                  </svg>
                </button>
                <button
                  @click="showSimpanRiwayatModal = true"
                  title="Simpan Riwayat"
                  class="px-2 py-1 bg-green-600 text-white rounded hover:bg-green-700 flex items-center justify-center"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                  </svg>
                </button>
                <Link
                  :href="route('pos.riwayatPembagian')"
                  title="Riwayat"
                  class="px-2 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center justify-center"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                </Link>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Content -->
      <div class="p-2">
        <!-- Empty State -->
        <div v-if="pembagian.data.length === 0" class="text-center py-8 bg-white rounded">
          <svg class="w-12 h-12 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
          </svg>
          <p class="text-gray-500 text-xs font-medium">Tidak ada data</p>
          <p class="text-gray-400 text-[10px] mt-0.5">Belum ada penjualan pada periode ini</p>
        </div>

        <!-- 2 Column Grid -->
        <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-2">
          <div
            v-for="(mitra, index) in pembagian.data"
            :key="index"
            class="bg-white rounded border border-gray-200 overflow-hidden"
          >
            <!-- Mitra Header (Compact) -->
            <div
              class="p-2 cursor-pointer hover:bg-gray-50 transition-colors border-b border-gray-100"
              @click="toggleExpand(index)"
            >
              <div class="flex items-start justify-between gap-2">
                <div class="flex-1 min-w-0">
                  <div class="flex items-center gap-1.5 mb-1">
                    <h3 class="font-bold text-sm text-gray-900 truncate">{{ mitra.supplier_name }}</h3>
                    <span
                      v-if="mitra.supplier_type"
                      class="px-1.5 py-0.5 text-[9px] font-semibold rounded-full bg-[#eae0cc] text-[#6b4700] flex-shrink-0"
                    >
                      {{ mitra.supplier_type }}
                    </span>
                  </div>
                  <div class="text-[10px] text-gray-500">
                    {{ mitra.total_items }} item &bull; {{ mitra.total_qty }} qty terjual
                  </div>
                  <div v-if="mitra.skema_bisnis" class="flex items-center gap-1 mt-0.5">
                    <span class="px-1.5 py-0.5 text-[9px] font-medium rounded bg-blue-100 text-blue-700">
                      {{ mitra.skema_bisnis.jenis_skema_label }}
                    </span>
                    <span v-if="!mitra.skema_bisnis.is_active" class="text-[9px] text-red-500 font-medium">
                      (Tidak Aktif)
                    </span>
                  </div>
                </div>

                <div class="text-right flex-shrink-0">
                  <div class="text-sm font-bold text-[#996600]">{{ formatCurrency(getFilteredMitraTotal(mitra)) }}</div>
                  <div class="text-[10px] text-gray-500">
                    {{ calculatePercentage(getFilteredMitraTotal(mitra)) }}%
                  </div>
                  <!-- Status pencairan badge (below percentage, clickable) -->
                  <Link
                    :href="route('pos.riwayatPembagian')"
                    @click.stop
                    :class="statusBadgeClass(getCairInfo(mitra.supplier_id))"
                    class="inline-flex items-center gap-0.5 mt-1 px-1.5 py-0.5 text-[8px] font-semibold rounded-full border transition-all hover:opacity-80 hover:shadow-sm cursor-pointer"
                    :title="statusBadgeTitle(getCairInfo(mitra.supplier_id))"
                  >
                    <span :class="statusDotClass(getCairInfo(mitra.supplier_id))" class="w-1.5 h-1.5 rounded-full"></span>
                    {{ statusBadgeLabel(getCairInfo(mitra.supplier_id)) }}
                  </Link>
                  <!-- Badge pengajuan pencairan baru -->
                  <div
                    v-if="getPengajuan(mitra.supplier_id).length > 0"
                    class="inline-flex items-center gap-0.5 mt-0.5 px-1.5 py-0.5 text-[8px] font-semibold rounded-full border bg-red-50 text-red-600 border-red-300"
                    title="Mitra mengajukan pencairan"
                  >
                    <span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse"></span>
                    Minta Cair
                  </div>
                  <svg
                    class="w-4 h-4 text-gray-400 transition-transform mx-auto mt-0.5"
                    :class="{ 'rotate-180': expandedMitra.includes(index) }"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                  </svg>
                </div>
              </div>
            </div>

            <!-- Items Detail (Expandable) -->
            <div
              v-if="expandedMitra.includes(index)"
              class="bg-gray-50"
            >
              <!-- Pembagian Skema -->
              <div v-if="mitra.pembagian" class="p-2 border-b border-gray-200 bg-blue-50">
                <div class="text-[9px] font-semibold text-gray-700 mb-1">Pembagian:</div>
                <div class="grid grid-cols-2 gap-1.5">
                  <div class="bg-white rounded p-1.5 border border-blue-200">
                    <div class="text-[9px] text-gray-600">Bagian Mitra</div>
                    <div class="text-xs font-bold text-green-700">
                      {{ formatCurrency(mitra.pembagian.bagian_vendor) }}
                    </div>
                  </div>
                  <div class="bg-white rounded p-1.5 border border-blue-200">
                    <div class="text-[9px] text-gray-600">Bagian Badan Usaha</div>
                    <div class="text-xs font-bold text-[#996600]">
                      {{ formatCurrency(mitra.pembagian.bagian_badan_usaha) }}
                    </div>
                  </div>
                </div>
              </div>

              <!-- Pengajuan Pencairan dari Mitra -->
              <div v-if="getPengajuan(mitra.supplier_id).length > 0" class="p-2 border-b border-gray-200 bg-red-50">
                <div class="text-[9px] font-semibold text-red-700 mb-1.5">Pengajuan Pencairan dari Mitra:</div>
                <div class="space-y-1.5">
                  <div
                    v-for="ajuan in getPengajuan(mitra.supplier_id)"
                    :key="ajuan.id"
                    class="bg-white rounded border border-red-200 p-2"
                  >
                    <div class="flex items-start justify-between gap-2">
                      <div class="text-[10px] text-gray-700">
                        <span class="font-medium">{{ formatDateShort(ajuan.tanggal_dari) }} s/d {{ formatDateShort(ajuan.tanggal_sampai) }}</span>
                        &bull; <span class="capitalize">{{ ajuan.metode === 'transfer_bank' ? 'Transfer Bank' : 'Tunai' }}</span>
                        <span v-if="ajuan.detail_rekening"> &bull; {{ ajuan.detail_rekening }}</span>
                        <div v-if="ajuan.catatan" class="text-gray-400 mt-0.5">{{ ajuan.catatan }}</div>
                        <span :class="ajuan.status === 'menunggu' ? 'text-red-600' : 'text-yellow-600'" class="font-semibold">
                          {{ ajuan.status === 'menunggu' ? 'Menunggu' : 'Diproses' }}
                        </span>
                      </div>
                      <div class="flex gap-1 flex-shrink-0">
                        <button
                          v-if="ajuan.status === 'menunggu'"
                          @click.stop="prosesAjuan(ajuan.id)"
                          class="px-1.5 py-0.5 text-[9px] font-medium bg-yellow-500 text-white rounded hover:bg-yellow-600 transition-colors"
                        >Proses</button>
                        <button
                          v-if="ajuan.status !== 'selesai'"
                          @click.stop="selesaiAjuan(ajuan.id)"
                          class="px-1.5 py-0.5 text-[9px] font-medium bg-green-600 text-white rounded hover:bg-green-700 transition-colors"
                        >Selesai</button>
                        <button
                          @click.stop="tolakAjuan(ajuan.id)"
                          class="px-1.5 py-0.5 text-[9px] font-medium bg-red-100 text-red-600 rounded hover:bg-red-200 transition-colors"
                        >Tolak</button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="p-2 space-y-1">
                <div class="text-[9px] font-semibold text-gray-700 mb-1.5">Detail Item:</div>
                <div class="max-h-[300px] overflow-y-auto space-y-1">
                  <div
                    v-for="(item, itemIndex) in mitra.items"
                    :key="itemIndex"
                    class="bg-white rounded p-1.5 border border-gray-200 flex items-center justify-between"
                  >
                    <div class="flex-1 min-w-0">
                      <div class="font-medium text-xs text-gray-900 truncate">
                        {{ item.nama_barang }}
                      </div>
                      <div class="text-[10px] text-gray-500">
                        {{ item.qty }} {{ item.satuan }}
                      </div>
                    </div>
                    <div class="font-bold text-xs text-gray-900 flex-shrink-0 ml-2">
                      {{ formatCurrency(item.subtotal) }}
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Pagination -->
        <div v-if="pembagian.data.length > 0 && (pembagian.next_page_url || pembagian.prev_page_url)" class="mt-3">
          <div class="flex gap-2 justify-center">
            <Link
              v-if="pembagian.prev_page_url"
              :href="pembagian.prev_page_url"
              class="px-3 py-1.5 text-xs bg-white border border-gray-300 rounded text-gray-700 hover:bg-gray-50"
            >
              ← Sebelumnya
            </Link>
            <Link
              v-if="pembagian.next_page_url"
              :href="pembagian.next_page_url"
              class="px-3 py-1.5 text-xs bg-[#996600] text-white rounded hover:bg-[#7a5100]"
            >
              Selanjutnya →
            </Link>
          </div>
          <div class="text-center mt-1.5 text-[10px] text-gray-500">
            {{ pembagian.from }}-{{ pembagian.to }} dari {{ pembagian.total }}
          </div>
        </div>
      </div>

      <!-- Modal Simpan Riwayat -->
      <div v-if="showSimpanRiwayatModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-lg w-full max-w-md">
          <div class="p-4 border-b">
            <h3 class="text-lg font-bold text-gray-900">Simpan Riwayat Pembagian</h3>
          </div>
          <form @submit.prevent="submitSimpanRiwayat">
            <div class="p-4 space-y-3">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Periode</label>
                <div class="text-sm text-gray-600 bg-gray-50 p-2 rounded">
                  {{ formatDate(startDate) }} s/d {{ formatDate(endDate) }}
                </div>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Total Mitra</label>
                <div class="text-sm text-gray-600 bg-gray-50 p-2 rounded">
                  {{ pembagian.total }} mitra
                </div>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Catatan (Opsional)</label>
                <textarea
                  v-model="catatanRiwayat"
                  rows="3"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] text-sm"
                  placeholder="Tambahkan catatan untuk riwayat ini..."
                ></textarea>
              </div>
            </div>
            <div class="p-4 border-t flex gap-2">
              <button
                type="button"
                @click="showSimpanRiwayatModal = false"
                class="flex-1 px-4 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50"
              >
                Batal
              </button>
              <button
                type="submit"
                class="flex-1 px-4 py-2 text-sm bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium"
              >
                Simpan
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </component>
</template>

<script setup>
import { ref, computed } from 'vue'
import { usePage, Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import CanteenLayout from '@/Layouts/CanteenLayout.vue'

const page = usePage()

const props = defineProps({
  workUnits: Array,
  selectedWorkUnitId: Number,
  startDate: String,
  endDate: String,
  verifiedOnly: Boolean,
  pembagian: Object,
  summary: Object,
  riwayatCair: Object, // keyed by supplier_id
  pengajuanPencairan: Object, // grouped by supplier_id
})

// Layout
const layoutComponent = computed(() => {
  const user = page.props.auth?.user
  if (user?.roles?.includes('canteen') && !user?.roles?.includes('officer') && !user?.roles?.includes('sysadmin')) {
    return CanteenLayout
  }
  return AdminLayout
})

// Computed
const totalLaba = computed(() => {
  return (props.summary.total_penjualan || 0) - (props.summary.total_vendor || 0)
})

const labaLainnya = computed(() => {
  return totalLaba.value - (props.summary.total_badan_usaha || 0)
})

// Local state
const expandedMitra = ref([])
const selectedWorkUnitId = ref(props.selectedWorkUnitId)
const startDate = ref(props.startDate)
const endDate = ref(props.endDate)
const showVerifiedOnly = ref(props.verifiedOnly)
const showSimpanRiwayatModal = ref(false)
const catatanRiwayat = ref('')
const activePaymentFilters = ref([])

// Calculate filtered pembagian based on payment filter
const calculateFilteredVendorTotal = () => {
  if (activePaymentFilters.value.length === 0 || !props.summary || !props.pembagian) {
    return props.summary?.total_vendor || 0
  }

  const totalPenjualan = props.summary.total_penjualan || 0
  if (totalPenjualan === 0) {
    return props.summary.total_vendor || 0
  }

  // Hitung total excluded amount dari semua payment method yang di-filter
  const totalExcluded = activePaymentFilters.value.reduce((sum, method) => {
    return sum + (props.summary[method] || 0)
  }, 0)

  if (totalExcluded === 0) {
    return props.summary.total_vendor || 0
  }

  const ratio = (totalPenjualan - totalExcluded) / totalPenjualan
  let total = 0
  props.pembagian.data.forEach(mitra => {
    if (mitra.pembagian) {
      total += (mitra.pembagian.bagian_vendor * ratio)
    }
  })
  return total
}

const calculateFilteredBadanUsahaTotal = () => {
  if (activePaymentFilters.value.length === 0 || !props.summary || !props.pembagian) {
    return props.summary?.total_badan_usaha || 0
  }

  const totalPenjualan = props.summary.total_penjualan || 0
  if (totalPenjualan === 0) {
    return props.summary.total_badan_usaha || 0
  }

  // Hitung total excluded amount dari semua payment method yang di-filter
  const totalExcluded = activePaymentFilters.value.reduce((sum, method) => {
    return sum + (props.summary[method] || 0)
  }, 0)

  if (totalExcluded === 0) {
    return props.summary.total_badan_usaha || 0
  }

  const ratio = (totalPenjualan - totalExcluded) / totalPenjualan
  let total = 0
  props.pembagian.data.forEach(mitra => {
    if (mitra.pembagian) {
      total += (mitra.pembagian.bagian_badan_usaha * ratio)
    }
  })
  return total
}

// Filtered summary - yang di-exclude tidak hilang, hanya disabled
const filteredSummary = computed(() => {
  if (activePaymentFilters.value.length === 0 || !props.summary) {
    return props.summary || {}
  }

  // Hitung total excluded amount dan count
  const totalExcluded = activePaymentFilters.value.reduce((sum, method) => {
    return sum + (props.summary[method] || 0)
  }, 0)

  return {
    ...props.summary,
    total_penjualan: props.summary.total_penjualan - totalExcluded,
    total_mitra: props.summary.total_mitra,
    total_items: props.summary.total_items,
    total_vendor: calculateFilteredVendorTotal(),
    total_badan_usaha: calculateFilteredBadanUsahaTotal(),
  }
})

const filteredTotalLaba = computed(() => {
  return (filteredSummary.value.total_penjualan || 0) - (filteredSummary.value.total_vendor || 0)
})

const filteredLabaLainnya = computed(() => {
  return filteredTotalLaba.value - (filteredSummary.value.total_badan_usaha || 0)
})

// Methods
const toggleExpand = (index) => {
  const idx = expandedMitra.value.indexOf(index)
  if (idx > -1) {
    expandedMitra.value.splice(idx, 1)
  } else {
    expandedMitra.value.push(index)
  }
}

const applyFilters = () => {
  router.get('/pengelola/pembagian-mitra', {
    work_unit_id: selectedWorkUnitId.value,
    start_date: startDate.value,
    end_date: endDate.value,
    verified_only: showVerifiedOnly.value ? 1 : 0,
  }, {
    preserveState: true,
    preserveScroll: true,
  })
}

const toggleVerifiedFilter = () => {
  showVerifiedOnly.value = !showVerifiedOnly.value
  router.get('/pengelola/pembagian-mitra', {
    work_unit_id: selectedWorkUnitId.value,
    start_date: startDate.value,
    end_date: endDate.value,
    verified_only: showVerifiedOnly.value ? 1 : 0,
  }, {
    preserveState: true,
    preserveScroll: true,
  })
}

const togglePaymentFilter = (paymentMethod) => {
  const index = activePaymentFilters.value.indexOf(paymentMethod)
  if (index > -1) {
    // Sudah ada, remove (uncheck)
    activePaymentFilters.value.splice(index, 1)
  } else {
    // Belum ada, add (check)
    activePaymentFilters.value.push(paymentMethod)
  }
}

const getFilteredMitraTotal = (mitra) => {
  // If no payment filter active, return original total
  if (activePaymentFilters.value.length === 0 || !props.summary) return mitra.total_penjualan

  const totalPenjualan = props.summary.total_penjualan || 0
  if (totalPenjualan === 0) return mitra.total_penjualan

  // Otherwise, proportionally reduce based on filtered ratio
  const ratio = filteredSummary.value.total_penjualan / totalPenjualan
  return mitra.total_penjualan * ratio
}

const getPengajuan = (supplierId) => {
  if (!props.pengajuanPencairan || !supplierId) return []
  return props.pengajuanPencairan[supplierId] ?? []
}

const totalPengajuan = computed(() => {
  if (!props.pengajuanPencairan) return 0
  return Object.values(props.pengajuanPencairan).reduce((sum, arr) => sum + arr.length, 0)
})

const getCairInfo = (supplierId) => {
  if (!props.riwayatCair || !supplierId) return null
  return props.riwayatCair[supplierId] ?? null
}

const metodeLabel = (metode) => metode === 'transfer_bank' ? 'Transfer Bank' : 'Tunai'

const statusBadgeLabel = (cairInfo) => {
  if (!cairInfo) return 'Belum Dicairkan'
  if (cairInfo.status_pencairan === 'mengajukan') return 'Mengajukan'
  if (cairInfo.status_pencairan === 'dicairkan') return 'Sudah Cair'
  return 'Belum Dicairkan'
}

const statusBadgeClass = (cairInfo) => {
  if (!cairInfo) return 'bg-gray-50 text-gray-500 border-gray-200'
  if (cairInfo.status_pencairan === 'mengajukan') return 'bg-yellow-50 text-yellow-700 border-yellow-300'
  if (cairInfo.status_pencairan === 'dicairkan') return 'bg-green-50 text-green-700 border-green-300'
  return 'bg-gray-50 text-gray-500 border-gray-200'
}

const statusDotClass = (cairInfo) => {
  if (!cairInfo) return 'bg-gray-400'
  if (cairInfo.status_pencairan === 'mengajukan') return 'bg-yellow-500'
  if (cairInfo.status_pencairan === 'dicairkan') return 'bg-green-500'
  return 'bg-gray-400'
}

const statusBadgeTitle = (cairInfo) => {
  if (!cairInfo) return 'Belum ada pengajuan pencairan'
  if (cairInfo.status_pencairan === 'mengajukan') return 'Mitra mengajukan pencairan - klik untuk proses'
  if (cairInfo.status_pencairan === 'dicairkan') return 'Dana sudah dicairkan'
  return 'Belum dicairkan'
}

const calculatePercentage = (amount) => {
  const total = filteredSummary.value?.total_penjualan || 0
  if (total === 0) return 0
  return ((amount / total) * 100).toFixed(1)
}

const formatCurrency = (value) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
  }).format(value || 0)
}

const downloadCSV = () => {
  const params = new URLSearchParams({
    work_unit_id: selectedWorkUnitId.value || '',
    start_date: startDate.value,
    end_date: endDate.value,
    verified_only: showVerifiedOnly.value ? 1 : '',
  })

  window.location.href = `/pengelola/pembagian-mitra/export?${params.toString()}`
}

const formatDateShort = (dateStr) => {
  if (!dateStr) return '-'
  return new Date(dateStr).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' })
}

const prosesAjuan = (id) => {
  router.post(route('pos.pengajuanPencairan.proses', id), {}, { preserveScroll: true })
}

const selesaiAjuan = (id) => {
  router.post(route('pos.pengajuanPencairan.selesai', id), {}, { preserveScroll: true })
}

const tolakAjuan = (id) => {
  router.post(route('pos.pengajuanPencairan.tolak', id), {}, { preserveScroll: true })
}

const formatDate = (dateStr) => {
  const date = new Date(dateStr)
  return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })
}

const submitSimpanRiwayat = () => {
  // Backend akan re-query semua data dari DB — tidak perlu kirim pembagian dari frontend
  router.post(route('pos.simpanRiwayatPembagian'), {
    work_unit_id: selectedWorkUnitId.value,
    start_date: startDate.value,
    end_date: endDate.value,
    catatan: catatanRiwayat.value,
  }, {
    onSuccess: () => {
      showSimpanRiwayatModal.value = false
      catatanRiwayat.value = ''
    }
  })
}
</script>
