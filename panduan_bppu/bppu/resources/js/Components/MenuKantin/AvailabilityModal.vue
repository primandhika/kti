<template>
  <div
    v-if="show"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
    @click.self="$emit('close')"
  >
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4 overflow-hidden">
      <div class="bg-gradient-to-r from-[#996600] to-[#7a5100] p-6">
        <h3 class="text-xl font-bold text-white">Pilih Jenis Menu</h3>
        <p class="text-sm text-[#f4efe5] mt-1">Tentukan menu yang akan dicetak</p>
      </div>

      <div class="p-6 space-y-4">
        <button
          @click="$emit('confirm', false)"
          class="w-full group hover:scale-[1.02] transition-all"
        >
          <div class="flex items-start p-5 border-2 border-gray-200 rounded-xl hover:border-[#996600] hover:bg-[#996600]/5 transition-all">
            <div class="flex-shrink-0 mr-4">
              <div class="w-12 h-12 bg-[#996600]/10 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-[#996600]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
              </div>
            </div>
            <div class="flex-1 text-left">
              <h4 class="font-bold text-gray-900 text-base mb-1">Cetak Semua Menu</h4>
              <p class="text-sm text-gray-600">Termasuk menu yang stok habis (dengan strikethrough)</p>
            </div>
          </div>
        </button>

        <button
          @click="$emit('confirm', true)"
          class="w-full group hover:scale-[1.02] transition-all"
        >
          <div class="flex items-start p-5 border-2 border-gray-200 rounded-xl hover:border-green-600 hover:bg-green-50 transition-all">
            <div class="flex-shrink-0 mr-4">
              <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
            </div>
            <div class="flex-1 text-left">
              <h4 class="font-bold text-gray-900 text-base mb-1">Menu Tersedia Saja</h4>
              <p class="text-sm text-gray-600">Hanya menu yang masih ada stoknya</p>
            </div>
          </div>
        </button>
      </div>

      <div class="bg-gray-50 px-6 py-4 border-t">
        <button
          @click="$emit('close')"
          class="w-full px-4 py-2.5 text-sm font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors"
        >
          Batal
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  show: { type: Boolean, required: true },
});

defineEmits(['close', 'confirm']);
</script>
