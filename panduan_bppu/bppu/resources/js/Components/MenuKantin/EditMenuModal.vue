<template>
  <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 overflow-y-auto" @click.self="$emit('close')">
    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full p-6 my-8">
      <h3 class="text-lg font-bold text-gray-900 mb-4">
        {{ isShopMode ? 'Edit Barang Toko' : 'Edit Menu Kantin' }}
      </h3>
      <p class="text-sm text-gray-600 mb-4">{{ selectedMenu?.nama_barang }}</p>

      <form @submit.prevent="$emit('submit')" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Nama Barang</label>
          <input
            v-model="localMenuForm.nama_barang"
            type="text"
            required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
          />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Kategori Barang</label>
            <select
              v-model="localMenuForm.kategori_id"
              @change="handleKategoriChange"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
            >
              <option :value="null">Pilih Kategori</option>
              <option v-for="kategori in kategoris" :key="kategori.id" :value="kategori.id">
                {{ kategori.nama }}
              </option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Sub Kategori {{ isShopMode ? 'Toko' : 'Menu' }}
            </label>
            <select
              v-model="localMenuForm.sub_kategori"
              :disabled="!localMenuForm.kategori_id"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent disabled:bg-gray-100 disabled:cursor-not-allowed"
            >
              <option value="">{{ !localMenuForm.kategori_id ? '-- Pilih Kategori dulu --' : '-- Pilih Sub Kategori --' }}</option>
              <option v-for="subKat in filteredSubKategoris" :key="subKat" :value="subKat">
                {{ subKat }}
              </option>
            </select>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Harga Jual</label>
            <input
              v-model.number="localMenuForm.harga_jual"
              type="number"
              required
              min="0"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Stok</label>
            <input
              v-model.number="localMenuForm.stok"
              type="number"
              required
              min="0"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent"
            />
          </div>
        </div>

        <div v-if="!isShopMode">
          <label class="flex items-center space-x-2">
            <input
              v-model="localMenuForm.tampilkan_di_menu"
              type="checkbox"
              class="w-4 h-4 text-[#996600] border-gray-300 rounded focus:ring-[#996600]"
            />
            <span class="text-sm font-medium text-gray-700">Tampilkan di Menu Kantin</span>
          </label>
        </div>

        <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
          <p class="text-xs text-gray-600">
            <span class="font-medium">Catatan:</span> Untuk mengubah Kode Barang, Satuan, atau Harga Beli, silakan edit di
            <Link href="/pengelola/opname-stock" class="text-[#996600] hover:underline font-medium">Master Barang</Link>
          </p>
        </div>

        <div class="flex justify-end space-x-3 pt-4 border-t">
          <button
            type="button"
            @click="$emit('close')"
            class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50"
          >
            Batal
          </button>
          <button
            type="submit"
            :disabled="updating"
            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg disabled:opacity-50"
          >
            {{ updating ? 'Menyimpan...' : 'Simpan' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { computed, ref, watch } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
  show: { type: Boolean, required: true },
  selectedMenu: { type: Object, default: null },
  menuForm: { type: Object, required: true },
  updating: { type: Boolean, default: false },
  kategoris: { type: Array, required: true },
  allSubKategoris: { type: Array, required: true },
  subKategoriData: { type: Array, default: () => [] },
  isShopMode: { type: Boolean, default: false },
});

defineEmits(['close', 'submit']);

const localMenuForm = computed(() => props.menuForm);

const filteredSubKategoris = computed(() => {
  if (!localMenuForm.value.kategori_id) return [];

  if (props.isShopMode && props.subKategoriData.length > 0) {
    return props.subKategoriData
      .filter(sub => sub.kategori_barang_id === localMenuForm.value.kategori_id)
      .map(sub => sub.nama);
  }

  return props.allSubKategoris;
});

const handleKategoriChange = () => {
  const currentSubKat = localMenuForm.value.sub_kategori;
  if (currentSubKat && !filteredSubKategoris.value.includes(currentSubKat)) {
    localMenuForm.value.sub_kategori = '';
  }
};
</script>
