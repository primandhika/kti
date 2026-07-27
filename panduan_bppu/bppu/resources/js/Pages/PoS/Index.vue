<template>
  <component :is="layoutComponent" pageTitle="Point of Sale">
    <div class="min-h-screen max-h-screen flex flex-col bg-gray-50 overflow-hidden">
      <!-- Header - Sticky -->
      <POSHeader
        ref="posHeaderRef"
        :work-units="workUnits"
        :selected-work-unit="selectedWorkUnit"
        :selected-work-unit-id="selectedWorkUnitId"
        :selected-work-unit-ids="selectedWorkUnitIds"
        v-model:search-query="searchQuery"
        v-model:selected-category="selectedCategory"
        :barcode-mode="barcodeMode"
        :scanner-type="scannerType"
        :categories="categories"
        :category-counts="categoryCounts"
        :count-diskon="countDiskon"
        v-model:filter-diskon="filterDiskon"
        :is-kantin-user="isKantinUser"
        @change-work-unit="changeWorkUnit"
        @toggle-barcode-mode="requestBarcodeMode"
        @search-enter="handleBarcodeSearch"
        @open-camera-scanner="showCameraScanner = true"
      />

      <!-- Main Content: 3 Sections Layout -->
      <div class="flex-1 overflow-hidden">
        <!-- Desktop/Tablet: 3 Columns -->
        <div class="hidden md:grid md:grid-cols-12 gap-3 p-3 h-full">
          <!-- Section 1: Product List (Left - 6 cols = 50%) -->
          <div class="md:col-span-6 flex flex-col gap-1.5 overflow-hidden">
            <!-- Refresh Button - Desktop -->
            <div class="flex items-center justify-between flex-shrink-0">
              <h2 class="text-sm font-semibold text-gray-700">Daftar Barang ({{ filteredBarangs.length }})</h2>
              <div class="flex items-center gap-2">
                <!-- Toggler Stok Ada -->
                <label class="flex items-center gap-1.5 cursor-pointer select-none" title="Sembunyikan barang habis stok">
                  <span class="text-xs font-medium" :class="filterStokAda ? 'text-[#996600]' : 'text-gray-500'">Stok Ada</span>
                  <div class="relative">
                    <input
                      type="checkbox"
                      v-model="filterStokAda"
                      class="sr-only peer"
                    />
                    <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-[#996600] rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-[#996600]"></div>
                  </div>
                </label>

                <button
                  @click="refreshBarangs(selectedWorkUnitIds)"
                  :disabled="isRefreshing"
                  :class="[
                    'flex items-center gap-1.5 px-2.5 py-1.5 text-xs rounded-lg font-medium transition-all',
                    isRefreshing
                      ? 'bg-gray-100 text-gray-400 cursor-not-allowed'
                      : 'text-white shadow-sm hover:shadow'
                  ]"
                  :style="!isRefreshing ? 'background-color: #996600;' : ''"
                  @mouseenter="!isRefreshing && ($event.target.style.backgroundColor = '#7a5100')"
                  @mouseleave="!isRefreshing && ($event.target.style.backgroundColor = '#996600')"
                >
                  <svg
                    :class="['w-3.5 h-3.5', isRefreshing && 'animate-spin']"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                  </svg>
                  <span>{{ isRefreshing ? 'Memperbarui...' : 'Perbarui' }}</span>
                </button>
              </div>
            </div>

            <div class="flex-1 overflow-hidden">
              <ProductList
                :barangs="filteredBarangs"
                :cart="cart"
                :is-kantin-user="isKantinUser"
                @add-to-cart="handleAddToCart"
                @increment="handleIncrementWithFocus"
                @decrement="handleDecrementWithFocus"
                @add-varian-to-cart="handleAddVarianWithFocus"
                @increment-varian="handleIncrementVarianWithFocus"
                @decrement-varian="handleDecrementVarianWithFocus"
                @open-image-modal="openImageModal"
                @open-pengajuan="openPengajuanModal"
                @open-pengajuan-tambah="showPengajuanTambahModal = true"
              />
            </div>
          </div>

          <!-- Section 2: Cart (Middle - 3 cols = 25%) -->
          <div class="md:col-span-3 overflow-hidden flex flex-col gap-3">
            <!-- Total Display - Always visible on desktop -->
            <div class="flex-shrink-0">
              <div class="bg-gradient-to-br from-[#f4efe5] via-[#eae0cc] to-[#f4efe5] rounded-lg shadow-xl p-4 border-2 border-[#996600] relative overflow-hidden">
                <div class="absolute top-0 left-0 w-16 h-16 bg-[#996600] opacity-10 rounded-br-full"></div>
                <div class="absolute bottom-0 right-0 w-16 h-16 bg-[#996600] opacity-10 rounded-tl-full"></div>

                <div class="relative z-10">
                  <div class="text-sm text-[#6b4700] mb-1 font-semibold">Total</div>
                  <div class="text-3xl lg:text-4xl text-[#996600] font-bold leading-tight break-all">
                    {{ formatRupiah(total) }}
                  </div>
                  <div v-if="diskon > 0" class="mt-2 text-xs text-[#895b00] font-medium">
                    Diskon: {{ formatRupiah(diskon) }}
                  </div>
                </div>
              </div>
            </div>

            <!-- Cart Section -->
            <div class="flex-1 overflow-hidden">
              <CartSection
                :cart="cart"
                @increment-qty="handleIncrementQtyWithFocus"
                @decrement-qty="handleDecrementQtyWithFocus"
                @remove-item="handleRemoveWithFocus"
                @clear-cart="handleClearWithFocus"
              />
            </div>
          </div>

          <!-- Section 3: Checkout (Right - 3 cols = 25%) -->
          <div class="md:col-span-3 overflow-hidden" data-checkout-area>
            <CheckoutSection
              :cart="cart"
              :subtotal="subtotal"
              :diskon="diskon"
              :total="total"
              @update:diskon="diskon = $event"
              @proceed-checkout="proceedToCheckout"
              @clear-cart="handleClearWithFocus"
            />
          </div>
        </div>

        <!-- Mobile: Single Column with Product List -->
        <div class="md:hidden h-full flex flex-col">
          <!-- Barcode Display Mobile - Only show in barcode mode -->
          <div class="flex-shrink-0 px-3 pt-3">
            <BarcodeDisplay
              :show="barcodeMode"
              :total="total"
              :subtotal="subtotal"
              :diskon="diskon"
              :item-count="cart.length"
              :scanner-type="scannerType"
              @open-camera-scanner="showCameraScanner = true"
            />
          </div>

          <!-- Product List -->
          <div class="flex-1 overflow-hidden">
            <ProductList
              :barangs="filteredBarangs"
              :cart="cart"
              :is-kantin-user="isKantinUser"
              @add-to-cart="handleAddToCart"
              @increment="handleIncrementWithFocus"
              @decrement="handleDecrementWithFocus"
              @add-varian-to-cart="handleAddVarianWithFocus"
              @increment-varian="handleIncrementVarianWithFocus"
              @decrement-varian="handleDecrementVarianWithFocus"
              @open-image-modal="openImageModal"
              @open-pengajuan="openPengajuanModal"
              @open-pengajuan-tambah="showPengajuanTambahModal = true"
            />
          </div>
        </div>
      </div>

      <!-- Mobile: Floating Cart Sidebar -->
      <MobileCartSidebar
        :show="showMobileCart"
        :cart="cart"
        :subtotal="subtotal"
        :total="total"
        v-model:diskon="diskon"
        @close="showMobileCart = false"
        @increment-qty="handleIncrementQtyWithFocus"
        @decrement-qty="handleDecrementQtyWithFocus"
        @remove-item="handleRemoveWithFocus"
        @clear-cart="handleClearWithFocus"
        @proceed-checkout="proceedToCheckout"
        data-checkout-area
      />

      <!-- Mobile: Sticky Footer with Cart Summary -->
      <MobileCartFooter
        :cart="cart"
        :total="total"
        :is-refreshing="isRefreshing"
        @show-cart="showMobileCart = true"
        @refresh="refreshBarangs(selectedWorkUnitIds)"
        @clear-cart="handleClearWithFocus"
        @proceed-checkout="proceedToCheckout"
      />
    </div>

    <!-- Modals -->
    <CheckoutModal
      :show="showCheckoutModal"
      :cart="cart"
      :subtotal="subtotal"
      :total="total"
      :diskon="diskon"
      :tanggal-transaksi="tanggalTransaksi"
      :metode-pembayaran="metodePembayaran"
      :bayar="bayar"
      :kembalian="kembalian"
      :nama-pelanggan="namaPelanggan"
      :catatan="catatan"
      :today="today"
      :buyers="buyers"
      :selected-buyer-id="selectedBuyerId"
      :points-settings="pointsSettings"
      data-checkout-area
      @close="showCheckoutModal = false"
      @process-payment="processPayment(selectedWorkUnitId, selectedWorkUnitIds)"
      @update:tanggal-transaksi="tanggalTransaksi = $event"
      @update:metode-pembayaran="metodePembayaran = $event"
      @update:bayar="bayar = $event"
      @update:nama-pelanggan="namaPelanggan = $event"
      @update:catatan="catatan = $event"
      @update:selected-buyer-id="selectedBuyerId = $event"
      @update:redeem-points="redeemPoints = $event"
      @update:diskon="diskon = $event"
      :applied-voucher="appliedVoucher"
      @update:applied-voucher="appliedVoucher = $event"
    />

    <SuccessModal
      :show="showSuccessModal"
      :success-message="successMessage"
      @close="closeSuccessModal"
    />

    <ImageModal
      :show="showImageModal"
      :selected-barang="selectedBarang"
      :image-preview="imagePreview"
      :selected-file="selectedFile"
      :is-uploading="isUploading"
      @close="closeImageModal"
      @upload-image="uploadImage(selectedWorkUnitId)"
      @delete-image="deleteImage(selectedWorkUnitId)"
      @file-change="handleFileChange"
    />

    <RefreshSummaryModal
      :show="showRefreshSummary"
      :summary="refreshSummary"
      @close="showRefreshSummary = false"
    />

    <BarcodeModeSelectModal
      :show="showModeSelectModal"
      @select="enableBarcodeMode"
      @cancel="cancelModeSelect"
    />

    <BarcodeCameraScanner
      :show="showCameraScanner"
      @close="handleCameraScannerClose"
      @barcode-scanned="handleCameraScanned"
    />

    <BarcodeSuggestionModal
      :show="showSuggestionModal"
      :suggestions="suggestedBarangs"
      @close="closeSuggestionModal"
      @select="selectSuggestion"
    />

    <!-- Keyboard Shortcuts Help -->
    <ShortcutsHelp :shortcuts="shortcuts" />

    <!-- Pengajuan Barang Modal - untuk kantin -->
    <PengajuanBarangModal
      :show="showPengajuanModal"
      :barang="pengajuanTargetBarang"
      :work-unit-id="selectedWorkUnitId"
      @close="showPengajuanModal = false"
    />

    <!-- Pengajuan Tambah Barang Modal - untuk kantin -->
    <PengajuanTambahBarangModal
      :show="showPengajuanTambahModal"
      :work-unit-id="selectedWorkUnitId"
      :kategoris="props.kategoris"
      :satuans="props.satuans"
      @close="showPengajuanTambahModal = false"
    />
  </component>
</template>

<script setup>
import { ref, computed, onMounted, nextTick } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import CanteenLayout from '@/Layouts/CanteenLayout.vue'
import ShopLayout from '@/Layouts/ShopLayout.vue'
import POSHeader from '@/Components/PoS/POSHeader.vue'
import ProductList from '@/Components/PoS/ProductList.vue'
import CartSection from '@/Components/PoS/CartSection.vue'
import CheckoutSection from '@/Components/PoS/CheckoutSection.vue'
import BarcodeDisplay from '@/Components/PoS/BarcodeDisplay.vue'
import ShortcutsHelp from '@/Components/PoS/ShortcutsHelp.vue'
import MobileCartSidebar from '@/Components/PoS/MobileCartSidebar.vue'
import MobileCartFooter from '@/Components/PoS/MobileCartFooter.vue'
import CheckoutModal from '@/Components/PoS/CheckoutModal.vue'
import SuccessModal from '@/Components/PoS/SuccessModal.vue'
import ImageModal from '@/Components/PoS/ImageModal.vue'
import RefreshSummaryModal from '@/Components/PoS/RefreshSummaryModal.vue'
import BarcodeCameraScanner from '@/Components/PoS/BarcodeCameraScanner.vue'
import BarcodeSuggestionModal from '@/Components/PoS/BarcodeSuggestionModal.vue'
import BarcodeModeSelectModal from '@/Components/PoS/BarcodeModeSelectModal.vue'
import PengajuanBarangModal from '@/Components/MasterBarang/PengajuanBarangModal.vue'
import PengajuanTambahBarangModal from '@/Components/MasterBarang/PengajuanTambahBarangModal.vue'
import { useCart } from '@/composables/useCart'
import { useCheckout } from '@/composables/useCheckout'
import { useBarang } from '@/composables/useBarang'
import { useBarcodeScanner } from '@/composables/useBarcodeScanner'
import { useKeyboardShortcuts } from '@/composables/useKeyboardShortcuts'

const formatRupiah = (value) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
  }).format(value)
}

const props = defineProps({
  workUnits: Array,
  selectedWorkUnit: Object,
  selectedWorkUnitIds: { type: Array, default: () => [] },
  barangs: Array,
  buyers: Array,
  pointsSettings: {
    type: Object,
    default: () => ({ minimal_poin_redeem: 5000, kurs_poin_ke_rupiah: 2 })
  },
  kategoris: { type: Array, default: () => [] },
  satuans: { type: Array, default: () => [] },
})

const page = usePage()
const posHeaderRef = ref(null)

// Layout Component
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

// Check if user is kantin only
const isKantinUser = computed(() => {
  const user = page.props.auth?.user
  return user?.roles?.includes('canteen') && !user?.roles?.includes('officer') && !user?.roles?.includes('sysadmin')
})

// Work unit
const selectedWorkUnitId = ref(props.selectedWorkUnit?.id)
const selectedWorkUnitIds = ref(
  props.selectedWorkUnitIds.length > 0 ? props.selectedWorkUnitIds : (props.selectedWorkUnit?.id ? [props.selectedWorkUnit.id] : [])
)

// Mobile cart
const showMobileCart = ref(false)

// Pengajuan barang
const showPengajuanModal = ref(false)
const pengajuanTargetBarang = ref(null)
const openPengajuanModal = (barang) => {
  pengajuanTargetBarang.value = barang
  showPengajuanModal.value = true
}

// Pengajuan tambah barang
const showPengajuanTambahModal = ref(false)

// Camera scanner
const showCameraScanner = ref(false)

// Composables
const {
  cart,
  diskon,
  subtotal,
  total,
  addToCart,
  addVarianToCart,
  handleIncrement,
  handleDecrement,
  handleIncrementVarian,
  handleDecrementVarian,
  incrementQty,
  decrementQty,
  removeFromCart,
  clearCart,
  updateCartStocks
} = useCart()

const {
  searchQuery,
  selectedCategory,
  filterDiskon,
  filterStokAda,
  categories,
  categoryCounts,
  countDiskon,
  filteredBarangs,
  isRefreshing,
  showRefreshSummary,
  refreshSummary,
  showImageModal,
  selectedBarang,
  selectedFile,
  imagePreview,
  isUploading,
  refreshBarangs,
  openImageModal,
  closeImageModal,
  handleFileChange,
  uploadImage,
  deleteImage
} = useBarang(props.barangs, updateCartStocks, {
  uploadImageRoute: '/pengelola/penjualan/upload-image',
  deleteImageRoute: '/pengelola/penjualan/delete-image'
})

const {
  showCheckoutModal,
  showSuccessModal,
  successMessage,
  metodePembayaran,
  bayar,
  kembalian,
  namaPelanggan,
  selectedBuyerId,
  redeemPoints,
  catatan,
  tanggalTransaksi,
  today,
  appliedVoucher,
  proceedToCheckout,
  processPayment,
  closeSuccessModal
} = useCheckout(cart, subtotal, total, diskon, refreshBarangs)

const {
  barcodeMode,
  scannerType,
  showModeSelectModal,
  requestBarcodeMode,
  enableBarcodeMode,
  cancelModeSelect,
  handleBarcodeSearch,
  refocusSearch,
  showSuggestionModal,
  suggestedBarangs,
  selectSuggestion,
  closeSuggestionModal
} = useBarcodeScanner(
  computed(() => posHeaderRef.value?.searchInputRef),
  filteredBarangs,
  addToCart
)

// Helper functions for shortcuts
const removeLastItem = () => {
  if (cart.value.length > 0) {
    cart.value.pop()
  }
}

const incrementLastItem = () => {
  if (cart.value.length > 0) {
    const lastIndex = cart.value.length - 1
    incrementQty(lastIndex)
  }
}

const decrementLastItem = () => {
  if (cart.value.length > 0) {
    const lastIndex = cart.value.length - 1
    decrementQty(lastIndex)
  }
}

const toggleBarcodeMode = () => {
  requestBarcodeMode()
}

const printLastReceipt = () => {
  // TODO: Implement print last receipt functionality
  console.log('Print last receipt')
}

// Keyboard shortcuts
const { shortcuts } = useKeyboardShortcuts({
  barcodeMode,
  cart,
  proceedToCheckout,
  clearCart,
  removeLastItem,
  incrementLastItem,
  decrementLastItem,
  toggleBarcodeMode,
  printLastReceipt
})

// Methods with auto-refocus for barcode mode
const handleAddToCart = (barang) => {
  addToCart(barang)
  refocusSearch()
}

const handleIncrementWithFocus = (barangId) => {
  handleIncrement(barangId)
  refocusSearch()
}

const handleDecrementWithFocus = (barangId) => {
  handleDecrement(barangId)
  refocusSearch()
}

const handleAddVarianWithFocus = (barang, varian) => {
  addVarianToCart(barang, varian)
  refocusSearch()
}

const handleIncrementVarianWithFocus = (barangId, varianId) => {
  handleIncrementVarian(barangId, varianId)
  refocusSearch()
}

const handleDecrementVarianWithFocus = (barangId, varianId) => {
  handleDecrementVarian(barangId, varianId)
  refocusSearch()
}

const handleIncrementQtyWithFocus = (index) => {
  incrementQty(index)
  refocusSearch()
}

const handleDecrementQtyWithFocus = (index) => {
  decrementQty(index)
  refocusSearch()
}

const handleRemoveWithFocus = (index) => {
  removeFromCart(index)
  refocusSearch()
}

const handleClearWithFocus = () => {
  clearCart()
  refocusSearch()
}

const changeWorkUnit = (ids) => {
  // ids bisa array (multi) atau single value dari dropdown lama
  const idsArray = Array.isArray(ids) ? ids : [ids]
  router.get(route('pos.index'), { work_unit_ids: idsArray })
}

// Flag: apakah scan terakhir perlu re-open kamera
const reopenCameraAfterScan = ref(false)

const handleCameraScanned = (barcode) => {
  searchQuery.value = barcode

  // Tandai untuk re-open jika mode camera aktif
  if (barcodeMode.value && scannerType.value === 'camera') {
    reopenCameraAfterScan.value = true
  }

  setTimeout(() => {
    handleBarcodeSearch(barcode)
    setTimeout(() => {
      searchQuery.value = ''
    }, 500)
  }, 100)
}

const handleCameraScannerClose = () => {
  showCameraScanner.value = false

  // Re-buka kamera otomatis setelah close jika perlu
  if (reopenCameraAfterScan.value) {
    reopenCameraAfterScan.value = false
    setTimeout(() => {
      if (barcodeMode.value && scannerType.value === 'camera') {
        showCameraScanner.value = true
      }
    }, 300)
  }
}

// Auto-focus search input on mount
onMounted(() => {
  nextTick(() => {
    if (posHeaderRef.value?.searchInputRef) {
      posHeaderRef.value.searchInputRef.focus()
    }
  })
})
</script>
