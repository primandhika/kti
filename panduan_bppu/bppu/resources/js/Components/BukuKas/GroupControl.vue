<template>
    <div class="flex items-center gap-1 sm:gap-2">
        <label class="text-xs sm:text-sm font-medium text-gray-700 hidden sm:inline">Kelompokkan:</label>
        <div class="flex items-center gap-0.5 sm:gap-1 bg-gray-100 rounded-lg p-0.5 sm:p-1">
            <button
                v-for="option in groupOptions"
                :key="option.value"
                @click="selectGroup(option.value)"
                :class="[
                    'px-2 py-1 sm:px-3 sm:py-1.5 text-xs sm:text-sm font-medium rounded-md transition-all',
                    modelValue === option.value
                        ? 'bg-white text-[#996600] shadow-sm'
                        : 'text-gray-600 hover:text-gray-900'
                ]"
            >
                {{ option.label }}
            </button>
        </div>
    </div>
</template>

<script setup>
const groupOptions = [
    { value: 'month', label: 'Per Bulan' },
    { value: 'year', label: 'Per Tahun' },
    { value: 'jenis', label: 'Per Jenis' },
];

const props = defineProps({
    modelValue: {
        type: String,
        default: 'month',
    },
});

const emit = defineEmits(['update:modelValue']);

const selectGroup = (value) => {
    emit('update:modelValue', value);
};
</script>
