<template>
  <div class="space-y-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
    <div class="flex items-center justify-between">
      <h3 class="text-sm font-semibold text-gray-900">Skema Bisnis</h3>
      <label class="flex items-center space-x-2 cursor-pointer">
        <input
          v-model="form.is_active"
          type="checkbox"
          class="rounded border-gray-300 text-[#996600] focus:ring-[#996600]"
        />
        <span class="text-sm font-medium text-gray-700">Berlakukan</span>
      </label>
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Skema *</label>
      <select
        v-model="form.jenis_skema"
        required
        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
      >
        <option value="">Pilih Jenis Skema</option>
        <option v-for="(label, value) in jenisSkemaOptions" :key="value" :value="value">
          {{ label }}
        </option>
      </select>
    </div>

    <!-- Konsinyasi Config -->
    <div v-if="form.jenis_skema === 'konsinyasi'" class="space-y-3">
      <div class="text-xs text-gray-600 bg-blue-50 p-3 rounded">
        <div class="font-semibold mb-1">Info Konsinyasi:</div>
        <ul class="list-disc list-inside space-y-0.5">
          <li>Mitra titip barang, dibayar setelah laku</li>
          <li>Mitra dapat: Harga Jual - Harga Konsinyasi</li>
          <li>Badan usaha dapat: Harga Konsinyasi (yang dibayarkan untuk barang)</li>
          <li>Set harga konsinyasi di data barang</li>
        </ul>
      </div>

      <div class="border-t border-gray-300 pt-3">
        <div class="font-medium text-sm text-gray-800 mb-2">Konsinyasi Bersyarat (Opsional)</div>
        <div class="text-xs text-gray-600 bg-yellow-50 p-2 rounded mb-3">
          Untuk item dengan harga di atas batas tertentu, badan usaha dapat nominal flat per porsi
        </div>

        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Min. Harga Item</label>
            <div class="relative">
              <span class="absolute left-3 top-2.5 text-gray-500 text-sm">Rp</span>
              <input
                v-model.number="form.minimum_harga_item"
                type="number"
                step="1000"
                min="0"
                class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent text-sm"
                placeholder="10000"
              />
            </div>
            <p class="text-xs text-gray-500 mt-1">Jika harga item {'>'} ini, gunakan flat fee</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Flat Fee per Porsi</label>
            <div class="relative">
              <span class="absolute left-3 top-2.5 text-gray-500 text-sm">Rp</span>
              <input
                v-model.number="form.nominal_flat"
                type="number"
                step="100"
                min="0"
                class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent text-sm"
                placeholder="1000"
              />
            </div>
            <p class="text-xs text-gray-500 mt-1">Untung badan usaha per porsi</p>
          </div>
        </div>

        <div v-if="form.minimum_harga_item && form.nominal_flat" class="mt-3 text-xs bg-green-50 p-2 rounded border border-green-200">
          <div class="font-semibold text-green-800 mb-1">Contoh Perhitungan:</div>
          <ul class="space-y-1 text-gray-700">
            <li>Item harga Rp {{ formatCurrency(form.minimum_harga_item + 1000) }} (di atas min): Badan usaha dapat Rp {{ formatCurrency(form.nominal_flat) }}/porsi</li>
            <li>Item harga Rp {{ formatCurrency(Math.max(form.minimum_harga_item - 1000, 5000)) }} (di bawah min): Konsinyasi normal (sesuai harga konsinyasi barang)</li>
          </ul>
        </div>
      </div>
    </div>

    <!-- Bagi Hasil Config -->
    <div v-if="form.jenis_skema === 'bagi_hasil'" class="grid grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">% Vendor/Tenant *</label>
        <div class="relative">
          <input
            v-model="form.persentase_vendor"
            type="number"
            step="0.01"
            min="0"
            max="100"
            required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent pr-8"
            placeholder="0.00"
          />
          <span class="absolute right-3 top-2.5 text-gray-500">%</span>
        </div>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">% Badan Usaha *</label>
        <div class="relative">
          <input
            v-model="form.persentase_badan_usaha"
            type="number"
            step="0.01"
            min="0"
            max="100"
            required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent pr-8"
            placeholder="0.00"
          />
          <span class="absolute right-3 top-2.5 text-gray-500">%</span>
        </div>
      </div>
      <div class="col-span-2 text-xs text-gray-600 bg-blue-50 p-2 rounded">
        Total: {{ totalPersentase }}% (idealnya 100%)
      </div>
    </div>

    <!-- Dropshipper Info -->
    <div v-if="form.jenis_skema === 'dropshipper'" class="text-xs text-gray-600 bg-yellow-50 p-3 rounded">
      <div class="font-semibold mb-1">Info Dropshipper:</div>
      <ul class="list-disc list-inside space-y-0.5">
        <li>Tidak ada stok, pesan ke vendor saat ada order</li>
        <li>Semua hasil penjualan untuk vendor</li>
        <li>Badan usaha tidak mendapat bagian</li>
      </ul>
    </div>

    <!-- Supplier Info -->
    <div v-if="form.jenis_skema === 'supplier'" class="text-xs text-gray-600 bg-green-50 p-3 rounded">
      <div class="font-semibold mb-1">Info Supplier:</div>
      <ul class="list-disc list-inside space-y-0.5">
        <li>Beli putus dari supplier</li>
        <li>Semua untung/rugi ditanggung badan usaha</li>
        <li>Tidak ada pembagian hasil dengan supplier</li>
      </ul>
    </div>

    <!-- Catatan -->
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Skema</label>
      <textarea
        v-model="form.catatan"
        rows="2"
        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent text-sm"
        placeholder="Catatan tambahan tentang skema bisnis (opsional)"
      ></textarea>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  form: Object,
  jenisSkemaOptions: Object,
})

const totalPersentase = computed(() => {
  const vendor = parseFloat(props.form.persentase_vendor) || 0
  const badanUsaha = parseFloat(props.form.persentase_badan_usaha) || 0
  return (vendor + badanUsaha).toFixed(2)
})

const formatCurrency = (value) => {
  return new Intl.NumberFormat('id-ID', {
    minimumFractionDigits: 0,
    maximumFractionDigits: 0,
  }).format(value)
}
</script>
