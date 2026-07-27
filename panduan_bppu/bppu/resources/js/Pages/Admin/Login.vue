<template>
  <div class="min-h-screen bg-gradient-to-br from-[#6b4700] via-[#996600] to-[#7a5100] flex items-center justify-center p-4">
    <div class="max-w-md w-full space-y-8">
      <!-- Back to Home Button -->
      <div class="flex justify-start">
        <a
          href="/"
          class="inline-flex items-center space-x-2 px-4 py-2 bg-white/10 backdrop-blur-lg rounded-xl text-white hover:bg-white/20 transition-all duration-200 border border-white/20"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
          </svg>
          <span class="font-medium">Kembali ke Beranda</span>
        </a>
      </div>

      <div class="text-center">
        <div class="mb-8">
          <div class="inline-flex items-center justify-center mb-4">
            <img src="/logo-white.png" alt="BPPU Logo" class="h-20 w-auto" />
          </div>
          <h2 class="text-3xl font-bold text-white">
            Login Admin
          </h2>
          <p class="mt-2 text-[#d6c199]">
            BPPU | IKIP Siliwangi
          </p>
        </div>
      </div>

      <div class="bg-white/10 backdrop-blur-lg rounded-2xl shadow-2xl p-8 border border-white/20">
        <form @submit.prevent="submit" class="space-y-6">
          <div>
            <label for="login" class="block text-sm font-medium text-white mb-2">
              Username atau Email
            </label>
            <input
              id="login"
              v-model="form.login"
              type="text"
              required
              autocomplete="username"
              class="appearance-none block w-full px-4 py-3 border border-white/20 rounded-xl bg-white/5 text-white placeholder-[#d6c199]/50 focus:outline-none focus:ring-2 focus:ring-white/50 focus:border-transparent transition duration-150"
              placeholder="username atau email@ikipsiliwangi.ac.id"
            />
            <div v-if="errors.login" class="mt-2 text-sm text-red-300">
              {{ errors.login }}
            </div>
          </div>

          <div>
            <label for="password" class="block text-sm font-medium text-white mb-2">
              Password
            </label>
            <input
              id="password"
              v-model="form.password"
              type="password"
              required
              autocomplete="current-password"
              class="appearance-none block w-full px-4 py-3 border border-white/20 rounded-xl bg-white/5 text-white placeholder-[#d6c199]/50 focus:outline-none focus:ring-2 focus:ring-white/50 focus:border-transparent transition duration-150"
              placeholder="••••••••"
            />
            <div v-if="errors.password" class="mt-2 text-sm text-red-300">
              {{ errors.password }}
            </div>
          </div>

          <div class="flex items-center justify-between">
            <div class="flex items-center">
              <input
                id="remember"
                v-model="form.remember"
                type="checkbox"
                class="h-4 w-4 text-[#996600] focus:ring-[#996600] border-white/20 rounded bg-white/5"
              />
              <label for="remember" class="ml-2 block text-sm text-white">
                Ingat saya
              </label>
            </div>
          </div>

          <div>
            <button
              type="submit"
              :disabled="processing"
              class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-semibold rounded-xl text-white bg-[#996600] hover:bg-[#ad8432] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white/50 transition duration-150 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <span v-if="processing" class="absolute left-0 inset-y-0 flex items-center pl-3">
                <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
              </span>
              {{ processing ? 'Memproses...' : 'Masuk' }}
            </button>
          </div>
        </form>
      </div>

      <p class="text-center text-sm text-[#d6c199]">
        &copy; 2025 BPPU IKIP Siliwangi. All rights reserved.
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
    default: () => ({})
  }
});

const form = useForm({
  login: '',
  password: '',
  remember: false
});

const processing = ref(false);

const submit = () => {
  processing.value = true;
  form.post('/pengelola/login', {
    onFinish: () => {
      processing.value = false;
    }
  });
};
</script>
