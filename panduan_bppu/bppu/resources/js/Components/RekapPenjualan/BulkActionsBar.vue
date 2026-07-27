<template>
  <div>
    <!-- Bulk Verification Actions -->
    <div v-if="canVerify && selectedCount > 0" class="mt-3 p-2 bg-[#eae0cc] rounded-lg border border-[#c1a366]">
      <div class="flex items-center justify-between gap-2">
        <span class="text-xs text-[#6b4700] font-medium">{{ selectedCount }} untuk verifikasi</span>
        <div class="flex gap-1">
          <button
            @click="$emit('verify-selected')"
            class="px-2 py-1 text-xs bg-[#996600] text-white rounded hover:bg-[#7a5100] font-medium"
          >
            Verifikasi
          </button>
          <button
            @click="$emit('clear-selection')"
            class="px-2 py-1 text-xs bg-gray-200 text-gray-700 rounded hover:bg-gray-300"
          >
            Batal
          </button>
        </div>
      </div>
    </div>

    <!-- Bulk Approval Actions -->
    <div v-if="canApprove && selectedForApprovalCount > 0" class="mt-3 p-2 bg-green-50 rounded-lg border border-green-200">
      <div class="flex items-center justify-between gap-2">
        <span class="text-xs text-green-900 font-medium">{{ selectedForApprovalCount }} untuk approve</span>
        <div class="flex gap-1">
          <button
            @click="$emit('approve-selected')"
            class="px-2 py-1 text-xs bg-green-600 text-white rounded hover:bg-green-700 font-medium"
          >
            Approve
          </button>
          <button
            @click="$emit('clear-approval-selection')"
            class="px-2 py-1 text-xs bg-gray-200 text-gray-700 rounded hover:bg-gray-300"
          >
            Batal
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  canVerify: { type: Boolean, default: false },
  canApprove: { type: Boolean, default: false },
  selectedCount: { type: Number, default: 0 },
  selectedForApprovalCount: { type: Number, default: 0 }
})

defineEmits(['verify-selected', 'clear-selection', 'approve-selected', 'clear-approval-selection'])
</script>
