import { ref } from 'vue';
import axios from 'axios';

export function usePointsSettings(initialData = {}, initialRules = []) {
  const pointsFormLoading = ref(false);
  const pointsForm = ref({
    minimal_poin_redeem: initialData.minimal_poin_redeem || 5000,
    kurs_poin_ke_rupiah: initialData.kurs_poin_ke_rupiah || 10,
  });

  // Point rules
  const pointRules = ref(initialRules);
  const pointRulesLoading = ref(false);
  const showRuleModal = ref(false);
  const editingRule = ref(null);
  const ruleFormLoading = ref(false);
  const ruleForm = ref({
    name: '',
    min_amount: 0,
    max_amount: null,
    points: 0,
    description: '',
    is_active: true,
  });

  const dispatchToast = (message, type = 'success') => {
    window.dispatchEvent(new CustomEvent('toast', { detail: { type, message } }));
  };

  const savePointsSettings = async () => {
    pointsFormLoading.value = true;
    try {
      await axios.post('/pengelola/settings/points', pointsForm.value);
      dispatchToast('Pengaturan poin berhasil disimpan');
    } catch (error) {
      dispatchToast(error.response?.data?.message || 'Gagal menyimpan pengaturan poin', 'error');
    } finally {
      pointsFormLoading.value = false;
    }
  };

  const openAddRuleModal = () => {
    editingRule.value = null;
    ruleForm.value = {
      name: '',
      min_amount: 0,
      max_amount: null,
      points: 0,
      description: '',
      is_active: true,
    };
    showRuleModal.value = true;
  };

  const openEditRuleModal = (rule) => {
    editingRule.value = rule;
    ruleForm.value = {
      name: rule.name,
      min_amount: parseFloat(rule.min_amount),
      max_amount: rule.max_amount ? parseFloat(rule.max_amount) : null,
      points: rule.points,
      description: rule.description || '',
      is_active: rule.is_active,
    };
    showRuleModal.value = true;
  };

  const closeRuleModal = () => {
    showRuleModal.value = false;
    editingRule.value = null;
  };

  const saveRule = async () => {
    ruleFormLoading.value = true;
    try {
      let response;
      if (editingRule.value) {
        response = await axios.put(`/pengelola/settings/point-rules/${editingRule.value.id}`, ruleForm.value);
      } else {
        response = await axios.post('/pengelola/settings/point-rules', ruleForm.value);
      }
      if (response.data.success) {
        pointRules.value = response.data.data;
        dispatchToast(response.data.message);
        closeRuleModal();
      }
    } catch (error) {
      dispatchToast(error.response?.data?.message || 'Gagal menyimpan rule poin', 'error');
    } finally {
      ruleFormLoading.value = false;
    }
  };

  const deleteRule = async (rule) => {
    if (!confirm(`Hapus rule "${rule.name}"?`)) return;
    try {
      const response = await axios.delete(`/pengelola/settings/point-rules/${rule.id}`);
      if (response.data.success) {
        pointRules.value = response.data.data;
        dispatchToast(response.data.message);
      }
    } catch (error) {
      dispatchToast(error.response?.data?.message || 'Gagal menghapus rule poin', 'error');
    }
  };

  const toggleRule = async (rule) => {
    try {
      const response = await axios.post(`/pengelola/settings/point-rules/${rule.id}/toggle`);
      if (response.data.success) {
        pointRules.value = response.data.data;
      }
    } catch (error) {
      dispatchToast('Gagal mengubah status rule', 'error');
    }
  };

  return {
    pointsForm,
    pointsFormLoading,
    savePointsSettings,
    pointRules,
    pointRulesLoading,
    showRuleModal,
    editingRule,
    ruleForm,
    ruleFormLoading,
    openAddRuleModal,
    openEditRuleModal,
    closeRuleModal,
    saveRule,
    deleteRule,
    toggleRule,
  };
}
