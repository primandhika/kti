<template>
    <div class="flex items-center gap-1.5 sm:gap-2">
        <!-- Sort By Field -->
        <div class="flex items-center gap-1 sm:gap-2">
            <label class="text-xs sm:text-sm font-medium text-gray-700 hidden sm:inline">Urutkan:</label>
            <select
                v-model="localSortField"
                @change="applySort"
                class="px-2 py-1 sm:px-3 sm:py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-[#996600] transition-all text-xs sm:text-sm bg-white"
            >
                <option value="created_at">Tanggal</option>
                <option value="nama">Nama</option>
                <option value="saldo">Saldo</option>
            </select>
        </div>

        <!-- Sort Direction -->
        <button
            @click="toggleDirection"
            class="p-1 sm:p-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-all"
            :title="localSortDirection === 'asc' ? 'Urutkan Menurun' : 'Urutkan Menaik'"
        >
            <svg
                v-if="localSortDirection === 'asc'"
                class="w-4 h-4 sm:w-5 sm:h-5 text-gray-700"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
            >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12" />
            </svg>
            <svg
                v-else
                class="w-4 h-4 sm:w-5 sm:h-5 text-gray-700"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
            >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h9m5-4v12m0 0l-4-4m4 4l4-4" />
            </svg>
        </button>
    </div>
</template>

<script setup>
import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    sortField: {
        type: String,
        default: 'created_at',
    },
    sortDirection: {
        type: String,
        default: 'desc',
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
});

const localSortField = ref(props.sortField);
const localSortDirection = ref(props.sortDirection);

watch(() => props.sortField, (newVal) => {
    localSortField.value = newVal;
});

watch(() => props.sortDirection, (newVal) => {
    localSortDirection.value = newVal;
});

const applySort = () => {
    const params = {
        ...props.filters,
        sort_field: localSortField.value,
        sort_direction: localSortDirection.value,
    };

    router.get(route('admin.bukukas.index'), params, {
        preserveState: true,
        preserveScroll: true,
    });
};

const toggleDirection = () => {
    localSortDirection.value = localSortDirection.value === 'asc' ? 'desc' : 'asc';
    applySort();
};
</script>
