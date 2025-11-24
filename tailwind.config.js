import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';


export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views*.blade.php',
        "./resources*.js",
        "./resources*.vue",
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
    darkMode: 'class', // Sem detecção automática pelo sistema
};
