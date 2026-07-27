<template>
  <transition name="modal">
    <div
      v-if="show"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4"
      @click.self="$emit('close')"
    >
      <div class="bg-white rounded-lg shadow-xl w-full max-w-md overflow-hidden">
        <div class="flex items-center justify-between p-4 bg-gradient-to-r from-[#996600] to-[#7a5100] border-b">
          <h3 class="text-lg font-bold text-white">Produk yang Mungkin Dimaksud</h3>
          <button
            @click="$emit('close')"
            class="p-1 hover:bg-white/20 rounded-full transition-colors"
          >
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <div class="p-4 space-y-3 max-h-96 overflow-y-auto">
          <div
            v-for="barang in suggestions"
            :key="barang.id"
            @click="$emit('select', barang)"
            class="p-3 border-2 border-gray-200 rounded-lg hover:border-[#996600] hover:bg-[#f4efe5] cursor-pointer transition-all"
          >
            <div class="flex items-start gap-3">
              <div
                v-if="barang.foto"
                class="w-16 h-16 flex-shrink-0 rounded-lg overflow-hidden bg-gray-100"
              >
                <img
                  :src="`/images/barang/${barang.foto}`"
                  :alt="barang.nama_barang"
                  class="w-full h-full object-cover"
                />
              </div>
              <div
                v-else
                class="w-16 h-16 flex-shrink-0 rounded-lg bg-gray-100 flex items-center justify-center"
              >
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
              </div>

              <div class="flex-1 min-w-0">
                <h4 class="font-semibold text-gray-900 truncate">{{ barang.nama_barang }}</h4>
                <p class="text-sm text-gray-600 mt-0.5">
                  <span class="font-medium">Kode:</span> {{ barang.kode_barang }}
                </p>
                <div v-if="barang.harga_satuan" class="text-sm font-semibold text-[#996600] mt-1">
                  Rp {{ formatCurrency(barang.harga_satuan) }}
                </div>
                <div v-else-if="barang.varians && barang.varians.length > 0" class="text-sm font-semibold text-[#996600] mt-1">
                  <span v-if="barang.varians.length === 1">
                    Rp {{ formatCurrency(barang.varians[0].harga) }}
                  </span>
                  <span v-else>
                    Rp {{ formatCurrency(getMinPrice(barang.varians)) }} - {{ formatCurrency(getMaxPrice(barang.varians)) }}
                  </span>
                </div>
                <div class="flex items-center gap-1 mt-1">
                  <svg class="w-3.5 h-3.5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                  </svg>
                  <span class="text-xs text-gray-500">{{ Math.round(barang.similarity * 100) }}% kemiripan</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="p-4 bg-gray-50 border-t">
          <button
            @click="$emit('close')"
            class="w-full px-4 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg font-medium transition-colors"
          >
            Batal
          </button>
        </div>
      </div>
    </div>
  </transition>
</template>

<script setup>
defineProps({
  show: Boolean,
  suggestions: Array
})

defineEmits(['close', 'select'])

const formatCurrency = (value) => {
  return new Intl.NumberFormat('id-ID').format(value)
}

const getMinPrice = (varians) => {
  return Math.min(...varians.map(v => v.harga))
}

const getMaxPrice = (varians) => {
  return Math.max(...varians.map(v => v.harga))
}
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

.modal-enter-active .bg-white,
.modal-leave-active .bg-white {
  transition: transform 0.3s ease;
}

.modal-enter-from .bg-white,
.modal-leave-to .bg-white {
  transform: scale(0.9);
}
</style>
