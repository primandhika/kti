<template>
  <div class="md:hidden fixed bottom-14 left-0 right-0 bg-white border-t shadow-lg z-40">
    <!-- Cart Summary - Scrollable -->
    <div v-if="cart.length > 0" class="max-h-32 overflow-y-auto border-b bg-gray-50">
      <div class="p-2 space-y-1">
        <div
          v-for="(item, index) in cart"
          :key="index"
          class="flex items-center justify-between text-xs gap-2 py-1"
        >
          <div class="flex-1 min-w-0">
            <span class="truncate text-gray-700">{{ item.nama_barang }} <span class="text-gray-500">({{ item.qty }}x)</span></span>
            <span
              v-if="item.work_unit_name"
              class="ml-1 text-[9px] px-1 py-0.5 rounded bg-[#f4efe5] text-[#7a5100] font-medium border border-[#d6c199]"
            >{{ item.work_unit_name }}</span>
          </div>
          <span class="font-semibold text-gray-900 flex-shrink-0">
            Rp {{ formatCurrency(item.qty * item.harga_satuan) }}
          </span>
        </div>
      </div>
      <!-- Total -->
      <div class="px-2 pb-2 pt-1 border-t border-gray-200 bg-white">
        <div class="flex items-center justify-between text-xs">
          <span class="font-semibold text-gray-700">Total</span>
          <span class="font-bold text-[#996600]">Rp {{ formatCurrency(total) }}</span>
        </div>
      </div>
    </div>

    <!-- Action Buttons -->
    <div class="p-2">
      <!-- All Action Buttons in One Row -->
      <div class="flex items-center gap-1.5">
        <!-- Bayar Button (Direct to checkout modal) -->
        <button
          v-if="cart.length > 0"
          @click="$emit('proceed-checkout')"
          class="flex-1 bg-[#996600] hover:bg-[#7a5100] text-white py-2 px-2 rounded-lg font-semibold text-xs flex items-center justify-center gap-1.5 transition-colors"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
          </svg>
          <span>Bayar</span>
        </button>

        <!-- Empty state button -->
        <button
          v-else
          disabled
          class="flex-1 bg-gray-300 text-gray-500 py-2 px-2 rounded-lg font-semibold text-xs cursor-not-allowed"
        >
          Keranjang Kosong
        </button>

        <!-- Edit Keranjang Button (Icon Only) -->
        <button
          v-if="cart.length > 0"
          @click="$emit('show-cart')"
          class="p-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition-colors"
          title="Edit Keranjang"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
          </svg>
        </button>

        <!-- Hapus Button (Icon Only) -->
        <button
          v-if="cart.length > 0"
          @click="$emit('clear-cart')"
          class="p-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-colors"
          title="Hapus semua item"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
          </svg>
        </button>

        <!-- Refresh Button (Icon Only) -->
        <button
          @click="$emit('refresh')"
          :disabled="isRefreshing"
          :class="[
            'p-2 rounded-lg transition-colors',
            isRefreshing
              ? 'bg-gray-100 text-gray-400 cursor-not-allowed'
              : 'bg-blue-500 hover:bg-blue-600 text-white'
          ]"
          :title="isRefreshing ? 'Memperbarui...' : 'Perbarui barang'"
        >
          <svg
            :class="['w-5 h-5', isRefreshing && 'animate-spin']"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
          </svg>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  cart: Array,
  total: Number,
  isRefreshing: Boolean
})

defineEmits(['show-cart', 'refresh', 'clear-cart', 'proceed-checkout'])

const formatCurrency = (value) => {
  return new Intl.NumberFormat('id-ID').format(value)
}
</script>
