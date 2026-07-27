<template>
  <AdminLayout :page-title="`Voucher: ${potongan.nama}`" page-subtitle="Kelola penerbitan dan status voucher">
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
              <span class="ml-1 md:ml-2 font-medium text-gray-800">{{ potongan.nama }}</span>
            </div>
          </li>
        </ol>
      </nav>

      <!-- Info Potongan -->
      <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
        <div>
          <div class="flex flex-wrap gap-2 text-sm">
            <span class="font-mono bg-gray-100 px-2 py-0.5 rounded text-gray-700">{{ potongan.kode_potongan }}</span>
            <span class="bg-[#f4efe5] text-[#6b4700] px-2 py-0.5 rounded">
              {{ potongan.tipe === 'persen' ? potongan.nilai + '%' : 'Rp ' + formatRupiah(potongan.nilai) }}
            </span>
            <span v-if="potongan.minimum_transaksi > 0" class="bg-gray-100 text-gray-600 px-2 py-0.5 rounded">
              min Rp {{ formatRupiah(potongan.minimum_transaksi) }}
            </span>
            <span
              :class="[
                'px-2 py-0.5 rounded font-medium',
                potongan.is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'
              ]"
            >
              {{ potongan.is_active ? 'Aktif' : 'Nonaktif' }}
            </span>
          </div>
          <div v-if="barangList && barangList.length > 0" class="mt-2 flex flex-wrap gap-1">
            <span class="text-xs text-gray-500">Khusus:</span>
            <span
              v-for="b in barangList"
              :key="b.id"
              class="text-xs bg-blue-50 text-blue-700 px-2 py-0.5 rounded"
            >{{ b.nama_barang }}</span>
          </div>
        </div>

        <button
          @click="showGenerateModal = true"
          :disabled="stats.sisa_kuota <= 0"
          class="flex items-center gap-2 bg-[#996600] text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-[#7a5100] transition-colors disabled:opacity-40 disabled:cursor-not-allowed"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          Generate Voucher
          <span class="text-xs opacity-75">(sisa {{ stats.sisa_kuota }})</span>
        </button>
      </div>

      <!-- Stat Cards -->
      <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 text-center">
          <div class="text-2xl font-bold text-gray-700">{{ stats.draft }}</div>
          <div class="text-xs text-gray-500 mt-1">Draft</div>
        </div>
        <div class="bg-white rounded-xl border border-blue-100 shadow-sm p-4 text-center">
          <div class="text-2xl font-bold text-blue-600">{{ stats.printed }}</div>
          <div class="text-xs text-gray-500 mt-1">Dicetak</div>
        </div>
        <div class="bg-white rounded-xl border border-green-100 shadow-sm p-4 text-center">
          <div class="text-2xl font-bold text-green-600">{{ stats.used }}</div>
          <div class="text-xs text-gray-500 mt-1">Terpakai</div>
        </div>
        <div class="bg-white rounded-xl border border-purple-100 shadow-sm p-4 text-center">
          <div class="text-2xl font-bold text-purple-600">{{ stats.assigned }}</div>
          <div class="text-xs text-gray-500 mt-1">Di-assign</div>
        </div>
        <div class="bg-white rounded-xl border border-[#e0d1b2] shadow-sm p-4 text-center">
          <div class="text-2xl font-bold text-[#996600]">{{ stats.sisa_kuota }}</div>
          <div class="text-xs text-gray-500 mt-1">Sisa Kuota</div>
        </div>
      </div>

      <!-- Filter & Aksi Bulk -->
      <div class="flex flex-wrap items-center justify-between gap-3">
        <div class="flex gap-2 flex-wrap">
          <button
            v-for="s in statusOptions"
            :key="s.value"
            @click="filterStatus(s.value)"
            :class="[
              'px-3 py-1.5 rounded-lg text-sm font-medium transition-colors',
              filters.status === s.value
                ? 'bg-[#996600] text-white'
                : 'bg-white text-gray-600 border border-gray-300 hover:bg-gray-50'
            ]"
          >
            {{ s.label }}
          </button>
        </div>
        <div class="flex gap-2 flex-wrap">
          <template v-if="selectedIds.length > 0">
            <button
              @click="showAssignModal = true"
              class="flex items-center gap-1.5 bg-purple-600 text-white px-3 py-1.5 rounded-lg text-sm hover:bg-purple-700 transition-colors"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
              </svg>
              Assign ke Member ({{ selectedIds.length }})
            </button>
            <button
              @click="printSelected"
              class="flex items-center gap-1.5 bg-blue-600 text-white px-3 py-1.5 rounded-lg text-sm hover:bg-blue-700 transition-colors"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
              </svg>
              Cetak Terpilih ({{ selectedIds.length }})
            </button>
            <button
              @click="cetakPdf('pdf')"
              class="flex items-center gap-1.5 bg-red-600 text-white px-3 py-1.5 rounded-lg text-sm hover:bg-red-700 transition-colors"
            >
              PDF
            </button>
            <button
              @click="cetakPdf('thermal')"
              class="flex items-center gap-1.5 bg-gray-700 text-white px-3 py-1.5 rounded-lg text-sm hover:bg-gray-800 transition-colors"
            >
              Thermal
            </button>
          </template>
          <button
            v-if="stats.draft > 0"
            @click="confirmPrintAll = true"
            class="flex items-center gap-1.5 bg-white border border-blue-400 text-blue-600 px-3 py-1.5 rounded-lg text-sm hover:bg-blue-50 transition-colors"
          >
            Cetak Semua Draft
          </button>
        </div>
      </div>

      <!-- Tabel Voucher -->
      <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
        <div v-if="vouchers.data.length === 0" class="py-16 text-center text-gray-400">
          <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
          </svg>
          <p class="font-medium">Belum ada voucher</p>
          <p class="text-sm mt-1">Generate voucher untuk potongan ini</p>
        </div>

        <div class="overflow-x-auto">
          <table v-if="vouchers.data.length > 0" class="w-full text-sm">
            <thead class="bg-[#f4efe5] border-b border-[#e0d1b2]">
              <tr>
                <th class="px-4 py-3 w-8">
                  <input
                    type="checkbox"
                    :checked="allDraftSelected"
                    @change="toggleSelectAll"
                    class="w-4 h-4 accent-[#996600]"
                  />
                </th>
                <th class="px-4 py-3 text-left text-[#6b4700] font-semibold">Kode Voucher</th>
                <th class="px-4 py-3 text-center text-[#6b4700] font-semibold">Status</th>
                <th class="px-4 py-3 text-center text-[#6b4700] font-semibold">Assigned Ke</th>
                <th class="px-4 py-3 text-center text-[#6b4700] font-semibold">Dicetak</th>
                <th class="px-4 py-3 text-center text-[#6b4700] font-semibold">Dipakai Oleh</th>
                <th class="px-4 py-3 text-center text-[#6b4700] font-semibold">Waktu Pakai</th>
                <th class="px-4 py-3 text-right text-[#6b4700] font-semibold">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              <tr
                v-for="v in vouchers.data"
                :key="v.id"
                class="hover:bg-gray-50 transition-colors"
              >
                <td class="px-4 py-3">
                  <input
                    v-if="v.status !== 'used'"
                    type="checkbox"
                    :value="v.id"
                    v-model="selectedIds"
                    class="w-4 h-4 accent-[#996600]"
                  />
                </td>
                <td class="px-4 py-3">
                  <span class="font-mono text-sm font-semibold tracking-widest text-gray-800">{{ v.kode_voucher }}</span>
                </td>
                <td class="px-4 py-3 text-center">
                  <span :class="statusClass(v.status)">{{ statusLabel(v.status) }}</span>
                </td>
                <td class="px-4 py-3 text-center text-xs">
                  <template v-if="v.assigned_to">
                    <div class="font-medium text-purple-700">{{ v.assigned_to?.name }}</div>
                    <div class="text-gray-400">{{ v.assigned_to?.member_code || v.assigned_to?.nim }}</div>
                  </template>
                  <span v-else class="text-gray-400">-</span>
                </td>
                <td class="px-4 py-3 text-center text-xs text-gray-500">
                  {{ v.printed_at ? formatDatetime(v.printed_at) : '-' }}
                </td>
                <td class="px-4 py-3 text-center text-xs">
                  <span v-if="v.used_by">{{ v.used_by.name }}</span>
                  <span v-else-if="v.penjualan" class="text-gray-500">{{ v.penjualan.nomor_transaksi }}</span>
                  <span v-else class="text-gray-400">-</span>
                </td>
                <td class="px-4 py-3 text-center text-xs text-gray-500">
                  {{ v.used_at ? formatDatetime(v.used_at) : '-' }}
                </td>
                <td class="px-4 py-3 text-right">
                  <div class="flex justify-end gap-1.5">
                    <button
                      v-if="v.status !== 'used'"
                      @click="openAssignSingle(v)"
                      class="text-xs bg-purple-50 text-purple-600 px-2 py-1 rounded hover:bg-purple-100 transition-colors"
                    >
                      Assign
                    </button>
                    <button
                      v-if="v.assigned_to && v.status !== 'used'"
                      @click="doUnassign(v)"
                      class="text-xs bg-gray-50 text-gray-500 px-2 py-1 rounded hover:bg-gray-100 transition-colors"
                    >
                      Unassign
                    </button>
                    <button
                      v-if="v.status !== 'used'"
                      @click="cetakSatu(v, 'pdf')"
                      class="text-xs bg-red-50 text-red-600 px-2 py-1 rounded hover:bg-red-100 transition-colors"
                      title="Cetak PDF"
                    >
                      PDF
                    </button>
                    <button
                      v-if="v.status !== 'used'"
                      @click="cetakSatu(v, 'thermal')"
                      class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded hover:bg-gray-200 transition-colors"
                      title="Cetak Thermal"
                    >
                      Thermal
                    </button>
                    <button
                      v-if="v.status === 'draft'"
                      @click="confirmDeleteVoucher = v"
                      class="text-xs bg-red-50 text-red-600 px-2 py-1 rounded hover:bg-red-100 transition-colors"
                    >
                      Hapus
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div v-if="vouchers.last_page > 1" class="px-4 py-3 border-t border-gray-100 flex items-center justify-between text-sm text-gray-500">
          <span>{{ vouchers.from }}-{{ vouchers.to }} dari {{ vouchers.total }}</span>
          <div class="flex gap-1">
            <Link
              v-for="link in vouchers.links"
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

    <!-- Modal Generate -->
    <GenerateVoucherModal
      v-if="showGenerateModal"
      :potongan="potongan"
      :sisa-kuota="stats.sisa_kuota"
      @close="showGenerateModal = false"
      @generated="showGenerateModal = false"
    />

    <!-- Modal Assign Member -->
    <AssignMemberModal
      v-if="showAssignModal"
      :potongan-id="potongan.id"
      :voucher-ids="assignTargetIds"
      :voucher-single="assignSingleVoucher"
      @close="closeAssignModal"
    />

    <!-- Confirm Print All -->
    <ConfirmModal
      v-if="confirmPrintAll"
      title="Cetak Semua Draft"
      :message="`Tandai semua ${stats.draft} voucher draft sebagai sudah dicetak?`"
      confirm-label="Cetak Semua"
      :loading="printingAll"
      @confirm="doPrintAll"
      @cancel="confirmPrintAll = false"
    />

    <!-- Confirm Delete Voucher -->
    <ConfirmModal
      v-if="confirmDeleteVoucher"
      title="Hapus Voucher"
      :message="`Hapus voucher ${confirmDeleteVoucher.kode_voucher}?`"
      confirm-label="Hapus"
      :loading="deletingVoucher"
      @confirm="doDeleteVoucher"
      @cancel="confirmDeleteVoucher = null"
    />
  </AdminLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import ConfirmModal from '@/Components/ConfirmModal.vue'
import GenerateVoucherModal from './GenerateVoucherModal.vue'
import AssignMemberModal from './AssignMemberModal.vue'

const props = defineProps({
  potongan: Object,
  vouchers: Object,
  filters: Object,
  stats: Object,
  barangList: { type: Array, default: () => [] },
})

const showGenerateModal = ref(false)
const showAssignModal = ref(false)
const selectedIds = ref([])
const confirmPrintAll = ref(false)
const printingAll = ref(false)
const confirmDeleteVoucher = ref(null)
const deletingVoucher = ref(false)
const assignTargetIds = ref([])
const assignSingleVoucher = ref(null)

const statusOptions = [
  { value: '', label: 'Semua' },
  { value: 'draft', label: 'Draft' },
  { value: 'printed', label: 'Dicetak' },
  { value: 'used', label: 'Terpakai' },
]

const selectableVouchers = computed(() => props.vouchers.data.filter(v => v.status !== 'used'))
const allDraftSelected = computed(() =>
  selectableVouchers.value.length > 0 &&
  selectableVouchers.value.every(v => selectedIds.value.includes(v.id))
)

function toggleSelectAll() {
  if (allDraftSelected.value) {
    selectedIds.value = []
  } else {
    selectedIds.value = selectableVouchers.value.map(v => v.id)
  }
}

function filterStatus(val) {
  const params = new URLSearchParams()
  if (val) params.set('status', val)
  window.location.href = `/pengelola/diskon/${props.potongan.id}/vouchers?` + params.toString()
}

function printSelected() {
  router.post(
    `/pengelola/diskon/${props.potongan.id}/vouchers/print`,
    { voucher_ids: selectedIds.value },
    { preserveScroll: true, onSuccess: () => { selectedIds.value = [] } }
  )
}

function doPrintAll() {
  printingAll.value = true
  router.post(
    `/pengelola/diskon/${props.potongan.id}/vouchers/print-all`,
    {},
    {
      preserveScroll: true,
      onFinish: () => {
        printingAll.value = false
        confirmPrintAll.value = false
      },
    }
  )
}

function cetakPdf(format) {
  submitPrintForm(selectedIds.value, format)
}

function cetakSatu(v, format) {
  submitPrintForm([v.id], format)
}

function submitPrintForm(ids, format) {
  const form = document.createElement('form')
  form.method = 'POST'
  form.action = `/pengelola/diskon/${props.potongan.id}/vouchers/print-pdf`
  form.target = '_blank'

  const csrfInput = document.createElement('input')
  csrfInput.type = 'hidden'
  csrfInput.name = '_token'
  csrfInput.value = document.querySelector('meta[name="csrf-token"]')?.content || ''
  form.appendChild(csrfInput)

  ids.forEach(id => {
    const input = document.createElement('input')
    input.type = 'hidden'
    input.name = 'voucher_ids[]'
    input.value = id
    form.appendChild(input)
  })

  const formatInput = document.createElement('input')
  formatInput.type = 'hidden'
  formatInput.name = 'format'
  formatInput.value = format
  form.appendChild(formatInput)

  document.body.appendChild(form)
  form.submit()
  document.body.removeChild(form)
}

function openAssignSingle(v) {
  assignSingleVoucher.value = v
  assignTargetIds.value = [v.id]
  showAssignModal.value = true
}

function openAssignBulk() {
  assignSingleVoucher.value = null
  assignTargetIds.value = [...selectedIds.value]
  showAssignModal.value = true
}

function closeAssignModal() {
  showAssignModal.value = false
  assignSingleVoucher.value = null
  assignTargetIds.value = []
}

function doUnassign(v) {
  router.delete(
    `/pengelola/diskon/${props.potongan.id}/vouchers/${v.id}/unassign`,
    { preserveScroll: true }
  )
}

function doDeleteVoucher() {
  if (!confirmDeleteVoucher.value) return
  deletingVoucher.value = true
  router.delete(
    `/pengelola/diskon/${props.potongan.id}/vouchers/${confirmDeleteVoucher.value.id}`,
    {
      preserveScroll: true,
      onFinish: () => {
        deletingVoucher.value = false
        confirmDeleteVoucher.value = null
      },
    }
  )
}

function statusClass(status) {
  return {
    draft: 'text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-600',
    printed: 'text-xs px-2 py-0.5 rounded-full bg-blue-100 text-blue-700',
    used: 'text-xs px-2 py-0.5 rounded-full bg-green-100 text-green-700',
  }[status]
}

function statusLabel(status) {
  return { draft: 'Draft', printed: 'Dicetak', used: 'Terpakai' }[status] || status
}

function formatRupiah(val) {
  return Number(val).toLocaleString('id-ID')
}

function formatDatetime(dt) {
  if (!dt) return '-'
  return new Date(dt).toLocaleString('id-ID', {
    day: '2-digit', month: 'short', year: 'numeric',
    hour: '2-digit', minute: '2-digit',
  })
}
</script>
