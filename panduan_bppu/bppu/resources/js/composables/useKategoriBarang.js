import { ref } from 'vue';
import axios from 'axios';

export function useKategoriBarang(initialData = []) {
  const kategoriBarangs = ref(initialData);
  const kategoriBarangsLoading = ref(false);
  const showKategoriBarangModal = ref(false);
  const editingKategoriBarang = ref(null);
  const kategoriBarangFormLoading = ref(false);
  const kategoriBarangForm = ref({
    nama: '',
    kode: '',
    deskripsi: '',
    is_active: true,
  });

  const dispatchToast = (message, type = 'success') => {
    window.dispatchEvent(new CustomEvent('toast', { detail: { type, message } }));
  };

  const loadKategoriBarangs = async () => {
    kategoriBarangsLoading.value = true;
    try {
      const response = await axios.get('/pengelola/kategori-barang');
      kategoriBarangs.value = response.data;
    } catch (error) {
      console.error('Error loading kategori barang:', error);
      dispatchToast('Gagal memuat data kategori barang', 'error');
    } finally {
      kategoriBarangsLoading.value = false;
    }
  };

  const openAddKategoriBarangModal = () => {
    editingKategoriBarang.value = null;
    kategoriBarangForm.value = {
      nama: '',
      kode: '',
      deskripsi: '',
      is_active: true,
    };
    showKategoriBarangModal.value = true;
  };

  const openEditKategoriBarangModal = (kategori) => {
    editingKategoriBarang.value = kategori;
    kategoriBarangForm.value = {
      nama: kategori.nama,
      kode: kategori.kode || '',
      deskripsi: kategori.deskripsi || '',
      is_active: kategori.is_active,
    };
    showKategoriBarangModal.value = true;
  };

  const closeKategoriBarangModal = () => {
    showKategoriBarangModal.value = false;
    editingKategoriBarang.value = null;
  };

  const saveKategoriBarang = async () => {
    kategoriBarangFormLoading.value = true;
    try {
      if (editingKategoriBarang.value) {
        await axios.put(`/pengelola/kategori-barang/${editingKategoriBarang.value.id}`, kategoriBarangForm.value);
        dispatchToast('Kategori barang berhasil diperbarui');
      } else {
        await axios.post('/pengelola/kategori-barang', kategoriBarangForm.value);
        dispatchToast('Kategori barang berhasil ditambahkan');
      }
      closeKategoriBarangModal();
      loadKategoriBarangs();
    } catch (error) {
      console.error('Error saving kategori barang:', error);
      dispatchToast(error.response?.data?.message || 'Gagal menyimpan kategori barang', 'error');
    } finally {
      kategoriBarangFormLoading.value = false;
    }
  };

  const toggleKategoriBarangStatus = async (kategori) => {
    try {
      await axios.post(`/pengelola/kategori-barang/${kategori.id}/toggle`);
      loadKategoriBarangs();
    } catch (error) {
      console.error('Error toggling kategori barang status:', error);
      dispatchToast('Gagal mengubah status kategori', 'error');
    }
  };

  const deleteKategoriBarang = async (kategori) => {
    if (!confirm(`Apakah Anda yakin ingin menghapus kategori "${kategori.nama}"?`)) {
      return;
    }

    try {
      await axios.delete(`/pengelola/kategori-barang/${kategori.id}`);
      dispatchToast('Kategori barang berhasil dihapus');
      loadKategoriBarangs();
    } catch (error) {
      console.error('Error deleting kategori barang:', error);
      dispatchToast(error.response?.data?.message || 'Gagal menghapus kategori barang', 'error');
    }
  };

  const reorderKategoriBarangs = async (reorderedItems) => {
    const previousOrder = [...kategoriBarangs.value];
    kategoriBarangs.value = reorderedItems;

    try {
      const response = await axios.post('/pengelola/kategori-barang/reorder', {
        items: reorderedItems.map((item, index) => ({
          id: item.id,
          display_order: index + 1,
        })),
      });

      if (response.data.success) {
        kategoriBarangs.value = response.data.data;
        dispatchToast('Urutan berhasil diperbarui');
      }
    } catch (error) {
      kategoriBarangs.value = previousOrder;
      dispatchToast(error.response?.data?.message || 'Gagal memperbarui urutan', 'error');
    }
  };

  return {
    kategoriBarangs,
    kategoriBarangsLoading,
    showKategoriBarangModal,
    editingKategoriBarang,
    kategoriBarangFormLoading,
    kategoriBarangForm,
    loadKategoriBarangs,
    openAddKategoriBarangModal,
    openEditKategoriBarangModal,
    closeKategoriBarangModal,
    saveKategoriBarang,
    toggleKategoriBarangStatus,
    deleteKategoriBarang,
    reorderKategoriBarangs,
  };
}
