<template>
  <div v-if="pagination.last_page > 1" class="bg-white rounded-lg shadow-sm p-3 md:p-4">
    <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
      <!-- Info -->
      <div class="text-xs md:text-sm text-gray-600">
        Menampilkan {{ pagination.from || 0 }} - {{ pagination.to || 0 }} dari {{ pagination.total || 0 }} data
      </div>

      <!-- Pagination Controls -->
      <div class="flex items-center space-x-1">
        <!-- First Page -->
        <Link
          v-if="pagination.current_page > 2"
          :href="pagination.first_page_url"
          preserve-scroll
          class="px-2 py-1 md:px-3 md:py-2 text-xs md:text-sm rounded border border-gray-300 hover:bg-gray-50 transition-colors"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
          </svg>
        </Link>

        <!-- Previous Page -->
        <Link
          v-if="pagination.prev_page_url"
          :href="pagination.prev_page_url"
          preserve-scroll
          class="px-2 py-1 md:px-3 md:py-2 text-xs md:text-sm rounded border border-gray-300 hover:bg-gray-50 transition-colors"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
          </svg>
        </Link>

        <!-- Page Numbers -->
        <template v-for="page in visiblePages" :key="page">
          <Link
            v-if="page !== '...'"
            :href="getPageUrl(page)"
            preserve-scroll
            :class="[
              'px-2 py-1 md:px-3 md:py-2 text-xs md:text-sm rounded border transition-colors',
              page === pagination.current_page
                ? 'bg-[#996600] text-white border-[#996600]'
                : 'border-gray-300 hover:bg-gray-50'
            ]"
          >
            {{ page }}
          </Link>
          <span v-else class="px-2 py-1 text-gray-500">...</span>
        </template>

        <!-- Next Page -->
        <Link
          v-if="pagination.next_page_url"
          :href="pagination.next_page_url"
          preserve-scroll
          class="px-2 py-1 md:px-3 md:py-2 text-xs md:text-sm rounded border border-gray-300 hover:bg-gray-50 transition-colors"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
          </svg>
        </Link>

        <!-- Last Page -->
        <Link
          v-if="pagination.current_page < pagination.last_page - 1"
          :href="pagination.last_page_url"
          preserve-scroll
          class="px-2 py-1 md:px-3 md:py-2 text-xs md:text-sm rounded border border-gray-300 hover:bg-gray-50 transition-colors"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
          </svg>
        </Link>
      </div>

      <!-- Per Page Selector -->
      <div class="flex items-center space-x-2">
        <label class="text-xs md:text-sm text-gray-600">Per halaman:</label>
        <select
          :value="pagination.per_page"
          @change="changePerPage($event.target.value)"
          class="px-2 py-1 text-xs md:text-sm border border-gray-300 rounded focus:ring-2 focus:ring-[#996600] focus:border-transparent"
        >
          <option value="25">25</option>
          <option value="50">50</option>
          <option value="100">100</option>
          <option value="200">200</option>
        </select>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
  pagination: {
    type: Object,
    required: true
  }
});

const visiblePages = computed(() => {
  const current = props.pagination.current_page;
  const last = props.pagination.last_page;
  const pages = [];

  if (last <= 7) {
    for (let i = 1; i <= last; i++) {
      pages.push(i);
    }
  } else {
    if (current <= 3) {
      for (let i = 1; i <= 5; i++) {
        pages.push(i);
      }
      pages.push('...');
      pages.push(last);
    } else if (current >= last - 2) {
      pages.push(1);
      pages.push('...');
      for (let i = last - 4; i <= last; i++) {
        pages.push(i);
      }
    } else {
      pages.push(1);
      pages.push('...');
      for (let i = current - 1; i <= current + 1; i++) {
        pages.push(i);
      }
      pages.push('...');
      pages.push(last);
    }
  }

  return pages;
});

const getPageUrl = (page) => {
  const url = new URL(window.location.href);
  url.searchParams.set('page', page);
  return url.pathname + url.search;
};

const changePerPage = (perPage) => {
  const url = new URL(window.location.href);
  url.searchParams.set('per_page', perPage);
  url.searchParams.delete('page');
  router.get(url.pathname + url.search, {}, { preserveScroll: true });
};
</script>
