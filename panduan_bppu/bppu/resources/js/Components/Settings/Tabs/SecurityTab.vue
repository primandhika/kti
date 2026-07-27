<template>
  <div class="space-y-6">
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
      <div class="flex items-start space-x-3">
        <svg class="w-5 h-5 text-yellow-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
        <div>
          <h4 class="text-sm font-semibold text-yellow-800">Perhatian</h4>
          <p class="text-sm text-yellow-700 mt-1">Gunakan password yang kuat dan unik untuk keamanan akun Anda</p>
        </div>
      </div>
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700 mb-2">Password Saat Ini</label>
      <input
        type="password"
        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-900 focus:border-transparent"
      />
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700 mb-2">Password Baru</label>
      <input
        type="password"
        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-900 focus:border-transparent"
      />
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700 mb-2">Konfirmasi Password Baru</label>
      <input
        type="password"
        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-900 focus:border-transparent"
      />
    </div>

    <div class="flex items-center justify-between py-3 border-t border-gray-200">
      <div>
        <p class="text-sm font-medium text-gray-900">Autentikasi Dua Faktor</p>
        <p class="text-xs text-gray-500">Tambahkan lapisan keamanan ekstra</p>
      </div>
      <button class="relative inline-flex h-6 w-11 items-center rounded-full bg-gray-200 transition-colors focus:outline-none focus:ring-2 focus:ring-primary-900 focus:ring-offset-2">
        <span class="translate-x-1 inline-block h-4 w-4 transform rounded-full bg-white transition-transform"></span>
      </button>
    </div>

    <div class="pt-4">
      <button class="px-6 py-2.5 bg-primary-900 text-white rounded-lg hover:bg-primary-950 transition-colors font-semibold">
        Ubah Password
      </button>
    </div>
  </div>
</template>
