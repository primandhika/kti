<template>
  <Teleport to="body">
    <Transition
      enter-active-class="transition ease-out duration-200"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition ease-in duration-150"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div v-if="show" class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-4">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black/40" @click="$emit('close')" />

        <!-- Modal -->
        <Transition
          enter-active-class="transition ease-out duration-200"
          enter-from-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
          enter-to-class="opacity-100 translate-y-0 sm:scale-100"
          leave-active-class="transition ease-in duration-150"
          leave-from-class="opacity-100 translate-y-0 sm:scale-100"
          leave-to-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        >
          <div v-if="show" class="relative bg-white rounded-2xl shadow-xl w-full max-w-sm">
            <!-- Header -->
            <div class="flex items-center justify-between p-4 border-b border-gray-100">
              <h2 class="text-sm font-semibold text-gray-800">Ganti Password</h2>
              <button
                @click="$emit('close')"
                class="p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>

            <!-- Form -->
            <form @submit.prevent="handleSubmit" class="p-4 space-y-3">
              <!-- Password lama -->
              <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Password Saat Ini <span class="text-red-500">*</span></label>
                <div class="relative">
                  <input
                    v-model="form.password_lama"
                    :type="showLama ? 'text' : 'password'"
                    required
                    class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2 pr-9 focus:outline-none focus:ring-2 focus:ring-[#996600]/30 focus:border-[#996600]"
                    placeholder="Password saat ini"
                  />
                  <button
                    type="button"
                    @click="showLama = !showLama"
                    class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
                  >
                    <svg v-if="showLama" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                    </svg>
                    <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                  </button>
                </div>
                <p v-if="errors.password_lama" class="text-xs text-red-500 mt-1">{{ errors.password_lama }}</p>
              </div>

              <!-- Password baru -->
              <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Password Baru <span class="text-red-500">*</span></label>
                <div class="relative">
                  <input
                    v-model="form.password_baru"
                    :type="showBaru ? 'text' : 'password'"
                    required
                    minlength="8"
                    class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2 pr-9 focus:outline-none focus:ring-2 focus:ring-[#996600]/30 focus:border-[#996600]"
                    placeholder="Minimal 8 karakter"
                  />
                  <button
                    type="button"
                    @click="showBaru = !showBaru"
                    class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
                  >
                    <svg v-if="showBaru" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                    </svg>
                    <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                  </button>
                </div>
                <p v-if="errors.password_baru" class="text-xs text-red-500 mt-1">{{ errors.password_baru }}</p>
              </div>

              <!-- Konfirmasi password baru -->
              <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Konfirmasi Password Baru <span class="text-red-500">*</span></label>
                <div class="relative">
                  <input
                    v-model="form.konfirmasi_password"
                    :type="showKonfirmasi ? 'text' : 'password'"
                    required
                    class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2 pr-9 focus:outline-none focus:ring-2 focus:ring-[#996600]/30 focus:border-[#996600]"
                    :class="konfirmasiTidakCocok ? 'border-red-300' : ''"
                    placeholder="Ulangi password baru"
                  />
                  <button
                    type="button"
                    @click="showKonfirmasi = !showKonfirmasi"
                    class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
                  >
                    <svg v-if="showKonfirmasi" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                    </svg>
                    <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                  </button>
                </div>
                <p v-if="konfirmasiTidakCocok" class="text-xs text-red-500 mt-1">Password tidak cocok.</p>
                <p v-if="errors.konfirmasi_password" class="text-xs text-red-500 mt-1">{{ errors.konfirmasi_password }}</p>
              </div>

              <!-- Error umum -->
              <p v-if="errors.general" class="text-xs text-red-600 bg-red-50 px-3 py-2 rounded-lg">{{ errors.general }}</p>

              <!-- Actions -->
              <div class="flex items-center space-x-2 pt-1">
                <button
                  type="button"
                  @click="$emit('close')"
                  class="flex-1 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl transition-colors"
                >
                  Batal
                </button>
                <button
                  type="submit"
                  :disabled="loading || konfirmasiTidakCocok"
                  class="flex-1 py-2.5 text-sm font-medium text-white bg-[#996600] hover:bg-[#895b00] rounded-xl transition-colors disabled:opacity-60 disabled:cursor-not-allowed"
                >
                  <span v-if="loading">Menyimpan...</span>
                  <span v-else>Simpan</span>
                </button>
              </div>
            </form>
          </div>
        </Transition>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
  show: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits(['close', 'success']);

const loading        = ref(false);
const errors         = ref({});
const showLama       = ref(false);
const showBaru       = ref(false);
const showKonfirmasi = ref(false);

const form = ref({
  password_lama:       '',
  password_baru:       '',
  konfirmasi_password: '',
});

const konfirmasiTidakCocok = computed(() => {
  return form.value.konfirmasi_password.length > 0
    && form.value.password_baru !== form.value.konfirmasi_password;
});

// Reset form saat modal ditutup
watch(() => props.show, (val) => {
  if (!val) {
    form.value = { password_lama: '', password_baru: '', konfirmasi_password: '' };
    errors.value = {};
    showLama.value = false;
    showBaru.value = false;
    showKonfirmasi.value = false;
  }
});

const handleSubmit = () => {
  if (konfirmasiTidakCocok.value) return;
  loading.value = true;
  errors.value  = {};

  router.post('/mitra/profil/ganti-password', form.value, {
    onSuccess: () => {
      loading.value = false;
      emit('success', 'Password berhasil diubah.');
      emit('close');
    },
    onError: (errs) => {
      loading.value = false;
      errors.value  = errs;
    },
    preserveScroll: true,
  });
};
</script>
