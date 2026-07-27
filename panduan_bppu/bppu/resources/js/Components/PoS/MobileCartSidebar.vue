<template>
  <transition name="slide">
    <div
      v-if="show"
      class="md:hidden fixed inset-0 z-50"
      @click.self="$emit('close')"
    >
      <div class="absolute inset-y-0 right-0 w-full max-w-md bg-white shadow-2xl flex flex-col">
        <!-- Cart Header -->
        <div class="flex items-center justify-between p-3 border-b gap-2 flex-shrink-0">
          <div class="flex items-center gap-3 flex-1">
            <h2 class="text-base font-bold">Keranjang ({{ cart.length }})</h2>
            <button
              v-if="cart.length > 0"
              @click="$emit('clear-cart')"
              class="text-gray-600 hover:text-gray-800 p-1 hover:bg-gray-100 rounded"
              title="Reset keranjang"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
              </svg>
            </button>
          </div>
          <button @click="$emit('close')" class="p-1.5 hover:bg-gray-100 rounded-full flex-shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <!-- Cart Items -->
        <div class="flex-1 overflow-y-auto p-4 space-y-3">
          <div
            v-for="(item, index) in cart"
            :key="index"
            class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg"
          >
            <div class="flex-1 min-w-0">
              <h4 class="font-semibold text-sm text-gray-800 truncate">{{ item.nama_barang }}</h4>
              <div class="flex items-center gap-1.5 flex-wrap">
                <p v-if="item.diskon_per_item > 0" class="text-xs text-gray-400 line-through">
                  Rp {{ formatCurrency(item.harga_satuan) }}
                </p>
                <p class="text-xs" :class="item.diskon_per_item > 0 ? 'text-green-700 font-semibold' : 'text-gray-500'">
                  Rp {{ formatCurrency(item.harga_satuan - (item.diskon_per_item || 0)) }}
                </p>
                <span v-if="item.diskon_per_item > 0" class="text-[9px] px-1 py-0.5 bg-green-100 text-green-700 rounded font-semibold">DISKON</span>
              </div>
            </div>
            <div class="flex items-center gap-2">
              <button
                @click="item.qty === 1 ? $emit('remove-item', index) : $emit('decrement-qty', index)"
                :class="[
                  'w-7 h-7 rounded-full flex items-center justify-center transition-colors',
                  item.qty === 1
                    ? 'bg-red-100 hover:bg-red-200 text-red-600'
                    : 'bg-gray-200 hover:bg-gray-300 text-gray-700'
                ]"
              >
                <svg v-if="item.qty === 1" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                </svg>
              </button>

              <span class="w-8 text-center font-semibold text-sm">{{ item.qty }}</span>

              <button
                @click="$emit('increment-qty', index)"
                class="w-7 h-7 rounded-full bg-[#996600] hover:bg-[#7a5100] text-white flex items-center justify-center transition-colors"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
              </button>
            </div>
          </div>

          <div v-if="cart.length === 0" class="text-center py-12 text-gray-400">
            <svg class="w-16 h-16 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <p class="text-sm">Keranjang kosong</p>
          </div>
        </div>

        <!-- Cart Footer -->
        <div v-if="cart.length > 0" class="border-t p-4 space-y-3 bg-gray-50 flex-shrink-0">
          <div class="space-y-3">
            <div class="flex justify-between items-center px-3 py-2 bg-white rounded-lg border border-gray-200">
              <span class="text-gray-600 text-sm">Subtotal</span>
              <span class="font-bold text-2xl text-gray-800">Rp {{ formatCurrency(subtotal) }}</span>
            </div>
            <div v-if="diskon > 0" class="flex justify-between items-center px-3 py-2 bg-orange-50 rounded-lg border border-orange-200">
              <span class="text-orange-700 text-sm font-medium">Diskon</span>
              <span class="font-bold text-lg text-orange-600">- Rp {{ formatCurrency(diskon) }}</span>
            </div>
            <div class="flex justify-between items-center px-3 py-3 bg-gradient-to-r from-[#f4efe5] to-[#eae0cc] rounded-lg border-2 border-[#996600] shadow-md">
              <span class="text-[#3d2800] text-sm font-semibold uppercase tracking-wide">Total</span>
              <span class="font-bold text-3xl text-[#996600]">Rp {{ formatCurrency(total) }}</span>
            </div>
          </div>

          <button
            @click="$emit('close')"
            class="w-full bg-[#996600] hover:bg-[#7a5100] text-white py-2.5 rounded-lg font-semibold text-sm transition-colors"
          >
            Tutup
          </button>
        </div>
      </div>
    </div>
  </transition>
</template>

<script setup>
defineProps({
  show: Boolean,
  cart: Array,
  subtotal: Number,
  total: Number,
  diskon: Number
})

defineEmits([
  'close',
  'increment-qty',
  'decrement-qty',
  'remove-item',
  'clear-cart',
  'proceed-checkout',
  'update:diskon'
])

const formatCurrency = (value) => {
  return new Intl.NumberFormat('id-ID').format(value)
}
</script>

<style scoped>
.slide-enter-active,
.slide-leave-active {
  transition: all 0.3s ease;
}

.slide-enter-from,
.slide-leave-to {
  transform: translateX(100%);
  opacity: 0;
}
</style>
