<template>
  <div
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
    @click.self="$emit('close')"
  >
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
      <div class="bg-gradient-to-r from-[#996600] to-[#7a5100] text-white p-4 rounded-t-lg">
        <div class="flex items-center gap-3">
          <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center flex-shrink-0">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <div>
            <h3 class="text-lg font-bold">Verifikasi Transaksi</h3>
            <p class="text-sm text-[#eae0cc] mt-0.5">Pilih tanggal verifikasi</p>
          </div>
        </div>
      </div>

      <div class="p-4">
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mb-4">
          <div class="flex gap-2">
            <svg class="w-5 h-5 text-yellow-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div class="text-sm text-yellow-800">
              <p class="font-medium">Perhatian!</p>
              <p class="text-xs mt-1">Transaksi akan dipindah ke tanggal yang dipilih.</p>
            </div>
          </div>
        </div>

        <!-- Date Selection -->
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-2">
            Tanggal Verifikasi
          </label>
          <div class="space-y-2">
            <!-- Hari Ini -->
            <label
              class="flex items-center p-3 border rounded-lg cursor-pointer transition-colors"
              :class="selectedDate === 'today' ? 'border-[#996600] bg-amber-50' : 'border-gray-200 hover:bg-gray-50'"
            >
              <input
                type="radio"
                name="verify-date"
                value="today"
                v-model="selectedDate"
                class="w-4 h-4 text-[#996600] focus:ring-[#996600]"
              />
              <div class="ml-3 flex-1">
                <div class="text-sm font-medium text-gray-900">Hari Ini</div>
                <div class="text-xs text-gray-500">{{ formatDateIndo(todayDate) }}</div>
              </div>
              <div v-if="selectedDate === 'today'" class="text-[#996600]">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
              </div>
            </label>

            <!-- Tanggal Lain - Hanya untuk Officer/Sysadmin -->
            <label
              v-if="canSelectCustomDate"
              class="flex items-center p-3 border rounded-lg cursor-pointer transition-colors"
              :class="selectedDate === 'custom' ? 'border-[#996600] bg-amber-50' : 'border-gray-200 hover:bg-gray-50'"
            >
              <input
                type="radio"
                name="verify-date"
                value="custom"
                v-model="selectedDate"
                class="w-4 h-4 text-[#996600] focus:ring-[#996600]"
              />
              <div class="ml-3 flex-1">
                <div class="text-sm font-medium text-gray-900">Tanggal Lain</div>
                <div class="text-xs text-gray-500">Pilih tanggal verifikasi</div>
              </div>
              <div v-if="selectedDate === 'custom'" class="text-[#996600]">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
              </div>
            </label>
          </div>

          <!-- Custom Date Picker -->
          <div v-if="selectedDate === 'custom'" class="mt-3">
            <input
              type="date"
              v-model="customDate"
              :min="isSysadmin ? undefined : todayDate"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-[#996600]"
            />
            <p v-if="!isSysadmin" class="text-xs text-gray-500 mt-1">
              Hanya bisa memilih tanggal hari ini atau setelahnya
            </p>
            <p v-else class="text-xs text-blue-500 mt-1">
              Sysadmin bisa memilih tanggal kapan saja
            </p>
          </div>
        </div>

        <!-- Metode Pembayaran -->
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-2">
            Metode Pembayaran
          </label>
          <div class="flex gap-2">
            <label
              v-for="method in paymentMethods"
              :key="method.value"
              class="flex-1 flex items-center justify-center px-3 py-2 border rounded-lg cursor-pointer transition-colors"
              :class="selectedPaymentMethod === method.value ? 'border-[#996600] bg-amber-50 text-[#996600] font-medium' : 'border-gray-200 hover:bg-gray-50 text-gray-700'"
            >
              <input
                type="radio"
                name="payment-method"
                :value="method.value"
                v-model="selectedPaymentMethod"
                class="sr-only"
              />
              <span class="text-sm">{{ method.label }}</span>
            </label>
          </div>
        </div>

        <p class="text-sm text-gray-700">
          Verifikasi transaksi untuk tanggal <span class="font-semibold">{{ getSelectedDateText() }}</span> dengan metode pembayaran <span class="font-semibold">{{ getPaymentMethodText() }}</span>?
        </p>
      </div>

      <div class="p-4 bg-gray-50 rounded-b-lg flex gap-2">
        <button
          @click="$emit('close')"
          class="flex-1 px-4 py-2.5 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 font-medium transition-colors"
        >
          Batal
        </button>
        <button
          @click="confirmVerify"
          :disabled="!isValidDate"
          class="flex-1 px-4 py-2.5 bg-[#996600] hover:bg-[#7a5100] text-white rounded-lg font-semibold transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
        >
          Verifikasi
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

const props = defineProps({
  penjualan: Object,
})

const emit = defineEmits(['close', 'confirm'])

const page = usePage()
const isSysadmin = computed(() => page.props.auth?.user?.roles?.includes('sysadmin'))
const isOfficer = computed(() => page.props.auth?.user?.roles?.includes('officer'))
const isKantinUser = computed(() => {
  const user = page.props.auth?.user
  return user?.roles?.includes('canteen') && !user?.roles?.includes('officer') && !user?.roles?.includes('sysadmin')
})

// Semua user bisa pilih tanggal custom (tapi kantin dibatasi min hari ini)
const canSelectCustomDate = computed(() => true)

const todayDate = new Date().toISOString().split('T')[0]
const selectedDate = ref('today')
const customDate = ref(todayDate)
const selectedPaymentMethod = ref(props.penjualan?.metode_pembayaran || 'tunai')

const paymentMethods = [
  { value: 'tunai', label: 'Tunai' },
  { value: 'qris', label: 'QRIS' },
  { value: 'transfer', label: 'Transfer' }
]

const isValidDate = computed(() => {
  if (selectedDate.value === 'today') return true
  if (selectedDate.value === 'custom' && customDate.value) return true
  return false
})

const formatDateIndo = (dateString) => {
  const date = new Date(dateString)
  const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }
  return date.toLocaleDateString('id-ID', options)
}

const getSelectedDateText = () => {
  if (selectedDate.value === 'today') {
    return formatDateIndo(todayDate)
  } else if (selectedDate.value === 'custom' && customDate.value) {
    return formatDateIndo(customDate.value)
  }
  return '-'
}

const getPaymentMethodText = () => {
  const method = paymentMethods.find(m => m.value === selectedPaymentMethod.value)
  return method ? method.label : '-'
}

const confirmVerify = () => {
  const verifyDate = selectedDate.value === 'today' ? todayDate : customDate.value
  emit('confirm', {
    date: verifyDate,
    payment_method: selectedPaymentMethod.value
  })
}
</script>
