import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
        './app/**/*.php',
    ],

    theme: {
        extend: {
            colors: {
                // Pulled from the FestLoop logo, flattened for print-poster feel.
                paper: '#F4EFE4',
                cream: '#FBF8F1',
                ink: {
                    DEFAULT: '#171A3D',
                    soft: '#4B4F72',
                    mute: '#8A8CA3',
                },
                tangerine: '#F2762E',
                plum: '#7A3E9D',
                sky: '#2E8FD6',
                butter: '#FFD447',
                moss: '#3C8D5A',
            },
            fontFamily: {
                sans: ['"Instrument Sans"', ...defaultTheme.fontFamily.sans],
                display: ['Anton', 'Impact', ...defaultTheme.fontFamily.sans],
                serif: ['"Instrument Serif"', ...defaultTheme.fontFamily.serif],
                mono: ['"JetBrains Mono"', ...defaultTheme.fontFamily.mono],
            },
            boxShadow: {
                hard: '4px 4px 0 0 #171A3D',
                'hard-sm': '2px 2px 0 0 #171A3D',
                'hard-lg': '8px 8px 0 0 #171A3D',
            },
            keyframes: {
                marquee: {
                    '0%': { transform: 'translateX(0)' },
                    '100%': { transform: 'translateX(-50%)' },
                },
                blink: {
                    '0%, 100%': { opacity: 1 },
                    '50%': { opacity: 0.25 },
                },
            },
            animation: {
                marquee: 'marquee 40s linear infinite',
                blink: 'blink 1.4s ease-in-out infinite',
            },
        },
    },

    plugins: [forms],
};
