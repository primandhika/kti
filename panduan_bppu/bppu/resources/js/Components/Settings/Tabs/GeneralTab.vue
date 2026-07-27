<template>
  <div class="space-y-6">
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-2">Nama Aplikasi</label>
      <input
        type="text"
        value="BPPU | IKIP Siliwangi"
        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-900 focus:border-transparent"
      />
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700 mb-2">Tagline</label>
      <input
        type="text"
        value="Sistem Pengelolaan BPPU"
        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-900 focus:border-transparent"
      />
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700 mb-2">Email Kontak</label>
      <input
        type="email"
        value="bppu@ikipsiliwangi.ac.id"
        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-900 focus:border-transparent"
      />
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700 mb-2">Zona Waktu</label>
      <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        <option>WIB (UTC+7)</option>
        <option>WITA (UTC+8)</option>
        <option>WIT (UTC+9)</option>
      </select>
    </div>

    <div class="flex items-center justify-between py-3 border-t border-gray-200">
      <div>
        <p class="text-sm font-medium text-gray-900">Mode Maintenance</p>
        <p class="text-xs text-gray-500">Aktifkan untuk menonaktifkan akses sementara</p>
      </div>
      <button class="relative inline-flex h-6 w-11 items-center rounded-full bg-gray-200 transition-colors focus:outline-none focus:ring-2 focus:ring-primary-900 focus:ring-offset-2">
        <span class="translate-x-1 inline-block h-4 w-4 transform rounded-full bg-white transition-transform"></span>
      </button>
    </div>

    <div class="pt-4">
      <button class="px-6 py-2.5 bg-primary-900 text-white rounded-lg hover:bg-primary-950 transition-colors font-semibold">
        Simpan Perubahan
      </button>
    </div>
  </div>
</template>
