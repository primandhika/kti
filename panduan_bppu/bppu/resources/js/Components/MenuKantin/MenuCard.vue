<template>
  <div class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow">
    <!-- Menu Image -->
    <div class="relative h-32 bg-gray-200">
      <img
        v-if="menu.gambar"
        :src="menu.gambar"
        :alt="menu.nama_barang"
        class="w-full h-full object-cover"
      />
      <div v-else class="w-full h-full flex items-center justify-center">
        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
      </div>

      <!-- Edit Menu Button (Pencil) -->
      <div class="absolute bottom-1.5 right-1.5">
        <button
          @click.stop="$emit('toggleDropdown', menu.id)"
          class="bg-white/90 hover:bg-white p-1.5 rounded-md shadow-md transition-colors relative z-10"
          title="Kelola Menu"
        >
          <svg class="w-4 h-4 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
          </svg>
        </button>

        <!-- Dropdown Menu -->
        <div
          v-if="activeDropdown === menu.id"
          @click.stop
          class="absolute bottom-full mb-1 right-0 w-40 bg-white rounded-lg shadow-lg border border-gray-200 z-50"
        >
          <button
            @click="$emit('editMenu', menu)"
            class="w-full flex items-center space-x-2 px-3 py-2 text-xs hover:bg-gray-50 transition-colors border-b border-gray-100"
          >
            <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
            <span class="text-gray-700">Edit Item</span>
          </button>
          <button
            @click="$emit('editDisplay', menu)"
            class="w-full flex items-center space-x-2 px-3 py-2 text-xs hover:bg-gray-50 transition-colors border-b border-gray-100"
          >
            <svg class="w-3.5 h-3.5 text-[#996600]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
            </svg>
            <span class="text-gray-700">Edit Display</span>
          </button>
          <button
            @click="$emit('uploadImage', menu)"
            class="w-full flex items-center space-x-2 px-3 py-2 text-xs hover:bg-gray-50 transition-colors"
          >
            <svg class="w-3.5 h-3.5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <span class="text-gray-700">Upload Gambar</span>
          </button>
        </div>
      </div>

      <!-- Availability Badge -->
      <div class="absolute top-1.5 right-1.5">
        <button
          @click="$emit('toggleAvailability', menu)"
          :class="[
            'px-1.5 py-0.5 text-xs font-semibold rounded-full transition-colors',
            menu.is_available
              ? 'bg-green-100 text-green-800 hover:bg-green-200'
              : 'bg-red-100 text-red-800 hover:bg-red-200'
          ]"
        >
          {{ menu.is_available ? 'Tersedia' : 'Habis' }}
        </button>
      </div>

      <!-- Stock Badge -->
      <div class="absolute top-1.5 left-1.5">
        <span
          :class="[
            'px-1.5 py-0.5 text-xs font-semibold rounded-full',
            menu.stok > 0 ? 'bg-blue-100 text-blue-800' : 'bg-orange-100 text-orange-800'
          ]"
        >
          Stok: {{ menu.stok }}
        </span>
      </div>
    </div>

    <!-- Menu Info -->
    <div class="p-3">
      <div class="mb-1.5">
        <h3 class="text-sm font-semibold text-gray-900 line-clamp-1">{{ menu.nama_barang }}</h3>
        <p class="text-xs text-gray-500 font-mono">{{ menu.kode_barang }}</p>
      </div>

      <p class="text-xs text-gray-600 mb-2 line-clamp-2 min-h-[2rem]">
        {{ menu.deskripsi_display || 'Belum ada deskripsi display' }}
      </p>

      <!-- Category & Unit -->
      <div class="flex items-center space-x-1 mb-2">
        <span v-if="menu.sub_kategori" class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-[#f4efe5] text-[#7a5100]">
          {{ menu.sub_kategori }}
        </span>
        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
          {{ menu.work_unit }}
        </span>
      </div>

      <!-- Price -->
      <div>
        <span class="text-lg font-bold text-[#996600]">
          Rp {{ formatPrice(menu.harga_jual) }}
        </span>
        <span class="text-xs text-gray-500">/ {{ menu.satuan }}</span>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  menu: { type: Object, required: true },
  activeDropdown: { type: [Number, null], default: null },
});

defineEmits(['toggleDropdown', 'editMenu', 'editDisplay', 'uploadImage', 'toggleAvailability']);

const formatPrice = (price) => {
  return new Intl.NumberFormat('id-ID').format(price);
};
</script>
