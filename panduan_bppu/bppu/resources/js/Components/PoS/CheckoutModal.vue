<template>
  <div
    v-if="show"
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
    @click.self="$emit('close')"
  >
    <div class="bg-white rounded-lg shadow-xl max-w-lg w-full p-4">
      <h3 class="text-lg font-bold text-gray-800 mb-3">Pembayaran</h3>

      <div class="space-y-2">
        <!-- Tanggal -->
        <div class="flex items-center gap-2">
          <label class="w-16 text-xs font-medium text-gray-700 flex-shrink-0">Tanggal</label>
          <input
            :value="tanggalTransaksi"
            @input="$emit('update:tanggalTransaksi', $event.target.value)"
            type="date"
            :max="today"
            class="flex-1 px-2 py-1.5 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-[#996600]"
          />
        </div>

        <!-- Notis multi-toko -->
        <div v-if="isMultiToko" class="flex items-start gap-2 p-2 bg-amber-50 border border-amber-200 rounded-lg text-xs text-amber-800">
          <svg class="w-4 h-4 flex-shrink-0 mt-0.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <span>Transaksi dari beberapa toko - voucher dan redeem poin tidak dapat digunakan. Member dapat di-assign untuk pemerolehan poin saja. Setiap toko dicatat sebagai transaksi terpisah.</span>
        </div>

        <!-- Kode Voucher -->
        <div v-if="!isMultiToko" class="flex items-start gap-2">
          <label class="w-16 text-xs font-medium text-gray-700 flex-shrink-0 pt-1.5">Voucher</label>
          <div class="flex-1">
            <div v-if="!appliedVoucher" class="flex gap-1.5">
              <input
                v-model="voucherInput"
                @keyup.enter="lookupVoucher"
                type="text"
                placeholder="cth. PTGGM-ABC123"
                class="flex-1 px-2 py-1.5 text-sm border rounded uppercase tracking-wider focus:ring-2 focus:ring-[#996600]"
                :class="voucherError ? 'border-red-400' : 'border-gray-300'"
                :disabled="voucherLoading"
              />
              <!-- Tombol scan kamera -->
              <button
                @click="showVoucherScanner = true"
                type="button"
                title="Scan voucher dengan kamera"
                class="px-2.5 py-1.5 text-xs bg-gray-100 text-gray-600 border border-gray-300 rounded hover:bg-gray-200 transition-colors"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
              </button>
              <button
                @click="lookupVoucher"
                :disabled="!voucherInput || voucherLoading"
                type="button"
                class="px-2.5 py-1.5 text-xs bg-[#996600] text-white rounded hover:bg-[#7a5100] disabled:opacity-40 transition-colors"
              >
                {{ voucherLoading ? '...' : 'Cek' }}
              </button>
            </div>
            <p v-if="voucherError" class="text-xs text-red-500 mt-1">{{ voucherError }}</p>
            <p v-else class="text-xs text-gray-400 mt-1">Kode di kartu/struk voucher, atau scan QR dengan tombol kamera</p>

            <!-- Voucher berhasil diterapkan -->
            <div v-if="appliedVoucher" class="flex items-center justify-between bg-green-50 border border-green-200 rounded px-2 py-1.5">
              <div>
                <span class="text-xs font-semibold text-green-800">{{ appliedVoucher.kode_voucher }}</span>
                <span class="text-xs text-green-700 ml-1.5">{{ appliedVoucher.nama_potongan }}</span>
                <span class="text-xs font-bold text-green-700 ml-1.5">- Rp {{ formatCurrency(appliedVoucher.nilai_potongan) }}</span>
              </div>
              <button @click="clearVoucher" type="button" class="text-green-600 hover:text-green-800 ml-2">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>

            <!-- Voucher tersedia untuk member ini -->
            <div v-if="!appliedVoucher && memberVouchers.length > 0" class="mt-1.5 border border-purple-200 rounded-lg bg-purple-50 p-2">
              <p class="text-xs font-semibold text-purple-800 mb-1.5">
                {{ memberVouchers.length }} voucher tersedia untuk member ini:
              </p>
              <div class="space-y-1">
                <button
                  v-for="mv in memberVouchers"
                  :key="mv.id"
                  @click="applyMemberVoucher(mv)"
                  type="button"
                  class="w-full text-left flex items-center justify-between px-2 py-1.5 bg-white rounded border border-purple-200 hover:border-purple-400 hover:bg-purple-50 transition-colors"
                >
                  <div>
                    <span class="font-mono text-xs font-bold text-purple-700 tracking-wider">{{ mv.kode_voucher }}</span>
                    <span class="text-xs text-gray-600 ml-2">{{ mv.nama_potongan }}</span>
                  </div>
                  <div class="text-right flex-shrink-0 ml-2">
                    <span class="text-xs font-bold text-[#996600]">
                      {{ mv.tipe === 'persen' ? mv.nilai + '%' : 'Rp ' + formatCurrency(mv.nilai) }}
                    </span>
                    <span v-if="mv.minimum_transaksi > 0" class="text-xs text-gray-400 block">
                      min Rp {{ formatCurrency(mv.minimum_transaksi) }}
                    </span>
                  </div>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Metode Pembayaran -->
        <div class="flex items-center gap-2">
          <label class="w-32 text-xs font-medium text-gray-700 flex-shrink-0">Metode</label>
          <select
            :value="metodePembayaran"
            @input="$emit('update:metodePembayaran', $event.target.value)"
            class="flex-1 px-2 py-1.5 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-[#996600]"
          >
            <option value="tunai">Tunai</option>
            <option value="transfer">Transfer</option>
            <option value="qris">QRIS</option>
            <option value="debit">Kartu Debit</option>
            <option value="kredit">Kartu Kredit</option>
          </select>
        </div>

        <!-- Jumlah Bayar & Kembalian (only for tunai) - 1 row -->
        <div v-if="metodePembayaran === 'tunai'">
          <div class="flex items-center gap-2">
            <label class="w-32 text-xs font-medium text-gray-700 flex-shrink-0">Jumlah Bayar</label>
            <input
              :value="bayar"
              @input="handleBayarInput"
              type="number"
              :min="totalAfterAllDiscounts"
              step="1000"
              class="flex-1 px-2 py-1.5 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-[#996600]"
            />
            <div class="w-48 flex-shrink-0">
              <div v-if="bayar >= totalAfterAllDiscounts" class="text-xs">
                <span class="text-gray-600">Kembalian:</span>
                <span class="font-bold text-green-600 ml-1">Rp {{ formatCurrency(kembalian) }}</span>
              </div>
              <div v-else-if="bayar > 0" class="text-xs text-red-600">
                ⚠️ Uang kurang!
              </div>
            </div>
          </div>
        </div>

        <!-- Pelanggan dengan dropdown -->
        <div class="flex items-start gap-2 relative">
          <label class="w-32 text-xs font-medium text-gray-700 flex-shrink-0 pt-1.5">{{ isMultiToko ? 'Member (Poin)' : 'Pelanggan' }}</label>
          <div class="flex-1">
            <div class="relative">
              <input
                v-model="searchBuyer"
                @input="handleSearchBuyer"
                @focus="showBuyerDropdown = true"
                @blur="handleBlurBuyer"
                type="text"
                placeholder="Ketik nama atau cari member..."
                class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-[#996600]"
              />

              <!-- Dropdown -->
              <div
                v-if="showBuyerDropdown && filteredBuyers.length > 0"
                class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded shadow-lg max-h-48 overflow-y-auto"
              >
                <button
                  v-for="buyer in filteredBuyers.slice(0, 5)"
                  :key="buyer.id"
                  @mousedown="selectBuyer(buyer)"
                  type="button"
                  class="w-full px-2 py-1.5 text-left hover:bg-gray-50 border-b border-gray-100 last:border-b-0"
                >
                  <div class="flex items-center justify-between">
                    <div class="flex-1">
                      <div class="text-xs font-semibold text-gray-800">{{ buyer.name }}</div>
                      <div class="text-xs text-gray-500">{{ buyer.member_code }} • {{ buyer.total_points }} pts</div>
                    </div>
                    <span
                      v-if="buyer.tier"
                      class="px-1.5 py-0.5 text-xs font-bold rounded text-white ml-2"
                      :style="{ backgroundColor: buyer.tier.color }"
                    >
                      {{ buyer.tier.name }}
                    </span>
                  </div>
                </button>
              </div>
            </div>

            <!-- Selected Buyer Info -->
            <div v-if="selectedBuyer" class="mt-1.5 p-1.5 bg-blue-50 rounded border border-blue-200">
              <div class="flex items-center justify-between">
                <div class="flex-1">
                  <div class="text-xs font-semibold text-blue-900">{{ selectedBuyer.name }}</div>
                  <div class="text-xs text-blue-700">
                    {{ selectedBuyer.member_code }} • {{ selectedBuyer.total_points }} poin
                  </div>
                  <div v-if="isMultiToko" class="text-xs text-amber-700 mt-0.5">
                    Poin akan dihitung per transaksi saat disetujui
                  </div>
                </div>
                <button @click="clearBuyer" type="button" class="text-blue-600 hover:text-blue-800 p-1">
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Redeem Points Section -->
        <div v-if="!isMultiToko && selectedBuyer && selectedBuyer.total_points > 0" class="border-t pt-2">
          <div class="bg-gradient-to-r from-amber-50 to-yellow-50 rounded-lg p-2 border border-amber-200">
            <div class="flex items-center justify-between mb-1.5">
              <div class="flex items-center gap-1.5 flex-1">
                <svg class="w-4 h-4 text-amber-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                </svg>
                <span class="text-xs font-semibold text-amber-900">{{ selectedBuyer.total_points }} poin (Rp {{ formatCurrency(selectedBuyer.total_points * pointsSettings.kurs_poin_ke_rupiah) }})</span>
                <span v-if="!canRedeemPoints" class="text-xs text-amber-700">• Minimal {{ formatCurrency(pointsSettings.minimal_poin_redeem) }} poin</span>
              </div>
            </div>

            <div v-if="canRedeemPoints" class="space-y-1.5">
              <div class="flex items-center gap-2">
                <input
                  v-model.number="pointsToRedeem"
                  @input="handlePointsInput"
                  type="number"
                  min="0"
                  :max="selectedBuyer.total_points"
                  placeholder="0"
                  class="flex-1 px-2 py-1 text-sm border border-amber-300 rounded focus:ring-2 focus:ring-amber-500 bg-white"
                />
                <button
                  @click="useAllPoints"
                  type="button"
                  class="px-2 py-1 text-xs font-semibold bg-amber-600 hover:bg-amber-700 text-white rounded transition-colors"
                >
                  Semua
                </button>
              </div>

              <div v-if="pointsToRedeem > 0" class="flex justify-between items-center text-xs pt-1.5 border-t border-amber-200">
                <span class="text-amber-800">Potongan {{ pointsToRedeem }} poin:</span>
                <span class="font-bold text-green-700">- Rp {{ formatCurrency(pointsToRedeem * pointsSettings.kurs_poin_ke_rupiah) }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Catatan -->
        <div class="flex items-start gap-2">
          <label class="w-32 text-xs font-medium text-gray-700 flex-shrink-0 pt-1.5">Catatan</label>
          <textarea
            :value="catatan"
            @input="$emit('update:catatan', $event.target.value)"
            rows="2"
            placeholder="Catatan transaksi (opsional)..."
            class="flex-1 px-2 py-1.5 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-[#996600]"
          ></textarea>
        </div>
      </div>

      <!-- Ringkasan item per toko (hanya muncul jika ada multi toko) -->
      <div v-if="itemsByUnit.length > 1" class="mt-3 border-t pt-3">
        <p class="text-xs font-semibold text-gray-600 mb-1.5">Rincian per Toko</p>
        <div class="space-y-2">
          <div v-for="group in itemsByUnit" :key="group.work_unit_name" class="bg-[#f4efe5] rounded-lg px-2.5 py-2">
            <p class="text-[11px] font-semibold text-[#7a5100] mb-1">{{ group.work_unit_name }}</p>
            <div class="space-y-0.5">
              <div
                v-for="item in group.items"
                :key="item.barang_id + '-' + (item.varian_id || '')"
                class="flex justify-between text-[11px] text-gray-700"
              >
                <span class="truncate flex-1">{{ item.nama_barang }} <span class="text-gray-500">x{{ item.qty }}</span></span>
                <span class="font-medium flex-shrink-0 ml-2">Rp {{ formatCurrency(item.qty * (item.harga_satuan - (item.diskon_per_item || 0))) }}</span>
              </div>
            </div>
            <div class="flex justify-between text-[11px] font-semibold text-[#996600] border-t border-[#d6c199] mt-1 pt-1">
              <span>Subtotal {{ group.work_unit_name }}</span>
              <span>Rp {{ formatCurrency(group.subtotal) }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Total Summary - Bottom -->
      <div class="mt-4 border-t pt-3">
        <div class="space-y-1.5 text-sm mb-3">
          <div class="flex justify-between text-gray-700">
            <span>Subtotal</span>
            <span class="font-medium">Rp {{ formatCurrency(subtotal) }}</span>
          </div>
          <div v-if="diskon > 0" class="flex justify-between text-orange-600">
            <span>{{ appliedVoucher ? `Voucher (${appliedVoucher.kode_voucher})` : 'Diskon' }}</span>
            <span class="font-medium">- Rp {{ formatCurrency(diskon) }}</span>
          </div>
          <div v-if="pointsToRedeem > 0" class="flex justify-between text-green-700">
            <span>Potongan Poin ({{ pointsToRedeem }} poin)</span>
            <span class="font-medium">- Rp {{ formatCurrency(pointsToRedeem * pointsSettings.kurs_poin_ke_rupiah) }}</span>
          </div>
          <div class="flex justify-between text-base font-bold text-[#996600] border-t pt-1.5">
            <span>Total Bayar</span>
            <span>Rp {{ formatCurrency(totalAfterAllDiscounts) }}</span>
          </div>
        </div>

        <div class="flex space-x-2">
          <button
            @click="$emit('close')"
            class="flex-1 px-3 py-2 border border-gray-300 rounded text-sm text-gray-700 hover:bg-gray-50 transition-colors"
          >
            Batal
          </button>
          <button
            @click="$emit('process-payment')"
            :disabled="!canProcessPayment"
            :class="[
              'flex-1 px-3 py-2 rounded text-sm font-semibold transition-colors flex items-center justify-center gap-2',
              canProcessPayment
                ? 'bg-[#996600] hover:bg-[#7a5100] text-white'
                : 'bg-gray-300 text-gray-500 cursor-not-allowed'
            ]"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <span>Proses</span>
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Scanner voucher kamera -->
  <BarcodeCameraScanner
    :show="showVoucherScanner"
    @close="showVoucherScanner = false"
    @barcode-scanned="onVoucherScanned"
  />
</template>

<script setup>
import { computed, ref, watch } from 'vue'
import BarcodeCameraScanner from './BarcodeCameraScanner.vue'

const props = defineProps({
  show: Boolean,
  cart: { type: Array, default: () => [] },
  subtotal: Number,
  total: Number,
  diskon: Number,
  tanggalTransaksi: String,
  metodePembayaran: String,
  bayar: Number,
  kembalian: Number,
  namaPelanggan: String,
  catatan: String,
  today: String,
  buyers: Array,
  selectedBuyerId: Number,
  pointsSettings: {
    type: Object,
    default: () => ({ minimal_poin_redeem: 5000, kurs_poin_ke_rupiah: 2 })
  },
  appliedVoucher: { type: Object, default: null },
})

// Kelompokkan item cart per toko - hanya aktif jika ada lebih dari 1 toko
const itemsByUnit = computed(() => {
  const groups = {}
  props.cart.forEach(item => {
    const key = item.work_unit_name || '__tanpa_toko__'
    if (!groups[key]) groups[key] = { work_unit_name: item.work_unit_name || 'Toko', items: [], subtotal: 0 }
    groups[key].items.push(item)
    groups[key].subtotal += item.qty * (item.harga_satuan - (item.diskon_per_item || 0))
  })
  return Object.values(groups)
})

const isMultiToko = computed(() => itemsByUnit.value.length > 1)

const emit = defineEmits([
  'close',
  'process-payment',
  'update:tanggalTransaksi',
  'update:metodePembayaran',
  'update:bayar',
  'update:namaPelanggan',
  'update:catatan',
  'update:selectedBuyerId',
  'update:redeemPoints',
  'update:diskon',
  'update:appliedVoucher',
])

const searchBuyer = ref('')
const showBuyerDropdown = ref(false)
const selectedBuyer = ref(null)
const pointsToRedeem = ref(0)

// Voucher lookup
const voucherInput = ref('')
const voucherLoading = ref(false)
const voucherError = ref('')
const showVoucherScanner = ref(false)
const memberVouchers = ref([])

const lookupVoucher = async () => {
  if (!voucherInput.value) return
  voucherError.value = ''
  voucherLoading.value = true

  try {
    const res = await fetch('/pengelola/diskon/check-voucher', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
        'Accept': 'application/json',
      },
      body: JSON.stringify({
        kode_voucher: voucherInput.value.toUpperCase(),
        total_transaksi: props.subtotal,
      }),
    })

    const data = await res.json()

    if (data.valid) {
      emit('update:appliedVoucher', data)
      emit('update:diskon', data.nilai_potongan)
      voucherInput.value = ''
      memberVouchers.value = []
    } else {
      voucherError.value = data.message || 'Voucher tidak valid.'
    }
  } catch {
    voucherError.value = 'Gagal menghubungi server.'
  } finally {
    voucherLoading.value = false
  }
}

const onVoucherScanned = (code) => {
  showVoucherScanner.value = false
  voucherInput.value = code.toUpperCase()
  lookupVoucher()
}

const applyMemberVoucher = (mv) => {
  voucherInput.value = mv.kode_voucher
  lookupVoucher()
}

const fetchMemberVouchers = async (userId) => {
  if (!userId) { memberVouchers.value = []; return }
  try {
    const res = await fetch(`/pengelola/diskon/member-vouchers?user_id=${userId}`, {
      headers: { 'Accept': 'application/json' },
    })
    memberVouchers.value = await res.json()
  } catch {
    memberVouchers.value = []
  }
}

const clearVoucher = () => {
  emit('update:appliedVoucher', null)
  emit('update:diskon', 0)
  voucherError.value = ''
  voucherInput.value = ''
}

const filteredBuyers = computed(() => {
  if (!searchBuyer.value) return props.buyers || []

  const search = searchBuyer.value.toLowerCase()
  return (props.buyers || []).filter(buyer =>
    buyer.name.toLowerCase().includes(search) ||
    buyer.member_code.toLowerCase().includes(search) ||
    (buyer.email && buyer.email.toLowerCase().includes(search)) ||
    (buyer.phone && buyer.phone.toLowerCase().includes(search))
  )
})

const handleSearchBuyer = () => {
  showBuyerDropdown.value = true
  // Emit nama pelanggan dari input manual
  emit('update:namaPelanggan', searchBuyer.value)
}

const handleBlurBuyer = () => {
  // Delay untuk memproses click pada dropdown item
  setTimeout(() => {
    showBuyerDropdown.value = false
    // Jika tidak ada buyer yang dipilih, tetap gunakan input manual
    if (!selectedBuyer.value && searchBuyer.value) {
      emit('update:namaPelanggan', searchBuyer.value)
    }
  }, 200)
}

const selectBuyer = (buyer) => {
  selectedBuyer.value = buyer
  searchBuyer.value = buyer.name
  showBuyerDropdown.value = false
  emit('update:selectedBuyerId', buyer.id)
  emit('update:namaPelanggan', buyer.name)
  if (!isMultiToko.value && !props.appliedVoucher) fetchMemberVouchers(buyer.id)
}

const clearBuyer = () => {
  selectedBuyer.value = null
  searchBuyer.value = ''
  pointsToRedeem.value = 0
  memberVouchers.value = []
  emit('update:selectedBuyerId', null)
  emit('update:namaPelanggan', '')
  emit('update:redeemPoints', 0)
}

const handlePointsInput = () => {
  if (pointsToRedeem.value < 0) pointsToRedeem.value = 0
  if (selectedBuyer.value && pointsToRedeem.value > selectedBuyer.value.total_points) {
    pointsToRedeem.value = selectedBuyer.value.total_points
  }

  if (!canRedeemPoints.value) {
    pointsToRedeem.value = 0
  }

  emit('update:redeemPoints', pointsToRedeem.value)
}

const useAllPoints = () => {
  if (selectedBuyer.value && canRedeemPoints.value) {
    pointsToRedeem.value = selectedBuyer.value.total_points
    emit('update:redeemPoints', pointsToRedeem.value)
  }
}

const canRedeemPoints = computed(() => {
  return selectedBuyer.value && selectedBuyer.value.total_points >= props.pointsSettings.minimal_poin_redeem
})

const totalAfterAllDiscounts = computed(() => {
  const afterDiscount = props.subtotal - (props.diskon || 0)
  const afterPoints = afterDiscount - (pointsToRedeem.value * props.pointsSettings.kurs_poin_ke_rupiah)
  return Math.max(0, afterPoints)
})

// Reset state saat modal ditutup
watch(() => props.show, (newVal) => {
  if (!newVal) {
    showBuyerDropdown.value = false
    pointsToRedeem.value = 0
    voucherInput.value = ''
    voucherError.value = ''
    memberVouchers.value = []
    showVoucherScanner.value = false
  }
})

const canProcessPayment = computed(() => {
  if (props.metodePembayaran === 'tunai') {
    return props.bayar >= totalAfterAllDiscounts.value
  }
  return true
})

const formatCurrency = (value) => {
  return new Intl.NumberFormat('id-ID').format(value)
}

const handleBayarInput = (event) => {
  emit('update:bayar', Number(event.target.value))
}
</script>
