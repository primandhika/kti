<template>
  <Transition
    enter-active-class="transition-all duration-300 ease-out"
    enter-from-class="opacity-0 -translate-y-4"
    enter-to-class="opacity-100 translate-y-0"
    leave-active-class="transition-all duration-200 ease-in"
    leave-from-class="opacity-100 translate-y-0"
    leave-to-class="opacity-0 -translate-y-4"
  >
    <div
      v-if="show"
      class="bg-gradient-to-br from-[#f4efe5] to-[#eae0cc] rounded-lg shadow-lg p-3 border-2 border-[#996600] mb-3 relative overflow-hidden"
    >
      <div class="absolute top-0 left-0 w-16 h-16 bg-[#996600] opacity-10 rounded-br-full"></div>
      <div class="absolute bottom-0 right-0 w-16 h-16 bg-[#996600] opacity-10 rounded-tl-full"></div>

      <div class="relative z-10">
        <div class="flex items-center justify-between mb-2">
          <div class="flex items-center gap-1.5">
            <div class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></div>
            <span class="text-xs font-semibold text-[#6b4700] uppercase tracking-wide">Barcode Mode</span>
          </div>
          <div class="flex items-center gap-1 text-[#895b00]">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <span class="text-xs font-medium">{{ itemCount }}</span>
          </div>
        </div>

        <div class="flex items-baseline justify-between gap-2">
          <div class="flex-1">
            <div class="text-xs text-[#7a5100] font-medium mb-0.5">Total</div>
            <div class="text-2xl font-bold text-[#996600] leading-tight break-all">
              {{ formatRupiah(total) }}
            </div>
          </div>
          <div v-if="diskon > 0" class="text-right">
            <div class="text-xs text-orange-700 font-medium">Diskon</div>
            <div class="text-sm font-semibold text-orange-600">-{{ formatRupiah(diskon) }}</div>
          </div>
        </div>

        <div class="mt-2 pt-2 border-t border-[#c1a366]">
          <!-- Mode camera: tampilkan tombol buka kamera -->
          <button
            v-if="scannerType === 'camera'"
            @click="$emit('open-camera-scanner')"
            class="w-full flex items-center justify-center gap-1.5 text-xs text-[#7a5100] font-medium hover:text-[#996600] transition-colors"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <span>Tap untuk scan barcode dengan kamera</span>
          </button>
          <!-- Mode USB: informasi status -->
          <div
            v-else
            class="flex items-center justify-center gap-1.5 text-xs text-[#7a5100] font-medium"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3H7a2 2 0 00-2 2v14a2 2 0 002 2h10a2 2 0 002-2V5a2 2 0 00-2-2h-2M9 3a2 2 0 012-2h2a2 2 0 012 2M9 3h6" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6M9 16h4" />
            </svg>
            <span>Siap terima scan USB scanner</span>
          </div>
        </div>
      </div>
    </div>
  </Transition>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  total: {
    type: Number,
    default: 0
  },
  subtotal: {
    type: Number,
    default: 0
  },
  diskon: {
    type: Number,
    default: 0
  },
  itemCount: {
    type: Number,
    default: 0
  },
  scannerType: {
    type: String,
    default: 'usb'
  }
})

defineEmits(['open-camera-scanner'])

const formatRupiah = (value) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
  }).format(value)
}
</script>

<style scoped>
</style>
