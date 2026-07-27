import { ref } from 'vue';
import axios from 'axios';

const blankPeriod = () => ({
  nama: '',
  nip: '',
  periode_mulai: '',
  periode_selesai: '',
});

const normalizePeriods = (initialData = {}) => {
  if (Array.isArray(initialData.kepala_bppu_periods) && initialData.kepala_bppu_periods.length > 0) {
    return initialData.kepala_bppu_periods.map((period) => ({
      nama: period.nama || '',
      nip: period.nip || '',
      periode_mulai: period.periode_mulai || '',
      periode_selesai: period.periode_selesai || '',
    }));
  }

  return [{
    nama: initialData.kepala_bppu_nama || '',
    nip: initialData.kepala_bppu_nip || '',
    periode_mulai: '',
    periode_selesai: '',
  }];
};

export function useReportSettings(initialData = {}) {
  const reportFormLoading = ref(false);
  const reportForm = ref({
    kepala_bppu_periods: normalizePeriods(initialData),
  });

  const saveReportSettings = async () => {
    reportFormLoading.value = true;
    try {
      await axios.post('/pengelola/settings/report', reportForm.value);
      alert('Pengaturan laporan berhasil disimpan');
    } catch (error) {
      console.error('Error saving report settings:', error);
      alert(error.response?.data?.message || 'Gagal menyimpan pengaturan laporan');
    } finally {
      reportFormLoading.value = false;
    }
  };

  return {
    reportFormLoading,
    reportForm,
    saveReportSettings,
    blankPeriod,
  };
}
