<template>
  <AdminLayout page-title="Laporan Keuangan">
    <div class="space-y-4">

      <!-- 1. Summary Cards -->
      <SummaryCards :summary="summary" :include-rekap="filters.include_rekap === '1'" />

      <!-- 2. Filter Panel -->
      <FilterPanel
        :filters="filters"
        :tahun-list="tahunList"
        :jenis-buku-kas-list="jenisBukuKasList"
        :buku-kas-list="bukuKasList"
        :kategori-list="kategoriList"
        :jenis-transaksi-list="jenisTransaksiList"
        @apply="applyFilters"
        @reset="resetFilters"
        @export-csv="doExportCsv"
      />

      <!-- 3. Tab Rekap -->
      <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
        <div class="border-b border-gray-100">
          <nav class="flex overflow-x-auto">
            <button
              v-for="tab in tabs"
              :key="tab.key"
              @click="activeTab = tab.key"
              class="px-5 py-3 text-sm font-medium border-b-2 whitespace-nowrap transition-colors"
              :class="activeTab === tab.key
                ? 'border-[#996600] text-[#996600]'
                : 'border-transparent text-gray-500 hover:text-gray-700'"
            >
              {{ tab.label }}
            </button>
          </nav>
        </div>

        <RekapTable
          v-if="activeTab === 'buku_kas'"
          title="Rekap per Buku Kas"
          label-kolom="Buku Kas"
          label-key="nama_buku_kas"
          :rows="rekapBukuKas"
        />
        <RekapTable
          v-else-if="activeTab === 'jenis'"
          title="Rekap per Jenis Buku Kas"
          label-kolom="Jenis Buku Kas"
          label-key="jenis_nama"
          :rows="rekapJenis"
        />
        <RekapTable
          v-else-if="activeTab === 'kategori'"
          title="Rekap per Kategori"
          label-kolom="Kategori"
          label-key="kategori"
          :rows="rekapKategori"
        />
        <RekapTable
          v-else-if="activeTab === 'jenis_trx'"
          title="Rekap per Jenis Transaksi"
          label-kolom="Jenis Transaksi"
          label-key="jenis_transaksi"
          :rows="rekapJenisTrx"
        />
        <TrenBulananTable
          v-else-if="activeTab === 'tren'"
          :data="trenBulanan"
        />
      </div>

      <!-- 4. Detail Transaksi -->
      <DetailTransaksiTable
        :transaksi="detailData"
        :loading="detailLoading"
        :sort-field="detailSort.field"
        :sort-dir="detailSort.dir"
        :per-page="detailPerPage"
        @sort-change="onSortChange"
        @go-to-page="onGoToPage"
        @change-per-page="onChangePerPage"
      />

    </div>
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import axios from 'axios'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import FilterPanel from '@/Components/LaporanKeuangan/FilterPanel.vue'
import SummaryCards from '@/Components/LaporanKeuangan/SummaryCards.vue'
import RekapTable from '@/Components/LaporanKeuangan/RekapTable.vue'
import TrenBulananTable from '@/Components/LaporanKeuangan/TrenBulananTable.vue'
import DetailTransaksiTable from '@/Components/LaporanKeuangan/DetailTransaksiTable.vue'

const props = defineProps({
  filters:            Object,
  summary:            Object,
  rekapBukuKas:       Array,
  rekapJenis:         Array,
  rekapKategori:      Array,
  rekapJenisTrx:      Array,
  trenBulanan:        Array,
  bukuKasList:        Array,
  jenisBukuKasList:   Array,
  kategoriList:       Array,
  jenisTransaksiList: Array,
  tahunList:          Array,
})

const activeTab = ref('buku_kas')

const tabs = [
  { key: 'buku_kas',  label: 'Per Buku Kas' },
  { key: 'jenis',     label: 'Per Jenis Buku Kas' },
  { key: 'kategori',  label: 'Per Kategori' },
  { key: 'jenis_trx', label: 'Per Jenis Transaksi' },
  { key: 'tren',      label: 'Tren Bulanan' },
]

// Detail transaksi (lazy via AJAX)
const detailData    = ref({ data: [], total: 0, last_page: 1, current_page: 1 })
const detailLoading = ref(false)
const detailSort    = ref({ field: 'tanggal', dir: 'desc' })
const detailPerPage = ref(20)
const detailPage    = ref(1)
const currentParams = ref({ ...props.filters })

async function loadDetail() {
  detailLoading.value = true
  try {
    const res = await axios.get(route('admin.laporan.detail'), {
      params: {
        ...currentParams.value,
        sort_field:     detailSort.value.field,
        sort_direction: detailSort.value.dir,
        per_page:       detailPerPage.value,
        page:           detailPage.value,
      }
    })
    detailData.value = res.data.transaksi
  } catch (e) {
    console.error(e)
  } finally {
    detailLoading.value = false
  }
}

function onSortChange({ field, dir }) {
  detailSort.value = { field, dir }
  detailPage.value = 1
  loadDetail()
}

function onGoToPage(page) {
  detailPage.value = page
  loadDetail()
}

function onChangePerPage(val) {
  detailPerPage.value = val
  detailPage.value    = 1
  loadDetail()
}

function applyFilters(params) {
  currentParams.value = params
  detailPage.value    = 1
  router.get(route('admin.laporan'), params, {
    preserveScroll: true,
    replace: true,
    onFinish: () => loadDetail(),
  })
}

function resetFilters() {
  currentParams.value = {}
  detailPage.value    = 1
  router.get(route('admin.laporan'), {}, {
    preserveScroll: true,
    replace: true,
    onFinish: () => loadDetail(),
  })
}

function doExportCsv(params) {
  const qs = new URLSearchParams(params).toString()
  window.location.href = route('admin.laporan.export-csv') + (qs ? '?' + qs : '')
}

// Load detail on mount
loadDetail()
</script>
