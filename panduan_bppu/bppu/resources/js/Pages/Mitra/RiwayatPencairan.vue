<template>
  <MitraLayout>
    <div class="space-y-4">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-lg font-bold text-gray-900">Riwayat Pencairan Dana</h1>
          <p class="text-xs text-gray-500 mt-0.5">{{ supplier.nama }} &bull; {{ supplier.kode }}</p>
        </div>
        <Link :href="route('mitra.dasbor')" class="text-sm text-[#996600] font-medium hover:underline">
          Kembali
        </Link>
      </div>

      <!-- Ringkasan -->
      <div class="grid grid-cols-2 gap-3">
        <div class="bg-green-50 rounded-xl border border-green-100 shadow-sm p-4">
          <p class="text-xs text-green-600">Sudah Dicairkan</p>
          <p class="text-lg font-bold text-green-700">{{ formatCurrency(ringkasan.total_sudah_cair) }}</p>
          <p class="text-[10px] text-green-500 mt-0.5">{{ ringkasan.jumlah_sudah_cair }} periode</p>
        </div>
        <div class="bg-orange-50 rounded-xl border border-orange-100 shadow-sm p-4">
          <p class="text-xs text-orange-600">Belum Dicairkan</p>
          <p class="text-lg font-bold text-orange-600">{{ formatCurrency(ringkasan.total_belum_cair) }}</p>
          <p class="text-[10px] text-orange-400 mt-0.5">{{ ringkasan.jumlah_belum_cair }} periode tercatat</p>
        </div>
        <div class="bg-red-50 rounded-xl border border-red-100 shadow-sm p-4" v-if="ringkasan.total_pengajuan > 0">
          <p class="text-xs text-red-600">Pengajuan Aktif</p>
          <p class="text-lg font-bold text-red-700">{{ formatCurrency(ringkasan.jumlah_pengajuan_diajukan) }}</p>
          <p class="text-[10px] text-red-400 mt-0.5">{{ ringkasan.total_pengajuan }} pengajuan menunggu</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
          <p class="text-xs text-gray-500">Total Hak Saya</p>
          <p class="text-lg font-bold text-gray-800">{{ formatCurrency(ringkasan.total_bagian_vendor) }}</p>
          <p class="text-[10px] text-gray-400 mt-0.5">dari periode yang tercatat</p>
        </div>
      </div>

      <!-- Pengajuan Pencairan -->
      <div v-if="pengajuan && pengajuan.length > 0" class="space-y-2">
        <h2 class="text-sm font-semibold text-gray-700">Pengajuan Pencairan Saya</h2>
        <div
          v-for="item in pengajuan"
          :key="item.id"
          :class="[
            'bg-white rounded-xl border shadow-sm p-4',
            item.status === 'menunggu' ? 'border-red-200' :
            item.status === 'diproses' ? 'border-yellow-300' :
            item.status === 'selesai'  ? 'border-green-200' : 'border-gray-200'
          ]"
        >
          <div class="flex items-start justify-between gap-2">
            <div class="flex-1 min-w-0">
              <div class="flex items-center gap-2 mb-1">
                <span class="text-[9px] font-mono text-gray-400 bg-gray-100 px-1.5 py-0.5 rounded">#{{ item.id }}</span>
                <span :class="statusPengajuanBadge(item.status)" class="inline-flex items-center gap-1 px-2 py-0.5 text-[9px] font-semibold rounded-full border">
                  <span :class="statusPengajuanDot(item.status)" class="w-1.5 h-1.5 rounded-full"></span>
                  {{ statusPengajuanLabel(item.status) }}
                </span>
              </div>
              <p class="text-sm font-semibold text-gray-800">
                {{ formatDateShort(item.tanggal_dari) }} &mdash; {{ formatDateShort(item.tanggal_sampai) }}
              </p>
              <p class="text-xs text-gray-500 mt-0.5">
                {{ item.metode === 'transfer_bank' ? 'Transfer Bank' : 'Tunai' }}
                <span v-if="item.detail_rekening"> &bull; {{ item.detail_rekening }}</span>
              </p>
              <p v-if="item.catatan" class="text-xs text-gray-400 mt-0.5">{{ item.catatan }}</p>
            </div>
            <div class="text-right flex-shrink-0">
              <p class="text-xs text-gray-400">Jumlah</p>
              <p class="text-base font-bold text-[#996600]">{{ formatCurrency(item.jumlah_diajukan) }}</p>
            </div>
          </div>
          <div v-if="item.status === 'menunggu'" class="mt-2 pt-2 border-t border-gray-100">
            <button
              @click="batalkanPengajuan(item.id)"
              class="text-xs text-red-500 hover:text-red-700 font-medium transition-colors"
            >
              Batalkan Pengajuan
            </button>
          </div>
        </div>
      </div>

      <!-- List riwayat -->
      <div v-if="riwayat.data.length === 0" class="bg-white rounded-xl border border-gray-100 shadow-sm p-10 text-center">
        <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z" />
        </svg>
        <p class="text-sm font-medium text-gray-500 mb-1">Belum ada periode pembagian tercatat</p>
        <p class="text-xs text-gray-400 max-w-xs mx-auto">Pengelola BPPU belum menyimpan riwayat pembagian untuk akun Anda. Silakan hubungi pengelola jika Anda sudah memiliki hak pencairan.</p>
      </div>

      <div v-else class="space-y-3">
        <div
          v-for="item in riwayat.data"
          :key="item.id"
          :class="[
            'bg-white rounded-xl border shadow-sm overflow-hidden',
            item.status_pencairan === 'dicairkan' ? 'border-green-200' : item.status_pencairan === 'mengajukan' ? 'border-yellow-300' : 'border-gray-200'
          ]"
        >
          <!-- Content -->
          <div class="p-4 space-y-3">
            <div class="flex items-start justify-between">
              <div>
                <p class="text-sm font-semibold text-gray-800">
                  {{ formatDateShort(item.periode_mulai) }} &mdash; {{ formatDateShort(item.periode_selesai) }}
                </p>
                <p class="text-xs text-gray-400 mt-0.5">
                  {{ item.work_unit?.name || 'Semua Unit' }} &bull; {{ item.jenis_skema }}
                  &bull; {{ item.total_items }} item ({{ item.total_qty }} qty)
                </p>
              </div>
              <div class="text-right">
                <p class="text-xs text-gray-400">Hak saya</p>
                <p class="text-base font-bold text-green-700">{{ formatCurrency(item.bagian_vendor) }}</p>
                <!-- Status badge -->
                <span :class="statusBadgeClass(item.status_pencairan)" class="inline-flex items-center gap-1 mt-1 px-2 py-0.5 text-[9px] font-semibold rounded-full border">
                  <span :class="statusDotClass(item.status_pencairan)" class="w-1.5 h-1.5 rounded-full"></span>
                  {{ statusLabel(item.status_pencairan) }}
                </span>
              </div>
            </div>

            <div class="border-t border-gray-100 pt-2 flex items-center justify-between text-xs text-gray-500">
              <span>Total Penjualan: <span class="font-medium text-gray-700">{{ formatCurrency(item.total_penjualan) }}</span></span>
              <span v-if="item.status_pencairan === 'dicairkan' && item.metode_pencairan" class="flex items-center gap-1">
                <span :class="item.metode_pencairan === 'transfer_bank' ? 'text-blue-600' : 'text-green-600'" class="font-medium">
                  {{ item.metode_pencairan === 'transfer_bank' ? 'Transfer Bank' : 'Tunai' }}
                </span>
                <span v-if="item.detail_transfer">- {{ item.detail_transfer }}</span>
              </span>
            </div>

            <div v-if="item.catatan_pencairan" class="bg-gray-50 rounded-lg px-3 py-2 text-xs text-gray-600">
              <span class="font-medium">Catatan: </span>{{ item.catatan_pencairan }}
            </div>

            <div v-if="item.status_pencairan === 'dicairkan'" class="text-[10px] text-gray-400 text-right">
              Dicairkan {{ formatDate(item.dicairkan_at) }}
              <span v-if="item.dicairkan_oleh">oleh {{ item.dicairkan_oleh.name }}</span>
            </div>

            <!-- Tombol aksi -->
            <div v-if="item.status_pencairan === 'belum_dicairkan'" class="pt-1">
              <button
                @click="ajukan(item.id)"
                class="w-full py-2 text-xs font-semibold text-[#996600] border border-[#996600] rounded-lg hover:bg-[#f4efe5] transition-colors"
              >
                Ajukan Pencairan
              </button>
            </div>
            <div v-else-if="item.status_pencairan === 'mengajukan'" class="pt-1 flex gap-2">
              <div class="flex-1 py-2 text-xs font-semibold text-yellow-700 bg-yellow-50 border border-yellow-200 rounded-lg text-center">
                Pengajuan terkirim, menunggu konfirmasi
              </div>
              <button
                @click="batalAjuan(item.id)"
                class="px-3 py-2 text-xs text-gray-500 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors"
              >
                Batal
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Pagination -->
      <div v-if="riwayat.last_page > 1" class="flex justify-center gap-2 pt-2">
        <Link
          v-if="riwayat.prev_page_url"
          :href="riwayat.prev_page_url"
          class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm hover:bg-gray-50"
        >
          Sebelumnya
        </Link>
        <Link
          v-if="riwayat.next_page_url"
          :href="riwayat.next_page_url"
          class="px-4 py-2 bg-[#996600] text-white rounded-lg text-sm hover:bg-[#7a5100]"
        >
          Selanjutnya
        </Link>
      </div>
    </div>
  </MitraLayout>
</template>

<script setup>
import { router } from '@inertiajs/vue3'
import { Link } from '@inertiajs/vue3'
import MitraLayout from '@/Layouts/MitraLayout.vue'

defineProps({
  supplier:  Object,
  riwayat:   Object,
  ringkasan: Object,
  pengajuan: Array,
})

const formatCurrency = (value) =>
  new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value || 0)

const formatDateShort = (dateStr) => {
  if (!dateStr) return '-'
  return new Date(dateStr).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' })
}

const formatDate = (dateStr) => {
  if (!dateStr) return '-'
  return new Date(dateStr).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })
}

const statusLabel = (status) => {
  if (status === 'dicairkan') return 'Sudah Cair'
  if (status === 'mengajukan') return 'Mengajukan'
  return 'Belum Dicairkan'
}

const statusBadgeClass = (status) => {
  if (status === 'dicairkan') return 'bg-green-50 text-green-700 border-green-300'
  if (status === 'mengajukan') return 'bg-yellow-50 text-yellow-700 border-yellow-300'
  return 'bg-gray-50 text-gray-500 border-gray-200'
}

const statusDotClass = (status) => {
  if (status === 'dicairkan') return 'bg-green-500'
  if (status === 'mengajukan') return 'bg-yellow-500'
  return 'bg-gray-400'
}

const statusPengajuanLabel = (status) => {
  const map = { menunggu: 'Menunggu', diproses: 'Diproses', selesai: 'Selesai', ditolak: 'Ditolak' }
  return map[status] ?? status
}

const statusPengajuanBadge = (status) => {
  if (status === 'menunggu') return 'bg-red-50 text-red-600 border-red-300'
  if (status === 'diproses') return 'bg-yellow-50 text-yellow-700 border-yellow-300'
  if (status === 'selesai')  return 'bg-green-50 text-green-700 border-green-300'
  return 'bg-gray-50 text-gray-500 border-gray-200'
}

const statusPengajuanDot = (status) => {
  if (status === 'menunggu') return 'bg-red-500 animate-pulse'
  if (status === 'diproses') return 'bg-yellow-500'
  if (status === 'selesai')  return 'bg-green-500'
  return 'bg-gray-400'
}

const batalkanPengajuan = (id) => {
  router.delete(route('mitra.batalkanPengajuanBaru', id), { preserveScroll: true })
}

const ajukan = (id) => {
  router.post(route('mitra.ajukanPencairan', id), {}, {
    preserveScroll: true,
  })
}

const batalAjuan = (id) => {
  router.post(route('mitra.batalkanAjuan', id), {}, {
    preserveScroll: true,
  })
}
</script>
