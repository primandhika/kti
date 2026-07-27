<template>
  <AdminLayout pageTitle="Laporan Penjualan per Item">
    <div class="min-h-screen bg-gray-50 p-4">
      <!-- Header & Filters -->
      <div class="bg-white rounded-xl shadow-md p-4 mb-4">
        <h1 class="text-2xl font-bold text-gray-800 mb-4">Laporan Penjualan per Item</h1>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-4">
          <div class="bg-blue-50 rounded-lg p-4">
            <div class="text-gray-600 text-sm uppercase font-medium">Total Item</div>
            <div class="text-blue-900 font-bold text-2xl">{{ summary.total_items }}</div>
          </div>
          <div class="bg-purple-50 rounded-lg p-4">
            <div class="text-gray-600 text-sm uppercase font-medium">Total Qty</div>
            <div class="text-purple-900 font-bold text-2xl">{{ summary.total_qty }}</div>
          </div>
          <div class="bg-green-50 rounded-lg p-4">
            <div class="text-gray-600 text-sm uppercase font-medium">Penjualan Bruto</div>
            <div class="text-green-900 font-bold text-xl">{{ formatCurrency(summary.penjualan_bruto) }}</div>
          </div>
          <div class="bg-blue-50 rounded-lg p-4">
            <div class="text-gray-600 text-sm uppercase font-medium">Total HPP</div>
            <div class="text-blue-900 font-bold text-xl">{{ formatCurrency(summary.total_hpp) }}</div>
          </div>
          <div class="bg-amber-50 rounded-lg p-4">
            <div class="text-gray-600 text-sm uppercase font-medium">Untung Kotor</div>
            <div class="text-amber-900 font-bold text-xl">{{ formatCurrency(summary.untung_kotor) }}</div>
          </div>
        </div>

        <!-- Filters -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3 mb-3">
          <input
            v-model="filters.start_date"
            type="date"
            @change="applyFilters"
            class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
          />
          <input
            v-model="filters.end_date"
            type="date"
            @change="applyFilters"
            class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
          />
          <select
            v-if="workUnits.length > 1"
            v-model="filters.work_unit_id"
            @change="applyFilters"
            class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
          >
            <option :value="null">Semua Unit</option>
            <option v-for="unit in workUnits" :key="unit.id" :value="unit.id">
              {{ unit.name }}
            </option>
          </select>
          <div class="flex gap-2">
            <button
              @click="resetFilters"
              class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors"
            >
              Reset
            </button>
            <button
              @click="exportCSV"
              class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center justify-center gap-2"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
              Export CSV
            </button>
            <button
              @click="downloadMitraProdukPdf"
              class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors flex items-center justify-center gap-2"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
              PDF Mitra
            </button>
          </div>
        </div>

        <!-- Search -->
        <div class="flex gap-3">
          <input
            ref="searchInput"
            v-model="searchQuery"
            type="text"
            placeholder="Cari barang (nama, kode)..."
            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
          />
          <div class="text-sm text-gray-600 flex items-center">
            Total: {{ filteredItems.length }} item
          </div>
        </div>
      </div>

      <!-- Desktop Table -->
      <div class="hidden md:block bg-white rounded-xl shadow-md overflow-hidden">
        <div class="overflow-x-auto">
          <table class="min-w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
              <tr>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Kode</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Nama Barang</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Satuan</th>
                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Total Qty</th>
                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Transaksi</th>
                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Penjualan (Bruto)</th>
                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">HPP</th>
                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Untung Kotor</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
              <tr v-for="(item, index) in paginatedItems" :key="index" class="hover:bg-gray-50">
                <td class="px-4 py-3 text-sm">
                  <button
                    @click="openDetailModal(item)"
                    class="font-mono text-[#996600] hover:text-[#7a5100] hover:underline font-semibold"
                  >
                    {{ item.kode_barang }}
                  </button>
                </td>
                <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ item.nama_barang }}</td>
                <td class="px-4 py-3 text-sm text-gray-600">{{ item.satuan }}</td>
                <td class="px-4 py-3 text-sm text-gray-900 text-right font-semibold">{{ item.total_qty }}</td>
                <td class="px-4 py-3 text-sm text-gray-600 text-right">
                  <button
                    @click="openDetailModal(item)"
                    class="text-blue-600 hover:text-blue-800 hover:underline"
                  >
                    {{ item.jumlah_transaksi }}x
                  </button>
                </td>
                <td class="px-4 py-3 text-sm text-green-600 font-semibold text-right">{{ formatCurrency(item.penjualan_bruto) }}</td>
                <td class="px-4 py-3 text-sm text-blue-600 text-right">{{ formatCurrency(item.total_hpp) }}</td>
                <td class="px-4 py-3 text-sm text-right">
                  <span :class="item.untung_kotor >= 0 ? 'text-green-600' : 'text-red-600'" class="font-semibold">
                    {{ formatCurrency(item.untung_kotor) }}
                  </span>
                  <span class="text-xs text-gray-500 ml-1">({{ item.margin_persen.toFixed(1) }}%)</span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Empty State -->
        <div v-if="filteredItems.length === 0" class="text-center py-12">
          <svg class="w-16 h-16 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
          <p class="text-gray-500">{{ searchQuery ? 'Tidak ada barang yang sesuai dengan pencarian' : 'Tidak ada data penjualan' }}</p>
        </div>
      </div>

      <!-- Pagination Desktop -->
      <div v-if="totalPages > 1" class="hidden md:flex justify-between items-center bg-white rounded-xl shadow-md px-6 py-4 mt-4">
        <div class="text-sm text-gray-700">
          Menampilkan {{ from }} - {{ to }} dari {{ filteredItems.length }} item
        </div>
        <div class="flex gap-2">
          <button
            @click="goToPage(currentPage - 1)"
            :disabled="currentPage === 1"
            class="px-3 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            ← Sebelumnya
          </button>
          <button
            v-for="page in displayPages"
            :key="page"
            @click="goToPage(page)"
            :class="[
              'px-3 py-2 text-sm rounded-lg',
              page === currentPage
                ? 'bg-[#996600] text-white font-semibold'
                : 'border border-gray-300 hover:bg-gray-50'
            ]"
          >
            {{ page }}
          </button>
          <button
            @click="goToPage(currentPage + 1)"
            :disabled="currentPage === totalPages"
            class="px-3 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            Berikutnya →
          </button>
        </div>
      </div>

      <!-- Pagination Mobile -->
      <div v-if="totalPages > 1" class="md:hidden bg-white rounded-lg shadow-md p-3 mt-4">
        <div class="flex flex-col gap-2">
          <div class="text-xs text-gray-600 text-center">
            Halaman {{ currentPage }} dari {{ totalPages }} ({{ filteredItems.length }} item)
          </div>
          <div class="flex justify-center gap-1">
            <button
              @click="goToPage(currentPage - 1)"
              :disabled="currentPage === 1"
              class="px-2 py-1 text-xs border border-gray-300 rounded hover:bg-gray-50 disabled:opacity-50"
            >
              ←
            </button>
            <button
              v-for="page in displayPages"
              :key="page"
              @click="goToPage(page)"
              :class="[
                'px-2 py-1 text-xs rounded',
                page === currentPage
                  ? 'bg-[#996600] text-white font-semibold'
                  : 'border border-gray-300 hover:bg-gray-50'
              ]"
            >
              {{ page }}
            </button>
            <button
              @click="goToPage(currentPage + 1)"
              :disabled="currentPage === totalPages"
              class="px-2 py-1 text-xs border border-gray-300 rounded hover:bg-gray-50 disabled:opacity-50"
            >
              →
            </button>
          </div>
        </div>
      </div>

      <!-- Mobile Cards -->
      <div class="md:hidden space-y-3">
        <div
          v-for="(item, index) in paginatedItems"
          :key="index"
          class="bg-white rounded-lg shadow-md overflow-hidden"
        >
          <div class="p-4">
            <div class="flex items-start justify-between mb-2">
              <div class="flex-1">
                <button
                  @click="openDetailModal(item)"
                  class="text-xs font-mono text-[#996600] hover:text-[#7a5100] hover:underline font-semibold"
                >
                  {{ item.kode_barang }}
                </button>
                <div class="font-semibold text-gray-900">{{ item.nama_barang }}</div>
                <div class="text-xs text-gray-600 mt-1">{{ item.satuan }}</div>
              </div>
              <div class="text-right">
                <div class="text-lg font-bold text-gray-900">{{ item.total_qty }}x</div>
                <button
                  @click="openDetailModal(item)"
                  class="text-xs text-blue-600 hover:text-blue-800"
                >
                  {{ item.jumlah_transaksi }} transaksi
                </button>
              </div>
            </div>

            <div class="grid grid-cols-3 gap-2 text-xs">
              <div class="bg-green-50 rounded p-2">
                <div class="text-gray-600">Bruto</div>
                <div class="font-semibold text-green-600">{{ formatCurrency(item.penjualan_bruto) }}</div>
              </div>
              <div class="bg-blue-50 rounded p-2">
                <div class="text-gray-600">HPP</div>
                <div class="font-semibold text-blue-600">{{ formatCurrency(item.total_hpp) }}</div>
              </div>
              <div class="bg-amber-50 rounded p-2">
                <div class="text-gray-600">Untung</div>
                <div :class="['font-semibold', item.untung_kotor >= 0 ? 'text-green-600' : 'text-red-600']">
                  {{ formatCurrency(item.untung_kotor) }}
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State Mobile -->
        <div v-if="filteredItems.length === 0" class="bg-white rounded-lg shadow-md p-8 text-center">
          <svg class="w-12 h-12 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
          <p class="text-gray-500 text-sm">{{ searchQuery ? 'Tidak ada barang yang sesuai dengan pencarian' : 'Tidak ada data penjualan' }}</p>
        </div>
      </div>
    </div>

    <!-- Detail Transaksi Modal -->
    <DetailTransaksiModal
      v-if="selectedBarang"
      :is-open="showDetailModal"
      :barang="selectedBarang"
      :filters="filters"
      @close="closeDetailModal"
    />
  </AdminLayout>
</template>

<script setup>
import { ref, computed, onMounted, nextTick } from 'vue'
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import DetailTransaksiModal from '@/Components/LaporanPenjualan/DetailTransaksiModal.vue'

const props = defineProps({
  workUnits: Array,
  selectedWorkUnitId: [Number, String],
  startDate: String,
  endDate: String,
  itemSummary: Array,
  summary: Object,
})

const filters = ref({
  work_unit_id: props.selectedWorkUnitId,
  start_date: props.startDate,
  end_date: props.endDate,
})

const searchQuery = ref('')
const searchInput = ref(null)
const currentPage = ref(1)
const itemsPerPage = 20

// Modal state
const showDetailModal = ref(false)
const selectedBarang = ref(null)

// Filtered items based on search
const filteredItems = computed(() => {
  if (!searchQuery.value) return props.itemSummary

  const query = searchQuery.value.toLowerCase()
  return props.itemSummary.filter(item => {
    return item.nama_barang.toLowerCase().includes(query) ||
           item.kode_barang.toLowerCase().includes(query)
  })
})

// Pagination
const totalPages = computed(() => {
  return Math.ceil(filteredItems.value.length / itemsPerPage)
})

const from = computed(() => {
  return (currentPage.value - 1) * itemsPerPage + 1
})

const to = computed(() => {
  return Math.min(currentPage.value * itemsPerPage, filteredItems.value.length)
})

const paginatedItems = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage
  const end = start + itemsPerPage
  return filteredItems.value.slice(start, end)
})

const displayPages = computed(() => {
  const pages = []
  const maxVisible = 5
  let start = Math.max(1, currentPage.value - Math.floor(maxVisible / 2))
  let end = Math.min(totalPages.value, start + maxVisible - 1)

  if (end - start + 1 < maxVisible) {
    start = Math.max(1, end - maxVisible + 1)
  }

  for (let i = start; i <= end; i++) {
    pages.push(i)
  }

  return pages
})

const goToPage = (page) => {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page
    window.scrollTo({ top: 0, behavior: 'smooth' })
  }
}

const applyFilters = () => {
  router.get('/pengelola/laporan-penjualan', filters.value, {
    preserveState: true,
    preserveScroll: true,
  })
}

const resetFilters = () => {
  const now = new Date()
  const startOfMonth = new Date(now.getFullYear(), now.getMonth(), 1)
  const today = now.toISOString().split('T')[0]

  filters.value = {
    work_unit_id: null,
    start_date: startOfMonth.toISOString().split('T')[0],
    end_date: today,
  }

  applyFilters()
}

const openDetailModal = (barang) => {
  selectedBarang.value = barang
  showDetailModal.value = true
}

const closeDetailModal = () => {
  showDetailModal.value = false
  selectedBarang.value = null
}

const formatCurrency = (value) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0,
  }).format(value || 0)
}

const formatDateTime = (dateString) => {
  const date = new Date(dateString)
  return new Intl.DateTimeFormat('id-ID', {
    day: '2-digit',
    month: 'short',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  }).format(date)
}

const exportCSV = () => {
  const params = new URLSearchParams({
    start_date: filters.value.start_date,
    end_date: filters.value.end_date,
  })

  if (filters.value.work_unit_id) {
    params.append('work_unit_id', filters.value.work_unit_id)
  }

  window.location.href = `/pengelola/laporan-penjualan/export?${params.toString()}`
}

const downloadMitraProdukPdf = () => {
  const params = new URLSearchParams({
    start_date: filters.value.start_date,
    end_date: filters.value.end_date,
  })

  if (filters.value.work_unit_id) {
    params.append('work_unit_id', filters.value.work_unit_id)
  }

  window.open(`/pengelola/laporan-penjualan/mitra-produk-pdf?${params.toString()}`, '_blank')
}

// Auto-focus search input
onMounted(() => {
  nextTick(() => {
    if (searchInput.value) {
      searchInput.value.focus()
    }
  })
})
</script>

