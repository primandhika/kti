<template>
  <Teleport to="body">
    <div class="fixed inset-0 z-50 flex items-center justify-center">
      <div class="absolute inset-0 bg-black/50" @click="$emit('close')" />
      <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-md mx-4">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
          <h2 class="text-lg font-semibold text-gray-800">
            {{ voucherSingle ? `Assign Voucher ${voucherSingle.kode_voucher}` : `Assign ${voucherIds.length} Voucher ke Member` }}
          </h2>
          <button @click="$emit('close')" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <div class="px-6 py-5 space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Cari Member</label>
            <input
              v-model="searchQuery"
              @input="debouncedSearch"
              type="text"
              placeholder="Cari nama, NIM, atau kode member..."
              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#996600]"
            />
          </div>

          <div v-if="searching" class="text-center text-sm text-gray-500 py-4">Mencari...</div>

          <div v-else-if="results.length > 0" class="max-h-52 overflow-y-auto border border-gray-200 rounded-lg divide-y divide-gray-100">
            <button
              v-for="m in results"
              :key="m.id"
              @click="selectMember(m)"
              :class="[
                'w-full text-left px-4 py-3 hover:bg-[#f4efe5] transition-colors',
                selectedMember?.id === m.id ? 'bg-[#f4efe5]' : ''
              ]"
            >
              <div class="font-medium text-gray-800 text-sm">{{ m.name }}</div>
              <div class="text-xs text-gray-500">
                <span v-if="m.member_code" class="mr-2">{{ m.member_code }}</span>
                <span v-if="m.nim">NIM: {{ m.nim }}</span>
              </div>
            </button>
          </div>

          <div v-else-if="searchQuery && !searching" class="text-center text-sm text-gray-400 py-4">
            Tidak ada member ditemukan
          </div>

          <!-- Selected -->
          <div v-if="selectedMember" class="bg-[#f4efe5] rounded-lg p-3 flex items-center justify-between">
            <div>
              <div class="font-medium text-[#6b4700] text-sm">{{ selectedMember.name }}</div>
              <div class="text-xs text-[#996600]">{{ selectedMember.member_code || selectedMember.nim }}</div>
            </div>
            <button @click="selectedMember = null" class="text-gray-400 hover:text-gray-600">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <div class="flex justify-end gap-2 pt-2 border-t border-gray-100">
            <button
              type="button"
              @click="$emit('close')"
              class="px-4 py-2 text-sm text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
            >
              Batal
            </button>
            <button
              @click="submit"
              :disabled="!selectedMember || submitting"
              class="px-4 py-2 text-sm text-white bg-[#996600] rounded-lg hover:bg-[#7a5100] transition-colors disabled:opacity-50"
            >
              {{ submitting ? 'Menyimpan...' : 'Assign' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import axios from 'axios'

const props = defineProps({
  potonganId: Number,
  voucherIds: { type: Array, default: () => [] },
  voucherSingle: { type: Object, default: null },
})

const emit = defineEmits(['close'])

const searchQuery = ref('')
const results = ref([])
const searching = ref(false)
const selectedMember = ref(null)
const submitting = ref(false)
let searchTimeout = null

function debouncedSearch() {
  clearTimeout(searchTimeout)
  if (!searchQuery.value.trim()) { results.value = []; return }
  searchTimeout = setTimeout(doSearch, 350)
}

async function doSearch() {
  searching.value = true
  try {
    const res = await axios.get('/pengelola/member/search', {
      params: { q: searchQuery.value, limit: 10 }
    })
    results.value = res.data.data || res.data || []
  } catch {
    results.value = []
  } finally {
    searching.value = false
  }
}

function selectMember(m) {
  selectedMember.value = m
  results.value = []
  searchQuery.value = m.name
}

function submit() {
  if (!selectedMember.value) return
  submitting.value = true

  const isSingle = !!props.voucherSingle
  const url = isSingle
    ? `/pengelola/diskon/${props.potonganId}/vouchers/${props.voucherSingle.id}/assign`
    : `/pengelola/diskon/${props.potonganId}/vouchers/assign-bulk`

  const data = isSingle
    ? { user_id: selectedMember.value.id }
    : { voucher_ids: props.voucherIds, user_id: selectedMember.value.id }

  router.post(url, data, {
    preserveScroll: true,
    onSuccess: () => emit('close'),
    onFinish: () => { submitting.value = false },
  })
}
</script>
