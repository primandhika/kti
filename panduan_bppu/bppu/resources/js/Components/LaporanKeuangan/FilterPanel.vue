<template>
  <div class="bg-white rounded-xl shadow-sm border border-gray-100 px-4 py-3">
    <div class="flex flex-wrap items-end gap-2">

      <!-- Mode -->
      <div class="flex flex-col gap-1 min-w-[130px]">
        <label class="text-[10px] font-semibold text-gray-400 uppercase tracking-wide">Periode</label>
        <select v-model="localFilters.mode" @change="onModeChange" class="select-sm">
          <option value="bulan">Per Bulan</option>
          <option value="tahun">Per Tahun</option>
          <option value="rentang">Rentang</option>
          <option value="semua">Semua</option>
        </select>
      </div>

      <!-- Bulan -->
      <div v-if="localFilters.mode === 'bulan'" class="flex flex-col gap-1 min-w-[120px]">
        <label class="text-[10px] font-semibold text-gray-400 uppercase tracking-wide">Bulan</label>
        <select v-model="localFilters.bulan" @change="autoApply" class="select-sm">
          <option v-for="b in bulanList" :key="b.value" :value="b.value">{{ b.label }}</option>
        </select>
      </div>

      <!-- Tahun -->
      <div v-if="localFilters.mode === 'bulan' || localFilters.mode === 'tahun'" class="flex flex-col gap-1 min-w-[80px]">
        <label class="text-[10px] font-semibold text-gray-400 uppercase tracking-wide">Tahun</label>
        <select v-model="localFilters.tahun" @change="autoApply" class="select-sm">
          <option v-for="t in tahunList" :key="t" :value="t">{{ t }}</option>
        </select>
      </div>

      <!-- Rentang dari -->
      <div v-if="localFilters.mode === 'rentang'" class="flex flex-col gap-1 min-w-[130px]">
        <label class="text-[10px] font-semibold text-gray-400 uppercase tracking-wide">Dari</label>
        <input type="date" v-model="localFilters.date_from" class="select-sm" />
      </div>

      <!-- Rentang sampai -->
      <div v-if="localFilters.mode === 'rentang'" class="flex flex-col gap-1 min-w-[130px]">
        <label class="text-[10px] font-semibold text-gray-400 uppercase tracking-wide">Sampai</label>
        <input type="date" v-model="localFilters.date_to" class="select-sm" />
      </div>

      <!-- Divider -->
      <div class="h-8 w-px bg-gray-200 self-end mb-0.5 hidden sm:block" />

      <!-- Jenis Buku Kas -->
      <div class="flex flex-col gap-1 min-w-[130px]">
        <label class="text-[10px] font-semibold text-gray-400 uppercase tracking-wide">Jenis Buku Kas</label>
        <select v-model="localFilters.jenis_buku_kas_id" @change="autoApply" class="select-sm">
          <option value="">Semua Jenis</option>
          <option v-for="j in jenisBukuKasList" :key="j.id" :value="j.id">{{ j.nama }}</option>
        </select>
      </div>

      <!-- Buku Kas -->
      <div class="flex flex-col gap-1 min-w-[140px]">
        <label class="text-[10px] font-semibold text-gray-400 uppercase tracking-wide">Buku Kas</label>
        <select v-model="localFilters.buku_kas_id" @change="autoApply" class="select-sm">
          <option value="">Semua</option>
          <option v-for="b in bukuKasList" :key="b.id" :value="b.id">{{ b.nama }}</option>
        </select>
      </div>

      <!-- Kategori -->
      <div class="flex flex-col gap-1 min-w-[140px]">
        <label class="text-[10px] font-semibold text-gray-400 uppercase tracking-wide">Kategori</label>
        <select v-model="localFilters.kategori" @change="autoApply" class="select-sm">
          <option value="">Semua</option>
          <option v-for="k in kategoriList" :key="k.id" :value="k.nama">{{ k.nama }}</option>
        </select>
      </div>

      <!-- Jenis Transaksi -->
      <div class="flex flex-col gap-1 min-w-[110px]">
        <label class="text-[10px] font-semibold text-gray-400 uppercase tracking-wide">Jenis Trx</label>
        <select v-model="localFilters.jenis_transaksi" @change="autoApply" class="select-sm">
          <option value="">Semua</option>
          <option v-for="j in jenisTransaksiList" :key="j" :value="j">{{ j }}</option>
        </select>
      </div>

      <!-- Tipe -->
      <div class="flex flex-col gap-1 min-w-[110px]">
        <label class="text-[10px] font-semibold text-gray-400 uppercase tracking-wide">Tipe</label>
        <select v-model="localFilters.tipe" @change="autoApply" class="select-sm">
          <option value="">Semua</option>
          <option value="pemasukan">Pemasukan</option>
          <option value="pengeluaran">Pengeluaran</option>
        </select>
      </div>

      <!-- Toggle include rekap -->
      <div class="flex flex-col gap-1 min-w-[150px]">
        <label class="text-[10px] font-semibold text-gray-400 uppercase tracking-wide">Tampilan</label>
        <label class="flex items-center gap-2 cursor-pointer select-none h-[34px]" @click.prevent="toggleIncludeRekap">
          <div class="relative">
            <div
              class="w-9 h-5 rounded-full transition-colors"
              :class="localFilters.include_rekap ? 'bg-amber-500' : 'bg-gray-300'"
            ></div>
            <div
              class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform"
              :class="localFilters.include_rekap ? 'translate-x-4' : 'translate-x-0'"
            ></div>
          </div>
          <span class="text-xs text-gray-600 leading-tight">Termasuk<br>buku induk</span>
        </label>
      </div>

      <!-- Tombol -->
      <div class="flex gap-2 self-end ml-auto">
        <button
          v-if="hasActiveFilters"
          @click="$emit('reset')"
          class="px-3 py-1.5 text-xs font-medium text-gray-500 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
        >
          Reset
        </button>
        <!-- Terapkan hanya untuk mode rentang (perlu isi 2 field tanggal dulu) -->
        <button
          v-if="localFilters.mode === 'rentang'"
          @click="autoApply"
          class="px-4 py-1.5 text-xs font-semibold bg-[#996600] text-white rounded-lg hover:bg-[#7a5100] transition-colors"
        >
          Terapkan
        </button>
        <button
          @click="$emit('export-csv', buildParams())"
          class="px-4 py-1.5 text-xs font-semibold bg-white border border-gray-300 text-gray-600 rounded-lg hover:bg-gray-50 transition-colors"
        >
          Export CSV
        </button>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  filters:            Object,
  tahunList:          Array,
  jenisBukuKasList:   Array,
  bukuKasList:        Array,
  kategoriList:       Array,
  jenisTransaksiList: Array,
})

const emit = defineEmits(['apply', 'reset', 'export-csv'])

const bulanList = [
  { value: 1, label: 'Januari' }, { value: 2, label: 'Februari' },
  { value: 3, label: 'Maret' },   { value: 4, label: 'April' },
  { value: 5, label: 'Mei' },     { value: 6, label: 'Juni' },
  { value: 7, label: 'Juli' },    { value: 8, label: 'Agustus' },
  { value: 9, label: 'September' },{ value: 10, label: 'Oktober' },
  { value: 11, label: 'November' },{ value: 12, label: 'Desember' },
]

const currentYear  = new Date().getFullYear()
const currentMonth = new Date().getMonth() + 1

function detectMode(f) {
  if (f?.date_from || f?.date_to) return 'rentang'
  if (f?.bulan) return 'bulan'
  if (f?.tahun) return 'tahun'
  return 'bulan'
}

const localFilters = ref({
  mode:              detectMode(props.filters),
  date_from:         props.filters?.date_from || '',
  date_to:           props.filters?.date_to || '',
  bulan:             props.filters?.bulan || currentMonth,
  tahun:             props.filters?.tahun || currentYear,
  buku_kas_id:       props.filters?.buku_kas_id || '',
  jenis_buku_kas_id: props.filters?.jenis_buku_kas_id || '',
  kategori:          props.filters?.kategori || '',
  jenis_transaksi:   props.filters?.jenis_transaksi || '',
  tipe:              props.filters?.tipe || '',
  include_rekap:     props.filters?.include_rekap === '1',
})

const hasActiveFilters = computed(() =>
  localFilters.value.buku_kas_id
  || localFilters.value.jenis_buku_kas_id
  || localFilters.value.kategori
  || localFilters.value.jenis_transaksi
  || localFilters.value.tipe
  || localFilters.value.date_from
  || localFilters.value.date_to
  || localFilters.value.include_rekap
)

function onModeChange() {
  localFilters.value.date_from = ''
  localFilters.value.date_to   = ''
  localFilters.value.bulan     = currentMonth
  localFilters.value.tahun     = currentYear
  if (localFilters.value.mode !== 'rentang') {
    autoApply()
  }
}

function toggleIncludeRekap() {
  localFilters.value.include_rekap = !localFilters.value.include_rekap
  autoApply()
}

function autoApply() {
  emit('apply', buildParams())
}

function buildParams() {
  const f = localFilters.value
  const p = {
    jenis_buku_kas_id: f.jenis_buku_kas_id || undefined,
    buku_kas_id:       f.buku_kas_id       || undefined,
    kategori:          f.kategori          || undefined,
    jenis_transaksi:   f.jenis_transaksi   || undefined,
    tipe:              f.tipe              || undefined,
    include_rekap:     f.include_rekap ? '1' : undefined,
  }
  if (f.mode === 'rentang') {
    p.date_from = f.date_from || undefined
    p.date_to   = f.date_to   || undefined
  } else if (f.mode === 'bulan') {
    p.bulan = f.bulan
    p.tahun = f.tahun
  } else if (f.mode === 'tahun') {
    p.tahun = f.tahun
  }
  return Object.fromEntries(Object.entries(p).filter(([, v]) => v !== undefined && v !== ''))
}
</script>

<style scoped>
.select-sm {
  @apply w-full px-2.5 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent bg-white;
}
</style>
