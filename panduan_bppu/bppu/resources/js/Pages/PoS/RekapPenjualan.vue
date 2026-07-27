<template>
  <component :is="layoutComponent" pageTitle="Rekap Penjualan">
    <template #sub-navbar>
      <div class="border-t border-[#eae0cc] bg-[#f4efe5] px-4 py-1 flex items-center gap-1.5 overflow-x-auto">
        <Link
          href="/pengelola/buku-tagihan"
          class="flex items-center gap-1 px-2.5 py-0.5 text-[11px] font-medium rounded-full bg-white border border-[#d6c199] text-[#6b4700] hover:bg-[#eae0cc] transition-colors whitespace-nowrap shrink-0"
        >
          <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
          Tagihan
        </Link>
        <Link
          href="/pengelola/pembagian-mitra"
          class="flex items-center gap-1 px-2.5 py-0.5 text-[11px] font-medium rounded-full bg-white border border-[#d6c199] text-[#6b4700] hover:bg-[#eae0cc] transition-colors whitespace-nowrap shrink-0"
        >
          <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
          </svg>
          Pembagian
        </Link>
        <button
          @click="openItemSummaryModal"
          class="flex items-center gap-1 px-2.5 py-0.5 text-[11px] font-medium rounded-full bg-white border border-[#d6c199] text-[#6b4700] hover:bg-[#eae0cc] transition-colors whitespace-nowrap shrink-0"
        >
          <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
          </svg>
          Ringkasan
        </button>
        <button
          v-if="canApprove"
          @click="downloadPdf"
          class="flex items-center gap-1 px-2.5 py-0.5 text-[11px] font-medium rounded-full bg-white border border-[#d6c199] text-[#6b4700] hover:bg-[#eae0cc] transition-colors whitespace-nowrap shrink-0"
        >
          <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
          </svg>
          Detail Rekapitulasi
        </button>
      </div>
    </template>

    <div class="bg-gray-50">
      <!-- Header Summary -->
      <HeaderSummary
        :summary="filteredSummary"
        :filters="filters"
        :work-units="workUnits"
        :is-kantin-user="isKantinUser"
        :is-today="isToday"
        :is-this-week="isThisWeek"
        :is-this-month="isThisMonth"
        :show-verified-only="showVerifiedOnly"
        :active-payment-filters="activePaymentFilters"
        :can-download-pdf="canApprove"
        @update:filters="updateFilters"
        @set-today="setToday"
        @set-week="setThisWeek"
        @set-month="setThisMonth"
        @reset="resetFilters"
        @show-summary="openItemSummaryModal"
        @toggle-verified-filter="toggleVerifiedFilter"
        @toggle-payment-filter="togglePaymentFilter"
      />

      <!-- Bulk Actions Bar -->
      <div class="px-3">
        <BulkActionsBar
          :can-verify="canVerify"
          :can-approve="canApprove"
          :selected-count="selectedTransactions.length"
          :selected-for-approval-count="selectedForApproval.length"
          @verify-selected="verifySelected"
          @clear-selection="clearSelection"
          @approve-selected="approveSelected"
          @clear-approval-selection="clearApprovalSelection"
        />
      </div>

      <!-- Transaction Cards Grid -->
      <div class="p-3">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 mb-4">
          <TransactionCard
            v-for="penjualan in filteredPenjualans.data"
            :key="penjualan.id"
            :penjualan="penjualan"
            :is-selected="selectedTransactions.includes(penjualan.id)"
            :selected-for-approval="selectedForApproval.includes(penjualan.id)"
            :permissions="{
              canBulkVerify,
              canVerify,
              canApprove,
              isKantinUser,
              canVerifyTransaction,
              canPrintReceipt,
              canCancel
            }"
            @toggle-selection="handleToggleSelection"
            @open-detail="openDetailModal"
          >
            <template #actions="{ penjualan: p }">
              <div class="flex gap-2 pt-2 flex-wrap">
                <!-- Print Struk -->
                <button
                  v-if="canPrintReceipt(p)"
                  @click="printReceipt(p)"
                  class="px-3 py-1.5 text-xs bg-[#996600] text-white rounded hover:bg-[#7a5100] font-medium flex items-center gap-1.5"
                >
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                  </svg>
                  Struk
                </button>

                <!-- Cetak Invoice (officer/sysadmin only) -->
                <button
                  v-if="canApprove"
                  @click="openInvoiceModal(p)"
                  class="px-3 py-1.5 text-xs bg-[#eae0cc] text-[#6b4700] rounded hover:bg-[#d6c199] font-medium flex items-center gap-1.5"
                >
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                  </svg>
                  Invoice
                </button>

                <!-- Tetapkan -->
                <button
                  v-if="canBulkVerify && !p.is_verified && !p.buyer_id"
                  @click="openAssignMemberModal(p)"
                  class="flex-1 px-2 py-1.5 text-xs bg-indigo-50 text-indigo-600 rounded hover:bg-indigo-100 font-medium flex items-center justify-center gap-1"
                  title="Tetapkan Member"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                  </svg>
                </button>

                <!-- Verifikasi -->
                <button
                  v-if="canVerify && !p.is_verified && canVerifyTransaction(p)"
                  @click="openVerifyModal(p.id)"
                  class="flex-1 px-3 py-1.5 text-xs bg-green-50 text-green-600 rounded hover:bg-green-100 font-medium"
                >
                  Verifikasi
                </button>

                <!-- Batal Verifikasi -->
                <button
                  v-if="canBulkVerify && p.is_verified && !p.is_approved && !p.is_recorded"
                  @click="unverifyTransaction(p.id)"
                  class="flex-1 px-3 py-1.5 text-xs bg-orange-50 text-orange-600 rounded hover:bg-orange-100 font-medium"
                >
                  Batal Verif
                </button>

                <!-- Setujui -->
                <button
                  v-if="canApprove && p.is_verified && !p.is_approved"
                  @click="approveSingle(p.id)"
                  class="flex-1 px-3 py-1.5 text-xs bg-blue-50 text-blue-600 rounded hover:bg-blue-100 font-medium"
                >
                  Setujui
                </button>

                <!-- Batal Setujui -->
                <button
                  v-if="canApprove && p.is_approved && !p.is_recorded"
                  @click="unapproveTransaction(p.id)"
                  class="flex-1 px-3 py-1.5 text-xs bg-red-50 text-red-600 rounded hover:bg-red-100 font-medium"
                >
                  Batal Setuju
                </button>

                <!-- Rekam -->
                <button
                  v-if="canBulkVerify && p.is_approved && !p.is_recorded"
                  @click="openRecordModal(p)"
                  class="flex-1 px-3 py-1.5 text-xs bg-purple-50 text-purple-600 rounded hover:bg-purple-100 font-medium"
                >
                  Catat
                </button>

                <!-- Batalkan -->
                <button
                  v-if="canCancel(p)"
                  @click="showCancelModal(p)"
                  class="flex-1 px-3 py-1.5 text-xs bg-red-50 text-red-600 rounded hover:bg-red-100 font-medium"
                >
                  Batalkan
                </button>
              </div>
            </template>
          </TransactionCard>
        </div>

        <!-- Empty State -->
        <div v-if="filteredPenjualans.data.length === 0" class="text-center py-12">
          <svg class="w-16 h-16 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
          </svg>
          <p class="text-gray-500 text-sm">Tidak ada transaksi</p>
          <p class="text-gray-400 text-xs mt-1">Coba ubah filter tanggal</p>
        </div>

        <!-- Pagination -->
        <div v-if="filteredPenjualans.data.length > 0 && (filteredPenjualans.next_page_url || filteredPenjualans.prev_page_url)" class="pt-2 pb-4">
          <div class="flex gap-2 justify-center">
            <Link
              v-if="filteredPenjualans.prev_page_url"
              :href="filteredPenjualans.prev_page_url"
              class="px-4 py-2 text-sm bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50"
            >
              ← Sebelumnya
            </Link>
            <Link
              v-if="filteredPenjualans.next_page_url"
              :href="filteredPenjualans.next_page_url"
              class="px-4 py-2 text-sm bg-[#996600] text-white rounded-lg hover:bg-[#7a5100]"
            >
              Selanjutnya →
            </Link>
          </div>
          <div class="text-center mt-2 text-xs text-gray-500">
            {{ filteredPenjualans.from }}-{{ filteredPenjualans.to }} dari {{ filteredPenjualans.total }}
          </div>
        </div>
      </div>

      <!-- Modals -->
      <VerifyModal
        v-if="showVerifyModal"
        :penjualan="verifyingPenjualan"
        @close="closeVerifyModal"
        @confirm="confirmVerify"
      />

      <DetailModal
        v-if="showDetailModal"
        :penjualan="detailPenjualan"
        @close="closeDetailModal"
      />

      <ItemSummaryModal
        v-if="showItemSummaryModal"
        :item-summary="itemSummary"
        :date-range="getCurrentDateRange(formatDateIndo)"
        @close="closeItemSummaryModal"
        @print="printItemSummary"
        @download-csv="downloadCSV"
      />

      <CancelConfirmModal
        :show="showCancelConfirmModal"
        :penjualan="selectedPenjualan"
        @close="closeCancelModal"
        @confirm="confirmCancel"
      />

      <AssignMemberModal
        v-if="showAssignMemberModal"
        :penjualan="selectedPenjualan"
        :buyers="buyers"
        @close="closeAssignMemberModal"
        @confirm="confirmAssignMember"
      />

      <RecordToBukuKasModal
        :show="showRecordModal"
        :penjualan="selectedPenjualan"
        :buku-kas-list="bukuKasList"
        @close="closeRecordModal"
        @confirm="confirmRecord"
      />

      <InvoicePrintModal
        :show="showInvoiceModal"
        :penjualan="invoicePenjualan"
        @close="closeInvoiceModal"
      />
    </div>
  </component>
</template>

<script setup>
import { ref, computed } from 'vue'
import { usePage, Link, router } from '@inertiajs/vue3'
import { useToast } from 'vue-toastification'
import { useThermalPrinter } from '@/composables/useThermalPrinter'
import { useRekapFilters } from '@/composables/useRekapFilters'
import { useRekapActions } from '@/composables/useRekapActions'
import { useFormatters } from '@/composables/useFormatters'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import CanteenLayout from '@/Layouts/CanteenLayout.vue'
import ShopLayout from '@/Layouts/ShopLayout.vue'
import HeaderSummary from '@/Components/RekapPenjualan/HeaderSummary.vue'
import BulkActionsBar from '@/Components/RekapPenjualan/BulkActionsBar.vue'
import TransactionCard from '@/Components/RekapPenjualan/TransactionCard.vue'
import DetailModal from '@/Components/RekapPenjualan/DetailModal.vue'
import ItemSummaryModal from '@/Components/RekapPenjualan/ItemSummaryModal.vue'
import VerifyModal from '@/Components/RekapPenjualan/VerifyModal.vue'
import CancelConfirmModal from '@/Components/RekapPenjualan/CancelConfirmModal.vue'
import AssignMemberModal from '@/Components/RekapPenjualan/AssignMemberModal.vue'
import RecordToBukuKasModal from '@/Components/RekapPenjualan/RecordToBukuKasModal.vue'
import InvoicePrintModal from '@/Components/RekapPenjualan/InvoicePrintModal.vue'

const toast = useToast()
const page = usePage()
const { printReceipt: printThermalReceipt } = useThermalPrinter()

const props = defineProps({
  workUnits: Array,
  selectedWorkUnitId: Number,
  startDate: String,
  endDate: String,
  penjualans: Object,
  summary: Object,
  itemSummary: Array,
  buyers: Array,
  bukuKasList: Array,
})

// Layout
const layoutComponent = computed(() => {
  const user = page.props.auth?.user
  if (user?.roles?.includes('canteen') && !user?.roles?.includes('officer') && !user?.roles?.includes('sysadmin')) {
    return CanteenLayout
  }
  if (user?.roles?.includes('shop') && !user?.roles?.includes('officer') && !user?.roles?.includes('sysadmin')) {
    return ShopLayout
  }
  return AdminLayout
})

// User permissions
const isKantinUser = computed(() => {
  const user = page.props.auth?.user
  return user?.roles?.includes('canteen') && !user?.roles?.includes('officer') && !user?.roles?.includes('sysadmin')
})

const canVerify = computed(() => {
  const user = page.props.auth?.user
  return user?.roles?.includes('canteen') || user?.roles?.includes('shop') || user?.roles?.includes('officer') || user?.roles?.includes('sysadmin')
})

const canBulkVerify = computed(() => {
  const user = page.props.auth?.user
  // Kantin, Shop, Officer, Sysadmin bisa bulk verify
  return user?.roles?.includes('canteen') || user?.roles?.includes('shop') || user?.roles?.includes('officer') || user?.roles?.includes('sysadmin')
})

const canApprove = computed(() => {
  const user = page.props.auth?.user
  return user?.roles?.includes('officer') || user?.roles?.includes('sysadmin')
})

const canPrintReceipt = (penjualan) => {
  const user = page.props.auth?.user
  if (user?.roles?.includes('officer') || user?.roles?.includes('sysadmin')) return true
  if (user?.roles?.includes('canteen') || user?.roles?.includes('shop')) return penjualan.user_id === user.id
  return false
}

const canVerifyTransaction = (penjualan) => {
  const user = page.props.auth?.user
  if (user?.roles?.includes('officer') || user?.roles?.includes('sysadmin')) return true
  if (user?.roles?.includes('canteen') || user?.roles?.includes('shop')) return penjualan.user_id === user.id
  return false
}

const canCancel = (penjualan) => {
  // Can't cancel if already recorded
  if (penjualan.is_recorded) return false

  const user = page.props.auth?.user

  // Sysadmin dan officer bisa cancel transaksi apapun (selama belum recorded)
  if (user?.roles?.includes('officer') || user?.roles?.includes('sysadmin')) return true

  // Kantin & Shop hanya bisa cancel transaksi mereka sendiri yang belum verified
  if ((user?.roles?.includes('canteen') || user?.roles?.includes('shop')) && penjualan.user_id === user.id && !penjualan.is_verified) return true

  return false
}

// Composables
const { formatCurrency, formatDateIndo } = useFormatters()

const {
  filters,
  applyFilters,
  setToday,
  setThisWeek,
  setThisMonth,
  resetFilters,
  isToday,
  isThisWeek,
  isThisMonth,
  getCurrentDateRange
} = useRekapFilters(props, isKantinUser)

const {
  selectedTransactions,
  selectedForApproval,
  toggleSelection,
  clearSelection,
  toggleApprovalSelection,
  clearApprovalSelection,
  verifySingle,
  verifySelected,
  verifyAll,
  unverifyTransaction,
  approveSingle,
  approveSelected,
  unapproveTransaction,
  recordSingle,
  cancelTransaction,
  assignMember
} = useRekapActions(toast)

// Local state
const showVerifyModal = ref(false)
const showDetailModal = ref(false)
const showInvoiceModal = ref(false)
const invoicePenjualan = ref(null)
const detailPenjualan = ref(null)
const showItemSummaryModal = ref(false)
const showCancelConfirmModal = ref(false)
const selectedPenjualan = ref(null)
const showAssignMemberModal = ref(false)
const verifyingId = ref(null)
const verifyingPenjualan = ref(null)
const showVerifiedOnly = ref(false)
const activePaymentFilters = ref([])
const showRecordModal = ref(false)

// Filter handlers
const updateFilters = (newFilters) => {
  filters.value = newFilters
  applyFilters()
}

const toggleVerifiedFilter = () => {
  showVerifiedOnly.value = !showVerifiedOnly.value

  const params = isKantinUser.value
    ? {
        work_unit_id: filters.value.work_unit_id,
        start_date: filters.value.date,
        end_date: filters.value.date,
        verified_only: showVerifiedOnly.value ? 1 : null
      }
    : {
        ...filters.value,
        verified_only: showVerifiedOnly.value ? 1 : null
      }

  router.get('/pengelola/rekap-penjualan', params, {
    preserveState: true,
    preserveScroll: true,
  })
}

const togglePaymentFilter = (paymentMethod) => {
  // Toggle untuk exclude/nonaktifkan metode pembayaran (checkbox behavior)
  // Yang diklik = yang di-exclude dari perhitungan
  const index = activePaymentFilters.value.indexOf(paymentMethod)
  if (index > -1) {
    // Sudah ada, remove (uncheck)
    activePaymentFilters.value.splice(index, 1)
  } else {
    // Belum ada, add (check)
    activePaymentFilters.value.push(paymentMethod)
  }
}

// Filtered data berdasarkan payment method
// Yang di-filter adalah yang TIDAK ada dalam activePaymentFilters (yang diklik di-exclude)
const filteredPenjualans = computed(() => {
  if (activePaymentFilters.value.length === 0) {
    return props.penjualans
  }

  return {
    ...props.penjualans,
    data: props.penjualans.data.filter(p => !activePaymentFilters.value.includes(p.metode_pembayaran))
  }
})

// Filtered summary
// Yang di-exclude adalah semua metode pembayaran yang diklik (multiple)
const filteredSummary = computed(() => {
  if (activePaymentFilters.value.length === 0) {
    return props.summary
  }

  // Hitung total excluded amount dan count dari semua payment method yang di-filter
  const totalExcluded = activePaymentFilters.value.reduce((sum, method) => {
    return sum + (props.summary[method] || 0)
  }, 0)

  const totalExcludedCount = activePaymentFilters.value.reduce((sum, method) => {
    return sum + (props.summary[`${method}_count`] || 0)
  }, 0)

  return {
    ...props.summary,
    total_transaksi: props.summary.total_transaksi - totalExcludedCount,
    total_penjualan: props.summary.total_penjualan - totalExcluded,
    // TETAP TAMPILKAN nilai aslinya, jangan di-set ke 0
    // Card akan di-render disabled via CSS di component HeaderSummary
  }
})

// Selection handlers
const handleToggleSelection = (id, isApproval) => {
  if (isApproval) {
    toggleApprovalSelection(id)
  } else {
    toggleSelection(id)
  }
}

// Modal handlers
const openVerifyModal = (id) => {
  const penjualan = filteredPenjualans.value.data.find(p => p.id === id)
  verifyingId.value = id
  verifyingPenjualan.value = penjualan
  showVerifyModal.value = true
}

const closeVerifyModal = () => {
  showVerifyModal.value = false
  verifyingId.value = null
  verifyingPenjualan.value = null
}

const confirmVerify = (data) => {
  verifySingle(verifyingId.value, data, closeVerifyModal)
}

const openDetailModal = (penjualan) => {
  detailPenjualan.value = penjualan
  showDetailModal.value = true
}

const closeDetailModal = () => {
  showDetailModal.value = false
  detailPenjualan.value = null
}

const openItemSummaryModal = () => {
  showItemSummaryModal.value = true
}

const closeItemSummaryModal = () => {
  showItemSummaryModal.value = false
}

const showCancelModal = (penjualan) => {
  selectedPenjualan.value = penjualan
  showCancelConfirmModal.value = true
}

const closeCancelModal = () => {
  showCancelConfirmModal.value = false
  selectedPenjualan.value = null
}

const confirmCancel = () => {
  cancelTransaction(selectedPenjualan.value.id, closeCancelModal)
}

const openAssignMemberModal = (penjualan) => {
  selectedPenjualan.value = penjualan
  showAssignMemberModal.value = true
}

const closeAssignMemberModal = () => {
  showAssignMemberModal.value = false
  selectedPenjualan.value = null
}

const confirmAssignMember = (buyerId) => {
  assignMember(selectedPenjualan.value.id, buyerId, closeAssignMemberModal)
}

const openRecordModal = (penjualan) => {
  selectedPenjualan.value = penjualan
  showRecordModal.value = true
}

const closeRecordModal = () => {
  showRecordModal.value = false
  selectedPenjualan.value = null
}

const confirmRecord = (data) => {
  recordSingle(selectedPenjualan.value.id, data, closeRecordModal)
}

const openInvoiceModal = (penjualan) => {
  invoicePenjualan.value = penjualan
  showInvoiceModal.value = true
}

const closeInvoiceModal = () => {
  showInvoiceModal.value = false
  invoicePenjualan.value = null
}

// Print receipt
const printReceipt = async (penjualan) => {
  try {
    await printThermalReceipt(penjualan)
  } catch (error) {
    console.error('Print error:', error)
    toast.error('Gagal mencetak struk')
  }
}

// Print item summary
const printItemSummary = async () => {
  try {
    const dateRange = getCurrentDateRange(formatDateIndo)

    await printThermalReceipt({
      isItemSummary: true,
      dateRange,
      items: props.itemSummary,
      total: props.itemSummary.reduce((sum, item) => sum + (item.total_harga || 0), 0),
      totalVerified: props.summary?.total_verified || 0,
      totalTransaksi: props.summary?.total_transaksi || 0
    })

    toast.success('Ringkasan item berhasil dicetak')
  } catch (error) {
    console.error('Print error:', error)
    toast.error('Gagal mencetak ringkasan')
  }
}

// Download CSV
const downloadCSV = () => {
  const params = new URLSearchParams({
    start_date: isKantinUser.value ? filters.value.date : filters.value.start_date,
    end_date: isKantinUser.value ? filters.value.date : filters.value.end_date,
  })

  if (filters.value.work_unit_id) {
    params.append('work_unit_id', filters.value.work_unit_id)
  }

  window.location.href = `/pengelola/rekap-penjualan/export-item-summary?${params.toString()}`
  toast.success('File CSV sedang diunduh')
}

const downloadPdf = () => {
  const params = new URLSearchParams({
    work_unit_id: filters.value.work_unit_id || '',
    start_date: isKantinUser.value ? filters.value.date : filters.value.start_date,
    end_date: isKantinUser.value ? filters.value.date : filters.value.end_date,
  })
  window.location.href = `/pengelola/rekap-penjualan/download-pdf?${params.toString()}`
}
</script>
