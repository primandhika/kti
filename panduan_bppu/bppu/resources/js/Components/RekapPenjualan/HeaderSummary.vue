<template>
  <div class="bg-white border-b sticky top-14 z-20 shadow-sm">
    <div class="p-3">
      <!-- Desktop Layout (lg+) -->
      <div class="hidden lg:flex items-center gap-3 mb-3">
        <!-- Summary Cards -->
        <div class="bg-[#eae0cc] rounded-lg px-3 py-1.5 min-w-[90px]">
          <div class="text-[#6b4700] text-[10px] uppercase font-medium">Transaksi</div>
          <div class="text-[#996600] font-bold text-base">{{ summary.total_transaksi }}</div>
        </div>
        <button
          @click="$emit('toggle-verified-filter')"
          class="bg-green-50 rounded-lg px-3 py-1.5 min-w-[110px] hover:bg-green-100 transition-colors text-left"
          :class="showVerifiedOnly ? 'ring-2 ring-green-600' : ''"
        >
          <div class="text-gray-600 text-[10px] uppercase font-medium flex items-center gap-1">
            Total
            <svg v-if="showVerifiedOnly" class="w-3 h-3 text-green-600" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
          </div>
          <div class="text-green-900 font-bold text-base">{{ formatCurrency(summary.total_penjualan) }}</div>
          <div v-if="showVerifiedOnly" class="text-[10px] text-green-600 font-medium mt-0.5">Verified Only</div>
        </button>

        <!-- Potongan Card (informasi diskon/voucher ke pembeli, tidak mengurangi bagian mitra) -->
        <div
          v-if="summary.total_diskon > 0"
          class="bg-amber-50 rounded-lg px-3 py-1.5 min-w-[100px]"
        >
          <div class="text-amber-700 text-[10px] uppercase font-medium">Potongan</div>
          <div class="text-amber-900 font-bold text-sm">- {{ formatCurrency(summary.total_diskon) }}</div>
          <div class="text-[9px] text-amber-600">voucher / diskon</div>
        </div>

        <!-- Payment Method Cards (Clickable) -->
        <button
          v-if="summary.tunai > 0"
          @click="$emit('toggle-payment-filter', 'tunai')"
          class="rounded-lg px-2 py-1.5 min-w-[85px] transition-colors text-left"
          :class="activePaymentFilters.includes('tunai')
            ? 'bg-gray-200 opacity-60'
            : 'bg-blue-50 hover:bg-blue-100'"
        >
          <div class="text-gray-600 text-[9px] uppercase font-medium">Tunai</div>
          <div class="text-blue-900 font-bold text-sm">{{ formatCurrency(summary.tunai) }}</div>
          <div class="text-[9px] text-gray-500">{{ summary.tunai_count }} trx</div>
        </button>

        <button
          v-if="summary.qris > 0"
          @click="$emit('toggle-payment-filter', 'qris')"
          class="rounded-lg px-2 py-1.5 min-w-[85px] transition-colors text-left"
          :class="activePaymentFilters.includes('qris')
            ? 'bg-gray-200 opacity-60'
            : 'bg-purple-50 hover:bg-purple-100'"
        >
          <div class="text-gray-600 text-[9px] uppercase font-medium">QRIS</div>
          <div class="text-purple-900 font-bold text-sm">{{ formatCurrency(summary.qris) }}</div>
          <div class="text-[9px] text-gray-500">{{ summary.qris_count }} trx</div>
        </button>

        <button
          v-if="summary.transfer > 0"
          @click="$emit('toggle-payment-filter', 'transfer')"
          class="rounded-lg px-2 py-1.5 min-w-[85px] transition-colors text-left"
          :class="activePaymentFilters.includes('transfer')
            ? 'bg-gray-200 opacity-60'
            : 'bg-indigo-50 hover:bg-indigo-100'"
        >
          <div class="text-gray-600 text-[9px] uppercase font-medium">Transfer</div>
          <div class="text-indigo-900 font-bold text-sm">{{ formatCurrency(summary.transfer) }}</div>
          <div class="text-[9px] text-gray-500">{{ summary.transfer_count }} trx</div>
        </button>

        <button
          v-if="summary.debit > 0"
          @click="$emit('toggle-payment-filter', 'debit')"
          class="rounded-lg px-2 py-1.5 min-w-[85px] transition-colors text-left"
          :class="activePaymentFilters.includes('debit')
            ? 'bg-gray-200 opacity-60'
            : 'bg-teal-50 hover:bg-teal-100'"
        >
          <div class="text-gray-600 text-[9px] uppercase font-medium">Debit</div>
          <div class="text-teal-900 font-bold text-sm">{{ formatCurrency(summary.debit) }}</div>
          <div class="text-[9px] text-gray-500">{{ summary.debit_count }} trx</div>
        </button>

        <button
          v-if="summary.kredit > 0"
          @click="$emit('toggle-payment-filter', 'kredit')"
          class="rounded-lg px-2 py-1.5 min-w-[85px] transition-colors text-left"
          :class="activePaymentFilters.includes('kredit')
            ? 'bg-gray-200 opacity-60'
            : 'bg-pink-50 hover:bg-pink-100'"
        >
          <div class="text-gray-600 text-[9px] uppercase font-medium">Kredit</div>
          <div class="text-pink-900 font-bold text-sm">{{ formatCurrency(summary.kredit) }}</div>
          <div class="text-[9px] text-gray-500">{{ summary.kredit_count }} trx</div>
        </button>

        <!-- Filters -->
        <div v-if="isKantinUser" class="flex gap-2 flex-1">
          <input
            :value="filters.date"
            @input="$emit('update:filters', { ...filters, date: $event.target.value })"
            type="date"
            class="px-2 py-1.5 text-xs border border-gray-300 rounded focus:ring-1 focus:ring-[#996600] focus:border-[#996600]"
          />
          <button
            @click="$emit('set-today')"
            class="px-3 py-1.5 text-xs bg-[#996600] text-white rounded hover:bg-[#7a5100] whitespace-nowrap"
          >
            Hari Ini
          </button>
        </div>
        <div v-else class="flex gap-2 flex-1">
          <div class="flex gap-1">
            <button
              @click="$emit('set-today')"
              :class="['px-2 py-1.5 text-xs rounded transition-colors', isToday ? 'bg-[#996600] text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200']"
            >
              Hari Ini
            </button>
            <button
              @click="$emit('set-week')"
              :class="['px-2 py-1.5 text-xs rounded transition-colors', isThisWeek ? 'bg-[#996600] text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200']"
            >
              Minggu Ini
            </button>
            <button
              @click="$emit('set-month')"
              :class="['px-2 py-1.5 text-xs rounded transition-colors', isThisMonth ? 'bg-[#996600] text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200']"
            >
              Bulan Ini
            </button>
          </div>
          <input
            :value="filters.start_date"
            @input="$emit('update:filters', { ...filters, start_date: $event.target.value })"
            type="date"
            class="px-2 py-1.5 text-xs border border-gray-300 rounded focus:ring-1 focus:ring-[#996600] focus:border-[#996600]"
          />
          <input
            :value="filters.end_date"
            @input="$emit('update:filters', { ...filters, end_date: $event.target.value })"
            type="date"
            class="px-2 py-1.5 text-xs border border-gray-300 rounded focus:ring-1 focus:ring-[#996600] focus:border-[#996600]"
          />
        </div>

        <select
          v-if="workUnits.length > 1"
          :value="filters.work_unit_id"
          @input="$emit('update:filters', { ...filters, work_unit_id: $event.target.value || null })"
          class="px-2 py-1.5 text-xs border border-gray-300 rounded focus:ring-1 focus:ring-[#996600] focus:border-[#996600]"
        >
          <option :value="null">Semua Unit</option>
          <option v-for="unit in workUnits" :key="unit.id" :value="unit.id">
            {{ unit.name }}
          </option>
        </select>

        <div class="relative flex-1 max-w-xs">
          <input
            :value="filters.search"
            @input="$emit('update:filters', { ...filters, search: $event.target.value })"
            type="text"
            placeholder="Cari kode / member..."
            class="w-full px-3 py-1.5 pl-8 text-xs border border-gray-300 rounded focus:ring-1 focus:ring-[#996600] focus:border-[#996600]"
          />
          <svg class="absolute left-2.5 top-2 w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
        </div>

        <button
          @click="$emit('reset')"
          class="px-3 py-1.5 text-xs bg-gray-100 text-gray-700 rounded hover:bg-gray-200 whitespace-nowrap"
        >
          Reset
        </button>

        <!-- placeholder, pills row di bawah -->
      </div>


      <!-- Mobile Layout -->
      <div class="lg:hidden">
        <div class="grid grid-cols-2 gap-2 text-xs mb-2">
          <div class="bg-[#eae0cc] rounded-lg p-2">
            <div class="text-[#6b4700] text-[10px] uppercase font-medium">Transaksi</div>
            <div class="text-[#996600] font-bold text-base">{{ summary.total_transaksi }}</div>
          </div>
          <button
            @click="$emit('toggle-verified-filter')"
            class="bg-green-50 rounded-lg p-2 hover:bg-green-100 transition-colors text-left"
            :class="showVerifiedOnly ? 'ring-2 ring-green-600' : ''"
          >
            <div class="text-gray-600 text-[10px] uppercase font-medium flex items-center gap-1">
              Total
              <svg v-if="showVerifiedOnly" class="w-3 h-3 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
              </svg>
            </div>
            <div class="text-green-900 font-bold text-base">{{ formatCurrency(summary.total_penjualan) }}</div>
            <div v-if="showVerifiedOnly" class="text-[10px] text-green-600 font-medium mt-0.5">Verified</div>
          </button>
        </div>

        <!-- Potongan Card Mobile (informasi diskon/voucher ke pembeli) -->
        <div v-if="summary.total_diskon > 0" class="mb-2">
          <div class="bg-amber-50 rounded-lg p-2">
            <div class="text-amber-700 text-[10px] uppercase font-medium">Potongan</div>
            <div class="text-amber-900 font-bold text-sm">- {{ formatCurrency(summary.total_diskon) }}</div>
            <div class="text-[9px] text-amber-600">voucher / diskon</div>
          </div>
        </div>

        <!-- Payment Method Cards Mobile -->
        <div class="grid grid-cols-3 gap-1.5 mb-2">
          <button
            v-if="summary.tunai > 0"
            @click="$emit('toggle-payment-filter', 'tunai')"
            class="rounded p-1.5 text-left transition-colors"
            :class="activePaymentFilters.includes('tunai')
              ? 'bg-gray-200 opacity-60'
              : 'bg-blue-50 hover:bg-blue-100'"
          >
            <div class="text-gray-600 text-[9px] uppercase font-medium">Tunai</div>
            <div class="text-blue-900 font-bold text-xs">{{ formatCurrency(summary.tunai) }}</div>
            <div class="text-[8px] text-gray-500">{{ summary.tunai_count }}</div>
          </button>

          <button
            v-if="summary.qris > 0"
            @click="$emit('toggle-payment-filter', 'qris')"
            class="rounded p-1.5 text-left transition-colors"
            :class="activePaymentFilters.includes('qris')
              ? 'bg-gray-200 opacity-60'
              : 'bg-purple-50 hover:bg-purple-100'"
          >
            <div class="text-gray-600 text-[9px] uppercase font-medium">QRIS</div>
            <div class="text-purple-900 font-bold text-xs">{{ formatCurrency(summary.qris) }}</div>
            <div class="text-[8px] text-gray-500">{{ summary.qris_count }}</div>
          </button>

          <button
            v-if="summary.transfer > 0"
            @click="$emit('toggle-payment-filter', 'transfer')"
            class="rounded p-1.5 text-left transition-colors"
            :class="activePaymentFilters.includes('transfer')
              ? 'bg-gray-200 opacity-60'
              : 'bg-indigo-50 hover:bg-indigo-100'"
          >
            <div class="text-gray-600 text-[9px] uppercase font-medium">Transfer</div>
            <div class="text-indigo-900 font-bold text-xs">{{ formatCurrency(summary.transfer) }}</div>
            <div class="text-[8px] text-gray-500">{{ summary.transfer_count }}</div>
          </button>

          <button
            v-if="summary.debit > 0"
            @click="$emit('toggle-payment-filter', 'debit')"
            class="rounded p-1.5 text-left transition-colors"
            :class="activePaymentFilters.includes('debit')
              ? 'bg-gray-200 opacity-60'
              : 'bg-teal-50 hover:bg-teal-100'"
          >
            <div class="text-gray-600 text-[9px] uppercase font-medium">Debit</div>
            <div class="text-teal-900 font-bold text-xs">{{ formatCurrency(summary.debit) }}</div>
            <div class="text-[8px] text-gray-500">{{ summary.debit_count }}</div>
          </button>

          <button
            v-if="summary.kredit > 0"
            @click="$emit('toggle-payment-filter', 'kredit')"
            class="rounded p-1.5 text-left transition-colors"
            :class="activePaymentFilters.includes('kredit')
              ? 'bg-gray-200 opacity-60'
              : 'bg-pink-50 hover:bg-pink-100'"
          >
            <div class="text-gray-600 text-[9px] uppercase font-medium">Kredit</div>
            <div class="text-pink-900 font-bold text-xs">{{ formatCurrency(summary.kredit) }}</div>
            <div class="text-[8px] text-gray-500">{{ summary.kredit_count }}</div>
          </button>
        </div>

        <div class="space-y-2">
          <div v-if="isKantinUser" class="flex gap-2">
            <input
              :value="filters.date"
              @input="$emit('update:filters', { ...filters, date: $event.target.value })"
              type="date"
              class="flex-1 px-2 py-1.5 text-xs border border-gray-300 rounded focus:ring-1 focus:ring-[#996600] focus:border-[#996600]"
            />
            <button
              @click="$emit('set-today')"
              class="px-3 py-1.5 text-xs bg-[#996600] text-white rounded hover:bg-[#7a5100]"
            >
              Hari Ini
            </button>
          </div>
          <div v-else class="space-y-2">
            <div class="grid grid-cols-3 gap-2">
              <button
                @click="$emit('set-today')"
                :class="['px-2 py-1.5 text-xs rounded transition-colors', isToday ? 'bg-[#996600] text-white' : 'bg-gray-100 text-gray-700']"
              >
                Hari Ini
              </button>
              <button
                @click="$emit('set-week')"
                :class="['px-2 py-1.5 text-xs rounded transition-colors', isThisWeek ? 'bg-[#996600] text-white' : 'bg-gray-100 text-gray-700']"
              >
                Minggu Ini
              </button>
              <button
                @click="$emit('set-month')"
                :class="['px-2 py-1.5 text-xs rounded transition-colors', isThisMonth ? 'bg-[#996600] text-white' : 'bg-gray-100 text-gray-700']"
              >
                Bulan Ini
              </button>
            </div>
            <div class="flex gap-2">
              <input
                :value="filters.start_date"
                @input="$emit('update:filters', { ...filters, start_date: $event.target.value })"
                type="date"
                class="flex-1 px-2 py-1.5 text-xs border border-gray-300 rounded focus:ring-1 focus:ring-[#996600] focus:border-[#996600]"
              />
              <input
                :value="filters.end_date"
                @input="$emit('update:filters', { ...filters, end_date: $event.target.value })"
                type="date"
                class="flex-1 px-2 py-1.5 text-xs border border-gray-300 rounded focus:ring-1 focus:ring-[#996600] focus:border-[#996600]"
              />
            </div>
          </div>

          <select
            v-if="workUnits.length > 1"
            :value="filters.work_unit_id"
            @input="$emit('update:filters', { ...filters, work_unit_id: $event.target.value || null })"
            class="px-2 py-1.5 text-xs border border-gray-300 rounded focus:ring-1 focus:ring-[#996600] focus:border-[#996600]"
          >
            <option :value="null">Semua Unit</option>
            <option v-for="unit in workUnits" :key="unit.id" :value="unit.id">
              {{ unit.name }}
            </option>
          </select>

          <div class="relative">
            <input
              :value="filters.search"
              @input="$emit('update:filters', { ...filters, search: $event.target.value })"
              type="text"
              placeholder="Cari kode / member..."
              class="w-full px-3 py-1.5 pl-8 text-xs border border-gray-300 rounded focus:ring-1 focus:ring-[#996600] focus:border-[#996600]"
            />
            <svg class="absolute left-2.5 top-2 w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </div>

          <button
            @click="$emit('reset')"
            class="px-3 py-1.5 text-xs bg-gray-100 text-gray-700 rounded hover:bg-gray-200"
          >
            Reset
          </button>
        </div>
      </div>

    </div>

  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import { useFormatters } from '@/composables/useFormatters'

const props = defineProps({
  summary: { type: Object, required: true },
  filters: { type: Object, required: true },
  workUnits: { type: Array, default: () => [] },
  isKantinUser: { type: Boolean, default: false },
  isToday: { type: Boolean, default: false },
  isThisWeek: { type: Boolean, default: false },
  isThisMonth: { type: Boolean, default: false },
  showVerifiedOnly: { type: Boolean, default: false },
  activePaymentFilters: { type: Array, default: () => [] },
  canDownloadPdf: { type: Boolean, default: false }
})

defineEmits(['update:filters', 'set-today', 'set-week', 'set-month', 'reset', 'show-summary', 'toggle-verified-filter', 'toggle-payment-filter'])

const { formatCurrency } = useFormatters()

const downloadPdf = () => {
  const params = new URLSearchParams({
    work_unit_id: props.filters.work_unit_id || '',
    start_date: props.isKantinUser ? props.filters.date : props.filters.start_date,
    end_date: props.isKantinUser ? props.filters.date : props.filters.end_date,
  })

  window.location.href = `/pengelola/rekap-penjualan/download-pdf?${params.toString()}`
}
</script>
