<template>
  <div class="min-h-screen bg-gray-50 flex flex-col justify-center px-4 py-12">
    <div class="max-w-sm mx-auto w-full">
      <!-- Logo -->
      <div class="text-center mb-8">
        <img src="/logo.png" alt="BPPU IKIP Siliwangi" class="h-16 mx-auto mb-4" />
        <h1 class="text-xl font-bold text-gray-800">Portal Mitra</h1>
        <p class="text-sm text-gray-500 mt-1">BPPU IKIP Siliwangi</p>
      </div>

      <!-- Card -->
      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-base font-semibold text-gray-700 mb-5">Masuk ke akun mitra Anda</h2>

        <form @submit.prevent="submit" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Username / Email</label>
            <input
              v-model="form.login"
              type="text"
              autocomplete="username"
              class="w-full px-3 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#996600]/30 focus:border-[#996600] transition-colors"
              :class="errors.login ? 'border-red-400 bg-red-50' : 'border-gray-300'"
              placeholder="username atau email"
            />
            <p v-if="errors.login" class="mt-1 text-xs text-red-600">{{ errors.login }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
            <div class="relative">
              <input
                v-model="form.password"
                :type="showPassword ? 'text' : 'password'"
                autocomplete="current-password"
                class="w-full px-3 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#996600]/30 focus:border-[#996600] transition-colors pr-10"
                :class="errors.password ? 'border-red-400 bg-red-50' : 'border-gray-300'"
                placeholder="password"
              />
              <button
                type="button"
                @click="showPassword = !showPassword"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
              >
                <svg v-if="showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                </svg>
                <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
              </button>
            </div>
            <p v-if="errors.password" class="mt-1 text-xs text-red-600">{{ errors.password }}</p>
          </div>

          <button
            type="submit"
            :disabled="form.processing"
            class="w-full bg-[#996600] hover:bg-[#895b00] text-white font-medium py-2.5 px-4 rounded-lg text-sm transition-colors disabled:opacity-60 disabled:cursor-not-allowed"
          >
            <span v-if="form.processing">Memproses...</span>
            <span v-else>Masuk</span>
          </button>
        </form>
      </div>

      <p class="text-center text-xs text-gray-400 mt-6">
        Hanya untuk akun mitra yang terdaftar
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';

defineProps({
  errors: {
    type: Object,
    default: () => ({}),
  },
});

const showPassword = ref(false);

const form = useForm({
  login: '',
  password: '',
  remember: false,
});

const submit = () => {
  form.post('/mitra/login', {
    onFinish: () => form.reset('password'),
  });
};
</script>
