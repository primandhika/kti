<template>
  <div
    class="bg-white rounded-lg shadow-sm border overflow-hidden transition-all"
    :class="cardBorderClass"
  >
    <!-- Card Header -->
    <div class="px-3 py-2" :class="headerClass">
      <div class="flex items-start justify-between gap-2">
        <div class="flex items-start gap-2 flex-1 min-w-0">
          <input
            v-if="showCheckbox"
            type="checkbox"
            :checked="isSelected || selectedForApproval"
            @change="$emit('toggle-selection', penjualan.id, selectedForApproval)"
            class="flex-shrink-0 w-5 h-5 rounded border-2 border-white cursor-pointer mt-0.5"
            :class="selectedForApproval ? 'accent-green-500' : 'accent-blue-500'"
          />

          <div class="flex-1 min-w-0">
            <button
              @click="handleTransactionNumberClick"
              class="text-white font-bold text-sm truncate hover:text-[#c1a366] transition-colors text-left w-full"
            >
              {{ penjualan.nomor_transaksi }}
            </button>
            <div class="text-[#c1a366] text-xs">
              {{ formatDateTime(penjualan.tanggal_transaksi) }}
            </div>
          </div>
        </div>

        <span
          class="text-xs px-2 py-0.5 rounded-full font-medium flex-shrink-0"
          :class="getStatusClass(penjualan)"
        >
          {{ getStatusText(penjualan) }}
        </span>
      </div>
    </div>

    <!-- Card Body -->
    <div class="p-3 space-y-2">
      <!-- Items -->
      <div class="text-xs text-gray-700">
        <div class="flex items-start gap-1.5">
          <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
          </svg>
          <span class="flex-1 line-clamp-2 leading-tight">
            {{ getItemsText(penjualan.items) }}
          </span>
        </div>
      </div>

      <!-- Kasir & Payment -->
      <div class="flex items-center justify-between text-xs gap-2">
        <div class="flex items-center gap-1.5 flex-1 min-w-0">
          <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
          </svg>
          <span class="text-gray-600 truncate">{{ penjualan.user?.name || '-' }}</span>
        </div>
        <div class="flex items-center gap-1.5 flex-shrink-0">
          <span
            class="text-xs px-2 py-0.5 rounded-full font-medium"
            :class="getPaymentMethodClass(penjualan.metode_pembayaran)"
          >
            {{ penjualan.metode_pembayaran }}
          </span>
          <button
            v-if="penjualan.foto_bukti"
            @click="showPhotoModal = true"
            class="text-xs px-2 py-0.5 rounded-full font-medium bg-green-50 text-green-600 hover:bg-green-100 transition-colors flex items-center gap-1"
          >
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            Bukti
          </button>
        </div>
      </div>

      <!-- Customer -->
      <div v-if="penjualan.nama_pelanggan || penjualan.buyer" class="flex items-center gap-1.5 text-xs">
        <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
        </svg>
        <span :class="penjualan.buyer ? 'text-indigo-600 font-medium' : 'text-gray-600'" class="truncate">
          {{ penjualan.buyer ? penjualan.buyer.name : penjualan.nama_pelanggan }}
        </span>
        <svg v-if="penjualan.buyer" class="w-4 h-4 text-indigo-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
        </svg>
      </div>

      <!-- Diskon / Voucher -->
      <div v-if="penjualan.diskon > 0" class="flex items-center justify-between text-xs pt-1 border-t border-dashed border-gray-200">
        <div class="flex items-center gap-1.5">
          <svg class="w-3.5 h-3.5 text-orange-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
          </svg>
          <span v-if="penjualan.voucher" class="text-orange-600 font-medium">
            {{ penjualan.voucher.potongan?.nama || 'Voucher' }}
            <span class="font-mono ml-1 text-gray-400">({{ penjualan.voucher.kode_voucher }})</span>
          </span>
          <span v-else class="text-orange-600">Diskon manual</span>
        </div>
        <span class="text-orange-600 font-semibold">- {{ formatCurrency(penjualan.diskon) }}</span>
      </div>

      <!-- Biaya layanan -->
      <div v-if="penjualan.biaya_layanan > 0" class="flex items-center justify-between text-xs pt-1 border-t border-dashed border-gray-200">
        <span class="text-gray-500">Biaya layanan</span>
        <span class="text-gray-600 font-medium">+ {{ formatCurrency(penjualan.biaya_layanan) }}</span>
      </div>

      <!-- Total -->
      <div class="flex justify-between items-center text-sm font-bold pt-2 border-t border-gray-200">
        <span class="text-gray-900">Total</span>
        <span class="text-green-600">{{ formatCurrency(penjualan.total) }}</span>
      </div>

      <!-- Verification Info -->
      <div v-if="penjualan.is_verified || penjualan.is_approved || penjualan.is_recorded" class="text-xs bg-gray-50 rounded p-2 space-y-1">
        <div v-if="penjualan.is_verified" class="flex items-center gap-1.5 text-green-700">
          <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
          </svg>
          <span>Diverifikasi oleh {{ penjualan.verified_by?.name || '-' }}</span>
        </div>
        <div v-if="penjualan.is_approved" class="flex items-center gap-1.5 text-blue-700">
          <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
          </svg>
          <span>Approved by {{ penjualan.approved_by?.name || '-' }}</span>
        </div>
        <div v-if="penjualan.is_recorded" class="flex items-center gap-1.5 text-purple-700">
          <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
            <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
            <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm9.707 5.707a1 1 0 00-1.414-1.414L9 12.586l-1.293-1.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
          </svg>
          <span>Recorded by {{ penjualan.recorded_by?.name || '-' }}</span>
        </div>
      </div>

      <!-- Actions -->
      <slot name="actions" :penjualan="penjualan"></slot>
    </div>

    <!-- Photo Modal -->
    <div
      v-if="showPhotoModal"
      class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 p-4"
      @click.self="showPhotoModal = false"
    >
      <div class="relative max-w-md md:max-w-lg w-full">
        <button
          @click="showPhotoModal = false"
          class="absolute -top-10 right-0 md:top-2 md:right-2 p-2 bg-white rounded-full hover:bg-gray-100 transition-colors z-10"
        >
          <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
        <img
          :src="penjualan.foto_bukti"
          :alt="`Bukti Transaksi ${penjualan.nomor_transaksi}`"
          class="w-full h-auto rounded-lg"
          @error="$event.target.src = '/images/no-image.png'"
        />
        <div class="bg-white rounded-b-lg p-3 text-center">
          <p class="text-sm text-gray-700 font-medium">{{ penjualan.nomor_transaksi }}</p>
          <p class="text-xs text-gray-500">Bukti Transaksi</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useFormatters } from '@/composables/useFormatters'

const props = defineProps({
  penjualan: { type: Object, required: true },
  isSelected: { type: Boolean, default: false },
  selectedForApproval: { type: Boolean, default: false },
  permissions: { type: Object, required: true }
})

const emit = defineEmits(['toggle-selection', 'open-detail'])

const showPhotoModal = ref(false)

const handleTransactionNumberClick = () => {
  // Jika checkbox tampil, toggle selection
  if (showCheckbox.value) {
    emit('toggle-selection', props.penjualan.id, props.selectedForApproval)
  } else {
    // Jika tidak ada checkbox, buka detail modal
    emit('open-detail', props.penjualan)
  }
}

const { formatCurrency, formatDateTime, getPaymentMethodClass, getStatusClass, getStatusText, getItemsText } = useFormatters()

const showCheckbox = computed(() => {
  const { penjualan, permissions } = props
  // Show checkbox untuk verifikasi
  if (!penjualan.is_verified && permissions.canVerify && permissions.canVerifyTransaction(penjualan)) return true
  // Show checkbox untuk approval
  if (penjualan.is_verified && !penjualan.is_approved && permissions.canApprove) return true
  return false
})

const cardBorderClass = computed(() => {
  if (props.isSelected || props.selectedForApproval) {
    return 'border-blue-500 ring-2 ring-blue-200'
  }
  if (props.penjualan.is_verified) {
    return 'border-green-200 ring-1 ring-green-100'
  }
  return 'border-gray-200'
})

const headerClass = computed(() => {
  if (props.penjualan.is_recorded) {
    return 'bg-gradient-to-r from-purple-700 to-purple-600'
  }
  if (props.penjualan.is_approved) {
    return 'bg-gradient-to-r from-blue-700 to-blue-600'
  }
  if (props.penjualan.is_verified) {
    return 'bg-gradient-to-r from-green-700 to-green-600'
  }
  return 'bg-gradient-to-r from-[#6b4700] to-[#5b3d00]'
})
</script>
