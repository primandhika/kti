<template>
  <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100">
      <h3 class="text-sm font-semibold text-gray-800">Tren Bulanan</h3>
    </div>

    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-100">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Bulan</th>
            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wide">Pemasukan</th>
            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wide">Pengeluaran</th>
            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wide">Saldo</th>
            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wide">Transaksi</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide w-48">Proporsi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
          <tr v-if="data.length === 0">
            <td colspan="6" class="px-4 py-6 text-center text-sm text-gray-400">Tidak ada data</td>
          </tr>
          <tr
            v-for="row in data"
            :key="`${row.tahun}-${row.bulan}`"
            class="hover:bg-[#f4efe5] transition-colors"
          >
            <td class="px-4 py-3 text-sm font-medium text-gray-800">{{ row.label }}</td>
            <td class="px-4 py-3 text-right text-sm text-green-600">{{ formatRp(row.total_pemasukan) }}</td>
            <td class="px-4 py-3 text-right text-sm text-red-600">{{ formatRp(row.total_pengeluaran) }}</td>
            <td
              class="px-4 py-3 text-right text-sm font-semibold"
              :class="row.saldo >= 0 ? 'text-[#996600]' : 'text-red-700'"
            >
              {{ formatRp(row.saldo) }}
            </td>
            <td class="px-4 py-3 text-right text-sm text-gray-500">{{ row.jumlah_transaksi }}</td>
            <td class="px-4 py-3">
              <div class="flex gap-1 items-center">
                <div class="flex-1 bg-gray-100 rounded-full h-2 overflow-hidden">
                  <div
                    class="h-2 rounded-full bg-green-500"
                    :style="{ width: pctPemasukan(row) + '%' }"
                  />
                </div>
                <span class="text-xs text-gray-400 w-8 text-right">{{ pctPemasukan(row) }}%</span>
              </div>
            </td>
          </tr>
        </tbody>
        <tfoot v-if="data.length > 0" class="bg-gray-50 border-t border-gray-200">
          <tr>
            <td class="px-4 py-3 text-xs font-semibold text-gray-700">Total</td>
            <td class="px-4 py-3 text-right text-xs font-semibold text-green-700">{{ formatRp(totalP) }}</td>
            <td class="px-4 py-3 text-right text-xs font-semibold text-red-700">{{ formatRp(totalK) }}</td>
            <td class="px-4 py-3 text-right text-xs font-semibold" :class="totalS >= 0 ? 'text-[#996600]' : 'text-red-700'">{{ formatRp(totalS) }}</td>
            <td class="px-4 py-3 text-right text-xs font-semibold text-gray-600">{{ totalTrx }}</td>
            <td />
          </tr>
        </tfoot>
      </table>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  data: { type: Array, default: () => [] },
})

const totalP   = computed(() => props.data.reduce((s, r) => s + r.total_pemasukan, 0))
const totalK   = computed(() => props.data.reduce((s, r) => s + r.total_pengeluaran, 0))
const totalS   = computed(() => totalP.value - totalK.value)
const totalTrx = computed(() => props.data.reduce((s, r) => s + r.jumlah_transaksi, 0))

const maxPemasukan = computed(() => Math.max(...props.data.map(r => r.total_pemasukan), 1))

function pctPemasukan(row) {
  return Math.round((row.total_pemasukan / maxPemasukan.value) * 100)
}

function formatRp(val) {
  return 'Rp ' + (val ?? 0).toLocaleString('id-ID', { minimumFractionDigits: 0 })
}
</script>
