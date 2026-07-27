<template>
  <div class="space-y-8">
    <!-- Kantin -->
    <div>
      <h3 class="text-base font-semibold text-gray-900 mb-1">Self-Order Kantin</h3>
      <p class="text-xs text-gray-500 mb-4">Konfigurasi minimal order dan biaya layanan untuk kantin</p>

      <div class="space-y-4">
        <!-- Minimal order kantin -->
        <div class="bg-white border border-gray-200 rounded-lg p-4 space-y-2">
          <label class="block text-xs font-semibold text-gray-700">Minimal Order</label>
          <div class="flex items-center gap-2">
            <span class="text-sm text-gray-500">Rp</span>
            <input
              v-model.number="form.kantin_minimal_order"
              type="number"
              min="0"
              step="1000"
              class="w-40 px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
            />
          </div>
          <p class="text-xs text-gray-400">Isi 0 jika tidak ada batas minimal order</p>
        </div>

        <!-- Biaya layanan kantin -->
        <div class="bg-white border border-gray-200 rounded-lg p-4 space-y-3">
          <div class="flex items-center gap-3">
            <input
              id="kantin_biaya_aktif"
              v-model="form.kantin_biaya_layanan_aktif"
              type="checkbox"
              class="w-4 h-4 rounded border-gray-300 text-[#996600] focus:ring-[#996600]"
            />
            <label for="kantin_biaya_aktif" class="text-sm font-semibold text-gray-700 cursor-pointer">
              Kenakan biaya layanan
            </label>
          </div>
          <div v-if="form.kantin_biaya_layanan_aktif" class="flex items-center gap-2 pl-7">
            <span class="text-sm text-gray-500">Rp</span>
            <input
              v-model.number="form.kantin_biaya_layanan"
              type="number"
              min="0"
              step="500"
              class="w-40 px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
            />
            <span class="text-xs text-gray-400">per transaksi</span>
          </div>
        </div>

        <!-- Verifikasi lokasi -->
        <div class="bg-white border border-gray-200 rounded-lg p-4 space-y-2">
          <div class="flex items-center gap-3">
            <input
              id="kantin_verif_lokasi_aktif"
              v-model="form.kantin_verif_lokasi_aktif"
              type="checkbox"
              class="w-4 h-4 rounded border-gray-300 text-[#996600] focus:ring-[#996600]"
            />
            <label for="kantin_verif_lokasi_aktif" class="text-sm font-semibold text-gray-700 cursor-pointer">
              Wajib verifikasi lokasi untuk area publik
            </label>
          </div>
          <p class="text-xs text-gray-400 pl-7">
            Jika aktif, guest yang memesan via QR meja di area publik wajib mengizinkan akses lokasi. Koordinat referensi diatur di halaman Pengelolaan Meja.
          </p>
        </div>
      </div>
    </div>

    <!-- Belanja -->
    <div>
      <h3 class="text-base font-semibold text-gray-900 mb-1">Self-Order Belanja</h3>
      <p class="text-xs text-gray-500 mb-4">Konfigurasi minimal order dan biaya layanan untuk toko/belanja</p>

      <div class="space-y-4">
        <!-- Minimal order belanja -->
        <div class="bg-white border border-gray-200 rounded-lg p-4 space-y-2">
          <label class="block text-xs font-semibold text-gray-700">Minimal Order</label>
          <div class="flex items-center gap-2">
            <span class="text-sm text-gray-500">Rp</span>
            <input
              v-model.number="form.belanja_minimal_order"
              type="number"
              min="0"
              step="1000"
              class="w-40 px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
            />
          </div>
          <p class="text-xs text-gray-400">Isi 0 jika tidak ada batas minimal order</p>
        </div>

        <!-- Biaya layanan belanja -->
        <div class="bg-white border border-gray-200 rounded-lg p-4 space-y-3">
          <div class="flex items-center gap-3">
            <input
              id="belanja_biaya_aktif"
              v-model="form.belanja_biaya_layanan_aktif"
              type="checkbox"
              class="w-4 h-4 rounded border-gray-300 text-[#996600] focus:ring-[#996600]"
            />
            <label for="belanja_biaya_aktif" class="text-sm font-semibold text-gray-700 cursor-pointer">
              Kenakan biaya layanan
            </label>
          </div>
          <div v-if="form.belanja_biaya_layanan_aktif" class="flex items-center gap-2 pl-7">
            <span class="text-sm text-gray-500">Rp</span>
            <input
              v-model.number="form.belanja_biaya_layanan"
              type="number"
              min="0"
              step="500"
              class="w-40 px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
            />
            <span class="text-xs text-gray-400">per transaksi</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Tombol simpan -->
    <div class="pt-4 border-t border-gray-200">
      <button
        type="button"
        :disabled="loading"
        @click="$emit('save')"
        class="px-6 py-2.5 bg-[#996600] text-white rounded-lg hover:bg-[#7a5100] transition-colors font-semibold disabled:opacity-50"
      >
        {{ loading ? 'Menyimpan...' : 'Simpan Perubahan' }}
      </button>
    </div>
  </div>
</template>

<script setup>
defineProps({
  form: { type: Object, required: true },
  loading: { type: Boolean, default: false },
});

defineEmits(['save']);
</script>
