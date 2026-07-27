<template>
  <div class="flex flex-col h-full max-h-[calc(100vh-120px)] bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    <!-- Cart Header -->
    <div class="flex items-center justify-between p-2 bg-gradient-to-r from-[#996600] to-[#7a5100] gap-2 flex-shrink-0">
      <h2 class="text-xs font-bold text-white">Keranjang ({{ cart.length }})</h2>
      <button
        v-if="cart.length > 0"
        @click="$emit('clear-cart')"
        class="text-white hover:text-[#eae0cc] p-1 hover:bg-white/20 rounded transition-colors"
        title="Reset keranjang"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
        </svg>
      </button>
    </div>

    <!-- Cart Items -->
    <div class="flex-1 overflow-y-auto p-2 space-y-1.5">
      <div
        v-for="(item, index) in cart"
        :key="index"
        class="flex items-center gap-2 p-1.5 bg-gray-50 rounded-lg"
      >
        <div class="flex-1 min-w-0">
          <div class="flex items-center gap-1">
            <h4 class="font-semibold text-xs text-gray-800 truncate">{{ item.nama_barang }}</h4>
            <span
              v-if="item.work_unit_name"
              class="flex-shrink-0 text-[9px] px-1 py-0.5 rounded bg-[#f4efe5] text-[#7a5100] font-medium border border-[#d6c199]"
            >{{ item.work_unit_name }}</span>
          </div>
          <div class="flex items-center gap-1">
            <p v-if="item.diskon_per_item > 0" class="text-[10px] text-gray-400 line-through">
              Rp {{ formatCurrency(item.harga_satuan) }}
            </p>
            <p class="text-xs text-gray-500">
              {{ item.qty }} × Rp {{ formatCurrency(item.harga_satuan - (item.diskon_per_item || 0)) }}
            </p>
          </div>
          <div class="flex items-center gap-1">
            <p class="text-xs font-semibold text-[#996600]">
              Rp {{ formatCurrency(item.qty * (item.harga_satuan - (item.diskon_per_item || 0))) }}
            </p>
            <span v-if="item.diskon_per_item > 0" class="text-[9px] px-1 py-0.5 bg-green-100 text-green-700 rounded font-semibold">DISKON</span>
          </div>
        </div>
        <div class="flex items-center gap-1">
          <!-- Tombol Minus / Trash -->
          <button
            @click="item.qty === 1 ? $emit('remove-item', index) : $emit('decrement-qty', index)"
            :class="[
              'w-6 h-6 rounded-full flex items-center justify-center transition-colors',
              item.qty === 1
                ? 'bg-red-100 hover:bg-red-200 text-red-600'
                : 'bg-gray-200 hover:bg-gray-300 text-gray-700'
            ]"
          >
            <!-- Trash icon jika qty = 1 -->
            <svg v-if="item.qty === 1" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
            <!-- Minus icon jika qty > 1 -->
            <svg v-else class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
            </svg>
          </button>

          <span class="w-6 text-center font-semibold text-xs">{{ item.qty }}</span>

          <!-- Tombol Plus -->
          <button
            @click="$emit('increment-qty', index)"
            class="w-6 h-6 rounded-full bg-[#996600] hover:bg-[#7a5100] text-white flex items-center justify-center transition-colors"
          >
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
  </div>
</template>

<script setup>
defineProps({
  cart: Array,
})

defineEmits(['increment-qty', 'decrement-qty', 'remove-item', 'clear-cart'])

const formatCurrency = (value) => {
  return new Intl.NumberFormat('id-ID').format(value)
}
</script>
