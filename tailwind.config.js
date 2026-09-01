import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Plus Jakarta Sans', 'Figtree', ...defaultTheme.fontFamily.sans],
                display: ['Outfit', 'Plus Jakarta Sans', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                araucaria: {
                    50: '#f2f9f5',
                    100: '#e1f2e6',
                    200: '#c5e5d0',
                    300: '#9ad1b0',
                    400: '#69b48a',
                    500: '#44976c',
                    600: '#327a55',
                    700: '#296145',
                    800: '#1b4332',
                    900: '#16382a',
                    950: '#0b1f17',
                },
                pine: {
                    50: '#fffbeb',
                    100: '#fef3c7',
                    200: '#fde68a',
                    300: '#fcd34d',
                    400: '#fbbf24',
                    500: '#f59e0b',
                    600: '#d97706',
                    700: '#b45309',
                    800: '#92400e',
                    900: '#78350f',
                }
            }
        },
    },

    plugins: [forms, typography],
};
