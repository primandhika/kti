<template>
  <div class="space-y-6">
    <div class="flex items-center space-x-6">
      <div class="w-24 h-24 bg-primary-900 rounded-full flex items-center justify-center text-white text-3xl font-bold">
        A
      </div>
      <div>
        <button class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-medium text-sm">
          Ubah Foto
        </button>
        <p class="text-xs text-gray-500 mt-2">JPG, PNG maksimal 2MB</p>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Depan</label>
        <input
          type="text"
          value="Admin"
          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-900 focus:border-transparent"
        />
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Belakang</label>
        <input
          type="text"
          value="User"
          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-900 focus:border-transparent"
        />
      </div>
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
      <input
        type="email"
        value="admin@bppu.ac.id"
        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-900 focus:border-transparent"
      />
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Telepon</label>
      <input
        type="tel"
        value="+62 812 3456 7890"
        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-900 focus:border-transparent"
      />
    </div>

    <div class="pt-4">
      <button class="px-6 py-2.5 bg-primary-900 text-white rounded-lg hover:bg-primary-950 transition-colors font-semibold">
        Update Profil
      </button>
    </div>
  </div>
</template>
