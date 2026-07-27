import { ref, computed } from 'vue';
import axios from 'axios';

export function useSubKategoriMenu(initialSubKategoris = []) {
    const subKategoris = ref(initialSubKategoris);
    const subKategorisLoading = ref(false);
    const showSubKategoriModal = ref(false);
    const editingSubKategori = ref(null);
    const subKategoriFormLoading = ref(false);

    const subKategoriForm = ref({
        nama: '',
        urutan: 0,
    });

    const openAddSubKategoriModal = () => {
        editingSubKategori.value = null;
        subKategoriForm.value = {
            nama: '',
            urutan: subKategoris.value.length + 1,
        };
        showSubKategoriModal.value = true;
    };

    const openEditSubKategoriModal = (subKategori) => {
        editingSubKategori.value = subKategori;
        subKategoriForm.value = {
            nama: subKategori.nama,
            urutan: subKategori.urutan,
        };
        showSubKategoriModal.value = true;
    };

    const closeSubKategoriModal = () => {
        showSubKategoriModal.value = false;
        editingSubKategori.value = null;
        subKategoriForm.value = {
            nama: '',
            urutan: 0,
        };
    };

    const saveSubKategori = async () => {
        subKategoriFormLoading.value = true;

        const url = editingSubKategori.value
            ? `/pengelola/setting/sub-kategori-menu/${editingSubKategori.value.id}`
            : '/pengelola/setting/sub-kategori-menu';

        const method = editingSubKategori.value ? 'put' : 'post';

        try {
            const response = await axios[method](url, subKategoriForm.value);

            if (response.data.success) {
                // Update local state with new data
                subKategoris.value = response.data.data;

                // Show success toast
                window.dispatchEvent(new CustomEvent('toast', {
                    detail: {
                        type: 'success',
                        message: response.data.message,
                    }
                }));

                closeSubKategoriModal();
            }
        } catch (error) {
            const message = error.response?.data?.message || 'Terjadi kesalahan saat menyimpan';
            window.dispatchEvent(new CustomEvent('toast', {
                detail: {
                    type: 'error',
                    message: message,
                }
            }));
        } finally {
            subKategoriFormLoading.value = false;
        }
    };

    const deleteSubKategori = async (id) => {
        if (confirm('Apakah Anda yakin ingin menghapus sub kategori ini?')) {
            try {
                const response = await axios.delete(`/pengelola/setting/sub-kategori-menu/${id}`);

                if (response.data.success) {
                    // Update local state with new data
                    subKategoris.value = response.data.data;

                    // Show success toast
                    window.dispatchEvent(new CustomEvent('toast', {
                        detail: {
                            type: 'success',
                            message: response.data.message,
                        }
                    }));
                }
            } catch (error) {
                const message = error.response?.data?.message || 'Terjadi kesalahan saat menghapus';
                window.dispatchEvent(new CustomEvent('toast', {
                    detail: {
                        type: 'error',
                        message: message,
                    }
                }));
            }
        }
    };

    const reorderSubKategoris = async (reorderedItems) => {
        // Optimistically update UI
        const previousOrder = [...subKategoris.value];
        subKategoris.value = reorderedItems;

        try {
            const response = await axios.post('/pengelola/setting/sub-kategori-menu/reorder', {
                items: reorderedItems.map((item, index) => ({
                    id: item.id,
                    urutan: index + 1
                }))
            });

            if (response.data.success) {
                // Update with server response
                subKategoris.value = response.data.data;

                // Show success toast
                window.dispatchEvent(new CustomEvent('toast', {
                    detail: {
                        type: 'success',
                        message: 'Urutan berhasil diperbarui',
                    }
                }));
            }
        } catch (error) {
            // Revert on error
            subKategoris.value = previousOrder;

            const message = error.response?.data?.message || 'Terjadi kesalahan saat mengubah urutan';
            window.dispatchEvent(new CustomEvent('toast', {
                detail: {
                    type: 'error',
                    message: message,
                }
            }));
        }
    };

    return {
        subKategoris,
        subKategorisLoading,
        showSubKategoriModal,
        editingSubKategori,
        subKategoriForm,
        subKategoriFormLoading,
        openAddSubKategoriModal,
        openEditSubKategoriModal,
        closeSubKategoriModal,
        saveSubKategori,
        deleteSubKategori,
        reorderSubKategoris,
    };
}
