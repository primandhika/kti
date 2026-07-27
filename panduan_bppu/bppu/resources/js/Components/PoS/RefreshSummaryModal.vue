<template>
  <transition name="modal-fade">
    <div
      v-if="show"
      class="fixed inset-0 z-50 flex items-center justify-center p-4"
      @click.self="$emit('close')"
    >
      <!-- Backdrop -->
      <div class="absolute inset-0 bg-black bg-opacity-50"></div>

      <!-- Modal -->
      <div class="relative bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[80vh] overflow-hidden">
        <!-- Header -->
        <div class="flex items-center justify-between p-4 border-b" style="background-color: #f4efe5;">
          <div class="flex items-center gap-2">
            <div class="w-10 h-10 rounded-full flex items-center justify-center" style="background-color: #996600;">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <div>
              <h3 class="text-lg font-bold text-gray-900">Data Berhasil Diperbarui</h3>
              <p class="text-xs text-gray-600">{{ summary.timestamp }}</p>
            </div>
          </div>
          <button
            @click="$emit('close')"
            class="p-1 rounded-full transition-colors"
            style="hover:background-color: #eae0cc;"
          >
            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <!-- Content -->
        <div class="p-4 overflow-y-auto max-h-[calc(80vh-140px)]">
          <!-- Summary Stats -->
          <div class="grid grid-cols-3 gap-3 mb-4">
            <div class="rounded-lg p-3 text-center" style="background-color: #f4efe5; border: 1px solid #d6c199;">
              <div class="text-2xl font-bold" style="color: #6b4700;">{{ summary.newItems.length }}</div>
              <div class="text-xs font-medium" style="color: #5b3d00;">Barang Baru</div>
            </div>
            <div class="rounded-lg p-3 text-center" style="background-color: #eae0cc; border: 1px solid #c1a366;">
              <div class="text-2xl font-bold" style="color: #996600;">{{ summary.stockUpdated.length }}</div>
              <div class="text-xs font-medium" style="color: #7a5100;">Stok Diperbarui</div>
            </div>
            <div class="bg-red-50 border border-red-200 rounded-lg p-3 text-center">
              <div class="text-2xl font-bold text-red-600">{{ summary.outOfStock.length }}</div>
              <div class="text-xs text-red-700 font-medium">Stok Habis</div>
            </div>
          </div>

          <!-- Barang Baru -->
          <div v-if="summary.newItems.length > 0" class="mb-4">
            <h4 class="font-semibold text-sm text-gray-800 mb-2 flex items-center gap-2">
              <span class="w-2 h-2 rounded-full bg-green-500"></span>
              Barang Baru ({{ summary.newItems.length }})
            </h4>
            <div class="space-y-2">
              <div
                v-for="item in summary.newItems"
                :key="item.id"
                class="flex items-center justify-between p-2 bg-green-50 border border-green-100 rounded text-sm"
              >
                <div class="flex-1">
                  <div class="font-medium text-gray-900">{{ item.nama_barang }}</div>
                  <div class="text-xs text-gray-600">{{ item.kategori }}</div>
                </div>
                <div class="text-right">
                  <div class="font-semibold text-green-700">Stok: {{ item.stok }}</div>
                  <div class="text-xs text-gray-600">Rp {{ formatCurrency(item.harga_jual) }}</div>
                </div>
              </div>
            </div>
          </div>

          <!-- Stok Diperbarui -->
          <div v-if="summary.stockUpdated.length > 0" class="mb-4">
            <h4 class="font-semibold text-sm text-gray-800 mb-2 flex items-center gap-2">
              <span class="w-2 h-2 rounded-full bg-blue-500"></span>
              Stok Diperbarui ({{ summary.stockUpdated.length }})
            </h4>
            <div class="space-y-2">
              <div
                v-for="item in summary.stockUpdated"
                :key="item.id"
                class="flex items-center justify-between p-2 bg-blue-50 border border-blue-100 rounded text-sm"
              >
                <div class="flex-1">
                  <div class="font-medium text-gray-900">{{ item.nama_barang }}</div>
                  <div class="text-xs text-gray-600">{{ item.kategori }}</div>
                </div>
                <div class="text-right">
                  <div class="flex items-center gap-2 justify-end">
                    <span class="text-gray-500 line-through text-xs">{{ item.oldStok }}</span>
                    <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                    </svg>
                    <span :class="[
                      'font-semibold',
                      item.stok > item.oldStok ? 'text-green-600' : 'text-orange-600'
                    ]">{{ item.stok }}</span>
                  </div>
                  <div class="text-xs text-gray-600">
                    {{ item.stok > item.oldStok ? '+' : '' }}{{ item.stok - item.oldStok }}
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Stok Habis -->
          <div v-if="summary.outOfStock.length > 0" class="mb-4">
            <h4 class="font-semibold text-sm text-gray-800 mb-2 flex items-center gap-2">
              <span class="w-2 h-2 rounded-full bg-red-500"></span>
              Stok Habis ({{ summary.outOfStock.length }})
            </h4>
            <div class="space-y-2">
              <div
                v-for="item in summary.outOfStock"
                :key="item.id"
                class="flex items-center justify-between p-2 bg-red-50 border border-red-100 rounded text-sm"
              >
                <div class="flex-1">
                  <div class="font-medium text-gray-900">{{ item.nama_barang }}</div>
                  <div class="text-xs text-gray-600">{{ item.kategori }}</div>
                </div>
                <div class="text-right">
                  <div class="font-semibold text-red-600">Habis</div>
                  <div class="text-xs text-gray-500">Dari: {{ item.oldStok }}</div>
                </div>
              </div>
            </div>
          </div>

          <!-- No Changes -->
          <div
            v-if="summary.newItems.length === 0 && summary.stockUpdated.length === 0 && summary.outOfStock.length === 0"
            class="text-center py-8"
          >
            <svg class="w-16 h-16 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="text-gray-600 font-medium">Tidak ada perubahan data</p>
            <p class="text-sm text-gray-500">Semua data barang sudah up to date</p>
          </div>
        </div>

        <!-- Footer -->
        <div class="border-t p-4 bg-gray-50">
          <button
            @click="$emit('close')"
            class="w-full text-white py-2.5 rounded-lg font-semibold transition-colors"
            style="background-color: #996600;"
            @mouseenter="$event.target.style.backgroundColor = '#7a5100'"
            @mouseleave="$event.target.style.backgroundColor = '#996600'"
          >
            Tutup
          </button>
        </div>
      </div>
    </div>
  </transition>
</template>

<script setup>
const props = defineProps({
  show: Boolean,
  summary: {
    type: Object,
    default: () => ({
      newItems: [],
      stockUpdated: [],
      outOfStock: [],
      timestamp: '',
    })
  }
})

defineEmits(['close'])

const formatCurrency = (value) => {
  return new Intl.NumberFormat('id-ID').format(value)
}
</script>

<style scoped>
.modal-fade-enter-active,
.modal-fade-leave-active {
  transition: opacity 0.3s ease;
}

.modal-fade-enter-from,
.modal-fade-leave-to {
  opacity: 0;
}

.modal-fade-enter-active .relative,
.modal-fade-leave-active .relative {
  transition: transform 0.3s ease;
}

.modal-fade-enter-from .relative,
.modal-fade-leave-to .relative {
  transform: scale(0.95);
}
</style>
