import { ref } from 'vue';
import axios from 'axios';

export function useWorkUnits(initialData = []) {
  const workUnits = ref(initialData);
  const workUnitsLoading = ref(false);
  const showWorkUnitModal = ref(false);
  const editingUnit = ref(null);
  const workUnitFormLoading = ref(false);
  const workUnitForm = ref({
    name: '',
    type: '',
    description: '',
    location: '',
    manager_name: '',
    contact_phone: '',
    contact_email: '',
    operating_hours: '',
    is_active: true,
    logoFile: null,
    logoPreview: null,
  });

  const loadWorkUnits = async () => {
    workUnitsLoading.value = true;
    try {
      const response = await axios.get('/pengelola/unit-kerja');
      workUnits.value = response.data;
    } catch (error) {
      console.error('Error loading work units:', error);
      alert('Gagal memuat data unit kerja');
    } finally {
      workUnitsLoading.value = false;
    }
  };

  const handleWorkUnitLogoUpload = (event) => {
    const file = event.target.files[0];
    if (!file) return;

    if (file.size > 2048 * 1024) {
      alert('Ukuran file terlalu besar. Maksimal 2MB');
      return;
    }

    workUnitForm.value.logoFile = file;
    workUnitForm.value.logoPreview = URL.createObjectURL(file);
  };

  const removeWorkUnitLogo = () => {
    workUnitForm.value.logoFile = null;
    workUnitForm.value.logoPreview = null;
  };

  const openAddModal = () => {
    editingUnit.value = null;
    workUnitForm.value = {
      name: '',
      type: '',
      description: '',
      location: '',
      manager_name: '',
      contact_phone: '',
      contact_email: '',
      operating_hours: '',
      is_active: true,
      logoFile: null,
      logoPreview: null,
    };
    showWorkUnitModal.value = true;
  };

  const openEditModal = (unit) => {
    editingUnit.value = unit;
    workUnitForm.value = {
      name: unit.name,
      type: unit.type,
      description: unit.description || '',
      location: unit.location || '',
      manager_name: unit.manager_name || '',
      contact_phone: unit.contact_phone || '',
      contact_email: unit.contact_email || '',
      operating_hours: unit.operating_hours || '',
      is_active: unit.is_active,
      logoFile: null,
      logoPreview: null,
    };
    showWorkUnitModal.value = true;
  };

  const closeWorkUnitModal = () => {
    showWorkUnitModal.value = false;
    editingUnit.value = null;
    if (workUnitForm.value.logoPreview) {
      URL.revokeObjectURL(workUnitForm.value.logoPreview);
    }
  };

  const saveWorkUnit = async () => {
    workUnitFormLoading.value = true;
    try {
      const formData = new FormData();
      formData.append('name', workUnitForm.value.name);
      formData.append('type', workUnitForm.value.type);
      formData.append('description', workUnitForm.value.description || '');
      formData.append('location', workUnitForm.value.location || '');
      formData.append('manager_name', workUnitForm.value.manager_name || '');
      formData.append('contact_phone', workUnitForm.value.contact_phone || '');
      formData.append('contact_email', workUnitForm.value.contact_email || '');
      formData.append('operating_hours', workUnitForm.value.operating_hours || '');
      formData.append('is_active', workUnitForm.value.is_active ? '1' : '0');

      if (workUnitForm.value.logoFile) {
        formData.append('logo', workUnitForm.value.logoFile);
      }

      if (editingUnit.value) {
        formData.append('_method', 'PUT');
        await axios.post(`/pengelola/unit-kerja/${editingUnit.value.id}`, formData, {
          headers: { 'Content-Type': 'multipart/form-data' }
        });
        alert('Unit kerja berhasil diperbarui');
      } else {
        await axios.post('/pengelola/unit-kerja', formData, {
          headers: { 'Content-Type': 'multipart/form-data' }
        });
        alert('Unit kerja berhasil ditambahkan');
      }
      closeWorkUnitModal();
      loadWorkUnits();
    } catch (error) {
      console.error('Error saving work unit:', error);
      alert('Gagal menyimpan unit kerja');
    } finally {
      workUnitFormLoading.value = false;
    }
  };

  const toggleUnitActive = async (unit) => {
    try {
      await axios.post(`/pengelola/unit-kerja/${unit.id}/toggle`);
      loadWorkUnits();
    } catch (error) {
      console.error('Error toggling unit status:', error);
      alert('Gagal mengubah status unit');
    }
  };

  const deleteUnit = async (unit) => {
    if (!confirm(`Apakah Anda yakin ingin menghapus unit "${unit.name}"?`)) {
      return;
    }

    try {
      await axios.delete(`/pengelola/unit-kerja/${unit.id}`);
      alert('Unit kerja berhasil dihapus');
      loadWorkUnits();
    } catch (error) {
      console.error('Error deleting work unit:', error);
      alert('Gagal menghapus unit kerja');
    }
  };

  return {
    workUnits,
    workUnitsLoading,
    showWorkUnitModal,
    editingUnit,
    workUnitFormLoading,
    workUnitForm,
    loadWorkUnits,
    handleWorkUnitLogoUpload,
    removeWorkUnitLogo,
    openAddModal,
    openEditModal,
    closeWorkUnitModal,
    saveWorkUnit,
    toggleUnitActive,
    deleteUnit,
  };
}
