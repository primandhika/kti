<template>
  <Transition name="modal">
    <div
      v-if="isOpen"
      class="fixed inset-0 z-50 overflow-y-auto"
      @click.self="closeModal"
    >
      <div class="flex min-h-screen items-center justify-center p-4">
        <!-- Backdrop -->
        <div
          class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"
          @click="closeModal"
        ></div>

        <!-- Modal Content -->
        <div
          class="relative bg-white rounded-xl shadow-2xl max-w-5xl w-full max-h-[90vh] flex flex-col"
        >
          <!-- Header -->
          <div class="flex items-center justify-between p-6 border-b border-gray-200">
            <div>
              <h2 class="text-2xl font-bold text-gray-900">Detail Transaksi</h2>
              <p class="text-sm text-gray-600 mt-1">
                <span class="font-mono text-[#996600]">{{ barang.kode_barang }}</span> - {{ barang.nama_barang }}
              </p>
            </div>
            <button
              @click="closeModal"
              class="text-gray-400 hover:text-gray-600 transition-colors"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <!-- Summary Cards -->
          <div class="grid grid-cols-3 gap-4 p-6 bg-gray-50 border-b">
            <div class="bg-white rounded-lg p-4 shadow-sm">
              <div class="text-xs text-gray-600 uppercase font-medium">Penjualan Bruto</div>
              <div class="text-lg font-bold text-green-600">{{ formatCurrency(barang.penjualan_bruto || 0) }}</div>
            </div>
            <div class="bg-white rounded-lg p-4 shadow-sm">
              <div class="text-xs text-gray-600 uppercase font-medium">Total HPP</div>
              <div class="text-lg font-bold text-blue-600">{{ formatCurrency(barang.total_hpp || 0) }}</div>
            </div>
            <div class="bg-white rounded-lg p-4 shadow-sm">
              <div class="text-xs text-gray-600 uppercase font-medium">Untung Kotor</div>
              <div class="text-lg font-bold text-amber-600">
                {{ formatCurrency(barang.untung_kotor || 0) }}
                <span class="text-sm text-gray-500 ml-1">({{ (barang.margin_persen || 0).toFixed(1) }}%)</span>
              </div>
            </div>
          </div>

          <!-- Loading State -->
          <div v-if="loading" class="flex-1 flex items-center justify-center p-12">
            <div class="text-center">
              <svg
                class="animate-spin h-12 w-12 text-[#996600] mx-auto mb-4"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
              >
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path
                  class="opacity-75"
                  fill="currentColor"
                  d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                ></path>
              </svg>
              <p class="text-gray-600">Memuat detail transaksi...</p>
            </div>
          </div>

          <!-- Table -->
          <div v-else class="flex-1 overflow-y-auto p-6">
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50 sticky top-0">
                  <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">No. Transaksi</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Tanggal</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Unit</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Varian</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Qty</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Harga</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">HPP</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Margin</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr
                    v-for="(item, index) in transactions"
                    :key="index"
                    class="hover:bg-gray-50"
                  >
                    <td class="px-4 py-3 text-sm font-mono text-[#996600]">{{ item.nomor_transaksi || '-' }}</td>
                    <td class="px-4 py-3 text-sm text-gray-600">{{ formatDateTime(item.tanggal_transaksi) }}</td>
                    <td class="px-4 py-3 text-sm text-gray-600">{{ item.work_unit || '-' }}</td>
                    <td class="px-4 py-3 text-sm text-gray-600">{{ item.varian || '-' }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900 text-right">{{ item.qty || 0 }} {{ item.satuan || '' }}</td>
                    <td class="px-4 py-3 text-sm text-green-600 font-semibold text-right">{{ formatCurrency(item.subtotal || 0) }}</td>
                    <td class="px-4 py-3 text-sm text-blue-600 text-right">{{ formatCurrency(item.total_hpp || 0) }}</td>
                    <td class="px-4 py-3 text-sm text-right">
                      <span :class="(item.margin || 0) >= 0 ? 'text-green-600' : 'text-red-600'" class="font-semibold">
                        {{ formatCurrency(item.margin || 0) }}
                      </span>
                    </td>
                  </tr>
                </tbody>
              </table>

              <!-- Empty State -->
              <div v-if="transactions.length === 0" class="text-center py-12">
                <svg class="w-16 h-16 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <p class="text-gray-500">Tidak ada data transaksi</p>
              </div>
            </div>
          </div>

          <!-- Pagination Footer -->
          <div
            v-if="!loading && pagination.last_page > 1"
            class="flex items-center justify-between px-6 py-4 border-t border-gray-200 bg-gray-50"
          >
            <div class="text-sm text-gray-700">
              Menampilkan {{ ((pagination.current_page - 1) * pagination.per_page) + 1 }} -
              {{ Math.min(pagination.current_page * pagination.per_page, pagination.total) }}
              dari {{ pagination.total }} transaksi
            </div>
            <div class="flex gap-2">
              <button
                @click="goToPage(pagination.current_page - 1)"
                :disabled="pagination.current_page === 1"
                class="px-3 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
              >
                ← Sebelumnya
              </button>
              <button
                v-for="page in displayPages"
                :key="page"
                @click="goToPage(page)"
                :class="[
                  'px-3 py-2 text-sm rounded-lg transition-colors',
                  page === pagination.current_page
                    ? 'bg-[#996600] text-white font-semibold'
                    : 'border border-gray-300 hover:bg-gray-100'
                ]"
              >
                {{ page }}
              </button>
              <button
                @click="goToPage(pagination.current_page + 1)"
                :disabled="pagination.current_page === pagination.last_page"
                class="px-3 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
              >
                Berikutnya →
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </Transition>
</template>

<script setup>
import { ref, computed, watch } from 'vue'

const props = defineProps({
  isOpen: Boolean,
  barang: Object,
  filters: Object,
})

const emit = defineEmits(['close'])

const loading = ref(false)
const transactions = ref([])
const pagination = ref({
  current_page: 1,
  per_page: 10,
  total: 0,
  last_page: 1,
})

const displayPages = computed(() => {
  const pages = []
  const maxVisible = 5
  let start = Math.max(1, pagination.value.current_page - Math.floor(maxVisible / 2))
  let end = Math.min(pagination.value.last_page, start + maxVisible - 1)

  if (end - start + 1 < maxVisible) {
    start = Math.max(1, end - maxVisible + 1)
  }

  for (let i = start; i <= end; i++) {
    pages.push(i)
  }

  return pages
})

const fetchTransactions = async (page = 1) => {
  loading.value = true
  try {
    console.log('Fetching transactions with params:', {
      barang_id: props.barang.barang_id,
      start_date: props.filters.start_date,
      end_date: props.filters.end_date,
      work_unit_id: props.filters.work_unit_id,
      page: page,
    })

    const response = await window.axios.get('/pengelola/laporan-penjualan/detail-transaksi', {
      params: {
        barang_id: props.barang.barang_id,
        start_date: props.filters.start_date,
        end_date: props.filters.end_date,
        work_unit_id: props.filters.work_unit_id,
        page: page,
      },
    })

    console.log('Response data:', response.data)

    transactions.value = response.data.data
    pagination.value = {
      current_page: response.data.current_page,
      per_page: response.data.per_page,
      total: response.data.total,
      last_page: response.data.last_page,
    }
  } catch (error) {
    console.error('Error fetching transactions:', error)
    if (error.response) {
      console.error('Error response:', error.response.data)
      console.error('Error status:', error.response.status)
    }
    transactions.value = []
  } finally {
    loading.value = false
  }
}

const goToPage = (page) => {
  if (page >= 1 && page <= pagination.value.last_page) {
    fetchTransactions(page)
  }
}

const closeModal = () => {
  emit('close')
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

// Watch for modal open to fetch data
watch(() => props.isOpen, (newVal) => {
  console.log('Modal isOpen changed:', newVal)
  if (newVal) {
    console.log('Starting to fetch transactions...')
    fetchTransactions(1)
  }
}, { immediate: true })
</script>

<style scoped>
.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}

.modal-enter-active .relative,
.modal-leave-active .relative {
  transition: transform 0.3s ease;
}

.modal-enter-from .relative,
.modal-leave-to .relative {
  transform: scale(0.9);
}
</style>
