import defaultTheme from 'tailwindcss/defaultTheme';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    safelist: [
        { pattern: /^(ps-|pe-|bg-|text-|btn-|grid-cols-\d+|col-span-\d+|[wh]-\d+)/ },
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: [ 'Figtree', ...defaultTheme.fontFamily.sans ],
            },
            colors: {
                primary: {
                    50: '#fff0f0',
                    100: '#ffdddd',
                    200: '#ffc0c0',
                    300: '#ff9494',
                    400: '#ff5757',
                    500: '#ff2323',
                    600: '#ff0000',
                    700: '#d70000',
                    800: '#b10303',
                    900: '#920a0a',
                    950: '#500000',
                }
            }
        },
    },
    plugins: [ typography ],
};
