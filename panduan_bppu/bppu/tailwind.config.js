/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
        './resources/js/**/*.js',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', 'ui-sans-serif', 'system-ui', 'sans-serif'],
            },
            colors: {
                primary: {
                    50: '#f4efe5',
                    100: '#eae0cc',
                    200: '#e0d1b2',
                    300: '#d6c199',
                    400: '#ccb27f',
                    500: '#c1a366',
                    600: '#b7934c',
                    700: '#ad8432',
                    800: '#a37519',
                    900: '#996600',
                    950: '#7a5100',
                },
            },
        },
    },
    plugins: [],
}
