import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                indigo: {
                    50:  '#fff0f6',
                    100: '#ffd6ea',
                    200: '#ffadd4',
                    300: '#ff84be',
                    400: '#f95ba2',
                    500: '#f43180',
                    600: '#d01568',
                    700: '#a30f52',
                    800: '#780a3c',
                    900: '#4d0626',
                    950: '#300318',
                },
            },
        },
    },

    plugins: [forms],
};
