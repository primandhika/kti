<template>
    <div class="min-h-screen bg-gray-50 py-8 px-4">
        <div class="max-w-4xl mx-auto">
            <div class="bg-white shadow-sm border border-gray-200">
                <!-- Header -->
                <div class="bg-[#996600] px-6 py-4 border-b border-gray-300">
                    <h1 class="text-xl font-bold text-white">
                        KETENTUAN DAN KEBIJAKAN KEANGGOTAAN
                    </h1>
                    <p class="text-xs text-amber-100 mt-1">
                        Kantin BPPU IKIP Siliwangi
                    </p>
                </div>

                <!-- Content -->
                <div class="px-6 py-6 space-y-5 text-sm">
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

            <!-- Back Button -->
            <div class="text-center mt-4">
                <button
                    @click="$router.back()"
                    class="text-xs text-gray-600 hover:text-gray-800 underline"
                >
                    Tutup
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
const props = defineProps({
    membershipTiers: {
        type: Array,
        required: true,
    },
});
</script>
