<template>
  <Transition
    enter-active-class="transition ease-out duration-200"
    enter-from-class="opacity-0"
    enter-to-class="opacity-100"
    leave-active-class="transition ease-in duration-150"
    leave-from-class="opacity-100"
    leave-to-class="opacity-0"
  >
    <div v-if="show" class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm" @click.self="$emit('close')">
      <Transition
        enter-active-class="transition ease-out duration-200"
        enter-from-class="translate-y-8 opacity-0 sm:translate-y-0 sm:scale-95"
        enter-to-class="translate-y-0 opacity-100 sm:scale-100"
      >
        <div v-if="show" class="w-full max-w-sm bg-white rounded-2xl shadow-xl overflow-hidden">
          <!-- Header -->
          <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-base font-semibold text-gray-800">Ajukan Pencairan Dana</h3>
            <button @click="$emit('close')" class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-400 transition-colors">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <div class="p-5 space-y-4">
            <!-- Periode -->
            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Dari Tanggal</label>
                <input
                  v-model="form.tanggal_dari"
                  type="date"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#996600]/30 focus:border-[#996600]"
                />
              </div>
              <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Sampai Tanggal</label>
                <input
                  v-model="form.tanggal_sampai"
                  type="date"
                  :min="form.tanggal_dari"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#996600]/30 focus:border-[#996600]"
                />
              </div>
            </div>

            <!-- Metode -->
            <div>
              <label class="block text-xs font-medium text-gray-700 mb-2">Metode Pencairan</label>
              <div class="grid grid-cols-2 gap-2">
                <button
                  type="button"
                  @click="form.metode = 'tunai'"
                  :class="[
                    'flex items-center gap-2 px-3 py-2.5 rounded-xl border-2 text-sm font-medium transition-colors',
                    form.metode === 'tunai'
                      ? 'border-[#996600] bg-[#f4efe5] text-[#6b4700]'
                      : 'border-gray-200 bg-white text-gray-600 hover:border-gray-300'
                  ]"
                >
                  <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                  </svg>
                  Tunai
                </button>
                <button
                  type="button"
                  @click="form.metode = 'transfer_bank'"
                  :class="[
                    'flex items-center gap-2 px-3 py-2.5 rounded-xl border-2 text-sm font-medium transition-colors',
                    form.metode === 'transfer_bank'
                      ? 'border-[#996600] bg-[#f4efe5] text-[#6b4700]'
                      : 'border-gray-200 bg-white text-gray-600 hover:border-gray-300'
                  ]"
                >
                  <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                  </svg>
                  Transfer
                </button>
              </div>
            </div>

            <!-- Detail rekening (jika transfer) -->
            <div v-if="form.metode === 'transfer_bank'">
              <label class="block text-xs font-medium text-gray-700 mb-1">Detail Rekening</label>
              <input
                v-model="form.detail_rekening"
                type="text"
                placeholder="Cth: BCA 1234567890 a.n. Nama"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#996600]/30 focus:border-[#996600]"
              />
            </div>

            <!-- Catatan -->
            <div>
              <label class="block text-xs font-medium text-gray-700 mb-1">Catatan (opsional)</label>
              <textarea
                v-model="form.catatan"
                rows="2"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#996600]/30 focus:border-[#996600] resize-none"
                placeholder="Informasi tambahan untuk pengelola..."
              ></textarea>
            </div>

            <!-- Info -->
            <div class="bg-amber-50 border border-amber-100 rounded-lg p-3 text-xs text-amber-700">
              Pengajuan akan diterima pengelola BPPU dan diproses secepatnya.
            </div>
          </div>

          <!-- Footer -->
          <div class="px-5 pb-5 flex space-x-3">
            <button
              @click="$emit('close')"
              class="flex-1 py-2.5 px-4 border border-gray-300 text-gray-700 text-sm font-medium rounded-xl hover:bg-gray-50 transition-colors"
            >
              Batal
            </button>
            <button
              @click="submit"
              :disabled="!isValid || isSubmitting"
              class="flex-1 py-2.5 px-4 bg-[#996600] hover:bg-[#895b00] text-white text-sm font-medium rounded-xl transition-colors disabled:opacity-40 disabled:cursor-not-allowed"
            >
              {{ isSubmitting ? 'Mengirim...' : 'Kirim Pengajuan' }}
            </button>
          </div>
        </div>
      </Transition>
    </div>
  </Transition>
</template>

<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
  show: Boolean,
});

const emit = defineEmits(['close', 'success']);

const form = ref({
  tanggal_dari: '',
  tanggal_sampai: '',
  metode: 'tunai',
  detail_rekening: '',
  catatan: '',
});
const isSubmitting = ref(false);

const isValid = computed(() => {
  if (!form.value.tanggal_dari || !form.value.tanggal_sampai) return false;
  if (form.value.metode === 'transfer_bank' && !form.value.detail_rekening) return false;
  return true;
});

const submit = () => {
  if (!isValid.value) return;
  isSubmitting.value = true;
  router.post(route('mitra.ajukanPencairanBaru'), {
    tanggal_dari: form.value.tanggal_dari,
    tanggal_sampai: form.value.tanggal_sampai,
    metode: form.value.metode,
    detail_rekening: form.value.metode === 'transfer_bank' ? form.value.detail_rekening : null,
    catatan: form.value.catatan,
  }, {
    onSuccess: () => {
      isSubmitting.value = false;
      form.value = { tanggal_dari: '', tanggal_sampai: '', metode: 'tunai', detail_rekening: '', catatan: '' };
      emit('close');
      emit('success', 'Pengajuan pencairan berhasil dikirim.');
    },
    onError: () => { isSubmitting.value = false; },
  });
};
</script>
