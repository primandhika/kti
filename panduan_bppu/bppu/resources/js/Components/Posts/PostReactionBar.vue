<template>
    <div class="flex flex-wrap items-center gap-3">
        <button
            v-for="reaction in reactions"
            :key="reaction.type"
            @click="toggle(reaction.type)"
            :disabled="loading === reaction.type"
            class="reaction-btn"
            :class="isActive(reaction.type) ? 'reaction-active' : 'reaction-inactive'"
        >
            <span class="reaction-icon" v-html="reaction.icon"></span>
            <span class="reaction-label">{{ reaction.label }}</span>
            <span
                v-if="counts[reaction.type] > 0"
                class="reaction-count"
                :class="isActive(reaction.type) ? 'text-white/80' : 'text-gray-500 dark:text-[#b7934c]'"
            >
                {{ counts[reaction.type] }}
            </span>
        </button>
    </div>
</template>

<script setup>
import { ref, reactive } from 'vue';
import axios from 'axios';

const props = defineProps({
    postId: { type: Number, required: true },
    initialCounts: { type: Object, default: () => ({}) },
    initialUserReactions: { type: Array, default: () => [] },
});

const reactions = [
    {
        type: 'like',
        label: 'Suka',
        icon: '<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M2 10.5a1.5 1.5 0 113 0v6a1.5 1.5 0 01-3 0v-6zM6 10.333v5.43a2 2 0 001.106 1.79l.05.025A4 4 0 008.943 18h5.416a2 2 0 001.962-1.608l1.2-6A2 2 0 0015.56 8H12V4a2 2 0 00-2-2 1 1 0 00-1 1v.667a4 4 0 01-.8 2.4L6.8 7.933a4 4 0 00-.8 2.4z"/></svg>',
    },
    {
        type: 'informatif',
        label: 'Informatif',
        icon: '<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>',
    },
    {
        type: 'inspiratif',
        label: 'Inspiratif',
        icon: '<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z"/></svg>',
    },
];

const counts = reactive({ ...props.initialCounts });
const userReactions = ref([...props.initialUserReactions]);
const loading = ref(null);

const isActive = (type) => userReactions.value.includes(type);

const toggle = async (type) => {
    if (loading.value) return;
    loading.value = type;

    try {
        const { data } = await axios.post(`/berita/${props.postId}/reactions`, {
            reaction_type: type,
        });

        Object.assign(counts, data.counts);
        userReactions.value = data.user_reactions;
    } catch (e) {
        // silent
    } finally {
        loading.value = null;
    }
};
</script>

<style scoped>
.reaction-btn {
    @apply inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-medium transition-all duration-200 border select-none;
}
.reaction-active {
    @apply bg-[#996600] border-[#996600] text-white shadow-sm;
}
.reaction-inactive {
    @apply bg-white dark:bg-[#2d1e00] border-gray-200 dark:border-[#3d2800] text-gray-700 dark:text-[#d6c199] hover:border-[#996600] hover:text-[#996600] dark:hover:border-[#ccb27f] dark:hover:text-[#ccb27f];
}
.reaction-icon {
    @apply flex items-center;
}
.reaction-label {
    @apply leading-none;
}
.reaction-count {
    @apply text-xs font-semibold;
}
</style>
