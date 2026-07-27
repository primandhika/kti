<template>
  <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between gap-4 flex-wrap">
      <h3 class="text-sm font-semibold text-gray-800">Detail Transaksi</h3>
      <div class="flex items-center gap-3">
        <select
          v-model="localPerPage"
          @change="$emit('change-per-page', localPerPage)"
          class="text-xs border border-gray-300 rounded-lg px-2 py-1 focus:ring-2 focus:ring-[#996600] focus:border-transparent"
        >
          <option :value="20">20 per halaman</option>
          <option :value="50">50 per halaman</option>
          <option :value="100">100 per halaman</option>
        </select>
        <span class="text-xs text-gray-400">{{ transaksi.total?.toLocaleString('id-ID') }} transaksi</span>
      </div>
    </div>

    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-100">
        <thead class="bg-gray-50">
          <tr>
            <th
              v-for="col in columns"
              :key="col.key"
              class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wide"
              :class="[col.align === 'right' ? 'text-right' : 'text-left', col.sortable ? 'cursor-pointer hover:text-[#996600] select-none' : '']"
              @click="col.sortable && toggleSort(col.key)"
            >
              {{ col.label }}
              <span v-if="col.sortable && localSort === col.key" class="ml-0.5">
                {{ localDir === 'asc' ? '↑' : '↓' }}
              </span>
            </th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
          <tr v-if="loading">
            <td :colspan="columns.length" class="px-4 py-8 text-center text-sm text-gray-400">Memuat data...</td>
          </tr>
          <tr v-else-if="!transaksi.data || transaksi.data.length === 0">
            <td :colspan="columns.length" class="px-4 py-8 text-center text-sm text-gray-400">Tidak ada transaksi</td>
          </tr>
          <tr
            v-else
            v-for="row in transaksi.data"
            :key="row.id"
            class="hover:bg-[#f4efe5] transition-colors"
          >
            <td class="px-4 py-3 text-xs text-gray-500 font-mono">{{ row.transaction_id }}</td>
            <td class="px-4 py-3 text-sm text-gray-700">{{ row.tanggal }}</td>
            <td class="px-4 py-3">
              <div class="flex items-center gap-1.5">
                <span
                  v-if="row.jenis_warna"
                  class="inline-block w-2 h-2 rounded-full flex-shrink-0"
                  :style="{ backgroundColor: row.jenis_warna }"
                />
                <span class="text-xs text-gray-700">{{ row.nama_buku_kas }}</span>
              </div>
            </td>
            <td class="px-4 py-3 text-xs text-gray-600">{{ row.kategori }}</td>
            <td class="px-4 py-3 text-xs text-gray-500">{{ row.jenis_transaksi }}</td>
            <td class="px-4 py-3 text-sm text-gray-700 max-w-xs truncate" :title="row.deskripsi">{{ row.deskripsi }}</td>
            <td class="px-4 py-3 text-right text-sm font-medium" :class="row.pemasukan > 0 ? 'text-green-600' : 'text-gray-300'">
              {{ row.pemasukan > 0 ? formatRp(row.pemasukan) : '-' }}
            </td>
            <td class="px-4 py-3 text-right text-sm font-medium" :class="row.pengeluaran > 0 ? 'text-red-600' : 'text-gray-300'">
              {{ row.pengeluaran > 0 ? formatRp(row.pengeluaran) : '-' }}
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div v-if="transaksi.last_page > 1" class="px-4 py-3 border-t border-gray-100 flex items-center justify-between gap-2 flex-wrap">
      <p class="text-xs text-gray-500">
        Halaman {{ transaksi.current_page }} dari {{ transaksi.last_page }}
        &nbsp;&middot;&nbsp; {{ transaksi.total?.toLocaleString('id-ID') }} transaksi
      </p>
      <div class="flex gap-1">
        <button
          :disabled="transaksi.current_page <= 1"
          @click="$emit('go-to-page', transaksi.current_page - 1)"
          class="px-3 py-1 text-xs border border-gray-300 rounded-lg disabled:opacity-40 hover:bg-gray-50"
        >
          Sebelumnya
        </button>
        <button
          v-for="page in visiblePages"
          :key="page"
          @click="typeof page === 'number' && $emit('go-to-page', page)"
          class="px-3 py-1 text-xs border rounded-lg"
          :class="page === transaksi.current_page
            ? 'bg-[#996600] text-white border-[#996600]'
            : page === '...' ? 'border-transparent cursor-default' : 'border-gray-300 hover:bg-gray-50'"
        >
          {{ page }}
        </button>
        <button
          :disabled="transaksi.current_page >= transaksi.last_page"
          @click="$emit('go-to-page', transaksi.current_page + 1)"
          class="px-3 py-1 text-xs border border-gray-300 rounded-lg disabled:opacity-40 hover:bg-gray-50"
        >
          Berikutnya
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  transaksi: { type: Object, default: () => ({ data: [], total: 0, last_page: 1, current_page: 1 }) },
  loading:   { type: Boolean, default: false },
  sortField: { type: String, default: 'tanggal' },
  sortDir:   { type: String, default: 'desc' },
  perPage:   { type: Number, default: 20 },
})

const emit = defineEmits(['sort-change', 'go-to-page', 'change-per-page'])

const localSort    = ref(props.sortField)
const localDir     = ref(props.sortDir)
const localPerPage = ref(props.perPage)

const columns = [
  { key: 'transaction_id', label: 'ID Transaksi', sortable: false },
  { key: 'tanggal',        label: 'Tanggal',      sortable: true },
  { key: 'nama_buku_kas',  label: 'Buku Kas',     sortable: false },
  { key: 'kategori',       label: 'Kategori',     sortable: true },
  { key: 'jenis_transaksi',label: 'Jenis',        sortable: true },
  { key: 'deskripsi',      label: 'Deskripsi',    sortable: false },
  { key: 'pemasukan',      label: 'Pemasukan',    sortable: true, align: 'right' },
  { key: 'pengeluaran',    label: 'Pengeluaran',  sortable: true, align: 'right' },
]

function toggleSort(key) {
  if (localSort.value === key) {
    localDir.value = localDir.value === 'asc' ? 'desc' : 'asc'
  } else {
    localSort.value = key
    localDir.value = 'desc'
  }
  emit('sort-change', { field: localSort.value, dir: localDir.value })
}

const visiblePages = computed(() => {
  const total   = props.transaksi.last_page
  const current = props.transaksi.current_page
  if (total <= 7) return Array.from({ length: total }, (_, i) => i + 1)
  const pages = []
  pages.push(1)
  if (current > 3) pages.push('...')
  for (let p = Math.max(2, current - 1); p <= Math.min(total - 1, current + 1); p++) pages.push(p)
  if (current < total - 2) pages.push('...')
  pages.push(total)
  return pages
})

function formatRp(val) {
  return 'Rp ' + (val ?? 0).toLocaleString('id-ID', { minimumFractionDigits: 0 })
}
</script>
