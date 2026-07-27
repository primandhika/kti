<template>
  <div class="flex flex-col h-full max-h-[calc(100vh-120px)] bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    <!-- Checkout Header -->
    <div class="p-2 bg-gradient-to-r from-[#996600] to-[#7a5100] flex-shrink-0">
      <h2 class="text-xs font-bold text-white">Pembayaran</h2>
    </div>

    <!-- Checkout Content -->
    <div class="flex-1 overflow-y-auto p-2 space-y-2">
      <!-- Cart Summary -->
      <div class="space-y-1.5 text-sm">
        <div class="flex justify-between">
          <span class="text-gray-600">Subtotal</span>
          <span class="font-semibold">Rp {{ formatCurrency(subtotal) }}</span>
        </div>
        <div class="flex justify-between items-center">
          <span class="text-gray-600">Diskon</span>
          <input
            :value="diskon"
            @input="$emit('update:diskon', Number($event.target.value))"
            type="number"
            min="0"
            :max="subtotal"
            class="w-32 text-right px-2 py-1 text-sm border border-gray-300 rounded"
          />
        </div>
        <div class="flex justify-between text-base font-bold border-t pt-1.5">
          <span>Total</span>
          <span class="text-[#996600]">Rp {{ formatCurrency(total) }}</span>
        </div>
      </div>

      <!-- Receipt Preview -->
      <div v-if="cart.length > 0" class="border-t pt-2">
        <h3 class="text-xs font-semibold text-gray-700 mb-1.5">Rincian:</h3>
        <div class="space-y-1 text-xs">
          <div
            v-for="(item, index) in cart"
            :key="index"
            class="flex justify-between items-start gap-2"
          >
            <div class="flex-1">
              <p class="font-medium text-gray-800">{{ item.nama_barang }}</p>
              <p class="text-gray-500">{{ item.qty }} × Rp {{ formatCurrency(item.harga_satuan) }}</p>
            </div>
            <p class="font-semibold text-gray-900">Rp {{ formatCurrency(item.qty * item.harga_satuan) }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Checkout Actions -->
    <div v-if="cart.length > 0" class="border-t p-2 bg-gray-50 flex-shrink-0">
      <div class="flex gap-2">
        <button
          @click="$emit('proceed-checkout')"
          class="flex-[3] bg-[#996600] hover:bg-[#7a5100] text-white py-2.5 rounded-lg font-semibold text-sm transition-colors flex items-center justify-center gap-2"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
          </svg>
          <span>Bayar</span>
        </button>
        <button
          @click="$emit('clear-cart')"
          class="flex-1 px-2 bg-gray-200 hover:bg-gray-300 text-gray-700 py-2.5 rounded-lg text-sm font-medium transition-colors flex items-center justify-center"
          title="Reset Keranjang"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
          </svg>
        </button>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else class="flex-1 flex items-center justify-center text-gray-400">
      <div class="text-center">
        <svg class="w-20 h-20 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
        </svg>
        <p class="text-sm">Tambahkan item untuk checkout</p>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  cart: Array,
  subtotal: Number,
  diskon: Number,
  total: Number,
})

defineEmits(['update:diskon', 'proceed-checkout', 'clear-cart'])

const formatCurrency = (value) => {
  return new Intl.NumberFormat('id-ID').format(value)
}
</script>
