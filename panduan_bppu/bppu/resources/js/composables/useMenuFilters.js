import { ref, watch, computed } from 'vue';
import { router } from '@inertiajs/vue3';

export function useMenuFilters(props, baseUrl = '/pengelola/menu-kantin') {
  const searchQuery = ref(props.filters?.search || '');
  const filterWorkUnit = ref(props.filters?.work_unit || '');
  const filterKategori = ref(props.filters?.kategori || '');

  // Watch for filter changes and update URL with debounce
  watch([searchQuery, filterWorkUnit, filterKategori], () => {
    router.get(baseUrl, {
      search: searchQuery.value,
      work_unit: filterWorkUnit.value,
      kategori: filterKategori.value,
    }, {
      preserveState: true,
      replace: true,
    });
  }, { debounce: 300 });

  // Computed stats
  const allMenus = computed(() => {
    return props.groupedMenus?.flatMap(g => g.items) || [];
  });

  const totalMenus = computed(() => {
    return allMenus.value.length;
  });

  const availableCount = computed(() => {
    return allMenus.value.filter(m => m.is_available).length;
  });

  const outOfStockCount = computed(() => {
    return allMenus.value.filter(m => m.stok === 0).length;
  });

  return {
    searchQuery,
    filterWorkUnit,
    filterKategori,
    allMenus,
    totalMenus,
    availableCount,
    outOfStockCount,
  };
}
