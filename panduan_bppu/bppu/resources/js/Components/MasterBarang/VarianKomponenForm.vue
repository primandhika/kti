<template>
  <div class="mt-2 border border-gray-200 rounded-lg bg-gray-50">
    <div class="px-3 py-2 flex items-center justify-between border-b border-gray-200">
      <span class="text-xs font-semibold text-gray-700">Komponen Bahan Baku</span>
      <button
        type="button"
        @click="tambahKomponen"
        class="text-xs text-[#996600] hover:text-[#7a5100] font-medium flex items-center gap-1"
      >
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Tambah Komponen
      </button>
    </div>

    <div v-if="komponens.length === 0" class="px-3 py-2 text-xs text-gray-400 italic">
      Belum ada komponen. Tambahkan bahan yang berkurang saat varian ini terjual.
    </div>

    <div v-else class="divide-y divide-gray-100">
      <div
        v-for="(komponen, idx) in komponens"
        :key="idx"
        class="px-3 py-2 flex items-start gap-2"
      >
        <!-- Autocomplete barang komponen -->
        <div class="flex-1 min-w-0 relative">
          <input
            v-model="komponen._search"
            type="text"
            :placeholder="komponen.nama_barang || 'Cari nama barang...'"
            class="w-full text-xs px-2 py-1.5 border border-gray-300 rounded focus:ring-1 focus:ring-[#996600] focus:border-[#996600]"
            @input="onSearchInput(idx)"
            @focus="onSearchFocus(idx)"
            @blur="onSearchBlur(idx)"
            autocomplete="off"
          />
          <div
            v-if="activeDropdown === idx && searchResults.length > 0"
            class="absolute z-50 top-full left-0 right-0 mt-0.5 bg-white border border-gray-200 rounded shadow-lg max-h-40 overflow-y-auto"
          >
            <button
              v-for="item in searchResults"
              :key="item.id"
              type="button"
              class="w-full text-left px-2.5 py-1.5 text-xs hover:bg-amber-50 hover:text-[#996600] flex justify-between gap-2"
              @mousedown.prevent="pilihKomponen(idx, item)"
            >
              <span class="font-medium truncate">{{ item.nama_barang }}</span>
              <span class="text-gray-400 whitespace-nowrap">{{ formatCurrency(item.harga_beli) }}/{{ item.satuan }}</span>
            </button>
          </div>
          <div
            v-if="activeDropdown === idx && isSearching"
            class="absolute z-50 top-full left-0 right-0 mt-0.5 bg-white border border-gray-200 rounded shadow-sm px-2.5 py-1.5 text-xs text-gray-400"
          >
            Mencari...
          </div>
        </div>

        <!-- Qty -->
        <div class="w-20 flex-shrink-0">
          <input
            :value="komponen.qty"
            @input="komponen.qty = parseFloat($event.target.value) || 0; emitUpdate()"
            type="number"
            min="0.001"
            step="0.001"
            class="w-full text-xs px-2 py-1.5 border border-gray-300 rounded focus:ring-1 focus:ring-[#996600] focus:border-[#996600] text-right"
            placeholder="Qty"
          />
        </div>

        <!-- Satuan -->
        <div class="w-16 flex-shrink-0">
          <input
            v-model="komponen.satuan"
            type="text"
            class="w-full text-xs px-2 py-1.5 border border-gray-300 rounded focus:ring-1 focus:ring-[#996600] focus:border-[#996600]"
            placeholder="Satuan"
            readonly
          />
        </div>

        <button
          type="button"
          @click="hapusKomponen(idx)"
          class="flex-shrink-0 p-1 text-red-400 hover:text-red-600 hover:bg-red-50 rounded"
          title="Hapus komponen"
        >
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
          </svg>
        </button>
      </div>
    </div>

    <!-- Rekomendasi harga jual -->
    <div v-if="rekomendasiHarga !== null" class="px-3 py-2 border-t border-gray-200 bg-amber-50 rounded-b-lg">
      <div class="flex items-center justify-between gap-2">
        <span class="text-xs text-amber-700">
          HPP komponen: <strong>{{ formatCurrency(totalHppKomponen) }}</strong>
          &bull; Rekomendasi (margin 30%):
        </span>
        <div class="flex items-center gap-1.5 flex-shrink-0">
          <strong class="text-xs text-[#996600]">{{ formatCurrency(rekomendasiHarga) }}</strong>
          <button
            type="button"
            @click="applyDanSimpan"
            :disabled="isSaving"
            class="text-xs bg-[#996600] hover:bg-[#7a5100] disabled:opacity-50 text-white px-2 py-0.5 rounded"
          >
            {{ isSaving ? 'Menyimpan...' : 'Pakai & Simpan' }}
          </button>
        </div>
      </div>
      <p v-if="saveError" class="text-xs text-red-600 mt-1">{{ saveError }}</p>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import axios from 'axios';

const props = defineProps({
  modelValue: { type: Array, default: () => [] },
  workUnitId: { type: [Number, String], required: true },
  varianId: { type: [Number, String], default: null },
  hppBase: { type: Number, default: 0 },
  hppTambahan: { type: Number, default: 0 },
});

const emit = defineEmits(['update:modelValue', 'apply-rekomendasi']);

const komponens = ref(
  (props.modelValue || []).map(k => ({
    ...k,
    _search: k.nama_barang || '',
  }))
);

// Sync dari prop hanya saat varian berbeda (accordion ditutup/dibuka untuk varian lain)
// Ditandai dengan perubahan barang_komponen_id pertama atau jumlah komponen
watch(() => (props.modelValue || []).map(k => k.barang_komponen_id).join(','), (newIds) => {
  const curIds = komponens.value.map(k => k.barang_komponen_id).join(',');
  if (newIds !== curIds) {
    komponens.value = (props.modelValue || []).map(k => ({
      ...k,
      _search: k.nama_barang || '',
    }));
  }
});

function emitUpdate() {
  emit('update:modelValue', komponens.value.map(k => ({
    barang_komponen_id: k.barang_komponen_id,
    qty: k.qty,
    satuan: k.satuan,
    nama_barang: k.nama_barang,
    harga_beli: k.harga_beli,
  })));
}

const activeDropdown = ref(null);
const searchResults = ref([]);
const isSearching = ref(false);
let searchTimer = null;

function tambahKomponen() {
  komponens.value.push({
    barang_komponen_id: null,
    nama_barang: '',
    qty: 1,
    satuan: '',
    harga_beli: 0,
    _search: '',
  });
  emitUpdate();
}

function hapusKomponen(idx) {
  komponens.value.splice(idx, 1);
  emitUpdate();
}

function pilihKomponen(idx, item) {
  komponens.value[idx].barang_komponen_id = item.id;
  komponens.value[idx].nama_barang = item.nama_barang;
  komponens.value[idx].satuan = item.satuan;
  komponens.value[idx].harga_beli = item.harga_beli;
  komponens.value[idx]._search = item.nama_barang;
  activeDropdown.value = null;
  searchResults.value = [];
  emitUpdate();
}

function onSearchInput(idx) {
  clearTimeout(searchTimer);
  searchResults.value = [];

  const q = komponens.value[idx]._search;
  if (!q || q.length < 2) {
    activeDropdown.value = null;
    return;
  }

  activeDropdown.value = idx;
  isSearching.value = true;

  searchTimer = setTimeout(async () => {
    try {
      const res = await axios.get(`/pengelola/opname-stock/${props.workUnitId}/barang/search-komponen`, {
        params: { q },
      });
      searchResults.value = res.data;
    } catch (e) {
      searchResults.value = [];
    } finally {
      isSearching.value = false;
    }
  }, 300);
}

function onSearchFocus(idx) {
  const q = komponens.value[idx]._search;
  if (q && q.length >= 2 && searchResults.value.length > 0) {
    activeDropdown.value = idx;
  }
}

function onSearchBlur(idx) {
  setTimeout(() => {
    if (activeDropdown.value === idx) {
      activeDropdown.value = null;
    }
  }, 150);
}

const totalHppKomponen = computed(() => {
  return komponens.value.reduce((sum, k) => {
    return sum + (parseFloat(k.qty) || 0) * (parseFloat(k.harga_beli) || 0);
  }, 0);
});

const rekomendasiHarga = computed(() => {
  if (komponens.value.length === 0) return null;
  const hppTotal = props.hppBase + props.hppTambahan + totalHppKomponen.value;
  return Math.round((hppTotal * 1.3) / 100) * 100;
});

const isSaving = ref(false);
const saveError = ref(null);

async function applyDanSimpan() {
  if (!props.varianId) {
    // Varian baru (belum ada id), cukup emit saja — akan tersimpan bareng form utama
    emit('apply-rekomendasi', rekomendasiHarga.value);
    return;
  }

  isSaving.value = true;
  saveError.value = null;
  try {
    await axios.post(
      `/pengelola/opname-stock/${props.workUnitId}/varian/${props.varianId}/komponens`,
      {
        harga_jual: rekomendasiHarga.value,
        komponens: komponens.value.map(k => ({
          barang_komponen_id: k.barang_komponen_id,
          qty: k.qty,
          satuan: k.satuan,
        })),
      }
    );
    emit('apply-rekomendasi', rekomendasiHarga.value);
  } catch (e) {
    saveError.value = 'Gagal menyimpan komponen.';
  } finally {
    isSaving.value = false;
  }
}

function formatCurrency(val) {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(val || 0);
}
</script>
