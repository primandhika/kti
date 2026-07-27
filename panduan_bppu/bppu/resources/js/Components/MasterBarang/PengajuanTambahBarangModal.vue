<template>
  <div
    v-if="show"
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
    @click.self="$emit('close')"
  >
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md">
      <!-- Header -->
      <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200">
        <h3 class="text-base font-semibold text-gray-800">Ajukan Tambah Barang Baru</h3>
        <button @click="$emit('close')" class="text-gray-400 hover:text-gray-600">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <!-- Body -->
      <div class="px-5 py-4 space-y-3">
        <!-- Nama Barang -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Nama Barang <span class="text-red-500">*</span></label>
          <input
            v-model="form.nama_barang"
            type="text"
            maxlength="255"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent text-sm"
            placeholder="Nama barang yang ingin ditambahkan"
          />
          <p v-if="errors.nama_barang" class="text-xs text-red-600 mt-1">{{ errors.nama_barang }}</p>
        </div>

        <!-- Kode Barcode -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Kode / Barcode <span class="text-gray-400 font-normal">(opsional)</span></label>
          <input
            v-model="form.kode_barang"
            type="text"
            maxlength="100"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent text-sm font-mono"
            placeholder="Kode atau nomor barcode"
          />
          <p v-if="errors.kode_barang" class="text-xs text-red-600 mt-1">{{ errors.kode_barang }}</p>
        </div>

        <!-- Kategori & Satuan -->
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Kategori <span class="text-gray-400 font-normal">(opsional)</span></label>
            <select
              v-model="form.kategori_id"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent text-sm"
            >
              <option value="">-- Pilih --</option>
              <option v-for="kat in kategoris" :key="kat.id" :value="kat.id">{{ kat.nama }}</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Satuan <span class="text-red-500">*</span></label>
            <select
              v-if="satuans.length > 0 && satuanPilihan !== 'lainnya'"
              v-model="satuanPilihan"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent text-sm"
            >
              <option value="">-- Pilih --</option>
              <option v-for="s in satuans" :key="s" :value="s">{{ s }}</option>
              <option value="lainnya">+ Lainnya...</option>
            </select>
            <div v-if="satuanPilihan === 'lainnya'" class="flex gap-1">
              <input
                v-model="form.satuan"
                type="text"
                maxlength="50"
                autofocus
                class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent text-sm"
                placeholder="Ketik satuan..."
              />
              <button
                type="button"
                @click="satuanPilihan = ''"
                class="px-2 py-2 text-gray-400 hover:text-gray-600"
                title="Batal"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>
            <input
              v-if="satuans.length === 0"
              v-model="form.satuan"
              type="text"
              maxlength="50"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent text-sm"
              placeholder="pcs, kg, liter..."
            />
            <p v-if="errors.satuan" class="text-xs text-red-600 mt-1">{{ errors.satuan }}</p>
          </div>
        </div>

        <!-- Stok Awal -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Stok Awal <span class="text-red-500">*</span></label>
          <input
            v-model.number="form.stok_awal"
            type="number"
            min="0"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent text-sm"
            placeholder="0"
          />
          <p v-if="errors.stok_awal" class="text-xs text-red-600 mt-1">{{ errors.stok_awal }}</p>
        </div>

        <!-- Harga Beli & Harga Jual -->
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Harga Beli <span class="text-red-500">*</span></label>
            <input
              v-model.number="form.harga_beli"
              type="number"
              min="0"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent text-sm"
              placeholder="0"
            />
            <p v-if="errors.harga_beli" class="text-xs text-red-600 mt-1">{{ errors.harga_beli }}</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Harga Jual <span class="text-red-500">*</span></label>
            <input
              v-model.number="form.harga_jual"
              type="number"
              min="0"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent text-sm"
              placeholder="0"
            />
            <p v-if="errors.harga_jual" class="text-xs text-red-600 mt-1">{{ errors.harga_jual }}</p>
          </div>
        </div>

        <!-- Keterangan -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan <span class="text-gray-400 font-normal">(opsional)</span></label>
          <textarea
            v-model="form.keterangan"
            rows="2"
            maxlength="500"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent text-sm resize-none"
            placeholder="Alasan atau info tambahan..."
          ></textarea>
        </div>

        <p v-if="errors.pengajuan" class="text-xs text-red-600 bg-red-50 border border-red-200 rounded-lg px-3 py-2">{{ errors.pengajuan }}</p>
      </div>

      <!-- Footer -->
      <div class="flex justify-end gap-2 px-5 py-4 border-t border-gray-200">
        <button
          type="button"
          @click="$emit('close')"
          class="px-4 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
        >
          Batal
        </button>
        <button
          type="button"
          :disabled="!canSubmit || submitting"
          @click="submit"
          class="px-4 py-2 text-sm text-white bg-[#996600] hover:bg-[#7a5100] disabled:bg-gray-300 disabled:cursor-not-allowed rounded-lg transition-colors flex items-center gap-2"
        >
          <svg v-if="submitting" class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
          </svg>
          Kirim Pengajuan
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import { useToast } from 'vue-toastification';

const toast = useToast();

const props = defineProps({
  show: Boolean,
  workUnitId: [String, Number],
  kategoris: { type: Array, default: () => [] },
  satuans: { type: Array, default: () => [] },
});

const emit = defineEmits(['close']);

const submitting = ref(false);
const errors = ref({});
const satuanPilihan = ref('pcs');

const form = ref({
  nama_barang: '',
  kode_barang: '',
  kategori_id: '',
  satuan: 'pcs',
  stok_awal: 0,
  harga_beli: 0,
  harga_jual: 0,
  keterangan: '',
});

// Sync satuanPilihan ke form.satuan kecuali saat "lainnya"
watch(satuanPilihan, (val) => {
  if (val !== 'lainnya') form.value.satuan = val;
});

const canSubmit = computed(() => {
  return form.value.nama_barang.trim() !== ''
    && form.value.satuan.trim() !== ''
    && form.value.harga_beli >= 0
    && form.value.harga_jual >= 0;
});

const submit = () => {
  if (!canSubmit.value || submitting.value) return;
  errors.value = {};
  submitting.value = true;

  router.post(
    `/pengelola/opname-stock/${props.workUnitId}/pengajuan-tambah`,
    form.value,
    {
      onSuccess: () => {
        toast.success('Pengajuan tambah barang berhasil dikirim.');
        emit('close');
      },
      onError: (err) => {
        errors.value = err;
        toast.error('Periksa kembali isian form.');
      },
      onFinish: () => {
        submitting.value = false;
      },
    }
  );
};

watch(() => props.show, (val) => {
  if (val) {
    satuanPilihan.value = 'pcs';
    form.value = { nama_barang: '', kode_barang: '', kategori_id: '', satuan: 'pcs', stok_awal: 0, harga_beli: 0, harga_jual: 0, keterangan: '' };
    errors.value = {};
  }
});
</script>
