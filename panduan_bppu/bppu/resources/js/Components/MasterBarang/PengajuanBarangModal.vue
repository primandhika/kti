<template>
  <div
    v-if="show"
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
    @click.self="$emit('close')"
  >
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md">
      <!-- Header -->
      <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200">
        <h3 class="text-base font-semibold text-gray-800">Ajukan Perubahan Barang</h3>
        <button @click="$emit('close')" class="text-gray-400 hover:text-gray-600">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <!-- Body -->
      <div class="px-5 py-4 space-y-4">
        <!-- Info barang -->
        <div class="bg-gray-50 rounded-lg px-4 py-3 text-sm">
          <div class="text-gray-500 text-xs mb-0.5">Barang</div>
          <div class="font-semibold text-gray-800">{{ barang?.nama_barang }}</div>
          <div class="text-xs text-gray-500 font-mono mt-0.5">{{ barang?.kode_barang }}</div>
        </div>

        <!-- Jenis pengajuan -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Perubahan</label>
          <div class="grid grid-cols-3 gap-2">
            <button
              type="button"
              @click="form.jenis = 'stok'"
              :class="[
                'px-3 py-2.5 rounded-lg border text-sm font-medium transition-colors',
                form.jenis === 'stok'
                  ? 'bg-[#996600] text-white border-[#996600]'
                  : 'bg-white text-gray-700 border-gray-300 hover:border-[#996600]'
              ]"
            >
              Stok
            </button>
            <button
              type="button"
              @click="form.jenis = 'nama_barang'"
              :class="[
                'px-3 py-2.5 rounded-lg border text-sm font-medium transition-colors',
                form.jenis === 'nama_barang'
                  ? 'bg-[#996600] text-white border-[#996600]'
                  : 'bg-white text-gray-700 border-gray-300 hover:border-[#996600]'
              ]"
            >
              Nama
            </button>
            <button
              type="button"
              @click="form.jenis = 'kode_barang'"
              :class="[
                'px-3 py-2.5 rounded-lg border text-sm font-medium transition-colors',
                form.jenis === 'kode_barang'
                  ? 'bg-[#996600] text-white border-[#996600]'
                  : 'bg-white text-gray-700 border-gray-300 hover:border-[#996600]'
              ]"
            >
              Kode/Barcode
            </button>
          </div>
        </div>

        <!-- Nilai saat ini -->
        <div v-if="form.jenis">
          <div class="text-xs text-gray-500 mb-1">{{ labelSaatIni }}</div>
          <div class="text-sm font-medium text-gray-700 bg-gray-50 px-3 py-2 rounded-lg border border-gray-200 font-mono">
            {{ nilaiSaatIni }}
          </div>
        </div>

        <!-- Nilai baru -->
        <div v-if="form.jenis">
          <label class="block text-sm font-medium text-gray-700 mb-1">
            {{ labelNilaiBaru }} <span class="text-red-500">*</span>
          </label>
          <input
            v-if="form.jenis === 'stok'"
            v-model.number="form.nilai_baru"
            type="number"
            min="0"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent text-sm"
            placeholder="Masukkan jumlah stok yang sebenarnya"
          />
          <input
            v-else
            v-model="form.nilai_baru"
            type="text"
            maxlength="255"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent text-sm font-mono"
            :placeholder="form.jenis === 'kode_barang' ? 'Masukkan kode barcode yang benar' : 'Masukkan nama barang yang benar'"
          />
          <p v-if="errors.nilai_baru" class="text-xs text-red-600 mt-1">{{ errors.nilai_baru }}</p>
        </div>

        <!-- Keterangan -->
        <div v-if="form.jenis">
          <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan <span class="text-gray-400 font-normal">(opsional)</span></label>
          <textarea
            v-model="form.keterangan"
            rows="2"
            maxlength="500"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent text-sm resize-none"
            placeholder="Alasan pengajuan..."
          ></textarea>
        </div>

        <!-- Error pengajuan -->
        <div v-if="errors.pengajuan" class="bg-red-50 border border-red-200 rounded-lg px-3 py-2 text-sm text-red-700">
          {{ errors.pengajuan }}
        </div>
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
  barang: Object,
  workUnitId: [String, Number],
});

const emit = defineEmits(['close']);

const submitting = ref(false);
const errors = ref({});

const form = ref({
  jenis: 'stok',
  nilai_baru: '',
  keterangan: '',
});

const labelSaatIni = computed(() => {
  if (form.value.jenis === 'stok') return 'Stok saat ini';
  if (form.value.jenis === 'nama_barang') return 'Nama saat ini';
  return 'Kode/Barcode saat ini';
});

const nilaiSaatIni = computed(() => {
  if (form.value.jenis === 'stok') return props.barang?.stok ?? 0;
  if (form.value.jenis === 'nama_barang') return props.barang?.nama_barang ?? '-';
  return props.barang?.kode_barang ?? '-';
});

const labelNilaiBaru = computed(() => {
  if (form.value.jenis === 'stok') return 'Stok yang Benar';
  if (form.value.jenis === 'nama_barang') return 'Nama Baru';
  return 'Kode/Barcode yang Benar';
});

const canSubmit = computed(() => {
  if (!form.value.jenis) return false;
  if (form.value.nilai_baru === '' || form.value.nilai_baru === null) return false;
  if (form.value.jenis === 'stok' && isNaN(Number(form.value.nilai_baru))) return false;
  return true;
});

const submit = () => {
  if (!canSubmit.value || submitting.value) return;
  errors.value = {};
  submitting.value = true;

  router.post(
    `/pengelola/opname-stock/${props.workUnitId}/barang/${props.barang.id}/pengajuan`,
    {
      jenis: form.value.jenis,
      nilai_baru: String(form.value.nilai_baru),
      keterangan: form.value.keterangan,
    },
    {
      onSuccess: () => {
        toast.success('Pengajuan berhasil dikirim. Menunggu persetujuan pengelola.');
        emit('close');
      },
      onError: (err) => {
        errors.value = err;
        if (err.pengajuan) {
          toast.error(err.pengajuan);
        } else {
          toast.error('Gagal mengirim pengajuan');
        }
      },
      onFinish: () => {
        submitting.value = false;
      },
    }
  );
};

// Reset form saat modal dibuka
watch(() => props.show, (val) => {
  if (val) {
    form.value = {
      jenis: 'stok',
      nilai_baru: props.barang?.stok ?? '',
      keterangan: '',
    };
    errors.value = {};
  }
});

// Update nilai_baru default saat jenis berubah
watch(() => form.value.jenis, (jenis) => {
  if (jenis === 'stok') {
    form.value.nilai_baru = props.barang?.stok ?? '';
  } else if (jenis === 'nama_barang') {
    form.value.nilai_baru = props.barang?.nama_barang ?? '';
  } else {
    form.value.nilai_baru = props.barang?.kode_barang ?? '';
  }
});
</script>
