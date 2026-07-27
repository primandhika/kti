<template>
  <div v-if="pengajuans.length > 0" class="mb-4">
    <div class="bg-amber-50 border border-amber-300 rounded-lg overflow-hidden">
      <div class="flex items-center gap-2 px-4 py-3 bg-amber-100 border-b border-amber-300">
        <svg class="w-5 h-5 text-amber-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
        <span class="text-sm font-semibold text-amber-800">
          {{ pengajuans.length }} Pengajuan Menunggu Persetujuan
        </span>
      </div>

      <div class="divide-y divide-amber-200">
        <div
          v-for="item in pengajuans"
          :key="item.id"
          class="px-4 py-3"
        >
          <div class="flex items-start justify-between gap-3">
            <div class="flex-1 min-w-0">
              <div class="flex items-center gap-2 flex-wrap">
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-amber-200 text-amber-800">
                  {{ { stok: 'Stok', nama_barang: 'Nama Barang', kode_barang: 'Kode/Barcode' }[item.jenis] ?? item.jenis }}
                </span>
                <span class="text-xs text-gray-500">oleh {{ item.diajukan_oleh }}</span>
                <span class="text-xs text-gray-400">{{ item.created_at }}</span>
              </div>
              <div class="mt-1.5 flex items-center gap-2 text-sm">
                <span class="text-gray-500 line-through text-xs">{{ item.nilai_lama }}</span>
                <svg class="w-3 h-3 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <span class="font-semibold text-gray-800">{{ item.nilai_baru }}</span>
              </div>
              <p v-if="item.keterangan" class="text-xs text-gray-500 mt-1 italic">{{ item.keterangan }}</p>
            </div>

            <!-- Aksi -->
            <div class="flex items-center gap-1.5 flex-shrink-0">
              <button
                type="button"
                :disabled="processing === item.id"
                @click="proses(item, 'setujui')"
                class="px-3 py-1.5 text-xs font-medium text-white bg-green-600 hover:bg-green-700 disabled:bg-gray-300 rounded-lg transition-colors flex items-center gap-1"
              >
                <svg v-if="processing === item.id && prosesAksi === 'setujui'" class="w-3 h-3 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Setujui
              </button>
              <button
                type="button"
                :disabled="processing === item.id"
                @click="konfirmasiTolak(item)"
                class="px-3 py-1.5 text-xs font-medium text-white bg-red-500 hover:bg-red-600 disabled:bg-gray-300 rounded-lg transition-colors flex items-center gap-1"
              >
                Tolak
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Inline tolak modal -->
    <div
      v-if="showTolakConfirm"
      class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-[60] p-4"
    >
      <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm p-5">
        <h4 class="text-sm font-semibold text-gray-800 mb-3">Tolak Pengajuan</h4>
        <p class="text-xs text-gray-600 mb-3">
          Tolak pengajuan {{ { stok: 'stok', nama_barang: 'nama barang', kode_barang: 'kode/barcode' }[tolakTarget?.jenis] ?? tolakTarget?.jenis }} dari <strong>{{ tolakTarget?.diajukan_oleh }}</strong>?
        </p>
        <textarea
          v-model="catatanTolak"
          rows="2"
          maxlength="500"
          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-400 focus:border-transparent text-sm resize-none mb-4"
          placeholder="Catatan penolakan (opsional)..."
        ></textarea>
        <div class="flex justify-end gap-2">
          <button
            type="button"
            @click="showTolakConfirm = false"
            class="px-4 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50"
          >
            Batal
          </button>
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
import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
  barang: Object,
  workUnitId: [String, Number],
});

const emit = defineEmits(['updated', 'close-modal']);

const pengajuans = ref([]);
const processing = ref(null);
const prosesAksi = ref(null);
const showTolakConfirm = ref(false);
const tolakTarget = ref(null);
const catatanTolak = ref('');

const loadPengajuans = async () => {
  if (!props.barang?.id || !props.workUnitId) return;
  try {
    const res = await fetch(`/pengelola/opname-stock/${props.workUnitId}/barang/${props.barang.id}/pengajuan`);
    const data = await res.json();
    pengajuans.value = data.pengajuans ?? [];
  } catch {
    pengajuans.value = [];
  }
};

const konfirmasiTolak = (item) => {
  tolakTarget.value = item;
  catatanTolak.value = '';
  showTolakConfirm.value = true;
};

const proses = (item, aksi) => {
  processing.value = item.id;
  prosesAksi.value = aksi;
  showTolakConfirm.value = false;

  router.post(
    `/pengelola/opname-stock/${props.workUnitId}/barang/${props.barang.id}/pengajuan/${item.id}/proses`,
    {
      aksi,
      catatan_pengelola: aksi === 'tolak' ? catatanTolak.value : undefined,
    },
    {
      onSuccess: () => {
        pengajuans.value = pengajuans.value.filter(p => p.id !== item.id);
        if (aksi === 'setujui') {
          emit('updated', { jenis: item.jenis, nilai_baru: item.nilai_baru });
          emit('close-modal');
        } else {
          emit('updated');
        }
      },
      onError: () => { },
      onFinish: () => {
        processing.value = null;
        prosesAksi.value = null;
      },
    }
  );
};

watch(() => props.barang?.id, () => {
  loadPengajuans();
}, { immediate: true });
</script>
