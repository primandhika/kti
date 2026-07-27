<template>
    <teleport to="body">
        <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto">
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-black bg-opacity-75 transition-opacity"></div>

            <!-- Modal -->
            <div class="flex min-h-full items-center justify-center p-3 sm:p-4">
                <div class="relative bg-white rounded-xl sm:rounded-2xl shadow-2xl max-w-md w-full p-4 sm:p-8 transform transition-all max-h-[95vh] overflow-y-auto">
                    <!-- Close Button / Back to Home -->
                    <button
                        @click="handleClose"
                        class="absolute top-3 right-3 sm:top-4 sm:right-4 text-gray-400 hover:text-gray-600 transition-colors z-10"
                    >
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>

                    <!-- Countdown Notification -->
                    <div v-if="showCountdown" class="absolute inset-0 bg-white bg-opacity-95 rounded-xl sm:rounded-2xl flex items-center justify-center z-20">
                        <div class="text-center p-6">
                            <svg class="w-16 h-16 mx-auto text-primary-900 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-lg font-semibold text-gray-900 mb-2">
                                Anda akan diarahkan ke laman utama
                            </p>
                            <p class="text-5xl font-bold text-primary-900">
                                {{ countdown }}
                            </p>
                        </div>
                    </div>

                    <!-- Logo/Header -->
                    <div class="text-center mb-4 sm:mb-6">
                        <div class="inline-flex items-center justify-center mb-3 sm:mb-4">
                            <img
                                src="/logo.png"
                                alt="Logo IKIP Siliwangi"
                                class="w-16 h-16 sm:w-20 sm:h-20 object-contain"
                            />
                        </div>
                        <h2 class="text-xl sm:text-2xl font-bold text-gray-900">
                            {{ isLogin ? 'Masuk ke Akun' : 'Daftar Akun Baru' }}
                        </h2>
                        <p class="text-sm sm:text-base text-gray-600 mt-1 sm:mt-2">
                            {{ isLogin ? 'Login untuk melanjutkan pemesanan' : 'Buat akun untuk mulai memesan' }}
                        </p>
                    </div>

                    <!-- Login Form -->
                    <form v-if="isLogin" @submit.prevent="handleLogin" class="space-y-3 sm:space-y-4">
                        <!-- Error Message -->
                        <div v-if="form.errors.login" class="bg-red-50 border border-red-200 text-red-700 px-3 py-2 sm:px-4 sm:py-3 rounded-lg text-sm">
                            {{ form.errors.login }}
                        </div>

                        <!-- Username/Email -->
                        <div class="flex flex-col sm:flex-row sm:items-center gap-1.5 sm:gap-3">
                            <label class="text-sm font-medium text-gray-700 sm:w-32 sm:flex-shrink-0">Username/Email</label>
                            <input
                                type="text"
                                v-model="form.login"
                                required
                                class="flex-1 px-3 py-2 sm:px-4 sm:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-900 focus:border-transparent text-sm sm:text-base"
                                placeholder="Masukkan username atau email"
                            />
                        </div>

                        <!-- Password -->
                        <div class="flex flex-col sm:flex-row sm:items-center gap-1.5 sm:gap-3">
                            <label class="text-sm font-medium text-gray-700 sm:w-32 sm:flex-shrink-0">Password</label>
                            <input
                                type="password"
                                v-model="form.password"
                                required
                                class="flex-1 px-3 py-2 sm:px-4 sm:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-900 focus:border-transparent text-sm sm:text-base"
                                placeholder="Masukkan password"
                            />
                        </div>

                        <!-- Remember Me -->
                        <div class="flex items-center sm:pl-0">
                            <div class="hidden sm:block sm:w-32 sm:flex-shrink-0"></div>
                            <div class="flex items-center">
                                <input
                                    type="checkbox"
                                    v-model="form.remember"
                                    id="remember"
                                    class="w-4 h-4 text-primary-900 border-gray-300 rounded focus:ring-primary-900"
                                />
                                <label for="remember" class="ml-2 text-sm text-gray-700">Ingat saya</label>
                            </div>
                        </div>

                        <!-- Login Button -->
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="w-full bg-primary-900 text-white py-2.5 sm:py-3 rounded-lg font-semibold hover:bg-primary-800 transition-colors duration-300 disabled:opacity-50 text-sm sm:text-base mt-2"
                        >
                            {{ form.processing ? 'Memproses...' : 'Masuk' }}
                        </button>
                    </form>

                    <!-- Register Form -->
                    <form v-else @submit.prevent="handleRegister" class="space-y-3 sm:space-y-4">
                        <!-- Error Messages -->
                        <div v-if="Object.keys(form.errors).length > 0" class="bg-red-50 border border-red-200 text-red-700 px-3 py-2 sm:px-4 sm:py-3 rounded-lg">
                            <ul class="list-disc list-inside text-xs sm:text-sm space-y-0.5">
                                <li v-for="(error, field) in form.errors" :key="field">{{ error }}</li>
                            </ul>
                        </div>

                        <!-- Name -->
                        <div class="flex flex-col sm:flex-row sm:items-center gap-1.5 sm:gap-3">
                            <label class="text-sm font-medium text-gray-700 sm:w-32 sm:flex-shrink-0">Nama Lengkap</label>
                            <input
                                type="text"
                                v-model="form.name"
                                required
                                class="flex-1 px-3 py-2 sm:px-4 sm:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-900 focus:border-transparent text-sm sm:text-base"
                                placeholder="Masukkan nama lengkap"
                            />
                        </div>

                        <!-- Email -->
                        <div class="flex flex-col sm:flex-row sm:items-center gap-1.5 sm:gap-3">
                            <label class="text-sm font-medium text-gray-700 sm:w-32 sm:flex-shrink-0">Email</label>
                            <input
                                type="email"
                                v-model="form.email"
                                required
                                class="flex-1 px-3 py-2 sm:px-4 sm:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-900 focus:border-transparent text-sm sm:text-base"
                                placeholder="email@example.com"
                            />
                        </div>

                        <!-- Username -->
                        <div class="flex flex-col sm:flex-row sm:items-center gap-1.5 sm:gap-3">
                            <label class="text-sm font-medium text-gray-700 sm:w-32 sm:flex-shrink-0">Username</label>
                            <input
                                type="text"
                                v-model="form.username"
                                required
                                class="flex-1 px-3 py-2 sm:px-4 sm:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-900 focus:border-transparent text-sm sm:text-base"
                                placeholder="Pilih username"
                            />
                        </div>

                        <!-- Phone -->
                        <div class="flex flex-col sm:flex-row sm:items-center gap-1.5 sm:gap-3">
                            <label class="text-sm font-medium text-gray-700 sm:w-32 sm:flex-shrink-0">No. HP</label>
                            <input
                                type="tel"
                                v-model="form.phone"
                                class="flex-1 px-3 py-2 sm:px-4 sm:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-900 focus:border-transparent text-sm sm:text-base"
                                placeholder="08xxxxxxxxxx (opsional)"
                            />
                        </div>

                        <!-- Password -->
                        <div class="flex flex-col sm:flex-row sm:items-center gap-1.5 sm:gap-3">
                            <label class="text-sm font-medium text-gray-700 sm:w-32 sm:flex-shrink-0">Password</label>
                            <input
                                type="password"
                                v-model="form.password"
                                required
                                class="flex-1 px-3 py-2 sm:px-4 sm:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-900 focus:border-transparent text-sm sm:text-base"
                                placeholder="Minimal 8 karakter"
                            />
                        </div>

                        <!-- Password Confirmation -->
                        <div class="flex flex-col sm:flex-row sm:items-center gap-1.5 sm:gap-3">
                            <label class="text-sm font-medium text-gray-700 sm:w-32 sm:flex-shrink-0">Konfirmasi</label>
                            <input
                                type="password"
                                v-model="form.password_confirmation"
                                required
                                class="flex-1 px-3 py-2 sm:px-4 sm:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-900 focus:border-transparent text-sm sm:text-base"
                                placeholder="Ulangi password"
                            />
                        </div>

                        <!-- Register Button -->
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="w-full bg-primary-900 text-white py-2.5 sm:py-3 rounded-lg font-semibold hover:bg-primary-800 transition-colors duration-300 disabled:opacity-50 text-sm sm:text-base mt-2"
                        >
                            {{ form.processing ? 'Memproses...' : 'Daftar' }}
                        </button>
                    </form>

                    <!-- Toggle Login/Register -->
                    <div class="mt-4 sm:mt-6 text-center">
                        <a
                            v-if="isLogin"
                            :href="route('member.register.form')"
                            class="text-primary-900 hover:text-primary-800 font-medium text-sm sm:text-base"
                        >
                            Belum punya akun? Daftar di sini
                        </a>
                        <button
                            v-else
                            @click="toggleMode"
                            class="text-primary-900 hover:text-primary-800 font-medium text-sm sm:text-base"
                        >
                            Sudah punya akun? Masuk di sini
                        </button>
                    </div>

                    <!-- Back to Home Link -->
                    <div class="mt-3 sm:mt-4 text-center">
                        <Link
                            href="/"
                            class="inline-flex items-center gap-2 text-xs sm:text-sm text-gray-600 hover:text-gray-900 transition-colors"
                        >
                            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                            <span>Kembali ke Beranda</span>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </teleport>
</template>

<script setup>
import { ref, reactive, watch, onMounted, onUnmounted } from 'vue';
import { Link, router } from '@inertiajs/vue3';

const emit = defineEmits(['close']);

const props = defineProps({
    show: { type: Boolean, default: false },
    cancelable: { type: Boolean, default: false },
});

const isLogin = ref(true);
const showCountdown = ref(false);
const countdown = ref(5);
let countdownInterval = null;

const form = reactive({
    login: '',
    password: '',
    remember: false,
    name: '',
    email: '',
    username: '',
    phone: '',
    password_confirmation: '',
    processing: false,
    errors: {}
});

// Load saved credentials from localStorage
const loadRememberedCredentials = () => {
    try {
        const savedLogin = localStorage.getItem('buyer_remember_login');
        if (savedLogin) {
            form.login = savedLogin;
            form.remember = true;
        }
    } catch (error) {
        console.error('Error loading saved credentials:', error);
    }
};

// Save credentials to localStorage
const saveCredentials = () => {
    try {
        if (form.remember) {
            localStorage.setItem('buyer_remember_login', form.login);
        } else {
            localStorage.removeItem('buyer_remember_login');
        }
    } catch (error) {
        console.error('Error saving credentials:', error);
    }
};

const toggleMode = () => {
    isLogin.value = !isLogin.value;
    form.errors = {};
};

const handleLogin = () => {
    form.processing = true;
    form.errors = {};

    // Save credentials based on remember me checkbox
    saveCredentials();

    router.post('/buyer/login', {
        login: form.login,
        password: form.password,
        remember: form.remember,
        redirect_to: window.location.pathname + window.location.search,
    }, {
        onError: (errors) => {
            form.errors = errors;
            form.processing = false;
        },
        onFinish: () => {
            form.processing = false;
        }
    });
};

const handleRegister = () => {
    form.processing = true;
    form.errors = {};

    router.post('/buyer/register', {
        name: form.name,
        email: form.email,
        username: form.username,
        phone: form.phone,
        password: form.password,
        password_confirmation: form.password_confirmation,
    }, {
        onError: (errors) => {
            form.errors = errors;
            form.processing = false;
        },
        onFinish: () => {
            form.processing = false;
        }
    });
};

const handleClose = () => {
    // Jika bisa dibatalkan (dipanggil dari flow pesanan), cukup tutup modal
    if (props.cancelable) {
        emit('close');
        return;
    }
    showCountdown.value = true;
    countdown.value = 5;

    countdownInterval = setInterval(() => {
        countdown.value--;

        if (countdown.value <= 0) {
            clearInterval(countdownInterval);
            window.location.href = '/';
        }
    }, 1000);
};

// Reset form and countdown when modal is closed
watch(() => props.show, (newVal) => {
    if (!newVal) {
        form.errors = {};
        showCountdown.value = false;
        countdown.value = 5;
        if (countdownInterval) {
            clearInterval(countdownInterval);
        }
    } else {
        // Load remembered credentials when modal opens
        loadRememberedCredentials();
    }
});

// Watch remember checkbox to clear saved data when unchecked
watch(() => form.remember, (newVal) => {
    if (!newVal && !form.processing) {
        localStorage.removeItem('buyer_remember_login');
    }
});

// Load on component mount
onMounted(() => {
    if (props.show) {
        loadRememberedCredentials();
    }
});

// Cleanup countdown interval on unmount
onUnmounted(() => {
    if (countdownInterval) {
        clearInterval(countdownInterval);
    }
});
</script>
