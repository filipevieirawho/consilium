import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['DM Sans', ...defaultTheme.fontFamily.sans],
                outfit: ['DM Sans', 'sans-serif'], // Forced override
                cormorant: ['DM Sans', 'sans-serif'], // Forced override
            },
            colors: {
                gold: '#c9a66b',
                dark: '#0e0e0e',
                black: '#000000',
            },
        },
    },

    plugins: [forms],
};
