<template>
  <div class="bg-white rounded-xl shadow-md p-4">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
      <!-- Left: Search & Filters -->
      <div class="flex flex-wrap items-center gap-2">
        <input
          :value="searchQuery"
          @input="$emit('update:searchQuery', $event.target.value)"
          type="text"
          placeholder="Cari menu..."
          class="px-3 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent text-xs w-48"
        />
        <select
          :value="filterKategori"
          @change="$emit('update:filterKategori', $event.target.value)"
          class="px-3 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent text-xs"
        >
          <option value="">Semua Kategori</option>
          <option v-for="kategori in kategoris" :key="kategori.id" :value="kategori.id">
            {{ kategori.nama }}
          </option>
        </select>
        <select
          :value="filterWorkUnit"
          @change="$emit('update:filterWorkUnit', $event.target.value)"
          class="px-3 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent text-xs"
        >
          <option value="">Semua Kantin</option>
          <option v-for="unit in workUnits" :key="unit.id" :value="unit.id">
            {{ unit.name }}
          </option>
        </select>

        <!-- Stats -->
        <div class="flex items-center gap-2 text-xs border-l pl-2 ml-1">
          <span class="text-gray-500">Total:</span>
          <span class="font-bold text-gray-900">{{ totalMenus }}</span>
          <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
          <span class="font-semibold text-green-600">{{ availableCount }}</span>
          <span class="w-1.5 h-1.5 rounded-full bg-orange-500"></span>
          <span class="font-semibold text-orange-600">{{ outOfStockCount }}</span>
        </div>
      </div>

      <!-- Right: Actions -->
      <div class="flex items-center gap-2">
        <!-- Toggle Semua -->
        <button
          @click="$emit('toggleShowAll')"
          class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg text-xs font-medium transition-colors"
          :class="showAllKategori ? 'bg-[#996600] text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
        >
          <div class="relative inline-flex h-3.5 w-6 items-center rounded-full transition-colors"
            :class="showAllKategori ? 'bg-white/20' : 'bg-gray-300'"
          >
            <span
              class="inline-block h-2.5 w-2.5 transform rounded-full bg-white shadow-sm transition-transform"
              :class="showAllKategori ? 'translate-x-3' : 'translate-x-0.5'"
            ></span>
          </div>
          <span>Semua</span>
        </button>

        <!-- Dropdown Cetak/Unduh -->
        <div class="relative" ref="dropdownRef">
          <button
            @click.stop="showDropdown = !showDropdown"
            class="inline-flex items-center px-2.5 py-1.5 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors text-xs"
          >
            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            Cetak
          </button>

          <div
            v-if="showDropdown"
            class="absolute right-0 mt-1 w-56 bg-white rounded-lg shadow-lg border border-gray-200 z-50"
          >
            <div class="px-3 py-2 text-[10px] font-semibold text-gray-400 uppercase tracking-wide border-b border-gray-100">Cetak</div>
            <div class="py-1">
              <button
                @click.stop="handlePrint"
                class="w-full flex items-center space-x-2 px-3 py-2 text-xs hover:bg-gray-50 transition-colors"
              >
                <svg class="w-3.5 h-3.5 text-[#996600]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                <div class="text-left">
                  <div class="font-medium text-gray-900">Cetak Berwarna</div>
                  <div class="text-[10px] text-gray-500">Dengan gambar, langsung print</div>
                </div>
              </button>
            </div>
            <div class="px-3 py-2 text-[10px] font-semibold text-gray-400 uppercase tracking-wide border-t border-b border-gray-100">Unduh PDF</div>
            <div class="py-1">
              <button
                @click.stop="handleDownloadPdf"
                class="w-full flex items-center space-x-2 px-3 py-2 text-xs hover:bg-gray-50 transition-colors"
              >
                <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <div class="text-left">
                  <div class="font-medium text-gray-900">Tanpa Gambar</div>
                  <div class="text-[10px] text-gray-500">Hemat tinta, download PDF</div>
                </div>
              </button>
            </div>
          </div>
        </div>

        <Link
          href="/pengelola/opname-stock"
          class="inline-flex items-center px-2.5 py-1.5 bg-[#996600] hover:bg-[#7a5100] text-white font-medium rounded-lg transition-colors text-xs"
        >
          <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          Master
        </Link>

        <Link
          href="/pengelola/setting?tab=subKategoriMenu"
          class="inline-flex items-center px-2.5 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition-colors text-xs"
        >
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
          </svg>
        </Link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
  searchQuery: { type: String, required: true },
  filterKategori: { type: String, required: true },
  filterWorkUnit: { type: String, required: true },
  totalMenus: { type: Number, required: true },
  availableCount: { type: Number, required: true },
  outOfStockCount: { type: Number, required: true },
  showAllKategori: { type: Boolean, required: true },
  kategoris: { type: Array, required: true },
  workUnits: { type: Array, required: true },
});

const emit = defineEmits(['toggleShowAll', 'printMenu', 'openDownloadModal', 'update:searchQuery', 'update:filterKategori', 'update:filterWorkUnit']);

const showDropdown = ref(false);
const dropdownRef = ref(null);

const handlePrint = () => {
  showDropdown.value = false;
  emit('printMenu', props.filterWorkUnit);
};

const handleDownloadPdf = () => {
  showDropdown.value = false;
  emit('openDownloadModal', props.filterWorkUnit);
};

const handleClickOutside = (e) => {
  if (dropdownRef.value && !dropdownRef.value.contains(e.target)) {
    showDropdown.value = false;
  }
};

onMounted(() => document.addEventListener('click', handleClickOutside));
onUnmounted(() => document.removeEventListener('click', handleClickOutside));
</script>
