import { ref } from 'vue';

export function useMenuDownload(baseUrl = '/pengelola/menu-kantin') {
  const showAvailabilityModal = ref(false);
  const pendingAction = ref(null); // { type: 'print'|'download', workUnit }

  const printMenu = (filterWorkUnit) => {
    pendingAction.value = { type: 'print', workUnit: filterWorkUnit };
    showAvailabilityModal.value = true;
  };

  const openDownloadModal = (filterWorkUnit) => {
    pendingAction.value = { type: 'download', workUnit: filterWorkUnit };
    showAvailabilityModal.value = true;
  };

  const confirmAction = (availableOnly) => {
    const action = pendingAction.value;
    showAvailabilityModal.value = false;
    pendingAction.value = null;

    if (!action) return;

    let url = `${baseUrl}/download`;
    const params = new URLSearchParams();
    if (action.workUnit) params.set('work_unit', action.workUnit);
    if (availableOnly) params.set('available_only', 'true');

    if (action.type === 'print') {
      params.set('theme', 'with-image');
      params.set('autoprint', '1');
      url += '?' + params.toString();
      window.open(url, '_blank');
    } else {
      params.set('theme', 'without-image');
      url += '?' + params.toString();
      window.location.href = url;
    }
  };

  const closeAvailabilityModal = () => {
    showAvailabilityModal.value = false;
    pendingAction.value = null;
  };

  return {
    showAvailabilityModal,
    printMenu,
    openDownloadModal,
    confirmAction,
    closeAvailabilityModal,
  };
}
