<template>
  <div class="space-y-6">
    <div>
      <h3 class="text-lg font-semibold text-gray-900 mb-4">Pengaturan Laporan</h3>
      <p class="text-sm text-gray-500 mb-6">Konfigurasi kepala BPPU yang akan ditampilkan pada laporan PDF sesuai periode laporan</p>
    </div>

    <form @submit.prevent="$emit('save')" class="space-y-6">
      <div class="space-y-4">
        <div
          v-for="(period, index) in form.kepala_bppu_periods"
          :key="index"
          class="border border-gray-200 rounded-lg p-4 space-y-4"
        >
          <div class="flex items-center justify-between gap-3">
            <h4 class="text-sm font-semibold text-gray-900">Periode Kepala BPPU {{ index + 1 }}</h4>
            <button
              v-if="form.kepala_bppu_periods.length > 1"
              type="button"
              @click="removePeriod(index)"
              class="text-sm font-medium text-red-600 hover:text-red-700"
            >
              Hapus
            </button>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Nama Kepala BPPU <span class="text-red-500">*</span>
              </label>
              <input
                v-model="period.nama"
                type="text"
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-900 focus:border-transparent"
                placeholder="Contoh: Dr. Nama Kepala BPPU, M.Pd."
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">NIP Kepala BPPU</label>
              <input
                v-model="period.nip"
                type="text"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-900 focus:border-transparent"
                placeholder="Contoh: 198501012010121001"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Periode Mulai <span class="text-red-500">*</span>
              </label>
              <input
                v-model="period.periode_mulai"
                type="date"
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-900 focus:border-transparent"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Periode Selesai <span class="text-red-500">*</span>
              </label>
              <input
                v-model="period.periode_selesai"
                type="date"
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-900 focus:border-transparent"
              />
            </div>
          </div>
        </div>
      </div>

      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pt-4 border-t border-gray-200">
        <button
          type="button"
          @click="addPeriod"
          class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-semibold"
        >
          Tambah Periode
        </button>
        <button
          type="submit"
          :disabled="loading"
          class="px-6 py-2.5 bg-primary-900 text-white rounded-lg hover:bg-primary-950 transition-colors font-semibold disabled:opacity-50"
        >
          {{ loading ? 'Menyimpan...' : 'Simpan Perubahan' }}
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
const props = defineProps({
  form: Object,
  loading: Boolean,
});

defineEmits(['save']);

const blankPeriod = () => ({
  nama: '',
  nip: '',
  periode_mulai: '',
  periode_selesai: '',
});

const ensurePeriods = () => {
  if (!Array.isArray(props.form.kepala_bppu_periods)) {
    props.form.kepala_bppu_periods = [blankPeriod()];
  }
};

const addPeriod = () => {
  ensurePeriods();
  props.form.kepala_bppu_periods.push(blankPeriod());
};

const removePeriod = (index) => {
  ensurePeriods();
  props.form.kepala_bppu_periods.splice(index, 1);

  if (props.form.kepala_bppu_periods.length === 0) {
    props.form.kepala_bppu_periods.push(blankPeriod());
  }
};
</script>
