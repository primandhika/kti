<template>
  <AdminLayout page-title="Log Aktivitas">
    <div class="space-y-6">
      <!-- Filter -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <form @submit.prevent="applyFilter" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-3">
          <input
            v-model="form.search"
            type="text"
            placeholder="Cari nama, deskripsi, IP..."
            class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#996600] focus:border-[#996600] col-span-2"
          />

          <select v-model="form.user_id" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#996600] focus:border-[#996600]">
            <option value="">Semua Pengguna</option>
            <option v-for="u in users" :key="u.id" :value="u.id">{{ u.name }}</option>
          </select>

          <select v-model="form.action" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#996600] focus:border-[#996600]">
            <option value="">Semua Aksi</option>
            <option v-for="a in actions" :key="a" :value="a">{{ actionLabel(a) }}</option>
          </select>

          <select v-model="form.module" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#996600] focus:border-[#996600]">
            <option value="">Semua Modul</option>
            <option v-for="m in modules" :key="m" :value="m">{{ m }}</option>
          </select>

          <div class="flex gap-2">
            <button type="submit" class="flex-1 bg-[#996600] text-white rounded-lg px-3 py-2 text-sm font-medium hover:bg-[#7a5100] transition-colors">
              Filter
            </button>
            <button type="button" @click="resetFilter" class="px-3 py-2 border border-gray-300 rounded-lg text-sm text-gray-600 hover:bg-gray-50 transition-colors">
              Reset
            </button>
          </div>
        </form>

        <div class="mt-3 flex gap-3">
          <input v-model="form.date_from" type="date" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#996600] focus:border-[#996600]" />
          <span class="self-center text-gray-400 text-sm">s/d</span>
          <input v-model="form.date_to" type="date" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#996600] focus:border-[#996600]" />
        </div>
      </div>

      <!-- Table -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
          <span class="text-sm text-gray-500">{{ logs.total }} entri ditemukan</span>
          <a :href="csvUrl" class="px-3 py-1.5 text-sm bg-[#996600] text-white rounded-lg hover:bg-[#7a5100] transition-colors">
            Download CSV
          </a>
        </div>

        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-100">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Waktu</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Pengguna</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Modul</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Keterangan</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">IP</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              <tr v-if="logs.data.length === 0">
                <td colspan="6" class="px-4 py-10 text-center text-sm text-gray-400">Belum ada log aktivitas</td>
              </tr>
              <tr v-for="log in logs.data" :key="log.id" class="hover:bg-gray-50 transition-colors">
                <td class="px-4 py-3 text-xs text-gray-500 whitespace-nowrap">{{ formatDateTime(log.created_at) }}</td>
                <td class="px-4 py-3">
                  <span class="text-sm font-medium text-gray-900">{{ log.user_name ?? 'Sistem' }}</span>
                </td>
                <td class="px-4 py-3">
                  <span :class="actionBadgeClass(log.action)" class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium">
                    {{ actionLabel(log.action) }}
                  </span>
                </td>
                <td class="px-4 py-3">
                  <span class="text-xs text-gray-600 bg-gray-100 px-2 py-0.5 rounded">{{ log.module }}</span>
                </td>
                <td class="px-4 py-3 text-sm text-gray-700 max-w-xs truncate" :title="log.description">{{ log.description }}</td>
                <td class="px-4 py-3 text-xs text-gray-400 font-mono">{{ log.ip_address }}</td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div v-if="logs.last_page > 1" class="px-5 py-4 border-t border-gray-100 flex items-center justify-between">
          <span class="text-sm text-gray-500">
            Halaman {{ logs.current_page }} dari {{ logs.last_page }}
            &nbsp;&bull;&nbsp; {{ logs.from }}-{{ logs.to }} dari {{ logs.total }}
          </span>
          <div class="flex gap-1">
            <Link
              v-if="logs.prev_page_url"
              :href="logs.prev_page_url"
              class="px-3 py-1.5 text-sm border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
            >Sebelumnya</Link>
            <template v-for="link in pageLinks" :key="link.label">
              <span
                v-if="link.active"
                class="px-3 py-1.5 text-sm bg-[#996600] text-white rounded-lg font-medium"
              >{{ link.label }}</span>
              <Link
                v-else-if="link.url"
                :href="link.url"
                class="px-3 py-1.5 text-sm border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
              >{{ link.label }}</Link>
              <span v-else class="px-2 py-1.5 text-sm text-gray-400">...</span>
            </template>
            <Link
              v-if="logs.next_page_url"
              :href="logs.next_page_url"
              class="px-3 py-1.5 text-sm border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
            >Selanjutnya</Link>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { reactive, computed } from 'vue';

const props = defineProps({
  logs: Object,
  users: Array,
  modules: Array,
  actions: Array,
  filters: Object,
});

const form = reactive({
  search: props.filters.search ?? '',
  user_id: props.filters.user_id ?? '',
  action: props.filters.action ?? '',
  module: props.filters.module ?? '',
  date_from: props.filters.date_from ?? '',
  date_to: props.filters.date_to ?? '',
});

const csvUrl = computed(() => {
  const params = new URLSearchParams();
  Object.entries(form).forEach(([k, v]) => { if (v) params.set(k, v); });
  params.set('download_csv', '1');
  return '/pengelola/activity-log?' + params.toString();
});

const pageLinks = computed(() => {
  return (props.logs.links ?? []).filter(l => l.label !== '&laquo; Previous' && l.label !== 'Next &raquo;');
});

function applyFilter() {
  const params = {};
  Object.entries(form).forEach(([k, v]) => { if (v) params[k] = v; });
  router.get('/pengelola/activity-log', params, { preserveState: true, replace: true });
}

function resetFilter() {
  Object.keys(form).forEach(k => form[k] = '');
  router.get('/pengelola/activity-log', {}, { preserveState: false });
}

function formatDateTime(val) {
  if (!val) return '-';
  return new Date(val).toLocaleString('id-ID', {
    day: '2-digit', month: '2-digit', year: 'numeric',
    hour: '2-digit', minute: '2-digit', second: '2-digit'
  });
}

const ACTION_LABELS = {
  login: 'Login',
  logout: 'Logout',
  create: 'Tambah',
  update: 'Ubah',
  delete: 'Hapus',
  view: 'Lihat',
  export: 'Ekspor',
};

function actionLabel(action) {
  return ACTION_LABELS[action] ?? action;
}

function actionBadgeClass(action) {
  const map = {
    login: 'bg-green-100 text-green-700',
    logout: 'bg-gray-100 text-gray-600',
    create: 'bg-blue-100 text-blue-700',
    update: 'bg-yellow-100 text-yellow-700',
    delete: 'bg-red-100 text-red-700',
    export: 'bg-purple-100 text-purple-700',
    view: 'bg-gray-100 text-gray-500',
  };
  return map[action] ?? 'bg-gray-100 text-gray-600';
}
</script>
