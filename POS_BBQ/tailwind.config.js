import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Poppins', 'sans-serif'],
            },
            fontSize: {
                'xs': '0.8125rem',   // 13px
                'sm': '0.9375rem',   // 15px
                'base': '1.0625rem', // 17px
                'lg': '1.1875rem',   // 19px
                'xl': '1.3125rem',   // 21px
                '2xl': '1.5625rem',  // 25px
                '3xl': '1.9375rem',  // 31px
                '4xl': '2.3125rem',  // 37px
            },
        },
    },

    plugins: [forms],
};
