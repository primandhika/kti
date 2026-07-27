<template>
  <Transition
    enter-active-class="transition-opacity duration-200 ease-out"
    enter-from-class="opacity-0"
    enter-to-class="opacity-100"
    leave-active-class="transition-opacity duration-150 ease-in"
    leave-from-class="opacity-100"
    leave-to-class="opacity-0"
  >
    <div
      v-if="show"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/60"
      @click.self="$emit('cancel')"
    >
      <Transition
        enter-active-class="transition-all duration-200 ease-out"
        enter-from-class="opacity-0 scale-95"
        enter-to-class="opacity-100 scale-100"
        leave-active-class="transition-all duration-150 ease-in"
        leave-from-class="opacity-100 scale-100"
        leave-to-class="opacity-0 scale-95"
      >
        <div v-if="show" class="bg-white rounded-xl shadow-2xl w-full max-w-sm mx-4 overflow-hidden">
          <!-- Header -->
          <div class="bg-gradient-to-r from-[#996600] to-[#7a5100] px-5 py-4">
            <h3 class="text-white font-bold text-base">Pilih Mode Scanner</h3>
            <p class="text-[#f4efe5] text-xs mt-0.5">Barcode mode aktif - pilih jenis scanner yang digunakan</p>
          </div>

          <!-- Options -->
          <div class="p-4 space-y-3">
            <!-- Opsi: USB/Hardware Scanner -->
            <button
              @click="$emit('select', 'usb')"
              class="w-full flex items-center gap-4 p-4 border-2 rounded-lg text-left transition-all hover:border-[#996600] hover:bg-[#f4efe5] group border-gray-200"
            >
              <div class="flex-shrink-0 w-12 h-12 rounded-lg bg-gray-100 group-hover:bg-[#eae0cc] flex items-center justify-center transition-colors">
                <svg class="w-6 h-6 text-gray-500 group-hover:text-[#996600] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3H7a2 2 0 00-2 2v14a2 2 0 002 2h10a2 2 0 002-2V5a2 2 0 00-2-2h-2M9 3a2 2 0 012-2h2a2 2 0 012 2M9 3h6" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6M9 16h4" />
                </svg>
              </div>
              <div class="flex-1 min-w-0">
                <div class="font-semibold text-gray-800 group-hover:text-[#6b4700] text-sm">USB / Hardware Scanner</div>
                <div class="text-xs text-gray-500 mt-0.5 leading-relaxed">Untuk scanner barcode fisik yang terhubung via USB. Cukup scan, barang langsung ditambahkan ke keranjang.</div>
              </div>
              <svg class="w-4 h-4 text-gray-400 group-hover:text-[#996600] flex-shrink-0 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
            </button>

            <!-- Opsi: Kamera Perangkat -->
            <button
              @click="$emit('select', 'camera')"
              class="w-full flex items-center gap-4 p-4 border-2 rounded-lg text-left transition-all hover:border-[#996600] hover:bg-[#f4efe5] group border-gray-200"
            >
              <div class="flex-shrink-0 w-12 h-12 rounded-lg bg-gray-100 group-hover:bg-[#eae0cc] flex items-center justify-center transition-colors">
                <svg class="w-6 h-6 text-gray-500 group-hover:text-[#996600] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
              </div>
              <div class="flex-1 min-w-0">
                <div class="font-semibold text-gray-800 group-hover:text-[#6b4700] text-sm">Kamera Perangkat</div>
                <div class="text-xs text-gray-500 mt-0.5 leading-relaxed">Gunakan kamera HP atau laptop untuk memindai barcode. Cocok untuk perangkat tanpa scanner fisik.</div>
              </div>
              <svg class="w-4 h-4 text-gray-400 group-hover:text-[#996600] flex-shrink-0 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
            </button>
          </div>

          <!-- Cancel -->
          <div class="px-4 pb-4">
            <button
              @click="$emit('cancel')"
              class="w-full py-2 text-sm text-gray-500 hover:text-gray-700 transition-colors"
            >
              Batal
            </button>
          </div>
        </div>
      </Transition>
    </div>
  </Transition>
</template>

<script setup>
defineProps({
  show: {
    type: Boolean,
    default: false
  }
})

defineEmits(['select', 'cancel'])
</script>
