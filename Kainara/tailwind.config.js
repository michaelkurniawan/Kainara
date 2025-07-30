// tailwind.config.js
import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        // Ini OK, untuk memastikan styling Pagination Laravel bekerja
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        
        // HAPUS BARIS INI: Tailwind TIDAK PERLU membaca file cache PHP
        // './storage/framework/views/*.php', 
        
        // INI PENTING DAN HARUS ADA: Semua Blade view kustom Anda
        './resources/views/**/*.blade.php', 

        // Tambahkan jika Anda menggunakan JS/Vue/React/Livewire di resources/js/
        './resources/js/**/*.js',
        './resources/js/**/*.vue',
        './resources/js/**/*.ts', // Jika menggunakan TypeScript
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Ancizar Sans', ...defaultTheme.fontFamily.sans],
                serif: ['Ancizar Serif', ...defaultTheme.fontFamily.serif],
            },
        },
    },

    plugins: [forms],
};