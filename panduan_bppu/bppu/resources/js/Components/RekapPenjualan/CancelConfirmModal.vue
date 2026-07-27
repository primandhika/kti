<template>
  <div
    v-if="show && penjualan"
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
    @click.self="$emit('close')"
  >
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
      <!-- Header -->
      <div class="bg-red-500 text-white p-4 rounded-t-lg">
        <div class="flex items-center gap-3">
          <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center flex-shrink-0">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
          </div>
          <div>
            <h3 class="text-lg font-bold">Batalkan Transaksi</h3>
            <p class="text-sm text-red-100 mt-0.5">Tindakan ini akan mengembalikan stok barang</p>
          </div>
        </div>
      </div>

      <!-- Content -->
      <div class="p-4">
        <div class="bg-gray-50 rounded-lg p-3 mb-4">
          <div class="text-xs text-gray-500 mb-1">No. Transaksi</div>
          <div class="font-mono text-sm font-bold text-gray-900 mb-2">{{ penjualan.nomor_transaksi }}</div>
          <div class="text-xs text-gray-600">
            {{ formatDateTime(penjualan.tanggal_transaksi) }}
          </div>
          <div class="mt-2 pt-2 border-t border-gray-200">
            <div class="flex justify-between text-sm">
              <span class="text-gray-600">Total Transaksi</span>
              <span class="font-bold text-gray-900">{{ formatCurrency(penjualan.total) }}</span>
            </div>
          </div>
        </div>

        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mb-4">
          <div class="flex gap-2">
            <svg class="w-5 h-5 text-yellow-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div class="text-sm text-yellow-800">
              <p class="font-medium">Perhatian!</p>
              <p class="text-xs mt-1">Stok barang akan dikembalikan ke inventory. Pastikan tindakan ini sudah benar.</p>
            </div>
          </div>
        </div>

        <p class="text-sm text-gray-700">
          Apakah Anda yakin ingin membatalkan transaksi ini?
        </p>
      </div>

      <!-- Actions -->
      <div class="p-4 bg-gray-50 rounded-b-lg flex gap-2">
        <button
          @click="$emit('close')"
          class="flex-1 px-4 py-2.5 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 font-medium transition-colors"
        >
          Tidak, Kembali
        </button>
        <button
          @click="$emit('confirm')"
          class="flex-1 px-4 py-2.5 bg-red-500 hover:bg-red-600 text-white rounded-lg font-semibold transition-colors"
        >
          Ya, Batalkan
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useFormatters } from '@/composables/useFormatters'

const { formatCurrency, formatDateTime } = useFormatters()

defineProps({
  show: Boolean,
  penjualan: Object,
})

defineEmits(['close', 'confirm'])
</script>
