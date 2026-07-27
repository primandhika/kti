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
          <div v-if="show" class="relative bg-white rounded-2xl shadow-xl w-full max-w-md max-h-[90vh] overflow-y-auto">
            <!-- Header -->
            <div class="flex items-center justify-between p-4 border-b border-gray-100 sticky top-0 bg-white z-10">
              <h2 class="text-sm font-semibold text-gray-800">Edit Profil</h2>
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
              <!-- Nama -->
              <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Nama <span class="text-red-500">*</span></label>
                <input
                  v-model="form.nama"
                  type="text"
                  required
                  class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#996600]/30 focus:border-[#996600]"
                  placeholder="Nama lengkap"
                />
                <p v-if="errors.nama" class="text-xs text-red-500 mt-1">{{ errors.nama }}</p>
              </div>

              <!-- Perusahaan -->
              <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Perusahaan / Nama Usaha</label>
                <input
                  v-model="form.perusahaan"
                  type="text"
                  class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#996600]/30 focus:border-[#996600]"
                  placeholder="Nama perusahaan (opsional)"
                />
              </div>

              <!-- Email -->
              <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Email</label>
                <input
                  v-model="form.email"
                  type="email"
                  class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#996600]/30 focus:border-[#996600]"
                  placeholder="email@contoh.com"
                />
                <p v-if="errors.email" class="text-xs text-red-500 mt-1">{{ errors.email }}</p>
              </div>

              <!-- Telepon -->
              <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Telepon</label>
                <input
                  v-model="form.telepon"
                  type="tel"
                  class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#996600]/30 focus:border-[#996600]"
                  placeholder="08xxxxxxxxxx"
                />
              </div>

              <!-- Kota -->
              <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Kota</label>
                <input
                  v-model="form.kota"
                  type="text"
                  class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#996600]/30 focus:border-[#996600]"
                  placeholder="Kota"
                />
              </div>

              <!-- Alamat -->
              <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Alamat</label>
                <textarea
                  v-model="form.alamat"
                  rows="2"
                  class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#996600]/30 focus:border-[#996600] resize-none"
                  placeholder="Alamat lengkap"
                />
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
                  :disabled="loading"
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
import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
  show: {
    type: Boolean,
    default: false,
  },
  supplier: {
    type: Object,
    required: true,
  },
});

const emit = defineEmits(['close', 'success']);

const loading = ref(false);
const errors  = ref({});

const form = ref({
  nama:       '',
  perusahaan: '',
  email:      '',
  telepon:    '',
  kota:       '',
  alamat:     '',
});

// Isi form dari data supplier ketika modal dibuka
watch(() => props.show, (val) => {
  if (val) {
    form.value = {
      nama:       props.supplier.nama       || '',
      perusahaan: props.supplier.perusahaan || '',
      email:      props.supplier.email      || '',
      telepon:    props.supplier.telepon    || '',
      kota:       props.supplier.kota       || '',
      alamat:     props.supplier.alamat     || '',
    };
    errors.value = {};
  }
});

const handleSubmit = () => {
  loading.value = true;
  errors.value  = {};

  router.post('/mitra/profil/update', form.value, {
    onSuccess: () => {
      loading.value = false;
      emit('success', 'Profil berhasil diperbarui.');
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
