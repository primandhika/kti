<template>
  <div>
    <!-- Trigger link saat belum expanded -->
    <button
      v-if="!expanded"
      type="button"
      @click="expanded = true"
      class="flex items-center gap-1.5 text-sm text-[#996600] hover:text-[#7a5100] transition-colors"
    >
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
      </svg>
      <span>Tambahkan Diskon / Potongan</span>
    </button>

    <!-- Form diskon -->
    <div v-else class="border border-dashed border-[#c1a366] rounded-lg p-3 bg-[#f4efe5]">
    <div class="flex items-center justify-between mb-2">
      <h4 class="text-sm font-semibold text-[#6b4700]">Diskon Produk</h4>
      <button
        type="button"
        @click="tutup"
        class="text-xs text-red-600 hover:text-red-700 underline"
      >
        {{ form.diskon_tipe ? 'Hapus Diskon' : 'Tutup' }}
      </button>
    </div>

    <!-- Tipe Diskon -->
    <div class="flex flex-col md:flex-row md:items-center gap-1 md:gap-0 mb-3">
      <label class="md:w-32 text-sm font-medium text-gray-700 flex-shrink-0">Tipe Diskon</label>
      <select
        v-model="form.diskon_tipe"
        class="flex-1 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
        @change="onTipeChange"
      >
        <option value="">-- Tidak Ada Diskon --</option>
        <option value="persen">Persentase (%)</option>
        <option value="nominal">Potongan Langsung (Rp)</option>
        <option value="harga_menjadi">Harga Menjadi (Rp)</option>
      </select>
    </div>

    <!-- Nilai Diskon (muncul jika tipe dipilih) -->
    <div v-if="form.diskon_tipe" class="space-y-3">
      <div class="flex flex-col md:flex-row md:items-center gap-1 md:gap-0">
        <label class="md:w-32 text-sm font-medium text-gray-700 flex-shrink-0">
          {{ labelNilai }}
        </label>
        <div class="flex-1 flex items-center gap-2">
          <input
            :value="form.diskon_nilai"
            @input="form.diskon_nilai = parseFloat($event.target.value) || 0"
            type="number"
            min="0"
            :max="form.diskon_tipe === 'persen' ? 100 : undefined"
            :step="form.diskon_tipe === 'persen' ? '0.1' : '1'"
            class="flex-1 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
            :placeholder="form.diskon_tipe === 'persen' ? 'Contoh: 10' : 'Contoh: 5000'"
          />
          <span class="text-sm text-gray-500 flex-shrink-0">{{ satuanNilai }}</span>
        </div>
      </div>

      <!-- Preview Harga -->
      <div v-if="previewHargaAkhir !== null" class="flex items-center gap-2 text-sm">
        <span class="text-gray-500">Harga akhir:</span>
        <span class="font-bold text-[#996600]">{{ formatCurrency(previewHargaAkhir) }}</span>
        <span v-if="previewPotongan > 0" class="text-green-600 text-xs">(hemat {{ formatCurrency(previewPotongan) }})</span>
      </div>

      <!-- Periode Diskon -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        <div class="flex flex-col md:flex-row md:items-center gap-1 md:gap-0">
          <label class="md:w-32 text-sm font-medium text-gray-700 flex-shrink-0">Mulai</label>
          <input
            v-model="form.diskon_mulai"
            type="date"
            class="flex-1 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
          />
        </div>
        <div class="flex flex-col md:flex-row md:items-center gap-1 md:gap-0">
          <label class="md:w-32 text-sm font-medium text-gray-700 flex-shrink-0">Selesai</label>
          <input
            v-model="form.diskon_selesai"
            type="date"
            :min="form.diskon_mulai || undefined"
            class="flex-1 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
          />
        </div>
      </div>

      <!-- Status diskon aktif -->
      <div
        v-if="form.diskon_tipe && form.diskon_nilai > 0"
        :class="[
          'text-xs px-2 py-1.5 rounded border',
          isDiskonAktifSekarang
            ? 'bg-green-50 text-green-700 border-green-300'
            : 'bg-yellow-50 text-yellow-700 border-yellow-300'
        ]"
      >
        <span v-if="isDiskonAktifSekarang">Diskon aktif sekarang</span>
        <span v-else-if="form.diskon_mulai && today < form.diskon_mulai">Diskon belum dimulai (mulai {{ form.diskon_mulai }})</span>
        <span v-else-if="form.diskon_selesai && today > form.diskon_selesai">Diskon sudah berakhir</span>
        <span v-else>Diskon aktif (tanpa batas waktu)</span>
      </div>
    </div>
  </div><!-- end form diskon -->
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'

const props = defineProps({
  form: Object,
})

// Buka otomatis jika barang sudah punya diskon
const expanded = ref(!!props.form.diskon_tipe)

watch(() => props.form.diskon_tipe, (val) => {
  if (val) expanded.value = true
})

const today = new Date().toISOString().split('T')[0]

const labelNilai = computed(() => {
  if (props.form.diskon_tipe === 'persen') return 'Persentase'
  if (props.form.diskon_tipe === 'nominal') return 'Potongan'
  if (props.form.diskon_tipe === 'harga_menjadi') return 'Harga Menjadi'
  return 'Nilai'
})

const satuanNilai = computed(() => {
  if (props.form.diskon_tipe === 'persen') return '%'
  return 'Rp'
})

const previewHargaAkhir = computed(() => {
  if (!props.form.diskon_tipe || !props.form.diskon_nilai || !props.form.harga_jual) return null
  const harga = parseFloat(props.form.harga_jual) || 0
  const nilai = parseFloat(props.form.diskon_nilai) || 0
  if (props.form.diskon_tipe === 'persen') return Math.max(0, harga - Math.round(harga * nilai / 100))
  if (props.form.diskon_tipe === 'nominal') return Math.max(0, harga - nilai)
  if (props.form.diskon_tipe === 'harga_menjadi') return Math.max(0, nilai)
  return null
})

const previewPotongan = computed(() => {
  if (previewHargaAkhir.value === null) return 0
  return Math.max(0, (parseFloat(props.form.harga_jual) || 0) - previewHargaAkhir.value)
})

const isDiskonAktifSekarang = computed(() => {
  if (!props.form.diskon_tipe || !props.form.diskon_nilai) return false
  if (props.form.diskon_mulai && today < props.form.diskon_mulai) return false
  if (props.form.diskon_selesai && today > props.form.diskon_selesai) return false
  return true
})

const onTipeChange = () => {
  if (!props.form.diskon_tipe) {
    clearDiskon()
  } else if (!props.form.diskon_mulai) {
    props.form.diskon_mulai = today
  }
}

const clearDiskon = () => {
  props.form.diskon_tipe = ''
  props.form.diskon_nilai = null
  props.form.diskon_mulai = null
  props.form.diskon_selesai = null
}

const tutup = () => {
  clearDiskon()
  expanded.value = false
}

const formatCurrency = (value) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0,
  }).format(value)
}
</script>
