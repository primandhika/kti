<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Halaman Tidak Ditemukan | BPPU IKIP Siliwangi</title>
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                        },
                    },
                },
            },
        }
    </script>
    <style>
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        .float-animation {
            animation: float 3s ease-in-out infinite;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 via-white to-blue-50 min-h-screen flex items-center justify-center px-4">
    <div class="max-w-2xl w-full text-center">
        <!-- Gambar Maung Mini -->
        <div class="mb-8 float-animation">
            <img src="/404-maungmini.png" alt="404 - Halaman Tidak Ditemukan" class="mx-auto max-w-md w-full">
        </div>

        <!-- Teks 404 -->
        <h1 class="text-6xl md:text-8xl font-bold text-primary-600 mb-4">404</h1>
        <h2 class="text-2xl md:text-3xl font-bold text-gray-800 mb-4">Halaman Tidak Ditemukan</h2>
        <p class="text-gray-600 text-lg mb-8">
            Maaf, halaman yang Anda cari tidak dapat ditemukan. Mungkin halaman tersebut telah dipindahkan atau dihapus.
        </p>

        <!-- Search Bar -->
        <form action="/" method="GET" class="max-w-xl mx-auto mb-8">
            <div class="relative">
                <input
                    type="text"
                    name="search"
                    placeholder="Cari halaman atau konten..."
                    class="w-full px-6 py-4 pr-14 rounded-full border-2 border-gray-300 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-200 text-gray-700 text-lg transition-all"
                >
                <button
                    type="submit"
                    class="absolute right-2 top-1/2 -translate-y-1/2 bg-gradient-to-r from-[#996600] to-[#CC8800] hover:from-[#CC8800] hover:to-[#996600] text-white p-3 rounded-full transition-all duration-200 hover:scale-105"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </button>
            </div>
        </form>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
            <a
                href="/"
                class="inline-flex items-center gap-2 bg-gradient-to-r from-[#996600] to-[#CC8800] hover:from-[#CC8800] hover:to-[#996600] text-white px-8 py-3 rounded-full font-bold text-lg transition-all duration-200 hover:scale-105 shadow-lg hover:shadow-xl"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                </svg>
                Kembali ke Beranda
            </a>
            <button
                onclick="history.back()"
                class="inline-flex items-center gap-2 bg-white hover:bg-gray-50 text-gray-700 px-8 py-3 rounded-full font-bold text-lg border-2 border-gray-300 transition-all duration-200 hover:scale-105 shadow-md hover:shadow-lg"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
                Halaman Sebelumnya
            </button>
        </div>

        <!-- Footer -->
        <div class="mt-12 pt-8 border-t border-gray-200">
            <p class="text-gray-500 text-sm">
                &copy; {{ date('Y') }} BPPU IKIP Siliwangi. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>
