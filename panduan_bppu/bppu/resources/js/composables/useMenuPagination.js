import { ref, computed } from 'vue';

export function useMenuPagination(props) {
  const activeSubKategori = ref('');
  const showAllKategori = ref(true);
  const itemsPerPage = ref(20);
  const currentPage = ref(1);

  const subKategoriList = computed(() => {
    return props.groupedMenus?.map(g => ({
      label: g.label,
      count: g.count
    })) || [];
  });

  const filteredGroups = computed(() => {
    if (showAllKategori.value) {
      return props.groupedMenus || [];
    } else {
      return props.groupedMenus?.filter(g => g.label === activeSubKategori.value) || [];
    }
  });

  const paginatedGroups = computed(() => {
    // Flatten all items untuk pagination
    const allItems = filteredGroups.value.flatMap(group =>
      group.items.map(item => ({ ...item, groupLabel: group.label }))
    );

    const totalToShow = currentPage.value * itemsPerPage.value;
    const paginatedItems = allItems.slice(0, totalToShow);

    // Group ulang items yang sudah dipaginate
    const grouped = {};
    paginatedItems.forEach(item => {
      if (!grouped[item.groupLabel]) {
        grouped[item.groupLabel] = {
          label: item.groupLabel,
          items: [],
          count: 0
        };
      }
      const { groupLabel, ...itemWithoutGroup } = item;
      grouped[item.groupLabel].items.push(itemWithoutGroup);
      grouped[item.groupLabel].count++;
    });

    return Object.values(grouped);
  });

  const totalItems = computed(() => {
    return filteredGroups.value.flatMap(g => g.items).length;
  });

  const hasMoreItems = computed(() => {
    return currentPage.value * itemsPerPage.value < totalItems.value;
  });

  const loadMore = () => {
    currentPage.value++;
  };

  const toggleShowAll = () => {
    showAllKategori.value = !showAllKategori.value;
    currentPage.value = 1;
  };

  const showAll = () => {
    showAllKategori.value = true;
    activeSubKategori.value = '';
    currentPage.value = 1;
  };

  const selectKategori = (label) => {
    activeSubKategori.value = label;
    showAllKategori.value = false;
    currentPage.value = 1;
  };

  const initDefaultKategori = () => {
    if (subKategoriList.value.length > 0 && !activeSubKategori.value) {
      activeSubKategori.value = subKategoriList.value[0].label;
    }
  };

  return {
    activeSubKategori,
    showAllKategori,
    currentPage,
    subKategoriList,
    paginatedGroups,
    hasMoreItems,
    loadMore,
    toggleShowAll,
    showAll,
    selectKategori,
    initDefaultKategori,
  };
}
