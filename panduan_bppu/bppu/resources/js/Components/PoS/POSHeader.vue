<template>
  <div class="bg-white shadow-sm border-b flex-shrink-0 sticky top-0 z-20">
    <div class="px-3 py-2">
      <!-- Row 1: Kasir dropdown + Barcode Mode + Search + Tanggal -->
      <div class="flex items-center gap-2">
        <!-- Kasir Multi-Select Dropdown -->
        <div class="flex items-center gap-2 flex-shrink-0" ref="workUnitDropdownRef">
          <h1 class="text-sm font-bold text-gray-800 hidden sm:block">Kasir</h1>

          <!-- Single unit: tampil teks saja -->
          <span v-if="workUnits.length === 1" class="text-xs text-gray-500">{{ workUnits[0]?.name }}</span>

          <!-- Multiple units: dropdown multi-select -->
          <div v-else class="relative">
            <button
              @click="showWorkUnitDropdown = !showWorkUnitDropdown"
              class="flex items-center gap-1.5 text-xs px-2 py-1.5 border border-gray-300 rounded focus:ring-1 focus:ring-[#996600] bg-white min-w-[120px] max-w-[200px]"
              :class="localSelectedIds.length > 1 ? 'border-[#996600] text-[#996600] font-medium' : ''"
            >
              <span class="truncate flex-1 text-left">
                {{ workUnitButtonLabel }}
              </span>
              <svg class="w-3.5 h-3.5 flex-shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </button>

            <!-- Dropdown panel -->
            <div
              v-if="showWorkUnitDropdown"
              class="absolute top-full left-0 mt-1 bg-white border border-gray-200 rounded-lg shadow-lg z-50 min-w-[180px]"
            >
              <div class="p-2 border-b border-gray-100 flex gap-2">
                <button
                  @click="selectAllWorkUnits"
                  class="text-xs text-[#996600] hover:underline"
                >Pilih Semua</button>
                <span class="text-gray-300">|</span>
                <button
                  @click="clearWorkUnits"
                  class="text-xs text-gray-500 hover:underline"
                >Reset</button>
              </div>
              <div class="py-1 max-h-48 overflow-y-auto">
                <label
                  v-for="unit in workUnits"
                  :key="unit.id"
                  class="flex items-center gap-2 px-3 py-2 hover:bg-gray-50 cursor-pointer"
                >
                  <input
                    type="checkbox"
                    :value="unit.id"
                    v-model="localSelectedIds"
                    class="rounded border-gray-300 text-[#996600] focus:ring-[#996600]"
                    @change="onWorkUnitChange"
                  />
                  <span class="text-xs text-gray-700">{{ unit.name }}</span>
                </label>
              </div>
            </div>
          </div>
        </div>

        <!-- Barcode Mode Toggle -->
        <div class="flex items-center gap-2 flex-shrink-0">
          <button
            @click="handleBarcodeModeToggle"
            :title="barcodeMode ? (scannerType === 'camera' ? 'Mode Kamera aktif - klik untuk nonaktifkan' : 'Mode USB Scanner aktif - klik untuk nonaktifkan') : 'Aktifkan Barcode Mode'"
            :class="[
              'flex items-center gap-1.5 px-2 py-1.5 rounded-lg text-xs font-medium border transition-all',
              barcodeMode
                ? 'bg-[#996600] border-[#7a5100] text-white'
                : 'bg-white border-gray-300 text-gray-700 hover:border-[#996600] hover:text-[#996600]'
            ]"
          >
            <!-- Icon USB -->
            <svg v-if="!barcodeMode || scannerType === 'usb'" class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3H7a2 2 0 00-2 2v14a2 2 0 002 2h10a2 2 0 002-2V5a2 2 0 00-2-2h-2M9 3a2 2 0 012-2h2a2 2 0 012 2M9 3h6" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6M9 16h4" />
            </svg>
            <!-- Icon Camera -->
            <svg v-else class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <span class="hidden sm:inline">
              {{ barcodeMode ? (scannerType === 'camera' ? 'Kamera' : 'USB') : 'Barcode' }}
            </span>
            <!-- Indikator aktif -->
            <span v-if="barcodeMode" class="w-1.5 h-1.5 rounded-full bg-green-300 animate-pulse"></span>
          </button>
        </div>

        <!-- Search Bar with Camera Button -->
        <div class="flex-1 min-w-0 relative flex items-center gap-1">
          <input
            ref="searchInputRef"
            :value="searchQuery"
            @input="$emit('update:searchQuery', $event.target.value)"
            @keydown.enter="handleSearchEnter"
            @focus="handleSearchFocus"
            type="text"
            :placeholder="barcodeMode && scannerType === 'usb' ? 'Scan barcode...' : 'Cari barang...'"
            :readonly="barcodeMode && scannerType === 'camera'"
            :class="[
              'flex-1 min-w-0 px-3 py-1.5 text-sm border rounded focus:ring-1 focus:ring-[#996600]',
              barcodeMode && scannerType === 'usb'
                ? 'border-[#996600] bg-[#f4efe5] font-medium'
                : 'border-gray-300',
              barcodeMode && scannerType === 'camera' ? 'cursor-pointer' : ''
            ]"
          />
          <!-- Tombol kamera hanya muncul jika mode camera -->
          <button
            v-if="barcodeMode && scannerType === 'camera'"
            @click="$emit('open-camera-scanner')"
            class="flex-shrink-0 p-1.5 bg-[#996600] hover:bg-[#7a5100] text-white rounded transition-colors"
            title="Buka Kamera Scanner"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
          </button>
        </div>

        <!-- Tanggal -->
        <div class="hidden md:flex items-center gap-1 text-xs text-gray-600 bg-gray-50 px-2 py-1.5 rounded border flex-shrink-0">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
          </svg>
          <span class="font-medium">{{ currentDate }}</span>
        </div>
      </div>

      <!-- Row 2: Kategori Tabs (Scrollable) -->
      <div class="mt-1.5 -mx-3 px-3">
        <div class="flex gap-1.5 overflow-x-auto scrollbar-hide pb-1">
          <!-- Tab Semua Kategori -->
          <button
            @click="$emit('update:selectedCategory', null)"
            :class="[
              'px-3 py-1.5 rounded-lg text-xs font-medium whitespace-nowrap transition-all flex-shrink-0 flex items-center gap-1',
              selectedCategory === null && !filterDiskon
                ? 'bg-[#996600] text-white shadow-sm'
                : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
            ]"
          >
            Semua
          </button>

          <!-- Tab per Kategori dengan badge count -->
          <button
            v-for="category in categories"
            :key="category"
            @click="$emit('update:selectedCategory', category)"
            :class="[
              'px-3 py-1.5 rounded-lg text-xs font-medium whitespace-nowrap transition-all flex-shrink-0 flex items-center gap-1',
              selectedCategory === category && !filterDiskon
                ? 'bg-[#996600] text-white shadow-sm'
                : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
            ]"
          >
            {{ category }}
            <span
              class="inline-flex items-center justify-center text-[10px] font-bold px-1 py-0.5 rounded-full min-w-[16px]"
              :class="selectedCategory === category && !filterDiskon ? 'bg-white/25 text-white' : 'bg-gray-300 text-gray-600'"
            >
              {{ categoryCounts[category] || 0 }}
            </span>
          </button>

          <!-- Separator -->
          <div v-if="countDiskon > 0" class="w-px bg-gray-300 flex-shrink-0 self-stretch my-1"></div>

          <!-- Pill Diskon - selalu di paling kanan, hanya muncul jika ada diskon aktif -->
          <button
            v-if="countDiskon > 0"
            @click="$emit('update:filterDiskon', !filterDiskon)"
            :class="[
              'px-3 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap transition-all flex-shrink-0 flex items-center gap-1.5',
              filterDiskon
                ? 'bg-green-600 text-white shadow-sm'
                : 'bg-green-50 text-green-800 border border-green-300 hover:bg-green-100'
            ]"
          >
            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
            </svg>
            Diskon
            <span
              class="inline-flex items-center justify-center text-[10px] font-bold px-1.5 py-0.5 rounded-full min-w-[18px]"
              :class="filterDiskon ? 'bg-white/25 text-white' : 'bg-green-200 text-green-800'"
            >
              {{ countDiskon }}
            </span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, nextTick, onMounted, onBeforeUnmount } from 'vue'

const props = defineProps({
  workUnits: Array,
  selectedWorkUnit: Object,
  selectedWorkUnitId: [String, Number],
  selectedWorkUnitIds: { type: Array, default: () => [] },
  searchQuery: String,
  selectedCategory: String,
  categories: Array,
  categoryCounts: { type: Object, default: () => ({}) },
  countDiskon: { type: Number, default: 0 },
  filterDiskon: { type: Boolean, default: false },
  barcodeMode: Boolean,
  scannerType: { type: String, default: 'usb' },
  isKantinUser: Boolean
})

const emit = defineEmits([
  'change-work-unit',
  'update:searchQuery',
  'update:selectedCategory',
  'update:filterDiskon',
  'toggle-barcode-mode',
  'search-enter',
  'open-camera-scanner'
])

const searchInputRef = ref(null)
const workUnitDropdownRef = ref(null)
const showWorkUnitDropdown = ref(false)

// Local state untuk multi-select, init dari props
const localSelectedIds = ref(
  props.selectedWorkUnitIds.length > 0
    ? [...props.selectedWorkUnitIds]
    : (props.selectedWorkUnitId ? [props.selectedWorkUnitId] : [])
)

const workUnitButtonLabel = computed(() => {
  if (localSelectedIds.value.length === 0) return 'Pilih toko...'
  if (localSelectedIds.value.length === 1) {
    const unit = props.workUnits.find(u => u.id == localSelectedIds.value[0])
    return unit?.name ?? 'Toko'
  }
  return `${localSelectedIds.value.length} toko dipilih`
})

const selectAllWorkUnits = () => {
  localSelectedIds.value = props.workUnits.map(u => u.id)
  emit('change-work-unit', [...localSelectedIds.value])
}

const clearWorkUnits = () => {
  // Reset ke first unit
  const firstId = props.workUnits[0]?.id
  localSelectedIds.value = firstId ? [firstId] : []
  emit('change-work-unit', [...localSelectedIds.value])
}

const onWorkUnitChange = () => {
  if (localSelectedIds.value.length === 0) {
    // Pastikan minimal 1 terpilih
    const firstId = props.workUnits[0]?.id
    if (firstId) localSelectedIds.value = [firstId]
  }
  emit('change-work-unit', [...localSelectedIds.value])
}

// Tutup dropdown saat klik di luar
const handleClickOutside = (event) => {
  if (workUnitDropdownRef.value && !workUnitDropdownRef.value.contains(event.target)) {
    showWorkUnitDropdown.value = false
  }
}

onMounted(() => document.addEventListener('mousedown', handleClickOutside))
onBeforeUnmount(() => document.removeEventListener('mousedown', handleClickOutside))

const currentDate = computed(() => {
  const today = new Date()
  const options = { weekday: 'short', year: 'numeric', month: 'short', day: 'numeric' }
  return today.toLocaleDateString('id-ID', options)
})

const handleSearchEnter = (event) => {
  if (props.barcodeMode) {
    emit('search-enter', event.target.value)
    // Clear search after enter in barcode mode
    nextTick(() => {
      emit('update:searchQuery', '')
    })
  }
}

const handleBarcodeModeToggle = () => {
  emit('toggle-barcode-mode')
}

const handleSearchFocus = (event) => {
  if (props.barcodeMode && props.scannerType === 'camera') {
    // Blur immediately to prevent keyboard
    event.target.blur()
    // Open camera scanner
    emit('open-camera-scanner')
  }
}

// Auto-focus search input saat barcode mode USB aktif
watch(() => props.barcodeMode, (newVal) => {
  if (newVal && props.scannerType === 'usb') {
    nextTick(() => {
      searchInputRef.value?.focus()
    })
  }
})

// Expose ref untuk parent component
defineExpose({
  searchInputRef
})
</script>

<style scoped>
/* Hide scrollbar for Chrome, Safari and Opera */
.scrollbar-hide::-webkit-scrollbar {
  display: none;
}

/* Hide scrollbar for IE, Edge and Firefox */
.scrollbar-hide {
  -ms-overflow-style: none;  /* IE and Edge */
  scrollbar-width: none;  /* Firefox */
}
</style>
