<template>
  <component :is="layoutComponent" pageTitle="Buku Tagihan">
    <div class="min-h-screen bg-gray-50">
      <!-- Header -->
      <div class="bg-white border-b sticky top-0 z-10 shadow-sm">
        <div class="px-3 py-2">
          <div class="flex items-center justify-between mb-2">
            <div class="flex items-center gap-2">
              <Link
                :href="route('admin.rekap-penjualan')"
                class="p-1.5 hover:bg-gray-100 rounded transition-colors"
              >
                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
              </Link>
              <div>
                <h1 class="text-base font-bold text-gray-900">Buku Tagihan</h1>
                <p class="text-[10px] text-gray-500">Transaksi member belum disetujui</p>
              </div>
            </div>
          </div>

          <!-- Summary Cards -->
          <div class="flex gap-1.5 mb-2">
            <div class="bg-blue-50 rounded px-2 py-1 flex-1">
              <div class="text-[9px] text-blue-600 font-medium">Member</div>
              <div class="text-sm font-bold text-blue-900">{{ summary.total_member }}</div>
            </div>
            <div class="bg-amber-50 rounded px-2 py-1 flex-1">
              <div class="text-[9px] text-amber-600 font-medium">Trx</div>
              <div class="text-sm font-bold text-amber-900">{{ summary.total_transaksi }}</div>
            </div>
            <div class="bg-red-50 rounded px-2 py-1 flex-1">
              <div class="text-[9px] text-red-600 font-medium">Total</div>
              <div class="text-xs font-bold text-red-900">{{ formatCurrency(summary.total_tagihan) }}</div>
            </div>
          </div>

          <!-- Filters -->
          <div class="grid grid-cols-2 gap-2 mb-2">
            <select
              v-model="localFilters.work_unit_id"
              @change="applyFilters"
              class="px-2 py-1.5 text-xs border border-gray-300 rounded focus:ring-2 focus:ring-[#996600] focus:border-transparent"
            >
              <option :value="null">Semua Unit</option>
              <option v-for="unit in workUnits" :key="unit.id" :value="unit.id">
                {{ unit.name }}
              </option>
            </select>

            <select
              v-model="localFilters.verified_filter"
              @change="applyFilters"
              class="px-2 py-1.5 text-xs border border-gray-300 rounded focus:ring-2 focus:ring-[#996600] focus:border-transparent"
            >
              <option :value="null">Semua Status</option>
              <option value="verified">Verified</option>
              <option value="pending">Pending</option>
            </select>

            <input
              v-model="localFilters.date_from"
              @change="applyFilters"
              type="date"
              placeholder="Dari Tanggal"
              class="px-2 py-1.5 text-xs border border-gray-300 rounded focus:ring-2 focus:ring-[#996600] focus:border-transparent"
            />

            <input
              v-model="localFilters.date_to"
              @change="applyFilters"
              type="date"
              placeholder="Sampai Tanggal"
              class="px-2 py-1.5 text-xs border border-gray-300 rounded focus:ring-2 focus:ring-[#996600] focus:border-transparent"
            />
          </div>

          <!-- Search + Sort -->
          <div class="grid grid-cols-2 gap-2 mb-2">
            <input
              v-model="localFilters.search"
              @input="debounceSearch"
              type="text"
              placeholder="Cari member..."
              class="px-2 py-1.5 text-xs border border-gray-300 rounded focus:ring-2 focus:ring-[#996600] focus:border-transparent"
            />

            <select
              v-model="localFilters.sort_field"
              @change="applyFilters"
              class="px-2 py-1.5 text-xs border border-gray-300 rounded focus:ring-2 focus:ring-[#996600] focus:border-transparent"
            >
              <option value="total_tagihan">Sort: Tagihan</option>
              <option value="buyer_name">Sort: Nama</option>
              <option value="total_transaksi">Sort: Jumlah Trx</option>
              <option value="transaksi_tertua">Sort: Tertua</option>
            </select>
          </div>

          <!-- Sort Direction + Reset -->
          <div class="grid grid-cols-2 gap-2 mb-2">
            <button
              @click="toggleSortDirection"
              class="px-2 py-1.5 text-xs border border-gray-300 rounded hover:bg-gray-50 font-medium flex items-center justify-center gap-1"
            >
              <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path v-if="localFilters.sort_direction === 'asc'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
              {{ localFilters.sort_direction === 'asc' ? 'Naik' : 'Turun' }}
            </button>

            <button
              v-if="hasActiveFilters"
              @click="resetFilters"
              class="px-2 py-1.5 text-xs bg-gray-100 text-gray-700 rounded hover:bg-gray-200 font-medium"
            >
              Reset Filter
            </button>
          </div>

          <!-- Action Buttons -->
          <div class="grid grid-cols-2 gap-2">
            <button
              @click="downloadCSV"
              class="px-3 py-1.5 text-xs bg-[#6b4700] text-white rounded hover:bg-[#5b3d00] font-medium flex items-center justify-center gap-1.5"
            >
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
              CSV
            </button>

            <button
              @click="downloadPDF"
              class="px-3 py-1.5 text-xs bg-red-700 text-white rounded hover:bg-red-800 font-medium flex items-center justify-center gap-1.5"
            >
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
              </svg>
              PDF
            </button>
          </div>
        </div>
      </div>

      <!-- Content -->
      <div class="p-2">
        <!-- Empty State -->
        <div v-if="tagihan.data.length === 0" class="text-center py-8 bg-white rounded">
          <svg class="w-12 h-12 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
          <p class="text-gray-500 text-xs font-medium">Tidak ada tagihan</p>
          <p class="text-gray-400 text-[10px] mt-0.5">Semua transaksi member sudah disetujui</p>
        </div>

        <!-- 2 Column Grid -->
        <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-2">
          <div
            v-for="item in tagihan.data"
            :key="item.buyer_id"
            class="bg-white rounded border border-gray-200 overflow-hidden"
          >
            <!-- Member Header (Compact) -->
            <div
              class="p-2 cursor-pointer hover:bg-gray-50 transition-colors border-b border-gray-100"
              @click="toggleExpand(item.buyer_id)"
            >
              <div class="flex items-start justify-between gap-2">
                <div class="flex-1 min-w-0">
                  <div class="flex items-center gap-1.5 mb-1">
                    <h3 class="font-bold text-sm text-gray-900 truncate">{{ item.buyer_name }}</h3>
                    <span
                      v-if="item.tier"
                      class="px-1.5 py-0.5 text-[9px] font-semibold rounded-full flex-shrink-0"
                      :style="{
                        backgroundColor: item.tier.color + '20',
                        color: item.tier.color
                      }"
                    >
                      {{ item.tier.name }}
                    </span>
                  </div>
                  <div class="text-[10px] text-gray-500 space-y-0.5">
                    <div>{{ item.member_code || '-' }} {{ item.phone ? '• ' + item.phone : '' }}</div>
                    <div>Tertua: {{ formatDateIndo(item.transaksi_tertua) }}</div>
                  </div>
                </div>

                <div class="text-right flex-shrink-0">
                  <div class="text-sm font-bold text-red-600">{{ formatCurrency(item.total_tagihan) }}</div>
                  <div class="text-[10px] text-gray-500">{{ item.total_transaksi }} trx</div>
                  <svg
                    class="w-4 h-4 text-gray-400 transition-transform mx-auto mt-0.5"
                    :class="{ 'rotate-180': expandedBuyers.includes(item.buyer_id) }"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                  </svg>
                </div>
              </div>
            </div>

            <!-- Transaksi Detail (Expandable) -->
            <div
              v-if="expandedBuyers.includes(item.buyer_id)"
              class="bg-gray-50"
            >
              <div class="p-2 space-y-1.5">
                <div
                  v-for="transaksi in item.transaksi"
                  :key="transaksi.id"
                  class="bg-white rounded p-2 border border-gray-200"
                >
                  <div class="flex items-start justify-between gap-2">
                    <div class="flex-1 min-w-0">
                      <div class="font-medium text-xs text-gray-900 truncate">
                        {{ transaksi.nomor_transaksi }}
                      </div>
                      <div class="text-[10px] text-gray-500 mt-0.5">
                        {{ formatDateTimeIndo(transaksi.tanggal_transaksi) }}
                      </div>
                      <div v-if="transaksi.work_unit_name" class="text-[10px] text-gray-500">
                        {{ transaksi.work_unit_name }}
                      </div>
                      <span
                        :class="transaksi.is_verified
                          ? 'bg-green-100 text-green-700'
                          : 'bg-yellow-100 text-yellow-700'"
                        class="inline-flex items-center px-1.5 py-0.5 text-[9px] font-medium rounded mt-1"
                      >
                        {{ transaksi.is_verified ? '✓ Verified' : 'Pending' }}
                      </span>
                    </div>
                    <div class="text-right flex-shrink-0">
                      <div class="font-bold text-xs text-gray-900">
                        {{ formatCurrency(transaksi.total) }}
                      </div>
                      <Link
                        :href="`/pengelola/rekap-penjualan?start_date=${formatDate(transaksi.tanggal_transaksi)}&end_date=${formatDate(transaksi.tanggal_transaksi)}`"
                        class="text-[10px] text-[#996600] hover:text-[#7a5100] font-medium mt-0.5 inline-block"
                      >
                        Lihat →
                      </Link>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Pagination -->
        <div v-if="tagihan.data.length > 0 && (tagihan.next_page_url || tagihan.prev_page_url)" class="mt-3">
          <div class="flex gap-2 justify-center">
            <Link
              v-if="tagihan.prev_page_url"
              :href="tagihan.prev_page_url"
              class="px-3 py-1.5 text-xs bg-white border border-gray-300 rounded text-gray-700 hover:bg-gray-50"
            >
              ← Sebelumnya
            </Link>
            <Link
              v-if="tagihan.next_page_url"
              :href="tagihan.next_page_url"
              class="px-3 py-1.5 text-xs bg-[#996600] text-white rounded hover:bg-[#7a5100]"
            >
              Selanjutnya →
            </Link>
          </div>
          <div class="text-center mt-1.5 text-[10px] text-gray-500">
            {{ tagihan.from }}-{{ tagihan.to }} dari {{ tagihan.total }}
          </div>
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
  filters: Object,
  tagihan: Object,
  summary: Object,
})

// Layout
const layoutComponent = computed(() => {
  const user = page.props.auth?.user
  if (user?.roles?.includes('canteen') && !user?.roles?.includes('officer') && !user?.roles?.includes('sysadmin')) {
    return CanteenLayout
  }
  return AdminLayout
})

// Local state
const expandedBuyers = ref([])

// Filters
const localFilters = ref({
  work_unit_id: props.filters.work_unit_id || null,
  search: props.filters.search || '',
  date_from: props.filters.date_from || '',
  date_to: props.filters.date_to || '',
  verified_filter: props.filters.verified_filter ?? 'pending',
  sort_field: props.filters.sort_field || 'total_tagihan',
  sort_direction: props.filters.sort_direction || 'desc',
})

const hasActiveFilters = computed(() => {
  return localFilters.value.work_unit_id ||
         localFilters.value.search ||
         localFilters.value.date_from ||
         localFilters.value.date_to ||
         (localFilters.value.verified_filter && localFilters.value.verified_filter !== 'pending')
})

// Methods
const toggleExpand = (buyerId) => {
  const index = expandedBuyers.value.indexOf(buyerId)
  if (index > -1) {
    expandedBuyers.value.splice(index, 1)
  } else {
    expandedBuyers.value.push(buyerId)
  }
}

// Debounce timer for search
let debounceTimer = null

const debounceSearch = () => {
  clearTimeout(debounceTimer)
  debounceTimer = setTimeout(() => {
    applyFilters()
  }, 500)
}

const applyFilters = () => {
  router.get('/pengelola/buku-tagihan', localFilters.value, {
    preserveState: true,
    preserveScroll: true,
  })
}

const resetFilters = () => {
  localFilters.value = {
    work_unit_id: null,
    search: '',
    date_from: '',
    date_to: '',
    verified_filter: 'pending',
    sort_field: 'total_tagihan',
    sort_direction: 'desc',
  }
  applyFilters()
}

const toggleSortDirection = () => {
  localFilters.value.sort_direction = localFilters.value.sort_direction === 'asc' ? 'desc' : 'asc'
  applyFilters()
}

const formatCurrency = (value) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
  }).format(value || 0)
}

const formatDateIndo = (dateString) => {
  const date = new Date(dateString)
  const options = { day: 'numeric', month: 'short', year: 'numeric' }
  return date.toLocaleDateString('id-ID', options)
}

const formatDateTimeIndo = (dateString) => {
  const date = new Date(dateString)
  const dateOptions = { day: 'numeric', month: 'short', year: 'numeric' }
  const timeOptions = { hour: '2-digit', minute: '2-digit' }
  return date.toLocaleDateString('id-ID', dateOptions) + ' ' + date.toLocaleTimeString('id-ID', timeOptions)
}

const formatDate = (dateString) => {
  const date = new Date(dateString)
  const year = date.getFullYear()
  const month = String(date.getMonth() + 1).padStart(2, '0')
  const day = String(date.getDate()).padStart(2, '0')
  return `${year}-${month}-${day}`
}

const downloadCSV = () => {
  const params = new URLSearchParams()

  if (localFilters.value.work_unit_id) params.append('work_unit_id', localFilters.value.work_unit_id)
  if (localFilters.value.search) params.append('search', localFilters.value.search)
  if (localFilters.value.date_from) params.append('date_from', localFilters.value.date_from)
  if (localFilters.value.date_to) params.append('date_to', localFilters.value.date_to)
  if (localFilters.value.verified_filter) params.append('verified_filter', localFilters.value.verified_filter)

  window.location.href = `/pengelola/buku-tagihan/export?${params.toString()}`
}

const downloadPDF = () => {
  const params = new URLSearchParams()

  if (localFilters.value.work_unit_id) params.append('work_unit_id', localFilters.value.work_unit_id)
  if (localFilters.value.search) params.append('search', localFilters.value.search)
  if (localFilters.value.date_from) params.append('date_from', localFilters.value.date_from)
  if (localFilters.value.date_to) params.append('date_to', localFilters.value.date_to)
  if (localFilters.value.verified_filter) params.append('verified_filter', localFilters.value.verified_filter)
  if (localFilters.value.sort_field) params.append('sort_field', localFilters.value.sort_field)
  if (localFilters.value.sort_direction) params.append('sort_direction', localFilters.value.sort_direction)

  window.location.href = `/pengelola/buku-tagihan/download-pdf?${params.toString()}`
}
</script>
