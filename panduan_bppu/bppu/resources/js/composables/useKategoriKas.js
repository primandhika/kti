import { ref } from 'vue';
import axios from 'axios';

export function useKategoriKas(initialData = []) {
  const kategoriKas = ref(initialData);
  const kategoriKasLoading = ref(false);
  const showKategoriKasModal = ref(false);
  const editingKategoriKas = ref(null);
  const kategoriKasFormLoading = ref(false);
  const kategoriKasForm = ref({
    nama: '',
    tipe: '',
    kode: '',
    deskripsi: '',
    is_active: true,
  });

  const loadKategoriKas = async () => {
    kategoriKasLoading.value = true;
    try {
      const response = await axios.get('/pengelola/kategori-transaksi');
      kategoriKas.value = response.data;
    } catch (error) {
      console.error('Error loading kategori kas:', error);
      alert('Gagal memuat data kategori kas');
    } finally {
      kategoriKasLoading.value = false;
    }
  };

  const openAddKategoriKasModal = () => {
    editingKategoriKas.value = null;
    kategoriKasForm.value = {
      nama: '',
      tipe: '',
      kode: '',
      deskripsi: '',
      is_active: true,
    };
    showKategoriKasModal.value = true;
  };

  const openEditKategoriKasModal = (kategori) => {
    editingKategoriKas.value = kategori;
    kategoriKasForm.value = {
      nama: kategori.nama,
      tipe: kategori.tipe,
      kode: kategori.kode || '',
      deskripsi: kategori.deskripsi || '',
      is_active: kategori.is_active,
    };
    showKategoriKasModal.value = true;
  };

  const closeKategoriKasModal = () => {
    showKategoriKasModal.value = false;
    editingKategoriKas.value = null;
  };

  const saveKategoriKas = async () => {
    kategoriKasFormLoading.value = true;
    try {
      if (editingKategoriKas.value) {
        await axios.put(`/pengelola/kategori-transaksi/${editingKategoriKas.value.id}`, kategoriKasForm.value);
        alert('Kategori kas berhasil diperbarui');
      } else {
        await axios.post('/pengelola/kategori-transaksi', kategoriKasForm.value);
        alert('Kategori kas berhasil ditambahkan');
      }
      closeKategoriKasModal();
      loadKategoriKas();
    } catch (error) {
      console.error('Error saving kategori kas:', error);
      alert(error.response?.data?.message || 'Gagal menyimpan kategori kas');
    } finally {
      kategoriKasFormLoading.value = false;
    }
  };

  const toggleKategoriKasStatus = async (kategori) => {
    try {
      await axios.post(`/pengelola/kategori-transaksi/${kategori.id}/toggle`);
      loadKategoriKas();
    } catch (error) {
      console.error('Error toggling kategori kas status:', error);
      alert('Gagal mengubah status kategori');
    }
  };

  const deleteKategoriKas = async (kategori) => {
    if (!confirm(`Apakah Anda yakin ingin menghapus kategori "${kategori.nama}"?`)) {
      return;
    }

    try {
      await axios.delete(`/pengelola/kategori-transaksi/${kategori.id}`);
      alert('Kategori kas berhasil dihapus');
      loadKategoriKas();
    } catch (error) {
      console.error('Error deleting kategori kas:', error);
      alert(error.response?.data?.message || 'Gagal menghapus kategori kas');
    }
  };

  return {
    kategoriKas,
    kategoriKasLoading,
    showKategoriKasModal,
    editingKategoriKas,
    kategoriKasFormLoading,
    kategoriKasForm,
    loadKategoriKas,
    openAddKategoriKasModal,
    openEditKategoriKasModal,
    closeKategoriKasModal,
    saveKategoriKas,
    toggleKategoriKasStatus,
    deleteKategoriKas,
  };
}
