import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

export function useMenuActions(baseUrl = '/pengelola/menu-kantin') {
  const showUploadModal = ref(false);
  const showEditModal = ref(false);
  const showEditMenuModal = ref(false);
  const selectedMenu = ref(null);
  const imageInput = ref(null);
  const imagePreview = ref(null);
  const selectedImageFile = ref(null);
  const uploading = ref(false);
  const updating = ref(false);
  const activeDropdown = ref(null);

  const displayForm = ref({
    deskripsi_display: '',
  });

  const menuForm = ref({
    nama_barang: '',
    kategori_id: null,
    sub_kategori: '',
    harga_jual: 0,
    stok: 0,
    tampilkan_di_menu: true,
  });

  // Upload Image
  const openUploadModal = (menu) => {
    closeDropdown();
    selectedMenu.value = menu;
    showUploadModal.value = true;
    imagePreview.value = null;
  };

  const closeUploadModal = () => {
    showUploadModal.value = false;
    selectedMenu.value = null;
    imagePreview.value = null;
    selectedImageFile.value = null;
  };

  const handleImageSelect = (event) => {
    const file = event.target.files[0];
    if (file) {
      selectedImageFile.value = file;
      const reader = new FileReader();
      reader.onload = (e) => {
        imagePreview.value = e.target.result;
      };
      reader.readAsDataURL(file);
    }
  };

  const uploadImage = () => {
    if (!selectedImageFile.value) {
      return;
    }

    const formData = new FormData();
    formData.append('gambar', selectedImageFile.value);

    uploading.value = true;

    router.post(`${baseUrl}/${selectedMenu.value.id}/display`, formData, {
      preserveState: true,
      preserveScroll: true,
      onSuccess: () => {
        closeUploadModal();
        uploading.value = false;
      },
      onError: () => {
        uploading.value = false;
      },
    });
  };

  // Edit Display
  const openEditDisplayModal = (menu) => {
    closeDropdown();
    selectedMenu.value = menu;
    displayForm.value.deskripsi_display = menu.deskripsi_display || '';
    showEditModal.value = true;
  };

  const closeEditModal = () => {
    showEditModal.value = false;
    selectedMenu.value = null;
  };

  const updateDisplay = () => {
    updating.value = true;

    router.post(`${baseUrl}/${selectedMenu.value.id}/display`, displayForm.value, {
      preserveState: true,
      preserveScroll: true,
      onSuccess: () => {
        closeEditModal();
        updating.value = false;
      },
      onError: () => {
        updating.value = false;
      },
    });
  };

  // Edit Menu
  const openEditMenuModal = (menu) => {
    closeDropdown();
    selectedMenu.value = menu;
    menuForm.value = {
      nama_barang: menu.nama_barang,
      kategori_id: menu.kategori_id,
      sub_kategori: menu.sub_kategori || '',
      harga_jual: menu.harga_jual,
      stok: menu.stok,
      tampilkan_di_menu: menu.tampilkan_di_menu ?? true,
    };
    showEditMenuModal.value = true;
  };

  const closeEditMenuModal = () => {
    showEditMenuModal.value = false;
    selectedMenu.value = null;
  };

  const updateMenu = () => {
    updating.value = true;

    router.put(`${baseUrl}/${selectedMenu.value.id}/barang`, menuForm.value, {
      preserveState: true,
      preserveScroll: true,
      onSuccess: () => {
        closeEditMenuModal();
        updating.value = false;
      },
      onError: () => {
        updating.value = false;
      },
    });
  };

  // Toggle Availability
  const toggleAvailability = (menu) => {
    router.post(`${baseUrl}/${menu.id}/toggle-availability`, {}, {
      preserveState: true,
      preserveScroll: true,
    });
  };

  // Dropdown
  const toggleDropdown = (menuId) => {
    if (activeDropdown.value === menuId) {
      activeDropdown.value = null;
    } else {
      activeDropdown.value = menuId;
    }
  };

  const closeDropdown = () => {
    activeDropdown.value = null;
  };

  return {
    // Upload
    showUploadModal,
    imageInput,
    imagePreview,
    uploading,
    openUploadModal,
    closeUploadModal,
    handleImageSelect,
    uploadImage,

    // Edit Display
    showEditModal,
    displayForm,
    openEditDisplayModal,
    closeEditModal,
    updateDisplay,

    // Edit Menu
    showEditMenuModal,
    menuForm,
    openEditMenuModal,
    closeEditMenuModal,
    updateMenu,

    // Common
    selectedMenu,
    updating,
    toggleAvailability,
    activeDropdown,
    toggleDropdown,
    closeDropdown,
  };
}
