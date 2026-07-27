<template>
  <div
    v-if="show && penjualan"
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
    @click.self="$emit('close')"
  >
    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
      <!-- Header -->
      <div class="bg-gradient-to-r from-[#6b4700] to-[#5b3d00] text-white p-4 sticky top-0 z-10">
        <div class="flex items-center justify-between">
          <div>
            <h3 class="text-lg font-bold">Detail Transaksi</h3>
            <p class="text-sm text-[#c1a366]">{{ penjualan.nomor_transaksi }}</p>
          </div>
          <button @click="$emit('close')" class="p-2 hover:bg-white/20 rounded-full transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>

      <!-- Content -->
      <div class="p-4">
        <!-- Info Transaksi -->
        <div class="bg-gray-50 rounded-lg p-4 mb-4">
          <h4 class="text-sm font-semibold text-gray-700 mb-3">Informasi Transaksi</h4>
          <div class="space-y-2 text-sm">
            <div class="flex justify-between">
              <span class="text-gray-600">Tanggal & Waktu:</span>
              <span class="font-medium text-gray-900">{{ formatDateTime(penjualan.tanggal_transaksi) }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">Unit Kerja:</span>
              <span class="font-medium text-gray-900">{{ penjualan.work_unit?.name || '-' }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">Kasir:</span>
              <span class="font-medium text-gray-900">{{ penjualan.user?.name || '-' }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">Metode Pembayaran:</span>
              <span
                class="px-2 py-0.5 rounded-full text-xs font-medium"
                :class="getPaymentMethodClass(penjualan.metode_pembayaran)"
              >
                {{ penjualan.metode_pembayaran }}
              </span>
            </div>
            <div v-if="penjualan.nama_pelanggan" class="flex justify-between">
              <span class="text-gray-600">Nama Pelanggan:</span>
              <span class="font-medium text-gray-900">{{ penjualan.nama_pelanggan }}</span>
            </div>
            <div v-if="penjualan.buyer" class="flex justify-between">
              <span class="text-gray-600">Member Buyer:</span>
              <span class="font-medium text-indigo-700">{{ penjualan.buyer.name }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">Status:</span>
              <span
                class="px-2 py-0.5 rounded-full text-xs font-medium"
                :class="getStatusClass(penjualan)"
              >
                {{ getStatusText(penjualan) }}
              </span>
            </div>
          </div>
        </div>

        <!-- Items List -->
        <div class="bg-gray-50 rounded-lg p-4 mb-4">
          <h4 class="text-sm font-semibold text-gray-700 mb-3">Item Terjual</h4>
          <div class="space-y-2">
            <div
              v-for="(item, index) in penjualan.items"
              :key="index"
              class="flex justify-between items-start bg-white rounded p-3"
            >
              <div class="flex-1">
                <div class="font-medium text-gray-900">{{ item.nama_barang }}</div>
                <div class="text-xs text-gray-500">{{ item.qty }} {{ item.satuan }} × Rp {{ formatCurrency(item.harga_satuan) }}</div>
                <div v-if="item.diskon_per_item > 0" class="text-xs text-red-600">
                  Diskon: -Rp {{ formatCurrency(item.diskon_per_item) }}
                </div>
              </div>
              <div class="text-right">
                <div class="font-semibold text-gray-900">Rp {{ formatCurrency(item.subtotal) }}</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Summary -->
        <div class="bg-gray-50 rounded-lg p-4 mb-4">
          <h4 class="text-sm font-semibold text-gray-700 mb-3">Ringkasan Pembayaran</h4>
          <div class="space-y-2 text-sm">
            <div class="flex justify-between">
              <span class="text-gray-600">Subtotal:</span>
              <span class="font-medium text-gray-900">Rp {{ formatCurrency(penjualan.subtotal) }}</span>
            </div>
            <div v-if="penjualan.diskon > 0" class="flex justify-between text-orange-600">
              <div class="flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                </svg>
                <span v-if="penjualan.voucher">
                  {{ penjualan.voucher.potongan?.nama || 'Voucher' }}
                  <span class="font-mono text-xs text-gray-400 ml-1">({{ penjualan.voucher.kode_voucher }})</span>
                </span>
                <span v-else>Diskon</span>
              </div>
              <span class="font-medium">-Rp {{ formatCurrency(penjualan.diskon) }}</span>
            </div>
            <div v-if="penjualan.biaya_layanan > 0" class="flex justify-between text-gray-600">
              <span>Biaya layanan:</span>
              <span class="font-medium">+Rp {{ formatCurrency(penjualan.biaya_layanan) }}</span>
            </div>
            <div class="flex justify-between text-lg font-bold border-t pt-2">
              <span class="text-gray-900">Total:</span>
              <span class="text-green-600">Rp {{ formatCurrency(penjualan.total) }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">Bayar:</span>
              <span class="font-medium text-gray-900">Rp {{ formatCurrency(penjualan.bayar) }}</span>
            </div>
            <div v-if="penjualan.kembalian > 0" class="flex justify-between text-green-600">
              <span>Kembalian:</span>
              <span class="font-semibold">Rp {{ formatCurrency(penjualan.kembalian) }}</span>
            </div>
          </div>
        </div>

        <!-- Verification History -->
        <div v-if="penjualan.is_verified || penjualan.is_approved || penjualan.is_recorded" class="bg-[#eae0cc] rounded-lg p-4 mb-4">
          <h4 class="text-sm font-semibold text-gray-700 mb-3">Riwayat Verifikasi</h4>
          <div class="space-y-2 text-sm">
            <div v-if="penjualan.is_verified" class="flex items-start gap-2">
              <svg class="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
              </svg>
              <div>
                <div class="font-medium text-green-700">Verified</div>
                <div class="text-xs text-gray-600">{{ penjualan.verified_by?.name || '-' }}</div>
                <div class="text-xs text-gray-500">{{ formatDateTime(penjualan.verified_at) }}</div>
              </div>
            </div>
            <div v-if="penjualan.is_approved" class="flex items-start gap-2">
              <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
              </svg>
              <div>
                <div class="font-medium text-blue-700">Approved</div>
                <div class="text-xs text-gray-600">{{ penjualan.approved_by?.name || '-' }}</div>
                <div class="text-xs text-gray-500">{{ formatDateTime(penjualan.approved_at) }}</div>
              </div>
            </div>
            <div v-if="penjualan.is_recorded" class="flex items-start gap-2">
              <svg class="w-5 h-5 text-purple-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm9.707 5.707a1 1 0 00-1.414-1.414L9 12.586l-1.293-1.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
              </svg>
              <div>
                <div class="font-medium text-purple-700">Recorded to Buku Kas</div>
                <div class="text-xs text-gray-600">{{ penjualan.recorded_by?.name || '-' }}</div>
                <div class="text-xs text-gray-500">{{ formatDateTime(penjualan.recorded_at) }}</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Foto Bukti Transaksi -->
        <div v-if="penjualan.foto_bukti" class="bg-gray-50 rounded-lg p-4 mb-4">
          <h4 class="text-sm font-semibold text-gray-700 mb-3">Foto Bukti Transaksi</h4>
          <div class="rounded-lg overflow-hidden border border-gray-200">
            <img
              :src="penjualan.foto_bukti"
              :alt="`Bukti Transaksi ${penjualan.nomor_transaksi}`"
              class="w-full h-auto"
              @error="$event.target.src = '/images/no-image.png'"
            />
          </div>
          <p class="text-xs text-gray-500 mt-2 text-center">Bukti transaksi diupload oleh kasir</p>
        </div>

        <!-- Catatan -->
        <div v-if="penjualan.catatan" class="bg-yellow-50 rounded-lg p-4">
          <h4 class="text-sm font-semibold text-gray-700 mb-2">Catatan</h4>
          <p class="text-sm text-gray-600">{{ penjualan.catatan }}</p>
        </div>
      </div>

      <!-- Footer -->
      <div class="p-4 bg-gray-50 border-t space-y-2">
        <!-- Print Button - Prominent -->
        <button
          v-if="canPrint"
          @click="$emit('print', penjualan)"
          class="w-full px-4 py-2.5 bg-[#996600] hover:bg-[#7a5100] text-white rounded-lg font-semibold transition-colors flex items-center justify-center gap-2"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
          </svg>
          Cetak Struk
        </button>

        <!-- Close Button -->
        <button
          @click="$emit('close')"
          class="w-full px-4 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg font-semibold transition-colors"
        >
          Tutup
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useFormatters } from '@/composables/useFormatters'

const { formatCurrency, formatDateTime, getPaymentMethodClass, getStatusClass, getStatusText } = useFormatters()

defineProps({
  show: Boolean,
  penjualan: Object,
  canPrint: Boolean,
})

defineEmits(['close', 'print'])
</script>
