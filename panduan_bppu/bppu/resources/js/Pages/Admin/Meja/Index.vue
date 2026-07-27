<template>
  <AdminLayout page-title="Pengelolaan Meja">
    <div class="space-y-4 pb-10">
      <!-- Breadcrumb -->
      <nav class="flex text-xs md:text-sm text-gray-600" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-2">
          <li class="inline-flex items-center">
            <Link href="/pengelola/dasbor" class="inline-flex items-center hover:text-[#996600]">
              <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
              </svg>
              Dasbor
            </Link>
          </li>
          <li aria-current="page">
            <div class="flex items-center">
              <svg class="w-3.5 h-3.5 text-gray-400 mx-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
              <span class="font-medium text-gray-800">Meja</span>
            </div>
          </li>
        </ol>
      </nav>

      <!-- Header -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
          <div>
            <h1 class="text-lg font-bold text-gray-900">Pengelolaan Meja</h1>
            <p class="text-sm text-gray-500 mt-0.5">Kelola lokasi dan daftar meja untuk pesanan</p>
          </div>
          <div class="flex items-center gap-2 flex-wrap">
            <!-- Mode pilih meja aktif -->
            <template v-if="selectMode">
              <span class="text-sm text-gray-500">
                {{ selectedMejaIds.length > 0 ? `${selectedMejaIds.length} meja dipilih` : 'Pilih meja...' }}
              </span>
              <a
                :href="cetakPdfUrl('kantin')"
                target="_blank"
                :class="selectedMejaIds.length === 0 ? 'pointer-events-none opacity-40' : ''"
                class="flex items-center gap-2 px-4 py-2 text-sm bg-[#996600] text-white rounded-lg hover:bg-[#7a5100] transition-colors"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                QR Kantin
              </a>
              <a
                :href="cetakPdfUrl('belanja')"
                target="_blank"
                :class="selectedMejaIds.length === 0 ? 'pointer-events-none opacity-40' : ''"
                class="flex items-center gap-2 px-4 py-2 text-sm border border-[#996600] text-[#996600] rounded-lg hover:bg-[#f4efe5] transition-colors"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                QR Toko
              </a>
              <button
                @click="cancelSelectMode"
                class="flex items-center gap-2 px-4 py-2 text-sm border border-gray-300 text-gray-600 rounded-lg hover:bg-gray-50 transition-colors"
              >
                Batal
              </button>
            </template>

            <!-- Mode normal -->
            <template v-else>
              <button
                @click="startSelectMode"
                class="flex items-center gap-2 px-4 py-2 text-sm border border-gray-300 text-gray-600 rounded-lg hover:bg-gray-50 transition-colors"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Cetak PDF
              </button>
            </template>
            <button
              @click="openLokasiModal(null)"
              class="flex items-center gap-2 px-4 py-2 text-sm border border-[#996600] text-[#996600] rounded-lg hover:bg-[#f4efe5] transition-colors"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
              Tambah Lokasi
            </button>
            <button
              @click="openMejaModal(null)"
              class="flex items-center gap-2 px-4 py-2 text-sm bg-[#996600] text-white rounded-lg hover:bg-[#7a5100] transition-colors"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
              </svg>
              Tambah Meja
            </button>
          </div>
        </div>
      </div>

      <!-- Stats -->
      <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 px-4 py-3 text-center">
          <p class="text-2xl font-bold text-[#996600]">{{ lokasis.length }}</p>
          <p class="text-xs text-gray-500 mt-0.5">Lokasi</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 px-4 py-3 text-center">
          <p class="text-2xl font-bold text-[#996600]">{{ totalMeja }}</p>
          <p class="text-xs text-gray-500 mt-0.5">Total Meja</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 px-4 py-3 text-center">
          <p class="text-2xl font-bold text-green-600">{{ lokasiAktif }}</p>
          <p class="text-xs text-gray-500 mt-0.5">Lokasi Aktif</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 px-4 py-3 text-center">
          <p class="text-2xl font-bold text-green-600">{{ mejaAktif }}</p>
          <p class="text-xs text-gray-500 mt-0.5">Meja Aktif</p>
        </div>
      </div>

      <!-- Lokasi & Meja List -->
      <div v-if="pagedLokasis.length > 0" class="space-y-4">
        <LokasiCard
          v-for="lokasi in pagedLokasis"
          :key="lokasi.id"
          :lokasi="lokasi"
          :select-mode="selectMode"
          :selected-meja-ids="selectedMejaIds"
          @edit-lokasi="openLokasiModal"
          @delete-lokasi="confirmDeleteLokasi"
          @add-meja="(lok) => openMejaModal(null, lok.id)"
          @edit-meja="openMejaModal"
          @delete-meja="confirmDeleteMeja"
          @show-qr="openQrModal"
          @toggle-meja="toggleMeja"
        />
      </div>
      <div v-else class="bg-white rounded-xl shadow-sm border border-gray-100 py-16 text-center">
        <svg class="w-12 h-12 mx-auto text-gray-200 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h7" />
        </svg>
        <p class="text-gray-500 text-sm">Belum ada lokasi. Tambah lokasi terlebih dahulu.</p>
      </div>

      <!-- Pagination -->
      <div v-if="totalPages > 1" class="flex items-center justify-center gap-1">
        <button
          @click="currentPage--"
          :disabled="currentPage === 1"
          class="px-3 py-1.5 text-sm border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-40 disabled:cursor-not-allowed"
        >
          &laquo; Prev
        </button>
        <button
          v-for="p in totalPages"
          :key="p"
          @click="currentPage = p"
          :class="p === currentPage ? 'bg-[#996600] text-white border-[#996600]' : 'border-gray-300 hover:bg-gray-50'"
          class="px-3 py-1.5 text-sm border rounded-lg"
        >
          {{ p }}
        </button>
        <button
          @click="currentPage++"
          :disabled="currentPage === totalPages"
          class="px-3 py-1.5 text-sm border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-40 disabled:cursor-not-allowed"
        >
          Next &raquo;
        </button>
      </div>
    </div>

    <!-- Toast -->
    <transition name="toast">
      <div
        v-if="toast.show"
        :class="toast.type === 'success' ? 'bg-green-600' : 'bg-red-600'"
        class="fixed bottom-5 right-5 z-[60] text-white text-sm px-4 py-3 rounded-xl shadow-lg max-w-xs"
      >
        {{ toast.message }}
      </div>
    </transition>

    <!-- Modals -->
    <LokasiFormModal
      v-if="showLokasiModal"
      :lokasi="selectedLokasi"
      :work-units="workUnits"
      @close="showLokasiModal = false"
      @saved="onLokasiSaved"
    />

    <MejaFormModal
      v-if="showMejaModal"
      :meja="selectedMeja"
      :lokasis="lokasis"
      :default-lokasi-id="defaultLokasiId"
      @close="showMejaModal = false"
      @saved="onMejaSaved"
    />

    <!-- QR Modal -->
    <MejaQrModal
      :show="showQrModal"
      :meja="selectedQrMeja"
      @close="showQrModal = false"
    />

    <!-- Delete Confirm -->
    <div v-if="deleteTarget" class="fixed inset-0 z-50 overflow-y-auto">
      <div class="flex min-h-screen items-center justify-center p-4">
        <div class="fixed inset-0 bg-gray-900/50" @click="deleteTarget = null"></div>
        <div class="relative bg-white rounded-xl shadow-xl w-full max-w-sm p-6 space-y-4">
          <h3 class="text-base font-semibold text-gray-900">Konfirmasi Hapus</h3>
          <p class="text-sm text-gray-600">{{ deleteTarget.message }}</p>
          <p v-if="deleteError" class="text-xs text-red-600 bg-red-50 rounded px-3 py-2">{{ deleteError }}</p>
          <div class="flex justify-end gap-2">
            <button @click="deleteTarget = null" class="px-4 py-2 text-sm text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50">Batal</button>
            <button @click="executeDelete" :disabled="deleteLoading" class="px-4 py-2 text-sm bg-red-600 text-white rounded-lg hover:bg-red-700 disabled:opacity-50">
              {{ deleteLoading ? 'Menghapus...' : 'Hapus' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import LokasiCard from '@/Components/Meja/LokasiCard.vue'
import LokasiFormModal from '@/Components/Meja/LokasiFormModal.vue'
import MejaFormModal from '@/Components/Meja/MejaFormModal.vue'
import MejaQrModal from '@/Components/Meja/MejaQrModal.vue'
import axios from 'axios'

// Select mode untuk cetak PDF
const selectMode = ref(false)
const selectedMejaIds = ref([])

function cetakPdfUrl(type = 'kantin') {
  const params = selectedMejaIds.value.map(id => `meja_ids[]=${id}`).join('&')
  const base = `/pengelola/meja/cetak-pdf?type=${type}`
  return params ? `${base}&${params}` : base
}

function startSelectMode() {
  selectMode.value = true
  selectedMejaIds.value = []
}

function cancelSelectMode() {
  selectMode.value = false
  selectedMejaIds.value = []
}

function toggleMeja(id, force = undefined) {
  if (force === true) {
    if (!selectedMejaIds.value.includes(id)) selectedMejaIds.value.push(id)
  } else if (force === false) {
    selectedMejaIds.value = selectedMejaIds.value.filter(x => x !== id)
  } else {
    const idx = selectedMejaIds.value.indexOf(id)
    if (idx === -1) selectedMejaIds.value.push(id)
    else selectedMejaIds.value.splice(idx, 1)
  }
}

const props = defineProps({
  lokasis:   { type: Array, default: () => [] },
  workUnits: { type: Array, default: () => [] },
})

const lokasis = ref(props.lokasis)
const workUnits = ref(props.workUnits)

const showLokasiModal = ref(false)
const selectedLokasi = ref(null)
const showMejaModal = ref(false)
const selectedMeja = ref(null)
const defaultLokasiId = ref(null)
const deleteTarget = ref(null)
const deleteError = ref('')
const deleteLoading = ref(false)

// QR modal
const showQrModal = ref(false)
const selectedQrMeja = ref(null)

function openQrModal(meja) {
  selectedQrMeja.value = meja
  showQrModal.value = true
}

// Pagination
const PER_PAGE = 8
const currentPage = ref(1)
const totalPages = computed(() => Math.ceil(lokasis.value.length / PER_PAGE))
const pagedLokasis = computed(() => {
  const start = (currentPage.value - 1) * PER_PAGE
  return lokasis.value.slice(start, start + PER_PAGE)
})

// Toast
const toast = ref({ show: false, message: '', type: 'success' })
let toastTimer = null

function showToast(message, type = 'success') {
  if (toastTimer) clearTimeout(toastTimer)
  toast.value = { show: true, message, type }
  toastTimer = setTimeout(() => { toast.value.show = false }, 3000)
}

const totalMeja = computed(() => lokasis.value.reduce((s, l) => s + (l.mejas?.length ?? 0), 0))
const lokasiAktif = computed(() => lokasis.value.filter(l => l.is_active).length)
const mejaAktif = computed(() => lokasis.value.reduce((s, l) => s + (l.mejas?.filter(m => m.is_active).length ?? 0), 0))

function openLokasiModal(lokasi) {
  selectedLokasi.value = lokasi
  showLokasiModal.value = true
}

function openMejaModal(meja, lokasiId = null) {
  selectedMeja.value = meja
  defaultLokasiId.value = meja ? meja.lokasi_meja_id : lokasiId
  showMejaModal.value = true
}

async function reloadData() {
  try {
    const res = await axios.get('/pengelola/meja/data')
    lokasis.value = res.data
  } catch {}
}

function onLokasiSaved(data, isEdit) {
  showLokasiModal.value = false
  showToast(isEdit ? 'Lokasi berhasil diperbarui.' : 'Lokasi berhasil ditambahkan.')
  reloadData()
}

function onMejaSaved(data, isEdit) {
  showMejaModal.value = false
  showToast(isEdit ? 'Meja berhasil diperbarui.' : 'Meja berhasil ditambahkan.')
  reloadData()
}

function confirmDeleteLokasi(lokasi) {
  deleteError.value = ''
  deleteTarget.value = {
    type: 'lokasi',
    id: lokasi.id,
    message: `Hapus lokasi "${lokasi.nama}"? Semua meja di lokasi ini juga akan terhapus.`,
  }
}

function confirmDeleteMeja(meja) {
  deleteError.value = ''
  deleteTarget.value = {
    type: 'meja',
    id: meja.id,
    message: `Hapus meja "${meja.nama || meja.kode_meja}"?`,
  }
}

async function executeDelete() {
  if (!deleteTarget.value) return
  deleteError.value = ''
  deleteLoading.value = true

  const url = deleteTarget.value.type === 'lokasi'
    ? `/pengelola/meja/lokasi/${deleteTarget.value.id}`
    : `/pengelola/meja/${deleteTarget.value.id}`

  const label = deleteTarget.value.type === 'lokasi' ? 'Lokasi' : 'Meja'

  try {
    await axios.delete(url)
    deleteTarget.value = null
    showToast(`${label} berhasil dihapus.`)
    reloadData()
  } catch (e) {
    deleteError.value = e.response?.data?.message || 'Gagal menghapus.'
  } finally {
    deleteLoading.value = false
  }
}
</script>

<style scoped>
.toast-enter-active, .toast-leave-active { transition: all 0.3s ease; }
.toast-enter-from, .toast-leave-to { opacity: 0; transform: translateY(10px); }
</style>
