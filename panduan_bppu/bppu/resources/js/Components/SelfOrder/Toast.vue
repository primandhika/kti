<template>
    <teleport to="body">
        <transition
            enter-active-class="transition-all duration-300 ease-out"
            enter-from-class="opacity-0 translate-y-4"
            enter-to-class="opacity-100 translate-y-0"
            leave-active-class="transition-all duration-200 ease-in"
            leave-from-class="opacity-100 translate-y-0"
            leave-to-class="opacity-0 translate-y-4"
        >
            <div
                v-if="show"
                class="fixed top-20 right-4 z-[60] max-w-sm w-full mx-4 sm:mx-0"
            >
                <div
                    :class="[
                        'p-4 rounded-lg shadow-lg border-l-4',
                        type === 'success' ? 'bg-green-50 border-green-500' : 'bg-red-50 border-red-500'
                    ]"
                >
                    <div class="flex items-start">
                        <!-- Icon -->
                        <div class="flex-shrink-0">
                            <svg
                                v-if="type === 'success'"
                                class="h-5 w-5 text-green-500"
                                fill="currentColor"
                                viewBox="0 0 20 20"
                            >
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <svg
                                v-else
                                class="h-5 w-5 text-red-500"
                                fill="currentColor"
                                viewBox="0 0 20 20"
                            >
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                        </div>

                        <!-- Message -->
                        <div class="ml-3 flex-1">
                            <p
                                :class="[
                                    'text-sm font-medium',
                                    type === 'success' ? 'text-green-800' : 'text-red-800'
                                ]"
                            >
                                {{ message }}
                            </p>
                        </div>

                        <!-- Close Button -->
                        <div class="ml-3 flex-shrink-0">
                            <button
                                @click="close"
                                :class="[
                                    'inline-flex rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2',
                                    type === 'success'
                                        ? 'text-green-500 hover:text-green-700 focus:ring-green-500'
                                        : 'text-red-500 hover:text-red-700 focus:ring-red-500'
                                ]"
                            >
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </transition>
    </teleport>
</template>

<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
    message: {
        type: String,
        default: ''
    },
    type: {
        type: String,
        default: 'success',
        validator: (value) => ['success', 'error'].includes(value)
    },
    duration: {
        type: Number,
        default: 5000
    }
});

const emit = defineEmits(['close']);

const show = ref(false);
let timeout = null;

const close = () => {
    show.value = false;
    if (timeout) {
        clearTimeout(timeout);
    }
    emit('close');
};

watch(() => props.message, (newValue) => {
    if (newValue) {
        show.value = true;

        if (timeout) {
            clearTimeout(timeout);
        }

        timeout = setTimeout(() => {
            close();
        }, props.duration);
    }
}, { immediate: true });
</script>
