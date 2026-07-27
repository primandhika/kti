<template>
  <div
    v-if="penjualan"
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
    @click.self="$emit('close')"
  >
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
      <!-- Header -->
      <div class="bg-gradient-to-r from-[#996600] to-[#7a5100] text-white p-4 rounded-t-lg">
        <div class="flex items-center gap-3">
          <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center flex-shrink-0">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
          </div>
          <div>
            <h3 class="text-lg font-bold">Tetapkan Member</h3>
            <p class="text-sm text-amber-100 mt-0.5">Hubungkan transaksi ke member</p>
          </div>
        </div>
      </div>

      <!-- Content -->
      <div class="p-4">
        <div class="bg-gray-50 rounded-lg p-3 mb-4">
          <div class="text-xs text-gray-500 mb-1">No. Transaksi</div>
          <div class="font-mono text-sm font-bold text-gray-900">{{ penjualan.nomor_transaksi }}</div>
          <div class="text-xs text-gray-600 mt-1">
            {{ formatDateTime(penjualan.tanggal_transaksi) }}
          </div>
          <div class="mt-2 pt-2 border-t border-gray-200">
            <div class="flex justify-between text-sm">
              <span class="text-gray-600">Total</span>
              <span class="font-bold text-gray-900">{{ formatCurrency(penjualan.total) }}</span>
            </div>
          </div>
        </div>

        <!-- Search Member -->
        <div class="mb-3">
          <label class="block text-xs font-medium text-gray-700 mb-1">Cari Member</label>
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Ketik nama atau kode member..."
            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-[#996600]"
          />
        </div>

        <!-- Buyer List -->
        <div class="mb-4 max-h-64 overflow-y-auto space-y-1">
          <div v-if="filteredBuyers.length === 0" class="text-center py-8">
            <svg class="w-12 h-12 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            <p class="text-sm text-gray-500">Tidak ada member ditemukan</p>
          </div>
          <div
            v-for="buyer in filteredBuyers"
            :key="buyer.id"
            @click="selectBuyer(buyer.id)"
            class="p-3 border rounded-lg cursor-pointer transition-colors hover:bg-amber-50"
            :class="selectedBuyerId === buyer.id ? 'border-[#996600] bg-amber-50' : 'border-gray-200'"
          >
            <div class="flex items-center justify-between">
              <div class="flex-1">
                <div class="font-medium text-sm text-gray-900">{{ buyer.name }}</div>
                <div class="text-xs text-gray-500">{{ buyer.member_code }}</div>
                <div v-if="buyer.membership_tier" class="text-xs text-[#996600] mt-0.5">
                  {{ buyer.membership_tier.name }}
                </div>
              </div>
              <div v-if="selectedBuyerId === buyer.id" class="flex-shrink-0">
                <svg class="w-5 h-5 text-[#996600]" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Actions -->
      <div class="p-4 bg-gray-50 rounded-b-lg flex gap-2">
        <button
          @click="$emit('close')"
          class="flex-1 px-4 py-2.5 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 font-medium transition-colors"
        >
          Batal
        </button>
        <button
          @click="confirmAssign"
          :disabled="!selectedBuyerId"
          class="flex-1 px-4 py-2.5 bg-[#996600] hover:bg-[#7a5100] text-white rounded-lg font-semibold transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
        >
          Tetapkan
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useFormatters } from '@/composables/useFormatters'

const { formatCurrency, formatDateTime } = useFormatters()

const props = defineProps({
  penjualan: Object,
  buyers: Array,
})

const emit = defineEmits(['close', 'confirm'])

const searchQuery = ref('')
const selectedBuyerId = ref(null)

const filteredBuyers = computed(() => {
  if (!props.buyers) return []

  if (!searchQuery.value) {
    return props.buyers
  }

  const query = searchQuery.value.toLowerCase()
  return props.buyers.filter(buyer =>
    buyer.name.toLowerCase().includes(query) ||
    buyer.member_code?.toLowerCase().includes(query)
  )
})

const selectBuyer = (buyerId) => {
  selectedBuyerId.value = buyerId
}

const confirmAssign = () => {
  if (selectedBuyerId.value) {
    emit('confirm', selectedBuyerId.value)
  }
}
</script>
