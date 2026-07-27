<template>
  <div
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-2 sm:p-4"
    @click.self="$emit('close')"
  >
    <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] overflow-hidden flex flex-col">
      <!-- Header -->
      <div class="bg-gradient-to-r from-[#996600] to-[#7a5100] text-white p-3 sm:p-4 sticky top-0 z-10">
        <div class="flex items-center justify-between">
          <div>
            <h3 class="text-base sm:text-lg font-bold">Ringkasan Item</h3>
            <p class="text-xs sm:text-sm text-[#eae0cc]">{{ dateRange }}</p>
          </div>
          <button @click="$emit('close')" class="p-2 hover:bg-white/20 rounded-full transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>

      <!-- Content -->
      <div class="flex-1 overflow-y-auto p-2 sm:p-4">
        <div v-if="paginatedItems.length > 0" class="space-y-2">
          <!-- Compact List -->
          <div
            v-for="(item, index) in paginatedItems"
            :key="index"
            class="bg-gray-50 rounded p-2 border border-gray-200 hover:bg-gray-100 transition-colors"
          >
            <!-- Main Info - 1 Line -->
            <div class="flex items-center justify-between gap-2 text-sm">
              <div class="flex items-center gap-2 flex-1 min-w-0">
                <button
                  v-if="item.stock_reductions && item.stock_reductions.length > 0"
                  @click="toggleDetail(index)"
                  class="text-[#996600] hover:text-[#7a5100] flex-shrink-0"
                >
                  <svg
                    :class="['w-3 h-3 transition-transform', expandedItems.includes(index) ? 'rotate-90' : '']"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                  </svg>
                </button>
                <span class="font-semibold text-gray-900 truncate">{{ item.nama_barang }}</span>
                <span class="text-gray-900 font-bold flex-shrink-0">×{{ item.total_qty }}</span>
                <span class="text-green-600 font-bold flex-shrink-0">{{ formatCurrency(item.total_harga) }}</span>
                <span v-if="item.supplier_name" class="text-xs text-gray-500 truncate">(Mitra: {{ item.supplier_name }})</span>
              </div>
            </div>

            <!-- Details (Expandable) -->
            <transition name="expand">
              <div v-show="expandedItems.includes(index)" class="mt-2 pt-2 border-t border-gray-300 space-y-1">
                <div
                  v-for="(red, rIdx) in item.stock_reductions"
                  :key="rIdx"
                  class="text-xs bg-white rounded px-2 py-1 flex items-center justify-between gap-2"
                >
                  <span class="font-mono text-[#996600] flex-shrink-0">{{ red.nomor_transaksi }}</span>
                  <span class="text-gray-500 text-[10px] flex-shrink-0">{{ formatDateTime(red.tanggal_transaksi).split(' ').slice(0, 2).join(' ') }}</span>
                  <span class="text-gray-700 flex-shrink-0">{{ red.qty }}× @ {{ formatCurrency(red.harga_satuan) }}</span>
                  <span class="font-semibold text-green-600 flex-shrink-0">{{ formatCurrency(red.subtotal) }}</span>
                </div>
              </div>
            </transition>
          </div>

          <!-- Total -->
          <div class="bg-[#996600] rounded p-2 text-white font-bold sticky bottom-0">
            <div class="flex justify-between items-center text-sm">
              <span>TOTAL</span>
              <span class="text-base">{{ formatCurrency(totalHarga) }}</span>
            </div>
          </div>
        </div>

        <!-- Pagination -->
        <div v-if="paginatedItems.length > 0 && totalPages > 1" class="mt-3 flex items-center justify-between gap-2 text-xs">
          <button
            @click="prevPage"
            :disabled="currentPage === 1"
            class="px-3 py-1.5 bg-white border border-gray-300 rounded text-gray-700 hover:bg-gray-50 disabled:opacity-50"
          >
            ←
          </button>
          <span class="text-gray-600">
            {{ currentPage }}/{{ totalPages }} ({{ itemFrom }}-{{ itemTo }}/{{ sortedItemSummary.length }})
          </span>
          <button
            @click="nextPage"
            :disabled="currentPage === totalPages"
            class="px-3 py-1.5 bg-white border border-gray-300 rounded text-gray-700 hover:bg-gray-50 disabled:opacity-50"
          >
            →
          </button>
        </div>

        <!-- Empty -->
        <div v-if="!paginatedItems.length" class="text-center py-12">
          <svg class="w-12 h-12 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
          </svg>
          <p class="text-gray-500 text-sm">Tidak ada item</p>
        </div>
      </div>

      <!-- Footer - Compact -->
      <div class="p-2 sm:p-3 bg-gray-50 border-t flex gap-2">
        <button
          @click="$emit('print')"
          :disabled="!itemSummary || itemSummary.length === 0"
          class="px-3 py-2 bg-[#996600] hover:bg-[#7a5100] text-white rounded font-semibold transition-colors disabled:opacity-50 text-xs sm:text-sm flex items-center gap-1.5"
          title="Cetak"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
          </svg>
          <span>Cetak</span>
        </button>
        <button
          @click="$emit('download-csv')"
          :disabled="!itemSummary || itemSummary.length === 0"
          class="px-3 py-2 bg-green-600 hover:bg-green-700 text-white rounded font-semibold transition-colors disabled:opacity-50 text-xs sm:text-sm flex items-center gap-1.5"
          title="Download CSV"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
          <span>CSV</span>
        </button>
        <button
          @click="$emit('close')"
          class="flex-1 px-3 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-100 font-medium transition-colors text-xs sm:text-sm"
        >
          Tutup
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useFormatters } from '@/composables/useFormatters'

const props = defineProps({
  itemSummary: { type: Array, default: () => [] },
  dateRange: { type: String, default: '' }
})

defineEmits(['close', 'print', 'download-csv'])

const { formatCurrency, formatDateTime } = useFormatters()

const currentPage = ref(1)
const itemsPerPage = 20
const expandedItems = ref([])

const sortedItemSummary = computed(() => {
  return [...props.itemSummary].sort((a, b) => {
    const nameA = (a.nama_barang || '').toLowerCase()
    const nameB = (b.nama_barang || '').toLowerCase()
    return nameA.localeCompare(nameB)
  })
})

const totalPages = computed(() => Math.ceil(sortedItemSummary.value.length / itemsPerPage))
const itemFrom = computed(() => (currentPage.value - 1) * itemsPerPage + 1)
const itemTo = computed(() => Math.min(currentPage.value * itemsPerPage, sortedItemSummary.value.length))

const paginatedItems = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage
  const end = start + itemsPerPage
  return sortedItemSummary.value.slice(start, end)
})

const totalHarga = computed(() => {
  return props.itemSummary.reduce((sum, item) => sum + (item.total_harga || 0), 0)
})

const toggleDetail = (index) => {
  const idx = expandedItems.value.indexOf(index)
  if (idx >= 0) {
    expandedItems.value.splice(idx, 1)
  } else {
    expandedItems.value.push(index)
  }
}

const prevPage = () => {
  if (currentPage.value > 1) currentPage.value--
}

const nextPage = () => {
  if (currentPage.value < totalPages.value) currentPage.value++
}
</script>

<style scoped>
.expand-enter-active, .expand-leave-active {
  transition: all 0.2s ease;
}
.expand-enter-from, .expand-leave-to {
  opacity: 0;
  max-height: 0;
}
</style>
