<template>
  <component :is="layoutComponent" page-title="Setting">
    <!-- Toast Notification -->
    <Transition
      enter-active-class="transition ease-out duration-200"
      enter-from-class="opacity-0 translate-y-2"
      enter-to-class="opacity-100 translate-y-0"
      leave-active-class="transition ease-in duration-150"
      leave-from-class="opacity-100 translate-y-0"
      leave-to-class="opacity-0 -translate-y-2"
    >
      <div v-if="toast.show" class="fixed top-4 right-4 z-50 max-w-md">
        <div class="flex items-center gap-3 px-4 py-3 rounded-lg shadow-lg text-sm font-medium"
          :class="toast.type === 'success' ? 'bg-green-600 text-white' : 'bg-red-600 text-white'">
          <svg v-if="toast.type === 'success'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
          </svg>
          <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
          {{ toast.message }}
        </div>
      </div>
    </Transition>

    <div class="space-y-6">
      <!-- Header -->
      <div class="bg-gradient-to-r from-primary-900 to-primary-950 rounded-2xl shadow-xl p-8 text-white">
        <div class="flex items-center space-x-4">
          <div class="w-16 h-16 bg-white/20 rounded-xl flex items-center justify-center">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
          </div>
          <div>
            <h1 class="text-3xl font-bold">Pengaturan Sistem</h1>
            <p class="text-gray-200 mt-1">Kelola konfigurasi dan preferensi aplikasi</p>
          </div>
        </div>
      </div>

      <!-- Settings Tabs -->
      <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
        <!-- Tab Navigation -->
        <div class="border-b border-gray-200">
          <nav class="flex -mb-px overflow-x-auto">
            <button
              @click="activeTab = 'general'"
              :class="[
                'px-6 py-4 text-sm font-medium whitespace-nowrap border-b-2 transition-colors',
                activeTab === 'general'
                  ? 'border-primary-900 text-primary-900'
                  : 'border-transparent text-gray-600 hover:text-gray-900 hover:border-gray-300'
              ]"
            >
              Umum
            </button>
            <button
              @click="activeTab = 'profile'"
              :class="[
                'px-6 py-4 text-sm font-medium whitespace-nowrap border-b-2 transition-colors',
                activeTab === 'profile'
                  ? 'border-primary-900 text-primary-900'
                  : 'border-transparent text-gray-600 hover:text-gray-900 hover:border-gray-300'
              ]"
            >
              Profil
            </button>
            <button
              @click="activeTab = 'security'"
              :class="[
                'px-6 py-4 text-sm font-medium whitespace-nowrap border-b-2 transition-colors',
                activeTab === 'security'
                  ? 'border-primary-900 text-primary-900'
                  : 'border-transparent text-gray-600 hover:text-gray-900 hover:border-gray-300'
              ]"
            >
              Keamanan
            </button>
            <button
              @click="activeTab = 'notification'"
              :class="[
                'px-6 py-4 text-sm font-medium whitespace-nowrap border-b-2 transition-colors',
                activeTab === 'notification'
                  ? 'border-primary-900 text-primary-900'
                  : 'border-transparent text-gray-600 hover:text-gray-900 hover:border-gray-300'
              ]"
            >
              Notifikasi
            </button>
            <button
              v-if="$page.props.auth.user.roles.includes('sysadmin') || $page.props.auth.user.roles.includes('head') || $page.props.auth.user.roles.includes('officer')"
              @click="activeTab = 'workUnits'"
              :class="[
                'px-6 py-4 text-sm font-medium whitespace-nowrap border-b-2 transition-colors',
                activeTab === 'workUnits'
                  ? 'border-primary-900 text-primary-900'
                  : 'border-transparent text-gray-600 hover:text-gray-900 hover:border-gray-300'
              ]"
            >
              Unit Kerja
            </button>
            <button
              v-if="$page.props.auth.user.roles.includes('sysadmin') || $page.props.auth.user.roles.includes('officer')"
              @click="activeTab = 'report'"
              :class="[
                'px-6 py-4 text-sm font-medium whitespace-nowrap border-b-2 transition-colors',
                activeTab === 'report'
                  ? 'border-primary-900 text-primary-900'
                  : 'border-transparent text-gray-600 hover:text-gray-900 hover:border-gray-300'
              ]"
            >
              Laporan
            </button>
            <button
              v-if="$page.props.auth.user.roles.includes('sysadmin')"
              @click="activeTab = 'points'"
              :class="[
                'px-6 py-4 text-sm font-medium whitespace-nowrap border-b-2 transition-colors',
                activeTab === 'points'
                  ? 'border-primary-900 text-primary-900'
                  : 'border-transparent text-gray-600 hover:text-gray-900 hover:border-gray-300'
              ]"
            >
              Poin Member
            </button>
            <button
              v-if="$page.props.auth.user.roles.includes('sysadmin')"
              @click="activeTab = 'kategoriBarang'"
              :class="[
                'px-6 py-4 text-sm font-medium whitespace-nowrap border-b-2 transition-colors',
                activeTab === 'kategoriBarang'
                  ? 'border-primary-900 text-primary-900'
                  : 'border-transparent text-gray-600 hover:text-gray-900 hover:border-gray-300'
              ]"
            >
              Kategori Barang
            </button>
            <button
              v-if="$page.props.auth.user.roles.includes('sysadmin')"
              @click="activeTab = 'kategoriKas'"
              :class="[
                'px-6 py-4 text-sm font-medium whitespace-nowrap border-b-2 transition-colors',
                activeTab === 'kategoriKas'
                  ? 'border-primary-900 text-primary-900'
                  : 'border-transparent text-gray-600 hover:text-gray-900 hover:border-gray-300'
              ]"
            >
              Kategori Kas
            </button>
            <button
              v-if="$page.props.auth.user.roles.includes('sysadmin')"
              @click="activeTab = 'subKategoriMenu'"
              :class="[
                'px-6 py-4 text-sm font-medium whitespace-nowrap border-b-2 transition-colors',
                activeTab === 'subKategoriMenu'
                  ? 'border-primary-900 text-primary-900'
                  : 'border-transparent text-gray-600 hover:text-gray-900 hover:border-gray-300'
              ]"
            >
              Sub Kategori Menu
            </button>
            <button
              v-if="$page.props.auth.user.roles.includes('sysadmin')"
              @click="activeTab = 'subKategoriToko'"
              :class="[
                'px-6 py-4 text-sm font-medium whitespace-nowrap border-b-2 transition-colors',
                activeTab === 'subKategoriToko'
                  ? 'border-primary-900 text-primary-900'
                  : 'border-transparent text-gray-600 hover:text-gray-900 hover:border-gray-300'
              ]"
            >
              Sub Kategori Toko
            </button>
            <button
              v-if="$page.props.auth.user.roles.includes('sysadmin')"
              @click="activeTab = 'selfOrder'"
              :class="[
                'px-6 py-4 text-sm font-medium whitespace-nowrap border-b-2 transition-colors',
                activeTab === 'selfOrder'
                  ? 'border-primary-900 text-primary-900'
                  : 'border-transparent text-gray-600 hover:text-gray-900 hover:border-gray-300'
              ]"
            >
              Self-Order
            </button>
          </nav>
        </div>

        <!-- Tab Content -->
        <div class="p-6">
          <GeneralTab v-show="activeTab === 'general'" />
          <ProfileTab v-show="activeTab === 'profile'" />
          <SecurityTab v-show="activeTab === 'security'" />
          <NotificationTab v-show="activeTab === 'notification'" />
          <WorkUnitsTab
            v-show="activeTab === 'workUnits'"
            :units-list="workUnitsData.workUnits.value"
            :loading="workUnitsData.workUnitsLoading.value"
            @add-unit="workUnitsData.openAddModal"
            @edit-unit="workUnitsData.openEditModal"
            @delete-unit="workUnitsData.deleteUnit"
            @toggle-unit-active="workUnitsData.toggleUnitActive"
          />
          <ReportTab
            v-show="activeTab === 'report'"
            :form="reportSettingsData.reportForm.value"
            :loading="reportSettingsData.reportFormLoading.value"
            @save="reportSettingsData.saveReportSettings"
          />
          <PointsTab
            v-show="activeTab === 'points'"
            :form="pointsSettingsData.pointsForm.value"
            :loading="pointsSettingsData.pointsFormLoading.value"
            :point-rules="pointsSettingsData.pointRules.value"
            :show-rule-modal="pointsSettingsData.showRuleModal.value"
            :editing-rule="pointsSettingsData.editingRule.value"
            :rule-form="pointsSettingsData.ruleForm.value"
            :rule-form-loading="pointsSettingsData.ruleFormLoading.value"
            @save="pointsSettingsData.savePointsSettings"
            @add-rule="pointsSettingsData.openAddRuleModal"
            @edit-rule="pointsSettingsData.openEditRuleModal"
            @delete-rule="pointsSettingsData.deleteRule"
            @toggle-rule="pointsSettingsData.toggleRule"
            @save-rule="pointsSettingsData.saveRule"
            @close-rule-modal="pointsSettingsData.closeRuleModal"
          />
          <KategoriBarangTab
            v-show="activeTab === 'kategoriBarang'"
            :kategori-list="kategoriBarangData.kategoriBarangs.value"
            :loading="kategoriBarangData.kategoriBarangsLoading.value"
            @add-kategori="kategoriBarangData.openAddKategoriBarangModal"
            @edit-kategori="kategoriBarangData.openEditKategoriBarangModal"
            @delete-kategori="kategoriBarangData.deleteKategoriBarang"
            @toggle-status="kategoriBarangData.toggleKategoriBarangStatus"
            @reorder="kategoriBarangData.reorderKategoriBarangs"
          />
          <KategoriKasTab
            v-show="activeTab === 'kategoriKas'"
            :kategori-list="kategoriKasData.kategoriKas.value"
            :loading="kategoriKasData.kategoriKasLoading.value"
            @add-kategori="kategoriKasData.openAddKategoriKasModal"
            @edit-kategori="kategoriKasData.openEditKategoriKasModal"
            @delete-kategori="kategoriKasData.deleteKategoriKas"
            @toggle-status="kategoriKasData.toggleKategoriKasStatus"
          />
          <SubKategoriMenuTab
            v-show="activeTab === 'subKategoriMenu'"
            :sub-kategori-list="subKategoriData.subKategoris.value"
            :loading="subKategoriData.subKategorisLoading.value"
            @add-sub-kategori="subKategoriData.openAddSubKategoriModal"
            @edit-sub-kategori="subKategoriData.openEditSubKategoriModal"
            @delete-sub-kategori="subKategoriData.deleteSubKategori"
            @reorder="subKategoriData.reorderSubKategoris"
          />
          <SubKategoriTokoTab
            v-show="activeTab === 'subKategoriToko'"
            :sub-kategori-list="subKategoriTokoData.subKategoris.value"
            :loading="subKategoriTokoData.subKategorisLoading.value"
            @add-sub-kategori="subKategoriTokoData.openAddSubKategoriModal"
            @edit-sub-kategori="subKategoriTokoData.openEditSubKategoriModal"
            @delete-sub-kategori="subKategoriTokoData.deleteSubKategori"
            @reorder="subKategoriTokoData.reorderSubKategoris"
          />
          <SelfOrderTab
            v-show="activeTab === 'selfOrder'"
            :form="selfOrderSettingsData.form.value"
            :loading="selfOrderSettingsData.loading.value"
            @save="selfOrderSettingsData.save"
          />
        </div>
      </div>
    </div>

    <!-- Modals -->
    <WorkUnitModal
      v-model="workUnitsData.showWorkUnitModal.value"
      :editing-unit="workUnitsData.editingUnit.value"
      :form="workUnitsData.workUnitForm.value"
      :loading="workUnitsData.workUnitFormLoading.value"
      @close="workUnitsData.closeWorkUnitModal"
      @save="workUnitsData.saveWorkUnit"
      @upload-logo="workUnitsData.handleWorkUnitLogoUpload"
      @remove-logo="workUnitsData.removeWorkUnitLogo"
    />

    <KategoriBarangModal
      v-model="kategoriBarangData.showKategoriBarangModal.value"
      :editing-kategori="kategoriBarangData.editingKategoriBarang.value"
      :form="kategoriBarangData.kategoriBarangForm.value"
      :loading="kategoriBarangData.kategoriBarangFormLoading.value"
      @close="kategoriBarangData.closeKategoriBarangModal"
      @save="kategoriBarangData.saveKategoriBarang"
    />

    <KategoriKasModal
      v-model="kategoriKasData.showKategoriKasModal.value"
      :editing-kategori="kategoriKasData.editingKategoriKas.value"
      :form="kategoriKasData.kategoriKasForm.value"
      :loading="kategoriKasData.kategoriKasFormLoading.value"
      @close="kategoriKasData.closeKategoriKasModal"
      @save="kategoriKasData.saveKategoriKas"
    />

    <SubKategoriMenuModal
      v-model="subKategoriData.showSubKategoriModal.value"
      :editing-sub-kategori="subKategoriData.editingSubKategori.value"
      :form="subKategoriData.subKategoriForm.value"
      :loading="subKategoriData.subKategoriFormLoading.value"
      @close="subKategoriData.closeSubKategoriModal"
      @save="subKategoriData.saveSubKategori"
    />

    <SubKategoriTokoModal
      v-model="subKategoriTokoData.showSubKategoriModal.value"
      :editing-sub-kategori="subKategoriTokoData.editingSubKategori.value"
      :form="subKategoriTokoData.subKategoriForm.value"
      :kategoris="kategoriBarangData.kategoriBarangs.value"
      :loading="subKategoriTokoData.subKategoriFormLoading.value"
      @close="subKategoriTokoData.closeSubKategoriModal"
      @save="subKategoriTokoData.saveSubKategori"
    />
  </component>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { usePage } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import CanteenLayout from '@/Layouts/CanteenLayout.vue';
import GeneralTab from '@/Components/Settings/Tabs/GeneralTab.vue';
import ProfileTab from '@/Components/Settings/Tabs/ProfileTab.vue';
import SecurityTab from '@/Components/Settings/Tabs/SecurityTab.vue';
import NotificationTab from '@/Components/Settings/Tabs/NotificationTab.vue';
import WorkUnitsTab from '@/Components/Settings/Tabs/WorkUnitsTab.vue';
import ReportTab from '@/Components/Settings/Tabs/ReportTab.vue';
import PointsTab from '@/Components/Settings/Tabs/PointsTab.vue';
import KategoriBarangTab from '@/Components/Settings/Tabs/KategoriBarangTab.vue';
import KategoriKasTab from '@/Components/Settings/Tabs/KategoriKasTab.vue';
import SubKategoriMenuTab from '@/Components/Settings/Tabs/SubKategoriMenuTab.vue';
import SubKategoriTokoTab from '@/Components/Settings/Tabs/SubKategoriTokoTab.vue';
import SelfOrderTab from '@/Components/Settings/Tabs/SelfOrderTab.vue';
import WorkUnitModal from '@/Components/Settings/Modals/WorkUnitModal.vue';
import KategoriBarangModal from '@/Components/Settings/Modals/KategoriBarangModal.vue';
import KategoriKasModal from '@/Components/Settings/Modals/KategoriKasModal.vue';
import SubKategoriMenuModal from '@/Components/Settings/Modals/SubKategoriMenuModal.vue';
import SubKategoriTokoModal from '@/Components/Settings/Modals/SubKategoriTokoModal.vue';
import { useWorkUnits } from '@/composables/useWorkUnits';
import { useKategoriBarang } from '@/composables/useKategoriBarang';
import { useKategoriKas } from '@/composables/useKategoriKas';
import { useSubKategoriMenu } from '@/composables/useSubKategoriMenu';
import { useSubKategoriToko } from '@/composables/useSubKategoriToko';
import { useReportSettings } from '@/composables/useReportSettings';
import { usePointsSettings } from '@/composables/usePointsSettings';
import { useSelfOrderSettings } from '@/composables/useSelfOrderSettings';

const props = defineProps({
  workUnits: {
    type: Array,
    default: () => []
  },
  kategoriBarangs: {
    type: Array,
    default: () => []
  },
  kategoriKas: {
    type: Array,
    default: () => []
  },
  subKategoris: {
    type: Array,
    default: () => []
  },
  subKategorisToko: {
    type: Array,
    default: () => []
  },
  reportSettings: {
    type: Object,
    default: () => ({})
  },
  pointsSettings: {
    type: Object,
    default: () => ({})
  },
  pointRules: {
    type: Array,
    default: () => []
  },
  selfOrderSettings: {
    type: Object,
    default: () => ({})
  }
});

const page = usePage();

const layoutComponent = computed(() => {
  const user = page.props.auth?.user
  if (user?.roles?.includes('canteen') && !user?.roles?.includes('officer') && !user?.roles?.includes('sysadmin')) {
    return CanteenLayout
  }
  return AdminLayout
});

const urlParams = new URLSearchParams(window.location.search);
const tabParam = urlParams.get('tab');
const activeTab = ref(tabParam || 'general');

const workUnitsData = useWorkUnits(props.workUnits);
const kategoriBarangData = useKategoriBarang(props.kategoriBarangs);
const kategoriKasData = useKategoriKas(props.kategoriKas);
const subKategoriData = useSubKategoriMenu(props.subKategoris);
const subKategoriTokoData = useSubKategoriToko(props.subKategorisToko);
const reportSettingsData = useReportSettings(props.reportSettings);
const pointsSettingsData = usePointsSettings(props.pointsSettings, props.pointRules);
const selfOrderSettingsData = useSelfOrderSettings(props.selfOrderSettings);

// Toast notification
const toast = ref({ show: false, message: '', type: 'success' });
let toastTimeout = null;

function showToast(message, type = 'success') {
  clearTimeout(toastTimeout);
  toast.value = { show: true, message, type };
  toastTimeout = setTimeout(() => { toast.value.show = false }, 3000);
}

// Listen to toast events from composables
function handleToastEvent(event) {
  showToast(event.detail.message, event.detail.type);
}

onMounted(() => {
  window.addEventListener('toast', handleToastEvent);
});

onUnmounted(() => {
  window.removeEventListener('toast', handleToastEvent);
  clearTimeout(toastTimeout);
});
</script>
