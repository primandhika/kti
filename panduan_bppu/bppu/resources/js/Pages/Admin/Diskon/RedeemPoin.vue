<template>
  <AdminLayout page-title="Redeem" page-subtitle="Riwayat potongan: voucher terpakai & redeem poin member">
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
          <li>
            <div class="flex items-center">
              <svg class="w-3 h-3 md:w-4 md:h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
              </svg>
              <Link href="/pengelola/diskon" class="ml-1 md:ml-2 hover:text-[#996600]">Diskon & Voucher</Link>
            </div>
          </li>
          <li aria-current="page">
            <div class="flex items-center">
              <svg class="w-3 h-3 md:w-4 md:h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
              </svg>
              <span class="ml-1 md:ml-2 font-medium text-gray-800">Redeem</span>
            </div>
          </li>
        </ol>
      </nav>

      <!-- Tab Navigation -->
      <div class="border-b border-gray-200">
        <nav class="flex gap-1">
          <Link
            href="/pengelola/diskon"
            class="px-4 py-2.5 text-sm font-medium text-gray-500 hover:text-[#996600] border-b-2 border-transparent hover:border-[#996600] transition-colors"
          >
            Voucher Diskon
          </Link>
          <span class="px-4 py-2.5 text-sm font-medium text-[#996600] border-b-2 border-[#996600]">
            Redeem
          </span>
        </nav>
      </div>

      <!-- Stats -->
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
          <div class="text-xs text-gray-500 mb-1">Total Redeem</div>
          <div class="text-2xl font-bold text-gray-800">{{ stats.total_transaksi.toLocaleString('id-ID') }}</div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
          <div class="text-xs text-gray-500 mb-1">Voucher Terpakai</div>
          <div class="text-2xl font-bold text-[#996600]">{{ stats.total_voucher.toLocaleString('id-ID') }}</div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
          <div class="text-xs text-gray-500 mb-1">Redeem Poin</div>
          <div class="text-2xl font-bold text-[#996600]">{{ stats.total_poin_redeem.toLocaleString('id-ID') }}</div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
          <div class="text-xs text-gray-500 mb-1">Total Nilai Potongan</div>
          <div class="text-2xl font-bold text-green-700">Rp {{ formatRupiah(stats.total_nilai) }}</div>
        </div>
      </div>

      <!-- Mode Toggle -->
      <div class="flex items-center gap-2">
        <span class="text-xs text-gray-500">Tampilan:</span>
        <div class="inline-flex rounded-lg border border-gray-200 p-0.5 bg-gray-50">
          <button
            @click="setMode('kode')"
            :class="[
              'px-3 py-1.5 text-xs font-medium rounded-md transition-colors',
              mode === 'kode' ? 'bg-[#996600] text-white shadow-sm' : 'text-gray-600 hover:text-[#996600]'
            ]"
          >
            Per Kode
          </button>
          <button
            @click="setMode('campaign')"
            :class="[
              'px-3 py-1.5 text-xs font-medium rounded-md transition-colors',
              mode === 'campaign' ? 'bg-[#996600] text-white shadow-sm' : 'text-gray-600 hover:text-[#996600]'
            ]"
          >
            Per Campaign
          </button>
        </div>
        <span v-if="mode === 'campaign'" class="text-[11px] text-gray-400">Voucher digabung per campaign; redeem poin tetap per baris</span>
      </div>

      <!-- Filter & Export -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div class="flex flex-wrap gap-2">
          <input
            v-model="filters.search"
            @input="debouncedSearch"
            type="text"
            placeholder="Cari member / kode voucher..."
            class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#996600] w-56"
          />
          <input v-model="filters.dari" @change="applyFilters" type="date" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#996600]" />
          <input v-model="filters.sampai" @change="applyFilters" type="date" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#996600]" />
        </div>
        <a :href="exportUrl" class="flex items-center gap-2 bg-[#996600] text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-[#7a5100] transition-colors">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
          </svg>
          Download CSV
        </a>
      </div>

      <!-- Tabel -->
      <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
        <div v-if="redeems.data.length === 0" class="py-16 text-center text-gray-400">
          <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <p class="font-medium">Belum ada data redeem</p>
          <p class="text-sm mt-1">Data muncul saat voucher terpakai atau member menukar poin</p>
        </div>

        <div v-else class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead class="bg-[#f4efe5] border-b border-[#e0d1b2]">
              <tr>
                <th class="px-4 py-3 text-left text-[#6b4700] font-semibold whitespace-nowrap">Tanggal</th>
                <th class="px-4 py-3 text-left text-[#6b4700] font-semibold">Member</th>
                <th class="px-4 py-3 text-left text-[#6b4700] font-semibold">Kode Voucher</th>
                <th class="px-4 py-3 text-right text-[#6b4700] font-semibold">Nilai Potongan</th>
                <th class="px-4 py-3 text-left text-[#6b4700] font-semibold">No. Transaksi</th>
                <th class="px-4 py-3 text-left text-[#6b4700] font-semibold">Unit Kerja</th>
                <th class="px-4 py-3 text-center text-[#6b4700] font-semibold">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              <tr v-for="r in redeems.data" :key="r.uid" class="hover:bg-gray-50 transition-colors">
                <td class="px-4 py-3 text-xs text-gray-500 whitespace-nowrap">{{ formatDate(r.tanggal) }}</td>
                <td class="px-4 py-3">
                  <template v-if="r.source === 'campaign'">
                    <span class="inline-flex items-center gap-1 text-xs text-gray-500">
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-1.13a4 4 0 10-4-4 4 4 0 004 4z" /></svg>
                      {{ r.jumlah_voucher }} member
                    </span>
                  </template>
                  <template v-else>
                    <div class="font-medium text-gray-800">{{ r.member_name || '-' }}</div>
                    <div class="text-xs text-gray-400">{{ r.member_email || '-' }}</div>
                  </template>
                </td>
                <td class="px-4 py-3">
                  <div v-if="r.source === 'campaign'">
                    <span class="inline-block bg-[#996600] text-white px-2 py-1 rounded text-xs font-semibold">{{ r.nama_potongan }}</span>
                    <div class="text-[11px] text-gray-400 mt-1">{{ r.jumlah_voucher }} voucher terpakai</div>
                  </div>
                  <div v-else-if="r.kode_voucher">
                    <span class="font-mono text-xs bg-[#f4efe5] text-[#6b4700] px-2 py-1 rounded font-semibold">{{ r.kode_voucher }}</span>
                    <div class="text-[11px] text-gray-400 mt-1">{{ r.nama_potongan }}</div>
                  </div>
                  <div v-else class="text-xs">
                    <span class="inline-block bg-purple-50 text-purple-700 px-2 py-1 rounded font-medium">Redeem Poin</span>
                    <div v-if="r.poin" class="text-[11px] text-gray-400 mt-1">{{ Number(r.poin).toLocaleString('id-ID') }} poin</div>
                  </div>
                </td>
                <td class="px-4 py-3 text-right font-semibold text-green-700 whitespace-nowrap">Rp {{ formatRupiah(r.nilai_potongan) }}</td>
                <td class="px-4 py-3">
                  <span v-if="r.nomor_transaksi" class="font-mono text-xs bg-gray-100 px-2 py-1 rounded text-gray-700">{{ r.nomor_transaksi }}</span>
                  <span v-else class="text-gray-400 text-xs">-</span>
                </td>
                <td class="px-4 py-3 text-xs text-gray-600">{{ r.work_unit_name || '-' }}</td>
                <td class="px-4 py-3">
                  <div class="flex items-center justify-center gap-1.5">
                    <button
                      @click="cetakKuitansi(r)"
                      class="p-1.5 text-gray-500 hover:text-[#996600] hover:bg-[#f4efe5] rounded transition-colors"
                      title="Cetak Kuitansi"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                      </svg>
                    </button>
                    <button
                      v-if="!r.is_recorded"
                      @click="openCatatModal(r)"
                      class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium bg-[#996600] text-white rounded hover:bg-[#7a5100] transition-colors whitespace-nowrap"
                    >
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                      </svg>
                      Buku Kas
                    </button>
                    <button
                      v-else
                      @click="batalkanCatatan(r)"
                      class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium bg-green-50 text-green-700 border border-green-200 rounded hover:bg-green-100 transition-colors whitespace-nowrap"
                      title="Sudah dicatat - klik untuk batalkan"
                    >
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                      </svg>
                      Tercatat
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div v-if="redeems.last_page > 1" class="px-4 py-3 border-t border-gray-100 flex items-center justify-between text-sm text-gray-500">
          <span>Menampilkan {{ redeems.from }}-{{ redeems.to }} dari {{ redeems.total }}</span>
          <div class="flex gap-1">
            <Link
              v-for="link in redeems.links"
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

    <!-- Modal Catat ke Buku Kas -->
    <CatatRedeemModal
      :show="showCatatModal"
      :redeem="selectedRedeem"
      :buku-kas-list="bukuKasList"
      :work-units="workUnits"
      :kategori-list="kategoriList"
      @close="showCatatModal = false"
    />
  </AdminLayout>
</template>

<script setup>
import { reactive, ref, computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import CatatRedeemModal from '@/Components/Diskon/CatatRedeemModal.vue'

const props = defineProps({
  redeems: Object,
  stats: Object,
  mode: { type: String, default: 'kode' },
  filters: Object,
  bukuKasList: { type: Array, default: () => [] },
  workUnits: { type: Array, default: () => [] },
  kategoriList: { type: Array, default: () => [] },
})

const mode = ref(props.mode)

const filters = reactive({
  search: props.filters?.search || '',
  dari: props.filters?.dari || '',
  sampai: props.filters?.sampai || '',
})

function setMode(m) {
  if (mode.value === m) return
  mode.value = m
  applyFilters()
}

const showCatatModal = ref(false)
const selectedRedeem = ref(null)

function openCatatModal(r) {
  selectedRedeem.value = r
  showCatatModal.value = true
}

function batalkanCatatan(r) {
  if (!confirm('Batalkan pencatatan potongan ini dari buku kas? Transaksi buku kas terkait akan dihapus.')) return
  router.post('/pengelola/diskon/redeem-poin/hapus-catatan', {
    source: r.source,
    redeem_id: r.id,
  }, { preserveScroll: true })
}

const exportUrl = computed(() => {
  const params = new URLSearchParams()
  if (filters.search) params.set('search', filters.search)
  if (filters.dari) params.set('dari', filters.dari)
  if (filters.sampai) params.set('sampai', filters.sampai)
  const qs = params.toString()
  return '/pengelola/diskon/redeem-poin/export' + (qs ? '?' + qs : '')
})

let searchTimeout = null
function debouncedSearch() {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => applyFilters(), 400)
}

function applyFilters() {
  const params = new URLSearchParams()
  if (filters.search) params.set('search', filters.search)
  if (filters.dari) params.set('dari', filters.dari)
  if (filters.sampai) params.set('sampai', filters.sampai)
  if (mode.value === 'campaign') params.set('mode', 'campaign')
  router.get('/pengelola/diskon/redeem-poin?' + params.toString())
}

function formatRupiah(val) {
  return Number(val || 0).toLocaleString('id-ID')
}

function formatDate(date) {
  if (!date) return '-'
  return new Date(date).toLocaleString('id-ID', {
    day: '2-digit', month: 'short', year: 'numeric',
    hour: '2-digit', minute: '2-digit',
  })
}

function cetakKuitansi(r) {
  const now = new Date().toLocaleString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' })
  const nilai = 'Rp ' + formatRupiah(r.nilai_potongan)
  const jenis = r.source === 'campaign'
    ? 'Campaign Voucher'
    : (r.source === 'voucher' ? 'Voucher Diskon' : 'Redeem Poin')
  const win = window.open('', '_blank', 'width=620,height=800')
  win.document.write(`<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Kuitansi Potongan</title>
<style>
  * { box-sizing: border-box; }
  body { font-family: Arial, sans-serif; color: #1e1400; margin: 0; padding: 24px; }
  .kuitansi { max-width: 520px; margin: 0 auto; border: 2px solid #996600; border-radius: 8px; padding: 24px; }
  .head { text-align: center; border-bottom: 2px solid #e0d1b2; padding-bottom: 12px; margin-bottom: 16px; }
  .head h1 { color: #996600; margin: 0; font-size: 20px; }
  .head p { margin: 4px 0 0; font-size: 12px; color: #6b4700; }
  .title { text-align: center; font-size: 16px; font-weight: bold; letter-spacing: 2px; margin: 16px 0; color: #6b4700; }
  table { width: 100%; border-collapse: collapse; font-size: 13px; }
  td { padding: 6px 4px; vertical-align: top; }
  td.label { color: #7a5100; width: 40%; }
  td.val { font-weight: 600; }
  .amount { margin-top: 16px; background: #f4efe5; border-radius: 6px; padding: 12px 16px; display: flex; justify-content: space-between; align-items: center; }
  .amount .lbl { font-size: 12px; color: #6b4700; }
  .amount .num { font-size: 20px; font-weight: bold; color: #996600; }
  .foot { margin-top: 28px; display: flex; justify-content: space-between; font-size: 12px; }
  .sign { text-align: center; width: 45%; }
  .sign .line { margin-top: 48px; border-top: 1px solid #1e1400; padding-top: 4px; }
</style>
</head>
<body>
  <div class="kuitansi">
    <div class="head">
      <h1>BPPU IKIP Siliwangi</h1>
      <p>Kuitansi Potongan Pembelian</p>
    </div>
    <div class="title">KUITANSI</div>
    <table>
      <tr><td class="label">Tanggal Potongan</td><td class="val">: ${formatDate(r.tanggal)}</td></tr>
      <tr><td class="label">Jenis Potongan</td><td class="val">: ${jenis}</td></tr>
      ${r.kode_voucher ? `<tr><td class="label">Kode Voucher</td><td class="val">: ${r.kode_voucher}</td></tr>` : ''}
      ${r.nama_potongan ? `<tr><td class="label">Nama Potongan</td><td class="val">: ${r.nama_potongan}</td></tr>` : ''}
      ${r.jumlah_voucher ? `<tr><td class="label">Jumlah Voucher</td><td class="val">: ${r.jumlah_voucher} voucher terpakai</td></tr>` : ''}
      ${r.poin ? `<tr><td class="label">Poin Ditukar</td><td class="val">: ${Number(r.poin).toLocaleString('id-ID')} poin</td></tr>` : ''}
      ${r.source !== 'campaign' ? `<tr><td class="label">Diberikan Kepada</td><td class="val">: ${r.member_name || '-'}</td></tr>` : ''}
      ${r.nomor_transaksi ? `<tr><td class="label">No. Transaksi</td><td class="val">: ${r.nomor_transaksi}</td></tr>` : ''}
      ${r.work_unit_name ? `<tr><td class="label">Unit Kerja</td><td class="val">: ${r.work_unit_name}</td></tr>` : ''}
    </table>
    <div class="amount">
      <span class="lbl">Nilai Potongan yang Ditanggung</span>
      <span class="num">${nilai}</span>
    </div>
    <div class="foot">
      <div class="sign">
        <div>Penerima</div>
        <div class="line">${r.source === 'campaign' ? '(.................)' : (r.member_name || '(.................)')}</div>
      </div>
      <div class="sign">
        <div>Dicetak ${now}</div>
        <div class="line">Pengelola</div>
      </div>
    </div>
  </div>
  <script>
    window.onload = function() { setTimeout(function(){ window.print(); }, 300); };
  <\/script>
</body>
</html>`)
  win.document.close()
}
</script>
