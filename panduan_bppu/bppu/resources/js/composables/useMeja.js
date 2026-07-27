import { ref } from 'vue';

// State global untuk meja yang dipilih (shared across components)
const selectedMeja = ref(null); // { id, kode_meja, nama, nomor, lokasi }
const isLocked = ref(false);    // true jika meja dikunci dari URL param

export function useMeja() {
    const setMeja = (meja) => {
        if (isLocked.value) return;
        selectedMeja.value = meja;
    };

    const lockMeja = (meja) => {
        selectedMeja.value = meja;
        isLocked.value = true;
    };

    const clearMeja = () => {
        if (isLocked.value) return;
        selectedMeja.value = null;
    };

    const getMejaLabel = () => {
        if (!selectedMeja.value) return null;
        const m = selectedMeja.value;
        const parts = [];
        if (m.lokasi) parts.push(m.lokasi);
        parts.push(m.nama || m.kode_meja);
        return parts.join(' - ');
    };

    return {
        selectedMeja,
        isLocked,
        setMeja,
        lockMeja,
        clearMeja,
        getMejaLabel,
    };
}
