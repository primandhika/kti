import './bootstrap';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import Toast from 'vue-toastification';
import 'vue-toastification/dist/index.css';
import '../css/toast.css';

const CHUNK_RELOAD_KEY = 'bppu:chunk-reload';

const reloadOnceForChunk = (context) => {
    try {
        const current = sessionStorage.getItem(CHUNK_RELOAD_KEY);
        if (current === context) {
            sessionStorage.removeItem(CHUNK_RELOAD_KEY);
            return false;
        }

        sessionStorage.setItem(CHUNK_RELOAD_KEY, context);
        window.location.reload();
        return true;
    } catch (_) {
        window.location.reload();
        return true;
    }
};

// Recover from stale manifest/chunk cache after deploy.
window.addEventListener('vite:preloadError', () => {
    reloadOnceForChunk(window.location.pathname);
});

// Suppress Cloudflare beacon errors (caused by ad blockers)
const originalConsoleError = console.error;
console.error = (...args) => {
    const errorMessage = args.join(' ');
    if (errorMessage.includes('cloudflareinsights') || errorMessage.includes('beacon.min.js')) {
        return; // Ignore Cloudflare analytics errors
    }
    originalConsoleError.apply(console, args);
};

const appName = 'BPPU | IKIP Siliwangi';

const toastOptions = {
    position: 'top-right',
    timeout: 3000,
    closeOnClick: true,
    pauseOnFocusLoss: false,
    pauseOnHover: true,
    draggable: true,
    draggablePercent: 0.6,
    showCloseButtonOnHover: false,
    hideProgressBar: false,
    closeButton: 'button',
    icon: true,
    rtl: false,
    transition: 'Vue-Toastification__fade',
    maxToasts: 3,
    newestOnTop: true
};

createInertiaApp({
    title: (title) => title ? `${title} | ${appName}` : appName,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue'))
        .then((component) => {
            sessionStorage.removeItem(CHUNK_RELOAD_KEY);
            return component;
        })
        .catch((error) => {
            const isChunkError = error?.message?.includes('Failed to fetch dynamically imported module')
                || error?.name === 'TypeError';

            if (isChunkError && reloadOnceForChunk(name)) {
                return new Promise(() => {});
            }

            throw error;
        }),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(Toast, toastOptions)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
