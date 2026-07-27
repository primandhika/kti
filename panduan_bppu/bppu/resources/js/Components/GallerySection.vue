<template>
    <section id="gallery" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Section Header -->
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Galeri</h2>
                <div class="w-24 h-1 bg-primary-900 mx-auto mb-4"></div>
                <p class="text-lg text-gray-600">Unit-unit usaha BPPU IKIP Siliwangi</p>
            </div>

            <!-- Gallery Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Gallery Item 1 -->
                <div
                    class="group relative overflow-hidden rounded-lg shadow-lg cursor-pointer"
                    data-aos="zoom-in"
                    data-aos-delay="100"
                    @click="openLightbox(0)"
                >
                    <img src="/storage/galeri/20260108_110413.jpg" alt="Kegiatan BPPU" class="aspect-[4/3] w-full h-full object-cover transition-transform duration-300 group-hover:scale-110">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        <div class="absolute bottom-0 left-0 right-0 p-6 text-white transform translate-y-4 group-hover:translate-y-0 transition-transform duration-300">
                            <h3 class="text-lg font-bold mb-1">Workshop Manajemen Usaha</h3>
                            <p class="text-sm text-white/80">Pelatihan pengelolaan unit usaha</p>
                        </div>
                    </div>
                </div>

                <!-- Gallery Item 2 -->
                <div
                    class="group relative overflow-hidden rounded-lg shadow-lg cursor-pointer"
                    data-aos="zoom-in"
                    data-aos-delay="200"
                    @click="openLightbox(1)"
                >
                    <img src="/storage/galeri/20260108_110453.jpg" alt="Kegiatan BPPU" class="aspect-[4/3] w-full h-full object-cover transition-transform duration-300 group-hover:scale-110">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        <div class="absolute bottom-0 left-0 right-0 p-6 text-white transform translate-y-4 group-hover:translate-y-0 transition-transform duration-300">
                            <h3 class="text-lg font-bold mb-1">Rapat Evaluasi</h3>
                            <p class="text-sm text-white/80">Evaluasi pencapaian unit usaha</p>
                        </div>
                    </div>
                </div>

                <!-- Gallery Item 3 -->
                <div
                    class="group relative overflow-hidden rounded-lg shadow-lg cursor-pointer"
                    data-aos="zoom-in"
                    data-aos-delay="300"
                    @click="openLightbox(2)"
                >
                    <img src="/storage/galeri/20260108_110559.jpg" alt="Kegiatan BPPU" class="aspect-[4/3] w-full h-full object-cover transition-transform duration-300 group-hover:scale-110">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        <div class="absolute bottom-0 left-0 right-0 p-6 text-white transform translate-y-4 group-hover:translate-y-0 transition-transform duration-300">
                            <h3 class="text-lg font-bold mb-1">Sosialisasi Program</h3>
                            <p class="text-sm text-white/80">Pengenalan program usaha kepada civitas</p>
                        </div>
                    </div>
                </div>

                <!-- Gallery Item 4 -->
                <div
                    class="group relative overflow-hidden rounded-lg shadow-lg cursor-pointer"
                    data-aos="zoom-in"
                    data-aos-delay="100"
                    @click="openLightbox(3)"
                >
                    <img src="/storage/galeri/20260108_110608.jpg" alt="Kegiatan BPPU" class="aspect-[4/3] w-full h-full object-cover transition-transform duration-300 group-hover:scale-110">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        <div class="absolute bottom-0 left-0 right-0 p-6 text-white transform translate-y-4 group-hover:translate-y-0 transition-transform duration-300">
                            <h3 class="text-lg font-bold mb-1">Audit Internal</h3>
                            <p class="text-sm text-white/80">Pelaksanaan audit periode 2025/2026</p>
                        </div>
                    </div>
                </div>

                <!-- Gallery Item 5 -->
                <div
                    class="group relative overflow-hidden rounded-lg shadow-lg cursor-pointer"
                    data-aos="zoom-in"
                    data-aos-delay="200"
                    @click="openLightbox(4)"
                >
                    <img src="/storage/galeri/20260108_110613.jpg" alt="Kegiatan BPPU" class="aspect-[4/3] w-full h-full object-cover transition-transform duration-300 group-hover:scale-110">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        <div class="absolute bottom-0 left-0 right-0 p-6 text-white transform translate-y-4 group-hover:translate-y-0 transition-transform duration-300">
                            <h3 class="text-lg font-bold mb-1">Monitoring & Evaluasi</h3>
                            <p class="text-sm text-white/80">Pemantauan kinerja unit usaha</p>
                        </div>
                    </div>
                </div>

                <!-- Gallery Item 6 -->
                <div
                    class="group relative overflow-hidden rounded-lg shadow-lg cursor-pointer"
                    data-aos="zoom-in"
                    data-aos-delay="300"
                    @click="openLightbox(5)"
                >
                    <img src="/storage/galeri/20260108_110744.jpg" alt="Kegiatan BPPU" class="aspect-[4/3] w-full h-full object-cover transition-transform duration-300 group-hover:scale-110">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        <div class="absolute bottom-0 left-0 right-0 p-6 text-white transform translate-y-4 group-hover:translate-y-0 transition-transform duration-300">
                            <h3 class="text-lg font-bold mb-1">Koordinasi Tim</h3>
                            <p class="text-sm text-white/80">Rapat koordinasi tim pengelola usaha</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- View All Button -->
            <div class="text-center mt-12" data-aos="fade-up" data-aos-delay="400">
                <a href="#" class="inline-block bg-primary-900 text-white px-8 py-3 rounded-full font-semibold hover:bg-primary-800 transition-all duration-300 transform hover:scale-105">
                    Lihat Semua Galeri
                </a>
            </div>
        </div>
    </section>
</template>

<script setup>
const openLightbox = (index) => {
    // Placeholder for lightbox functionality
    console.log('Open lightbox for image:', index);
};
</script>
