<template>
  <div
    v-if="show"
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
  >
    <div class="bg-white rounded-xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
      <div class="sticky top-0 z-10">
        <!-- Header dengan gradient -->
        <div class="bg-gradient-to-r from-[#996600] to-[#7a5100] px-5 py-2.5 flex justify-between items-center">
          <div class="flex items-center gap-2">
            <div class="p-1 bg-white/20 rounded-md">
              <svg v-if="isEditMode" class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
              </svg>
              <svg v-else class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
              </svg>
            </div>
            <h2 class="text-sm font-semibold text-white">
              {{ isEditMode ? 'Edit Barang' : 'Tambah Barang Baru' }}
            </h2>
          </div>
          <button @click="$emit('close')" class="p-1 hover:bg-white/20 rounded-full transition-colors">
            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <!-- Tabs (Only for Add Modal) -->
        <div v-if="!isEditMode" class="flex bg-[#7a5100]">
          <button
            type="button"
            @click="activeTab = 'manual'"
            :class="[
              'px-5 py-2.5 font-medium text-sm transition-colors border-b-2',
              activeTab === 'manual'
                ? 'text-white border-white bg-white/10'
                : 'text-white/60 border-transparent hover:text-white hover:bg-white/10'
            ]"
          >
            Manual
          </button>
          <button
            type="button"
            @click="activeTab = 'import'"
            :class="[
              'px-5 py-2.5 font-medium text-sm transition-colors border-b-2 flex items-center gap-2',
              activeTab === 'import'
                ? 'text-white border-white bg-white/10'
                : 'text-white/60 border-transparent hover:text-white hover:bg-white/10'
            ]"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
            </svg>
            Impor dari Unit Kerja
          </button>
        </div>
      </div>

      <!-- Manual Form Tab -->
      <ManualBarangForm
        v-if="activeTab === 'manual'"
        :form="form"
        :kategoris="kategoris"
        :suppliers="suppliers"
        :users="users"
        :is-edit-mode="isEditMode"
        :generating-plu="generatingPLU"
        :editing-barang="editingBarang"
        :work-unit-id="workUnitId"
        @submit="$emit('submit', form)"
        @generate-plu="$emit('generate-plu')"
        @close="$emit('close')"
        @close-modal="$emit('close')"
      />

      <!-- Import Tab -->
      <ImportBarangForm
        v-if="activeTab === 'import'"
        :other-work-units="otherWorkUnits"
        :available-barangs="availableBarangs"
        :loading-barangs="loadingBarangs"
        :importing-barangs="importingBarangs"
        :import-form="importForm"
        @load-barangs="$emit('load-barangs', $event)"
        @toggle-select-all="$emit('toggle-select-all')"
        @import="$emit('import')"
        @close="$emit('close')"
      />
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue';
import ManualBarangForm from './ManualBarangFormCompact.vue';
import ImportBarangForm from './ImportBarangForm.vue';

const props = defineProps({
  show: Boolean,
  isEditMode: Boolean,
  form: Object,
  kategoris: Array,
  suppliers: { type: Array, default: () => [] },
  otherWorkUnits: Array,
  users: { type: Array, default: () => [] },
  availableBarangs: Array,
  loadingBarangs: Boolean,
  importingBarangs: Boolean,
  generatingPLU: Boolean,
  importForm: Object,
  editingBarang: { type: Object, default: null },
  workUnitId: { type: [String, Number], default: null },
});

defineEmits([
  'close',
  'submit',
  'generate-plu',
  'load-barangs',
  'toggle-select-all',
  'import'
]);

const activeTab = ref('manual');

// Reset tab when modal closes
watch(() => props.show, (newVal) => {
  if (!newVal) {
    activeTab.value = 'manual';
  }
});
</script>
