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

    safelist: [
        // Cores emerald (devolução, status)
        { pattern: /bg-emerald-(50|100|500|600|700)/ },
        { pattern: /text-emerald-(600|700|800)/ },
        { pattern: /border-emerald-(400|500|600)/ },
        { pattern: /hover:bg-emerald-(700|800)/ },
        // Cores green (status badges)
        { pattern: /bg-green-(100|600|700)/ },
        { pattern: /text-green-(600|700|800)/ },
        // Cores yellow/orange (status badges)
        { pattern: /bg-yellow-(100|200)/ },
        { pattern: /text-yellow-(600|700|800)/ },
        { pattern: /bg-orange-(100)/ },
        { pattern: /text-orange-(700|800)/ },
        // Cores red (exclusão, alertas)
        { pattern: /bg-red-(100|600)/ },
        { pattern: /text-red-(600|700|800)/ },
    ],

    plugins: [forms],
};
