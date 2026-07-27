import { ref } from 'vue';
import axios from 'axios';

export function useSelfOrderSettings(initialData = {}) {
    const loading = ref(false);

    const form = ref({
        kantin_minimal_order: initialData.kantin_minimal_order ?? 0,
        belanja_minimal_order: initialData.belanja_minimal_order ?? 20000,
        kantin_biaya_layanan_aktif: initialData.kantin_biaya_layanan_aktif ?? false,
        kantin_biaya_layanan: initialData.kantin_biaya_layanan ?? 0,
        belanja_biaya_layanan_aktif: initialData.belanja_biaya_layanan_aktif ?? false,
        belanja_biaya_layanan: initialData.belanja_biaya_layanan ?? 0,
        kantin_verif_lokasi_aktif: initialData.kantin_verif_lokasi_aktif ?? false,
    });

    const save = async () => {
        loading.value = true;
        try {
            await axios.post('/pengelola/settings/selforder', form.value);
            window.dispatchEvent(new CustomEvent('toast', {
                detail: { message: 'Pengaturan self-order berhasil disimpan', type: 'success' }
            }));
        } catch (error) {
            window.dispatchEvent(new CustomEvent('toast', {
                detail: {
                    message: error.response?.data?.message || 'Gagal menyimpan pengaturan',
                    type: 'error'
                }
            }));
        } finally {
            loading.value = false;
        }
    };

    return { form, loading, save };
}
