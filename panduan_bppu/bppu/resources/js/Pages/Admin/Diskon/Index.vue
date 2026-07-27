<template>
  <AdminLayout page-title="Diskon & Voucher" page-subtitle="Kelola program potongan dan penerbitan voucher">
    <div class="space-y-6">

      <!-- Breadcrumbs -->
      <nav class="flex text-xs md:text-sm text-gray-600" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-2">
          <li class="inline-flex items-center">
            <Link href="/pengelola/dasbor" class="inline-flex items-center hover:text-[#996600]">
              <svg class="w-3 h-3 md:w-4 md:h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
              </svg>
              Beranda
            </Link>
          </li>
          <li aria-current="page">
            <div class="flex items-center">
              <svg class="w-3 h-3 md:w-4 md:h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
              </svg>
              <span class="ml-1 md:ml-2 font-medium text-gray-800">Diskon & Voucher</span>
            </div>
          </li>
        </ol>
      </nav>

      <!-- Tab Navigation -->
      <div class="border-b border-gray-200">
        <nav class="flex gap-1">
          <span class="px-4 py-2.5 text-sm font-medium text-[#996600] border-b-2 border-[#996600]">
            Voucher Diskon
          </span>
          <Link
            href="/pengelola/diskon/redeem-poin"
            class="px-4 py-2.5 text-sm font-medium text-gray-500 hover:text-[#996600] border-b-2 border-transparent hover:border-[#996600] transition-colors"
          >
            Redeem Poin
          </Link>
        </nav>
      </div>

      <!-- Header & Tombol Tambah -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div class="flex gap-2">
          <input
            v-model="filters.search"
            @input="debouncedSearch"
            type="text"
            placeholder="Cari nama atau kode potongan..."
            class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#996600] w-64"
          />
          <select
            v-model="filters.tipe"
            @change="applyFilters"
            class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#996600]"
          >
            <option value="">Semua Tipe</option>
            <option value="persen">Persen (%)</option>
            <option value="nominal">Nominal (Rp)</option>
          </select>
          <select
            v-model="filters.status"
            @change="applyFilters"
            class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#996600]"
          >
            <option value="">Semua Status</option>
            <option value="aktif">Aktif</option>
            <option value="nonaktif">Nonaktif</option>
          </select>
        </div>
        <div class="flex gap-2">
          <a
            :href="exportUrl"
            class="flex items-center gap-2 border border-[#996600] text-[#996600] px-4 py-2 rounded-lg text-sm font-medium hover:bg-[#f4efe5] transition-colors"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
            </svg>
            Download CSV
          </a>
          <button
            @click="showImportModal = true"
            class="flex items-center gap-2 border border-gray-400 text-gray-600 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l4-4m0 0l4 4m-4-4v12" />
            </svg>
            Impor CSV
          </button>
          <button
            @click="showModal = true"
            class="flex items-center gap-2 bg-[#996600] text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-[#7a5100] transition-colors"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Tambah Potongan
          </button>
        </div>
      </div>

      <!-- Tabel -->
      <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
        <div v-if="potongan.data.length === 0" class="py-16 text-center text-gray-400">
          <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
          </svg>
          <p class="font-medium">Belum ada potongan</p>
          <p class="text-sm mt-1">Buat program potongan untuk menerbitkan voucher</p>
        </div>

        <table v-else class="w-full text-sm">
          <thead class="bg-[#f4efe5] border-b border-[#e0d1b2]">
            <tr>
              <th class="px-4 py-3 text-left text-[#6b4700] font-semibold">Kode</th>
              <th class="px-4 py-3 text-left text-[#6b4700] font-semibold">Nama Potongan</th>
              <th class="px-4 py-3 text-center text-[#6b4700] font-semibold">Tipe & Nilai</th>
              <th class="px-4 py-3 text-center text-[#6b4700] font-semibold">Voucher</th>
              <th class="px-4 py-3 text-center text-[#6b4700] font-semibold">Berlaku</th>
              <th class="px-4 py-3 text-center text-[#6b4700] font-semibold">Status</th>
              <th class="px-4 py-3 text-right text-[#6b4700] font-semibold">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr
              v-for="p in potongan.data"
              :key="p.id"
              class="hover:bg-gray-50 transition-colors"
            >
              <td class="px-4 py-3">
                <span class="font-mono text-xs bg-gray-100 px-2 py-1 rounded text-gray-700">{{ p.kode_potongan }}</span>
              </td>
              <td class="px-4 py-3">
                <div class="font-medium text-gray-800">{{ p.nama }}</div>
                <div v-if="p.deskripsi" class="text-xs text-gray-400 mt-0.5 truncate max-w-[200px]">{{ p.deskripsi }}</div>
              </td>
              <td class="px-4 py-3 text-center">
                <span class="font-semibold text-[#996600]">
                  {{ p.tipe === 'persen' ? p.nilai + '%' : 'Rp ' + formatRupiah(p.nilai) }}
                </span>
                <div v-if="p.tipe === 'persen' && p.maksimum_potongan" class="text-xs text-gray-400">maks Rp {{ formatRupiah(p.maksimum_potongan) }}</div>
                <div v-if="p.minimum_transaksi > 0" class="text-xs text-gray-400">min Rp {{ formatRupiah(p.minimum_transaksi) }}</div>
              </td>
              <td class="px-4 py-3">
                <div class="flex justify-center gap-1 text-xs">
                  <span class="bg-gray-100 text-gray-600 px-2 py-0.5 rounded" title="Draft">D: {{ p.draft_count }}</span>
                  <span class="bg-blue-50 text-blue-600 px-2 py-0.5 rounded" title="Printed">P: {{ p.printed_count }}</span>
                  <span class="bg-green-50 text-green-600 px-2 py-0.5 rounded" title="Used">U: {{ p.used_count }}</span>
                </div>
                <div class="text-center text-xs text-gray-400 mt-1">kuota: {{ p.vouchers_count }}/{{ p.kuota_voucher }}</div>
              </td>
              <td class="px-4 py-3 text-center text-xs text-gray-500">
                <template v-if="p.berlaku_mulai || p.berlaku_sampai">
                  <div v-if="p.berlaku_mulai">{{ formatDate(p.berlaku_mulai) }}</div>
                  <div class="text-gray-400">s/d</div>
                  <div v-if="p.berlaku_sampai">{{ formatDate(p.berlaku_sampai) }}</div>
                </template>
                <span v-else class="text-gray-400">-</span>
              </td>
              <td class="px-4 py-3 text-center">
                <span
                  :class="[
                    'text-xs px-2 py-1 rounded-full font-medium',
                    p.is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'
                  ]"
                >
                  {{ p.is_active ? 'Aktif' : 'Nonaktif' }}
                </span>
              </td>
              <td class="px-4 py-3 text-right">
                <div class="flex justify-end gap-2">
                  <Link
                    :href="`/pengelola/diskon/${p.id}/vouchers`"
                    class="text-xs bg-[#f4efe5] text-[#6b4700] px-2 py-1 rounded hover:bg-[#e0d1b2] transition-colors"
                  >
                    Voucher
                  </Link>
                  <button
                    @click="openEdit(p)"
                    class="text-xs bg-blue-50 text-blue-600 px-2 py-1 rounded hover:bg-blue-100 transition-colors"
                  >
                    Edit
                  </button>
                  <button
                    @click="confirmDelete(p)"
                    class="text-xs bg-red-50 text-red-600 px-2 py-1 rounded hover:bg-red-100 transition-colors"
                  >
                    Hapus
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>

        <!-- Pagination -->
        <div v-if="potongan.last_page > 1" class="px-4 py-3 border-t border-gray-100 flex items-center justify-between text-sm text-gray-500">
          <span>Menampilkan {{ potongan.from }}-{{ potongan.to }} dari {{ potongan.total }}</span>
          <div class="flex gap-1">
            <Link
              v-for="link in potongan.links"
              :key="link.label"
              :href="link.url || ''"
              :class="[
                'px-3 py-1 rounded border transition-colors',
                link.active ? 'bg-[#996600] text-white border-[#996600]' : 'border-gray-300 hover:bg-gray-50',
                !link.url ? 'opacity-40 pointer-events-none' : ''
              ]"
              v-html="link.label"
            />
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Tambah/Edit -->
    <PotonganModal
      v-if="showModal"
      :editing="editingItem"
      :barang-options="barangOptions"
      @close="closeModal"
      @saved="closeModal"
    />

    <!-- Modal Import CSV -->
    <ImportCsvModal
      v-if="showImportModal"
      @close="showImportModal = false"
    />

    <!-- Confirm Delete -->
    <ConfirmModal
      v-if="deleteTarget"
      :title="`Hapus Potongan: ${deleteTarget.nama}`"
      message="Semua voucher draft milik potongan ini juga akan dihapus. Lanjutkan?"
      confirm-label="Hapus"
      :loading="deleting"
      @confirm="doDelete"
      @cancel="deleteTarget = null"
    />
  </AdminLayout>
</template>

<script setup>
import { ref, reactive, computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PotonganModal from './PotonganModal.vue'
import ImportCsvModal from './ImportCsvModal.vue'
import ConfirmModal from '@/Components/ConfirmModal.vue'

const props = defineProps({
  potongan: Object,
  filters: Object,
  barangOptions: { type: Array, default: () => [] },
})

const showModal = ref(false)
const showImportModal = ref(false)
const editingItem = ref(null)
const deleteTarget = ref(null)
const deleting = ref(false)

const filters = reactive({
  search: props.filters?.search || '',
  tipe: props.filters?.tipe || '',
  status: props.filters?.status || '',
})

const exportUrl = computed(() => {
  const params = new URLSearchParams()
  if (filters.search) params.set('search', filters.search)
  if (filters.tipe) params.set('tipe', filters.tipe)
  if (filters.status) params.set('status', filters.status)
  const qs = params.toString()
  return '/pengelola/diskon/export-voucher' + (qs ? '?' + qs : '')
})

let searchTimeout = null
function debouncedSearch() {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => applyFilters(), 400)
}

function applyFilters() {
  const params = new URLSearchParams()
  if (filters.search) params.set('search', filters.search)
  if (filters.tipe) params.set('tipe', filters.tipe)
  if (filters.status) params.set('status', filters.status)
  window.location.href = '/pengelola/diskon?' + params.toString()
}

function openEdit(p) {
  editingItem.value = p
  showModal.value = true
}

function closeModal() {
  showModal.value = false
  editingItem.value = null
}

function confirmDelete(p) {
  deleteTarget.value = p
}

function doDelete() {
  if (!deleteTarget.value) return
  deleting.value = true
  router.delete(`/pengelola/diskon/${deleteTarget.value.id}`, {
    preserveScroll: true,
    onFinish: () => {
      deleting.value = false
      deleteTarget.value = null
    },
  })
}

function formatRupiah(val) {
  return Number(val).toLocaleString('id-ID')
}

function formatDate(date) {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' })
}
</script>
