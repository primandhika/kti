<template>
  <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <!-- Lokasi Header -->
    <div class="flex items-center justify-between px-5 py-4 bg-[#f4efe5] border-b border-[#e0d1b2]">
      <div class="flex items-center gap-3">
        <!-- Checkbox select-all lokasi (hanya muncul saat mode pilih) -->
        <input
          v-if="selectMode"
          type="checkbox"
          :checked="lokasiAllSelected"
          :indeterminate="lokasiPartialSelected"
          @change="toggleSelectLokasi"
          class="w-4 h-4 rounded text-[#996600] border-gray-300 focus:ring-[#996600] cursor-pointer flex-shrink-0"
        />
        <div class="w-8 h-8 rounded-lg bg-[#996600] flex items-center justify-center text-white font-bold text-xs">
          {{ lokasi.kode || lokasi.nama.charAt(0) }}
        </div>
        <div>
          <h3 class="font-semibold text-gray-900 text-sm">{{ lokasi.nama }}</h3>
          <p class="text-xs text-gray-500">{{ lokasi.mejas_count ?? lokasi.mejas?.length ?? 0 }} meja</p>
        </div>
      </div>
      <div class="flex items-center gap-2">
        <span v-if="lokasi.is_public" class="text-xs px-2 py-0.5 rounded-full font-medium bg-blue-100 text-blue-700">
          Publik
        </span>
        <span :class="lokasi.is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'" class="text-xs px-2 py-0.5 rounded-full font-medium">
          {{ lokasi.is_active ? 'Aktif' : 'Nonaktif' }}
        </span>
        <button @click="$emit('edit-lokasi', lokasi)" class="p-1.5 rounded-lg hover:bg-[#d6c199]/50 text-[#7a5100] transition-colors" title="Edit Lokasi">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
          </svg>
        </button>
        <button @click="$emit('delete-lokasi', lokasi)" class="p-1.5 rounded-lg hover:bg-red-50 text-red-500 transition-colors" title="Hapus Lokasi">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
          </svg>
        </button>
        <button @click="$emit('add-meja', lokasi)" class="flex items-center gap-1 px-3 py-1.5 text-xs bg-[#996600] text-white rounded-lg hover:bg-[#7a5100] transition-colors">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          Meja
        </button>
      </div>
    </div>

    <!-- Meja List -->
    <div v-if="lokasi.mejas && lokasi.mejas.length > 0" class="divide-y divide-gray-50">
      <div
        v-for="meja in lokasi.mejas"
        :key="meja.id"
        class="flex items-center justify-between px-5 py-3 hover:bg-gray-50 transition-colors"
      >
        <div class="flex items-center gap-3">
          <!-- Checkbox per meja (hanya muncul saat mode pilih) -->
          <input
            v-if="selectMode"
            type="checkbox"
            :checked="selectedMejaIds.includes(meja.id)"
            @change="$emit('toggle-meja', meja.id)"
            class="w-4 h-4 rounded text-[#996600] border-gray-300 focus:ring-[#996600] cursor-pointer flex-shrink-0"
          />
          <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-gray-100 text-gray-700 font-mono text-xs font-semibold">
            {{ meja.kode_meja }}
          </span>
          <div>
            <p class="text-sm font-medium text-gray-800">{{ meja.nama || 'Meja ' + meja.kode_meja }}</p>
            <p v-if="meja.nomor" class="text-xs text-gray-400">No. {{ meja.nomor }}</p>
          </div>
        </div>
        <div class="flex items-center gap-2">
          <span :class="meja.is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'" class="text-xs px-2 py-0.5 rounded-full font-medium">
            {{ meja.is_active ? 'Aktif' : 'Nonaktif' }}
          </span>
          <button @click="$emit('show-qr', meja)" class="p-1.5 rounded-lg hover:bg-[#f4efe5] text-gray-400 hover:text-[#996600] transition-colors" title="Tampilkan QR Code">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
            </svg>
          </button>
          <button @click="$emit('edit-meja', meja)" class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-400 hover:text-[#996600] transition-colors">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
          </button>
          <button @click="$emit('delete-meja', meja)" class="p-1.5 rounded-lg hover:bg-red-50 text-gray-400 hover:text-red-500 transition-colors">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
          </button>
        </div>
      </div>
    </div>
    <div v-else class="px-5 py-6 text-center text-sm text-gray-400 italic">
      Belum ada meja di lokasi ini.
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  lokasi:         { type: Object,  required: true },
  selectMode:     { type: Boolean, default: false },
  selectedMejaIds:{ type: Array,   default: () => [] },
})

const emit = defineEmits(['edit-lokasi', 'delete-lokasi', 'add-meja', 'edit-meja', 'delete-meja', 'show-qr', 'toggle-meja'])

const aktivMejas = computed(() => (props.lokasi.mejas || []).filter(m => m.is_active))

const lokasiAllSelected = computed(() =>
  aktivMejas.value.length > 0 && aktivMejas.value.every(m => props.selectedMejaIds.includes(m.id))
)

const lokasiPartialSelected = computed(() =>
  !lokasiAllSelected.value && aktivMejas.value.some(m => props.selectedMejaIds.includes(m.id))
)

function toggleSelectLokasi() {
  if (lokasiAllSelected.value) {
    aktivMejas.value.forEach(m => emit('toggle-meja', m.id, false))
  } else {
    aktivMejas.value.forEach(m => emit('toggle-meja', m.id, true))
  }
}
</script>
