<template>
  <div class="space-y-3">
    <!-- Info bar: mode double counting -->
    <div
      class="flex items-start gap-2.5 px-4 py-2.5 rounded-lg text-sm"
      :class="includeRekap
        ? 'bg-amber-50 border border-amber-200 text-amber-800'
        : 'bg-green-50 border border-green-200 text-green-800'"
    >
      <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>
      <span v-if="!includeRekap">
        Laporan hanya menampilkan transaksi asli. Transfer antar buku kas (buku induk) disembunyikan agar tidak terjadi penghitungan ganda.
        Aktifkan toggle <strong>Termasuk buku induk</strong> di filter untuk melihat semua transaksi.
      </span>
      <span v-else>
        Mode <strong>termasuk buku induk</strong> aktif. Transaksi transfer antar kas ikut dihitung &mdash; angka bisa lebih besar dari nilai sebenarnya karena ada penghitungan ganda.
      </span>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Total Pemasukan</p>
        <p class="mt-1 text-2xl font-bold text-green-600">{{ formatRp(summary.total_pemasukan) }}</p>
      </div>
      <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Total Pengeluaran</p>
        <p class="mt-1 text-2xl font-bold text-red-600">{{ formatRp(summary.total_pengeluaran) }}</p>
      </div>
      <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Saldo Bersih</p>
        <p
          class="mt-1 text-2xl font-bold"
          :class="summary.saldo >= 0 ? 'text-[#996600]' : 'text-red-600'"
        >
          {{ formatRp(summary.saldo) }}
        </p>
      </div>
      <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Jumlah Transaksi</p>
        <p class="mt-1 text-2xl font-bold text-gray-800">{{ summary.jumlah_transaksi.toLocaleString('id-ID') }}</p>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  summary: Object,
  includeRekap: { type: Boolean, default: false },
})

function formatRp(val) {
  return 'Rp ' + (val ?? 0).toLocaleString('id-ID', { minimumFractionDigits: 0 })
}
</script>
