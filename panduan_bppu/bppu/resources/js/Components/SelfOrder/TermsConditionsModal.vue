<template>
    <!-- Modal Backdrop -->
    <Transition
        enter-active-class="transition-opacity duration-200"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition-opacity duration-150"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div
            v-if="show"
            @click="$emit('close')"
            class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
        >
            <!-- Modal Content -->
            <Transition
                enter-active-class="transition-all duration-200"
                enter-from-class="opacity-0 scale-95"
                enter-to-class="opacity-100 scale-100"
                leave-active-class="transition-all duration-150"
                leave-from-class="opacity-100 scale-100"
                leave-to-class="opacity-0 scale-95"
            >
                <div
                    v-if="show"
                    @click.stop
                    class="bg-white shadow-xl max-w-4xl w-full max-h-[90vh] overflow-hidden flex flex-col"
                >
                    <!-- Header -->
                    <div class="bg-[#996600] px-6 py-4 border-b border-gray-300 flex items-center justify-between flex-shrink-0">
                        <div>
                            <h1 class="text-xl font-bold text-white">
                                KETENTUAN DAN KEBIJAKAN KEANGGOTAAN
                            </h1>
                            <p class="text-xs text-amber-100 mt-1">
                                Kantin BPPU IKIP Siliwangi
                            </p>
                        </div>
                        <button
                            @click="$emit('close')"
                            class="text-white hover:text-amber-100 transition-colors ml-4"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Content - Scrollable -->
                    <div class="px-6 py-6 overflow-y-auto flex-1">
                        <div class="space-y-5 text-sm">
                            <!-- 1. Pendahuluan -->
                            <section>
                                <h2 class="text-base font-bold text-gray-900 mb-2">
                                    1. PENDAHULUAN
                                </h2>
                                <p class="text-gray-700 leading-relaxed">
                                    Dengan mendaftar sebagai member Kantin BPPU IKIP Siliwangi, Anda menyetujui untuk mematuhi seluruh ketentuan dan kebijakan yang tercantum dalam dokumen ini. Program ini dirancang khusus untuk mahasiswa IKIP Siliwangi.
                                </p>
                            </section>

                            <!-- 2. Privasi dan Keamanan Data -->
                            <section>
                                <h2 class="text-base font-bold text-gray-900 mb-2">
                                    2. PRIVASI DAN KEAMANAN DATA
                                </h2>
                                <div class="bg-green-50 border border-green-200 px-3 py-2 mb-3">
                                    <p class="text-xs text-green-800 font-semibold">
                                        Kami berkomitmen penuh melindungi privasi Anda. Data Anda TIDAK AKAN disebarluaskan, dijual, atau diperdagangkan kepada pihak ketiga.
                                    </p>
                                </div>
                                <div class="text-gray-700 space-y-2 leading-relaxed">
                                    <p><strong>Data yang dikumpulkan:</strong> NIM, nama lengkap, email, riwayat transaksi, dan feedback survey.</p>
                                    <p><strong>Penggunaan data:</strong> Verifikasi keanggotaan, pengelolaan poin, notifikasi, analisis peningkatan layanan, dan komunikasi transaksi.</p>
                                </div>
                            </section>

                            <!-- 3. Sistem Poin dan Membership Tier -->
                            <section>
                                <h2 class="text-base font-bold text-gray-900 mb-2">
                                    3. SISTEM POIN DAN TINGKAT KEANGGOTAAN
                                </h2>
                                <div class="text-gray-700 space-y-2 leading-relaxed">
                                    <p><strong>Perolehan poin:</strong> Setiap Rp 1.000 = 1 poin dasar × multiplier tier. Poin otomatis terakumulasi dan tidak hangus.</p>

                                    <!-- Compact Membership Tiers Table -->
                                    <div class="overflow-x-auto mt-3">
                                        <table class="w-full text-xs border border-gray-300">
                                            <thead class="bg-gray-100">
                                                <tr>
                                                    <th class="border border-gray-300 px-2 py-1 text-left">Tier</th>
                                                    <th class="border border-gray-300 px-2 py-1 text-right">Min. Poin</th>
                                                    <th class="border border-gray-300 px-2 py-1 text-right">Diskon</th>
                                                    <th class="border border-gray-300 px-2 py-1 text-right">Multiplier</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="tier in membershipTiers" :key="tier.id">
                                                    <td class="border border-gray-300 px-2 py-1 font-semibold">{{ tier.name }}</td>
                                                    <td class="border border-gray-300 px-2 py-1 text-right">{{ tier.min_points.toLocaleString('id-ID') }}</td>
                                                    <td class="border border-gray-300 px-2 py-1 text-right">{{ tier.discount_percentage }}%</td>
                                                    <td class="border border-gray-300 px-2 py-1 text-right">{{ tier.points_multiplier }}x</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <p class="text-xs italic text-gray-600">Contoh: Belanja Rp 50.000 dengan tier Silver (1.2x) = 50 poin × 1.2 = 60 poin.</p>
                                </div>
                            </section>

                            <!-- 4. Syarat dan Ketentuan Keanggotaan -->
                            <section>
                                <h2 class="text-base font-bold text-gray-900 mb-2">
                                    4. SYARAT DAN KETENTUAN
                                </h2>
                                <p class="text-gray-700 leading-relaxed">
                                    Keanggotaan terbuka untuk mahasiswa aktif IKIP Siliwangi. Satu NIM untuk satu akun. Data harus sesuai Sikap/Siakad dan email harus diverifikasi. Keanggotaan berlaku selama status mahasiswa aktif. Poin tidak hangus.
                                </p>
                            </section>

                            <!-- 5. Hak dan Kewajiban Member -->
                            <section>
                                <h2 class="text-base font-bold text-gray-900 mb-2">
                                    5. HAK DAN KEWAJIBAN
                                </h2>
                                <p class="text-gray-700 leading-relaxed">
                                    <strong>Hak:</strong> Poin transaksi, diskon tier, promo, riwayat transaksi, self-order, memberikan feedback.
                                    <strong>Kewajiban:</strong> Jaga kerahasiaan akun, tidak menyalahgunakan sistem, bertransaksi dengan itikad baik, laporkan kesalahan, perbarui data, patuhi ketentuan.
                                </p>
                            </section>

                            <!-- 6. Penggunaan Sistem Self-Order -->
                            <section>
                                <h2 class="text-base font-bold text-gray-900 mb-2">
                                    6. SISTEM SELF-ORDER
                                </h2>
                                <p class="text-gray-700 leading-relaxed">
                                    Pemesanan online tersedia. Pesanan terkonfirmasi tidak dapat dibatalkan. Pembayaran di tempat saat pengambilan. Poin otomatis ditambahkan. Member bertanggung jawab atas kebenaran pesanan.
                                </p>
                            </section>

                            <!-- 7. Pembatalan dan Penghentian Keanggotaan -->
                            <section>
                                <h2 class="text-base font-bold text-gray-900 mb-2">
                                    7. PENGHENTIAN KEANGGOTAAN
                                </h2>
                                <p class="text-gray-700 leading-relaxed">
                                    Member dapat mengajukan penghentian kapan saja (poin akan hangus). Pengelola berhak menonaktifkan akun jika terbukti kecurangan, penyalahgunaan sistem, data palsu, atau melanggar ketentuan.
                                </p>
                            </section>

                            <!-- 8. Perubahan Ketentuan -->
                            <section>
                                <h2 class="text-base font-bold text-gray-900 mb-2">
                                    8. PERUBAHAN KETENTUAN
                                </h2>
                                <p class="text-gray-700 leading-relaxed">
                                    BPPU IKIP Siliwangi berhak mengubah ketentuan sewaktu-waktu. Perubahan diinformasikan via email/pengumuman. Penggunaan layanan setelah perubahan dianggap menyetujui ketentuan baru.
                                </p>
                            </section>

                            <!-- 9. Kontak -->
                            <section>
                                <h2 class="text-base font-bold text-gray-900 mb-2">
                                    9. KONTAK
                                </h2>
                                <p class="text-gray-700 leading-relaxed">
                                    Pertanyaan atau bantuan: <strong>bppu@ikipsiliwangi.ac.id</strong> atau kunjungi Kantin BPPU IKIP Siliwangi.
                                </p>
                            </section>

                            <!-- 10. Persetujuan -->
                            <section class="bg-gray-50 border border-gray-300 p-3">
                                <p class="text-xs text-gray-700 text-center">
                                    Dengan mencentang persetujuan pada formulir pendaftaran, Anda menyatakan telah membaca, memahami, dan menyetujui seluruh ketentuan dan kebijakan dalam dokumen ini.
                                </p>
                            </section>

                            <!-- Footer -->
                            <div class="text-center text-xs text-gray-500 pt-3 border-t border-gray-200">
                                <p>Terakhir diperbarui: Februari 2026 | © 2026 BPPU IKIP Siliwangi</p>
                            </div>
                        </div>
                    </div>

                    <!-- Footer Actions -->
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end flex-shrink-0">
                        <button
                            @click="$emit('close')"
                            class="px-6 py-2 bg-[#996600] hover:bg-[#7a5100] text-white font-semibold rounded transition-colors text-sm"
                        >
                            Tutup
                        </button>
                    </div>
                </div>
            </Transition>
        </div>
    </Transition>
</template>

<script setup>
defineProps({
    show: {
        type: Boolean,
        required: true,
    },
    membershipTiers: {
        type: Array,
        default: () => [],
    },
});

defineEmits(['close']);
</script>
