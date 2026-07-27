<template>
    <form @submit.prevent="$emit('submit')" class="space-y-8">
        <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
            <p class="text-sm text-amber-800">
                Bantu kami meningkatkan layanan dengan mengisi survey singkat berikut. Rating: 1 (Sangat Tidak Puas) - 5 (Sangat Puas)
            </p>
        </div>

        <!-- Likert Scale Questions -->
        <div class="space-y-6">
            <h3 class="text-lg font-semibold text-gray-800 border-b pb-2">Penilaian Kantin</h3>

            <!-- Rating Pelayanan -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-3">
                    Bagaimana pelayanan kantin BPPU saat ini? <span class="text-red-500">*</span>
                </label>
                <div class="flex gap-2 justify-center">
                    <button
                        v-for="rating in 5"
                        :key="`pelayanan-${rating}`"
                        type="button"
                        @click="form.rating_pelayanan = rating"
                        :class="[
                            'w-14 h-14 rounded-full font-semibold transition-all',
                            form.rating_pelayanan === rating
                                ? 'bg-[#996600] text-white scale-110 shadow-lg'
                                : 'bg-gray-200 text-gray-600 hover:bg-gray-300'
                        ]"
                    >
                        {{ rating }}
                    </button>
                </div>
                <p v-if="errors.rating_pelayanan" class="mt-2 text-sm text-red-600">{{ errors.rating_pelayanan[0] }}</p>
            </div>

            <!-- Rating Kualitas -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-3">
                    Bagaimana kualitas menu yang tersedia saat ini? <span class="text-red-500">*</span>
                </label>
                <div class="flex gap-2 justify-center">
                    <button
                        v-for="rating in 5"
                        :key="`kualitas-${rating}`"
                        type="button"
                        @click="form.rating_kualitas = rating"
                        :class="[
                            'w-14 h-14 rounded-full font-semibold transition-all',
                            form.rating_kualitas === rating
                                ? 'bg-[#996600] text-white scale-110 shadow-lg'
                                : 'bg-gray-200 text-gray-600 hover:bg-gray-300'
                        ]"
                    >
                        {{ rating }}
                    </button>
                </div>
                <p v-if="errors.rating_kualitas" class="mt-2 text-sm text-red-600">{{ errors.rating_kualitas[0] }}</p>
            </div>

            <!-- Rating Variasi -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-3">
                    Bagaimana variasi menu yang tersedia saat ini? <span class="text-red-500">*</span>
                </label>
                <div class="flex gap-2 justify-center">
                    <button
                        v-for="rating in 5"
                        :key="`variasi-${rating}`"
                        type="button"
                        @click="form.rating_variasi = rating"
                        :class="[
                            'w-14 h-14 rounded-full font-semibold transition-all',
                            form.rating_variasi === rating
                                ? 'bg-[#996600] text-white scale-110 shadow-lg'
                                : 'bg-gray-200 text-gray-600 hover:bg-gray-300'
                        ]"
                    >
                        {{ rating }}
                    </button>
                </div>
                <p v-if="errors.rating_variasi" class="mt-2 text-sm text-red-600">{{ errors.rating_variasi[0] }}</p>
            </div>

            <!-- Rating Harga -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-3">
                    Bagaimana kesesuaian harga dengan kualitas menu? <span class="text-red-500">*</span>
                </label>
                <div class="flex gap-2 justify-center">
                    <button
                        v-for="rating in 5"
                        :key="`harga-${rating}`"
                        type="button"
                        @click="form.rating_harga = rating"
                        :class="[
                            'w-14 h-14 rounded-full font-semibold transition-all',
                            form.rating_harga === rating
                                ? 'bg-[#996600] text-white scale-110 shadow-lg'
                                : 'bg-gray-200 text-gray-600 hover:bg-gray-300'
                        ]"
                    >
                        {{ rating }}
                    </button>
                </div>
                <p v-if="errors.rating_harga" class="mt-2 text-sm text-red-600">{{ errors.rating_harga[0] }}</p>
            </div>

            <!-- Rating Suasana -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-3">
                    Bagaimana suasana kantin saat ini? <span class="text-red-500">*</span>
                </label>
                <div class="flex gap-2 justify-center">
                    <button
                        v-for="rating in 5"
                        :key="`suasana-${rating}`"
                        type="button"
                        @click="form.rating_suasana = rating"
                        :class="[
                            'w-14 h-14 rounded-full font-semibold transition-all',
                            form.rating_suasana === rating
                                ? 'bg-[#996600] text-white scale-110 shadow-lg'
                                : 'bg-gray-200 text-gray-600 hover:bg-gray-300'
                        ]"
                    >
                        {{ rating }}
                    </button>
                </div>
                <p v-if="errors.rating_suasana" class="mt-2 text-sm text-red-600">{{ errors.rating_suasana[0] }}</p>
            </div>
        </div>

        <!-- Essay Questions -->
        <div class="space-y-6">
            <h3 class="text-lg font-semibold text-gray-800 border-b pb-2">Pendapat Anda</h3>

            <!-- Menu Favorit -->
            <div>
                <label for="menu_favorit" class="block text-sm font-medium text-gray-700 mb-2">
                    Menu favorit Anda di Kantin IKIP Siliwangi saat ini <span class="text-red-500">*</span>
                </label>
                <textarea
                    id="menu_favorit"
                    v-model="form.menu_favorit"
                    rows="3"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent transition"
                    placeholder="Tuliskan menu favorit Anda..."
                    maxlength="500"
                    required
                ></textarea>
                <div class="flex justify-between mt-1">
                    <p v-if="errors.menu_favorit" class="text-sm text-red-600">{{ errors.menu_favorit[0] }}</p>
                    <p class="text-xs text-gray-500">{{ form.menu_favorit.length }}/500</p>
                </div>
            </div>

            <!-- Menu Rekomendasi -->
            <div>
                <label for="menu_rekomendasi" class="block text-sm font-medium text-gray-700 mb-2">
                    Menu apa yang kamu rekomendasikan untuk ada di Kantin IKIP Siliwangi? <span class="text-red-500">*</span>
                </label>
                <textarea
                    id="menu_rekomendasi"
                    v-model="form.menu_rekomendasi"
                    rows="3"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent transition"
                    placeholder="Tuliskan rekomendasi menu Anda..."
                    maxlength="500"
                    required
                ></textarea>
                <div class="flex justify-between mt-1">
                    <p v-if="errors.menu_rekomendasi" class="text-sm text-red-600">{{ errors.menu_rekomendasi[0] }}</p>
                    <p class="text-xs text-gray-500">{{ form.menu_rekomendasi.length }}/500</p>
                </div>
            </div>

            <!-- Kritik dan Saran -->
            <div>
                <label for="kritik_saran" class="block text-sm font-medium text-gray-700 mb-2">
                    Kritik dan saran untuk kantin <span class="text-red-500">*</span>
                </label>
                <textarea
                    id="kritik_saran"
                    v-model="form.kritik_saran"
                    rows="4"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#996600] focus:border-transparent transition"
                    placeholder="Tuliskan kritik dan saran Anda..."
                    maxlength="1000"
                    required
                ></textarea>
                <div class="flex justify-between mt-1">
                    <p v-if="errors.kritik_saran" class="text-sm text-red-600">{{ errors.kritik_saran[0] }}</p>
                    <p class="text-xs text-gray-500">{{ form.kritik_saran.length }}/1000</p>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex gap-4 pt-4">
            <button
                type="button"
                @click="$emit('back')"
                class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-3 px-6 rounded-lg transition duration-200"
            >
                ← Kembali
            </button>
            <button
                type="submit"
                :disabled="processing || !isFormValid"
                class="flex-1 bg-[#996600] hover:bg-[#7a5100] text-white font-semibold py-3 px-6 rounded-lg transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed shadow-lg hover:shadow-xl"
            >
                <span v-if="processing">Mengirim...</span>
                <span v-else>Kirim Survey</span>
            </button>
        </div>
    </form>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    form: {
        type: Object,
        required: true,
    },
    errors: {
        type: Object,
        default: () => ({}),
    },
    processing: {
        type: Boolean,
        default: false,
    },
});

defineEmits(['submit', 'back']);

const isFormValid = computed(() => {
    return (
        props.form.rating_pelayanan > 0 &&
        props.form.rating_kualitas > 0 &&
        props.form.rating_variasi > 0 &&
        props.form.rating_harga > 0 &&
        props.form.rating_suasana > 0 &&
        props.form.menu_favorit.trim() !== '' &&
        props.form.menu_rekomendasi.trim() !== '' &&
        props.form.kritik_saran.trim() !== ''
    );
});
</script>
