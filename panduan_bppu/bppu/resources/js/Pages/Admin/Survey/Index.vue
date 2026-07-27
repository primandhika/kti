<template>
  <AdminLayout page-title="Hasil Survey Member" page-subtitle="Data survey yang diisi saat pendaftaran member">
    <!-- Stats -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-7 gap-3 mb-6">
      <div class="col-span-2 sm:col-span-3 lg:col-span-1 bg-gradient-to-br from-[#6b4700] to-[#996600] rounded-xl p-4 text-white">
        <p class="text-xs text-[#e0d1b2]">Total Responden</p>
        <p class="text-3xl font-bold mt-1">{{ stats.total }}</p>
      </div>
      <StatCard label="Pelayanan" :value="stats.avg_pelayanan" />
      <StatCard label="Kualitas" :value="stats.avg_kualitas" />
      <StatCard label="Variasi" :value="stats.avg_variasi" />
      <StatCard label="Harga" :value="stats.avg_harga" />
      <StatCard label="Suasana" :value="stats.avg_suasana" />
      <div class="bg-[#996600]/10 border border-[#996600]/30 rounded-xl p-4">
        <p class="text-xs text-[#6b4700]">Rata-rata</p>
        <p class="text-2xl font-bold text-[#996600] mt-1">{{ stats.avg_keseluruhan }}</p>
        <p class="text-xs text-gray-500 mt-0.5">dari 5</p>
      </div>
    </div>

    <!-- Filter -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
      <div class="flex items-center gap-3">
        <div class="flex-1 relative">
          <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
          <input
            v-model="searchInput"
            type="text"
            placeholder="Cari nama, username, email, atau isi jawaban..."
            class="w-full pl-9 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#996600] focus:border-transparent"
            @input="onSearch"
          />
        </div>
      </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="bg-gray-50 border-b border-gray-200">
              <th class="px-4 py-3 text-left font-semibold text-gray-600">Member</th>
              <th
                v-for="col in ratingCols"
                :key="col.key"
                class="px-3 py-3 text-center font-semibold text-gray-600 cursor-pointer hover:text-[#996600] whitespace-nowrap"
                @click="sort(col.key)"
              >
                <div class="flex items-center justify-center space-x-1">
                  <span>{{ col.label }}</span>
                  <SortIcon :field="col.key" :current-sort="filters.sort" :direction="filters.direction" />
                </div>
              </th>
              <th class="px-4 py-3 text-left font-semibold text-gray-600">Menu Favorit</th>
              <th
                class="px-3 py-3 text-center font-semibold text-gray-600 cursor-pointer hover:text-[#996600] whitespace-nowrap"
                @click="sort('created_at')"
              >
                <div class="flex items-center justify-center space-x-1">
                  <span>Tanggal</span>
                  <SortIcon field="created_at" :current-sort="filters.sort" :direction="filters.direction" />
                </div>
              </th>
              <th class="px-4 py-3 text-center font-semibold text-gray-600">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-if="surveys.data.length === 0">
              <td colspan="9" class="px-4 py-12 text-center text-gray-400">
                Belum ada data survey
              </td>
            </tr>
            <tr
              v-for="survey in surveys.data"
              :key="survey.id"
              class="hover:bg-gray-50 transition-colors"
            >
              <td class="px-4 py-3">
                <div>
                  <p class="font-medium text-gray-800">{{ survey.user?.name }}</p>
                  <p class="text-xs text-gray-500">{{ survey.user?.username }}</p>
                </div>
              </td>
              <td v-for="col in ratingCols" :key="col.key" class="px-3 py-3 text-center">
                <RatingBadge :value="survey[col.key]" />
              </td>
              <td class="px-4 py-3 max-w-[160px]">
                <p class="text-gray-700 truncate text-xs">{{ survey.menu_favorit }}</p>
              </td>
              <td class="px-3 py-3 text-center text-xs text-gray-500 whitespace-nowrap">
                {{ formatDateShort(survey.created_at) }}
              </td>
              <td class="px-4 py-3 text-center">
                <button
                  @click="openDetail(survey)"
                  class="px-3 py-1.5 bg-[#996600] hover:bg-[#ad8432] text-white text-xs font-medium rounded-lg transition-colors"
                >
                  Detail
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="surveys.data.length > 0" class="px-4 py-3 border-t border-gray-200 flex items-center justify-between text-sm text-gray-600">
        <span>
          Menampilkan <span class="font-medium">{{ surveys.from }}</span> sampai
          <span class="font-medium">{{ surveys.to }}</span> dari
          <span class="font-medium">{{ surveys.total }}</span> data
        </span>
        <div class="flex items-center space-x-1">
          <Link
            v-for="(link, index) in surveys.links"
            :key="index"
            :href="link.url"
            :class="[
              'px-3 py-1.5 rounded-lg text-sm transition-colors',
              link.active
                ? 'bg-[#996600] text-white font-medium'
                : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-300',
              !link.url && 'opacity-50 cursor-not-allowed'
            ]"
            v-html="link.label"
          />
        </div>
      </div>
    </div>

    <SurveyDetailModal :show="showModal" :survey="selectedSurvey" @close="showModal = false" />
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import SurveyDetailModal from '@/Components/Survey/SurveyDetailModal.vue';
import StatCard from '@/Components/Survey/StatCard.vue';
import RatingBadge from '@/Components/Survey/RatingBadge.vue';
import SortIcon from '@/Components/Survey/SortIcon.vue';

const props = defineProps({
  surveys: Object,
  stats: Object,
  filters: Object,
});

const showModal = ref(false);
const selectedSurvey = ref(null);
const searchInput = ref(props.filters?.search || '');

let searchTimeout = null;

const ratingCols = [
  { key: 'rating_pelayanan', label: 'Pelayanan' },
  { key: 'rating_kualitas', label: 'Kualitas' },
  { key: 'rating_variasi', label: 'Variasi' },
  { key: 'rating_harga', label: 'Harga' },
  { key: 'rating_suasana', label: 'Suasana' },
];

const openDetail = (survey) => {
  selectedSurvey.value = survey;
  showModal.value = true;
};

const onSearch = () => {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    router.get('/pengelola/survey', { ...props.filters, search: searchInput.value, page: 1 }, {
      preserveState: true,
      replace: true,
    });
  }, 400);
};

const sort = (field) => {
  const direction = props.filters?.sort === field && props.filters?.direction === 'asc' ? 'desc' : 'asc';
  router.get('/pengelola/survey', { ...props.filters, sort: field, direction }, {
    preserveState: true,
    replace: true,
  });
};

const formatDateShort = (date) => {
  if (!date) return '-';
  return new Date(date).toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'short',
    year: 'numeric',
  });
};
</script>
