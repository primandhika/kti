<template>
  <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
      <h3 class="text-sm font-semibold text-gray-800">{{ title }}</h3>
      <span class="text-xs text-gray-400">{{ rows.length }} item</span>
    </div>

    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-100">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">{{ labelKolom }}</th>
            <th
              v-for="col in sortableCols"
              :key="col.key"
              class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wide cursor-pointer select-none hover:text-[#996600]"
              @click="toggleSort(col.key)"
            >
              {{ col.label }}
              <span v-if="sortKey === col.key" class="ml-1">{{ sortDir === 'asc' ? '↑' : '↓' }}</span>
            </th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
          <tr v-if="sorted.length === 0">
            <td :colspan="sortableCols.length + 1" class="px-4 py-6 text-center text-sm text-gray-400">
              Tidak ada data
            </td>
          </tr>
          <tr
            v-for="(row, i) in sorted"
            :key="i"
            class="hover:bg-[#f4efe5] transition-colors"
          >
            <td class="px-4 py-3">
              <div class="flex items-center gap-2">
                <span
                  v-if="row.jenis_warna"
                  class="inline-block w-2.5 h-2.5 rounded-full flex-shrink-0"
                  :style="{ backgroundColor: row.jenis_warna }"
                />
                <span class="text-sm text-gray-800 font-medium">{{ row[labelKey] }}</span>
                <span v-if="row.jenis_nama && labelKey !== 'jenis_nama'" class="text-xs text-gray-400">({{ row.jenis_nama }})</span>
              </div>
            </td>
            <td class="px-4 py-3 text-right text-sm text-green-600 font-medium">{{ formatRp(row.total_pemasukan) }}</td>
            <td class="px-4 py-3 text-right text-sm text-red-600 font-medium">{{ formatRp(row.total_pengeluaran) }}</td>
            <td class="px-4 py-3 text-right text-sm font-semibold" :class="row.saldo >= 0 ? 'text-[#996600]' : 'text-red-700'">
              {{ formatRp(row.saldo) }}
            </td>
            <td class="px-4 py-3 text-right text-sm text-gray-500">{{ row.jumlah_transaksi }}</td>
          </tr>
        </tbody>
        <tfoot v-if="rows.length > 0" class="bg-gray-50 border-t border-gray-200">
          <tr>
            <td class="px-4 py-3 text-xs font-semibold text-gray-700">Total</td>
            <td class="px-4 py-3 text-right text-xs font-semibold text-green-700">{{ formatRp(totalPemasukan) }}</td>
            <td class="px-4 py-3 text-right text-xs font-semibold text-red-700">{{ formatRp(totalPengeluaran) }}</td>
            <td class="px-4 py-3 text-right text-xs font-semibold" :class="totalSaldo >= 0 ? 'text-[#996600]' : 'text-red-700'">{{ formatRp(totalSaldo) }}</td>
            <td class="px-4 py-3 text-right text-xs font-semibold text-gray-600">{{ totalTrx }}</td>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  title: String,
  labelKolom: { type: String, default: 'Nama' },
  labelKey: { type: String, default: 'nama' },
  rows: { type: Array, default: () => [] },
})

const sortableCols = [
  { key: 'total_pemasukan', label: 'Pemasukan' },
  { key: 'total_pengeluaran', label: 'Pengeluaran' },
  { key: 'saldo', label: 'Saldo' },
  { key: 'jumlah_transaksi', label: 'Transaksi' },
]

const sortKey = ref('total_pemasukan')
const sortDir = ref('desc')

function toggleSort(key) {
  if (sortKey.value === key) {
    sortDir.value = sortDir.value === 'asc' ? 'desc' : 'asc'
  } else {
    sortKey.value = key
    sortDir.value = 'desc'
  }
}

const sorted = computed(() => {
  return [...props.rows].sort((a, b) => {
    const av = a[sortKey.value] ?? 0
    const bv = b[sortKey.value] ?? 0
    return sortDir.value === 'asc' ? av - bv : bv - av
  })
})

const totalPemasukan  = computed(() => props.rows.reduce((s, r) => s + (r.total_pemasukan ?? 0), 0))
const totalPengeluaran = computed(() => props.rows.reduce((s, r) => s + (r.total_pengeluaran ?? 0), 0))
const totalSaldo      = computed(() => totalPemasukan.value - totalPengeluaran.value)
const totalTrx        = computed(() => props.rows.reduce((s, r) => s + (r.jumlah_transaksi ?? 0), 0))

function formatRp(val) {
  return 'Rp ' + (val ?? 0).toLocaleString('id-ID', { minimumFractionDigits: 0 })
}
</script>
