import { ref, watch } from 'vue';

// Shared state across all components
const isDark = ref(false);
let initialized = false;

const updateDarkMode = () => {
    if (isDark.value) {
        document.documentElement.classList.add('dark');
        localStorage.setItem('theme', 'dark');
    } else {
        document.documentElement.classList.remove('dark');
        localStorage.setItem('theme', 'light');
    }
};

const initDarkMode = () => {
    if (initialized) return;

    const savedTheme = localStorage.getItem('theme');

    // Default ke light mode, hanya dark kalau user explicitly pilih dark
    isDark.value = savedTheme === 'dark';
    updateDarkMode();

    initialized = true;
};

// flush: 'sync' agar DOM update terjadi sinkron di dalam startViewTransition callback
watch(isDark, () => {
    updateDarkMode();
}, { flush: 'sync' });

export function useDarkMode() {
    const toggleDarkMode = () => {
        if (!document.startViewTransition) {
            isDark.value = !isDark.value;
            return;
        }

        document.startViewTransition(() => {
            isDark.value = !isDark.value;
        });
    };

    return {
        isDark,
        toggleDarkMode,
        initDarkMode
    };
}
