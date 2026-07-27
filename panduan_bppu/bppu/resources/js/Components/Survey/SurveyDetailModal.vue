<template>
  <Teleport to="body">
    <Transition
      enter-active-class="transition ease-out duration-200"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition ease-in duration-150"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50">
        <Transition
          enter-active-class="transition ease-out duration-200"
          enter-from-class="opacity-0 scale-95"
          enter-to-class="opacity-100 scale-100"
          leave-active-class="transition ease-in duration-150"
          leave-from-class="opacity-100 scale-100"
          leave-to-class="opacity-0 scale-95"
        >
          <div v-if="show" class="bg-white rounded-xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <!-- Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-[#6b4700] to-[#996600] rounded-t-xl">
              <div>
                <h2 class="text-lg font-bold text-white">Detail Hasil Survey</h2>
                <p class="text-sm text-[#e0d1b2]">{{ survey?.user?.name }}</p>
              </div>
              <button @click="$emit('close')" class="p-2 rounded-lg hover:bg-white/10 text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>

            <!-- Content -->
            <div v-if="survey" class="p-6 space-y-6">
              <!-- Info member -->
              <div class="bg-gray-50 rounded-lg p-4 space-y-1">
                <div class="flex items-center space-x-2 text-sm">
                  <span class="text-gray-500 w-24">Nama</span>
                  <span class="font-medium text-gray-800">{{ survey.user?.name }}</span>
                </div>
                <div class="flex items-center space-x-2 text-sm">
                  <span class="text-gray-500 w-24">Username</span>
                  <span class="text-gray-700">{{ survey.user?.username }}</span>
                </div>
                <div class="flex items-center space-x-2 text-sm">
                  <span class="text-gray-500 w-24">Email</span>
                  <span class="text-gray-700">{{ survey.user?.email }}</span>
                </div>
                <div class="flex items-center space-x-2 text-sm">
                  <span class="text-gray-500 w-24">Tanggal</span>
                  <span class="text-gray-700">{{ formatDate(survey.created_at) }}</span>
                </div>
              </div>

              <!-- Rating section -->
              <div>
                <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-3">Penilaian Likert (1-5)</h3>
                <div class="space-y-3">
                  <RatingRow label="Pelayanan" :value="survey.rating_pelayanan" />
                  <RatingRow label="Kualitas Menu" :value="survey.rating_kualitas" />
                  <RatingRow label="Variasi Menu" :value="survey.rating_variasi" />
                  <RatingRow label="Kesesuaian Harga" :value="survey.rating_harga" />
                  <RatingRow label="Suasana Kantin" :value="survey.rating_suasana" />
                </div>
              </div>

              <!-- Essay section -->
              <div class="space-y-4">
                <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider">Jawaban Esai</h3>
                <EssayBlock label="Menu Favorit" :value="survey.menu_favorit" />
                <EssayBlock label="Rekomendasi Menu" :value="survey.menu_rekomendasi" />
                <EssayBlock label="Kritik & Saran" :value="survey.kritik_saran" />
              </div>
            </div>

            <!-- Footer -->
            <div class="px-6 py-4 border-t border-gray-200 flex justify-end">
              <button
                @click="$emit('close')"
                class="px-4 py-2 bg-[#996600] hover:bg-[#ad8432] text-white text-sm font-medium rounded-lg transition-colors"
              >
                Tutup
              </button>
            </div>
          </div>
        </Transition>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import RatingRow from './RatingRow.vue';
import EssayBlock from './EssayBlock.vue';

defineProps({
  show: Boolean,
  survey: Object,
});

defineEmits(['close']);

const formatDate = (date) => {
  if (!date) return '-';
  return new Date(date).toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'long',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  });
};
</script>
