<template>
  <div v-if="pengajuans.length > 0" class="mb-4">
    <div class="bg-blue-50 border border-blue-300 rounded-lg overflow-hidden">
      <div class="flex items-center gap-2 px-4 py-3 bg-blue-100 border-b border-blue-300">
        <svg class="w-5 h-5 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        <span class="text-sm font-semibold text-blue-800">
          {{ pengajuans.length }} Pengajuan Tambah Barang Baru
        </span>
      </div>

      <div class="divide-y divide-blue-200">
        <div v-for="item in pengajuans" :key="item.id" class="px-4 py-3">
          <div class="flex items-start justify-between gap-3">
            <div class="flex-1 min-w-0 space-y-1">
              <div class="flex items-center gap-2 flex-wrap">
                <span class="font-semibold text-sm text-gray-800">{{ item.nama_barang }}</span>
                <span v-if="item.kode_barang" class="text-xs font-mono bg-gray-100 px-1.5 py-0.5 rounded text-gray-600">{{ item.kode_barang }}</span>
              </div>
              <div class="flex gap-3 text-xs text-gray-600 flex-wrap">
                <span v-if="item.kategori">Kategori: <strong>{{ item.kategori }}</strong></span>
                <span>Satuan: <strong>{{ item.satuan }}</strong></span>
                <span>Beli: <strong>{{ formatRp(item.harga_beli) }}</strong></span>
                <span>Jual: <strong>{{ formatRp(item.harga_jual) }}</strong></span>
              </div>
              <div class="text-xs text-gray-400">
                oleh {{ item.diajukan_oleh }} &bull; {{ item.created_at }}
              </div>
              <p v-if="item.keterangan" class="text-xs text-gray-500 italic">{{ item.keterangan }}</p>
            </div>

            <div class="flex items-center gap-1.5 flex-shrink-0">
              <button
                type="button"
                :disabled="processing === item.id"
                @click="bukaSetujui(item)"
                class="px-3 py-1.5 text-xs font-medium text-white bg-green-600 hover:bg-green-700 disabled:bg-gray-300 rounded-lg transition-colors"
              >
                Setujui
              </button>
              <button
                type="button"
                :disabled="processing === item.id"
                @click="bukaTolaK(item)"
                class="px-3 py-1.5 text-xs font-medium text-white bg-red-500 hover:bg-red-600 disabled:bg-gray-300 rounded-lg transition-colors"
              >
                Tolak
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Setujui -->
    <div v-if="showSetujuiModal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-[60] p-4">
      <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm p-5 space-y-3">
        <h4 class="text-sm font-semibold text-gray-800">Setujui & Buat Barang</h4>
        <p class="text-xs text-gray-500">Periksa dan lengkapi data jika perlu sebelum menyetujui.</p>

        <div>
          <label class="block text-xs font-medium text-gray-700 mb-1">Kode Barang</label>
          <input v-model="setujuiForm.kode_barang" type="text" class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-sm font-mono focus:ring-2 focus:ring-[#996600] focus:border-transparent" />
        </div>
        <div v-if="kategoris.length > 0">
          <label class="block text-xs font-medium text-gray-700 mb-1">Kategori</label>
          <select v-model="setujuiForm.kategori_id" class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#996600] focus:border-transparent">
            <option value="">-- Pilih Kategori --</option>
            <option v-for="kat in kategoris" :key="kat.id" :value="kat.id">{{ kat.nama }}</option>
          </select>
        </div>
        <div class="grid grid-cols-2 gap-2">
          <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Harga Beli</label>
            <input v-model.number="setujuiForm.harga_beli" type="number" min="0" class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#996600] focus:border-transparent" />
          </div>
          <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Harga Jual</label>
            <input v-model.number="setujuiForm.harga_jual" type="number" min="0" class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#996600] focus:border-transparent" />
          </div>
        </div>
        <div class="grid grid-cols-2 gap-2">
          <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Stok Awal <span class="text-red-500">*</span></label>
            <input v-model.number="setujuiForm.stok_awal" type="number" min="0" class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#996600] focus:border-transparent" />
          </div>
          <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Min. Stok</label>
            <input v-model.number="setujuiForm.minimum_stok" type="number" min="0" class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#996600] focus:border-transparent" />
          </div>
        </div>
        <div>
          <label class="block text-xs font-medium text-gray-700 mb-1">Catatan (opsional)</label>
          <textarea v-model="setujuiForm.catatan_pengelola" rows="2" class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-sm resize-none focus:ring-2 focus:ring-[#996600] focus:border-transparent"></textarea>
        </div>

        <p v-if="prosesError" class="text-xs text-red-600 bg-red-50 border border-red-200 rounded px-2 py-1">{{ prosesError }}</p>

        <div class="flex justify-end gap-2 pt-1">
          <button type="button" @click="showSetujuiModal = false" class="px-4 py-2 text-sm text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">Batal</button>
          <button
            type="button"
            :disabled="processing === setujuiTarget?.id"
            @click="proses(setujuiTarget, 'setujui')"
            class="px-4 py-2 text-sm text-white bg-green-600 hover:bg-green-700 disabled:bg-gray-300 rounded-lg"
          >
            Setujui & Buat Barang
          </button>
        </div>
      </div>
    </div>

    <!-- Modal Tolak -->
    <div v-if="showTolakModal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-[60] p-4">
      <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm p-5 space-y-3">
        <h4 class="text-sm font-semibold text-gray-800">Tolak Pengajuan</h4>
        <p class="text-xs text-gray-600">Tolak pengajuan tambah barang "<strong>{{ tolakTarget?.nama_barang }}</strong>"?</p>
        <textarea
          v-model="tolakCatatan"
          rows="2"
          maxlength="500"
          class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm resize-none focus:ring-2 focus:ring-red-400 focus:border-transparent"
          placeholder="Catatan penolakan (opsional)..."
        ></textarea>
        <div class="flex justify-end gap-2">
          <button type="button" @click="showTolakModal = false" class="px-4 py-2 text-sm text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">Batal</button>
          <button
            type="button"
            :disabled="processing === tolakTarget?.id"
            @click="proses(tolakTarget, 'tolak')"
            class="px-4 py-2 text-sm text-white bg-red-500 hover:bg-red-600 disabled:bg-gray-300 rounded-lg"
          >
            Tolak
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';
import { useToast } from 'vue-toastification';

const toast = useToast();

const props = defineProps({
  workUnitId: [String, Number],
  kategoris: { type: Array, default: () => [] },
});

const emit = defineEmits(['updated']);

const pengajuans = ref([]);
const processing = ref(null);
const prosesError = ref('');

const showSetujuiModal = ref(false);
const showTolakModal = ref(false);
const setujuiTarget = ref(null);
const tolakTarget = ref(null);
const tolakCatatan = ref('');

const setujuiForm = ref({
  kode_barang: '',
  kategori_id: '',
  harga_beli: 0,
  harga_jual: 0,
  stok_awal: 0,
  minimum_stok: 0,
  catatan_pengelola: '',
});

const loadPengajuans = async () => {
  if (!props.workUnitId) return;
  try {
    const res = await fetch(`/pengelola/opname-stock/${props.workUnitId}/pengajuan-tambah`);
    const data = await res.json();
    pengajuans.value = data.pengajuans ?? [];
  } catch {
    pengajuans.value = [];
  }
};

const formatRp = (val) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(val);

const bukaSetujui = (item) => {
  setujuiTarget.value = item;
  prosesError.value = '';
  setujuiForm.value = {
    kode_barang: item.kode_barang ?? '',
    kategori_id: '',
    harga_beli: item.harga_beli,
    harga_jual: item.harga_jual,
    stok_awal: item.stok_awal ?? 0,
    minimum_stok: 0,
    catatan_pengelola: '',
  };
  showSetujuiModal.value = true;
};

const bukaTolaK = (item) => {
  tolakTarget.value = item;
  tolakCatatan.value = '';
  showTolakModal.value = true;
};

const proses = (item, aksi) => {
  processing.value = item.id;
  prosesError.value = '';
  showTolakModal.value = false;

  const payload = aksi === 'setujui'
    ? { aksi, ...setujuiForm.value }
    : { aksi, catatan_pengelola: tolakCatatan.value };

  router.post(
    `/pengelola/opname-stock/${props.workUnitId}/pengajuan-tambah/${item.id}/proses`,
    payload,
    {
      onSuccess: () => {
        const msg = aksi === 'setujui'
          ? `Barang "${item.nama_barang}" berhasil ditambahkan.`
          : 'Pengajuan ditolak.';
        toast.success(msg);
        showSetujuiModal.value = false;
        pengajuans.value = pengajuans.value.filter(p => p.id !== item.id);
        emit('updated');
      },
      onError: (err) => {
        prosesError.value = err.pengajuan ?? 'Gagal memproses pengajuan.';
        if (!err.pengajuan) toast.error('Gagal memproses pengajuan.');
      },
      onFinish: () => {
        processing.value = null;
      },
    }
  );
};

onMounted(loadPengajuans);
</script>
