<template>
  <Teleport to="body">
    <div class="fixed inset-0 z-50 flex items-center justify-center">
      <div class="absolute inset-0 bg-black/50" @click="$emit('close')" />
      <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-sm mx-4">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
          <h2 class="text-lg font-semibold text-gray-800">Generate Voucher</h2>
          <button @click="$emit('close')" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <form @submit.prevent="submit" class="px-6 py-5 space-y-4">
          <div class="bg-[#f4efe5] rounded-lg p-3 text-sm text-[#6b4700]">
            Sisa kuota: <strong>{{ sisaKuota }}</strong> voucher untuk "<strong>{{ potongan.nama }}</strong>"
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Jumlah Voucher <span class="text-red-500">*</span>
            </label>
            <input
              v-model="form.jumlah"
              type="number"
              min="1"
              :max="sisaKuota"
              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#996600]"
              :class="{ 'border-red-400': form.errors.jumlah }"
            />
            <p v-if="form.errors.jumlah" class="text-xs text-red-500 mt-1">{{ form.errors.jumlah }}</p>
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
              type="submit"
              :disabled="form.processing"
              class="px-4 py-2 text-sm text-white bg-[#996600] rounded-lg hover:bg-[#7a5100] transition-colors disabled:opacity-50"
            >
              {{ form.processing ? 'Generating...' : 'Generate' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'

const props = defineProps({
  potongan: Object,
  sisaKuota: Number,
})

const emit = defineEmits(['close', 'generated'])

const form = useForm({ jumlah: 1 })

function submit() {
  form.post(`/pengelola/diskon/${props.potongan.id}/vouchers/generate`, {
    preserveScroll: true,
    onSuccess: () => emit('generated'),
  })
}
</script>
