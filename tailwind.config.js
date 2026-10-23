/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './app/**/*.php',
    ],
    theme: {
        extend: {
            colors: {
                brand: {
                    50: '#fff7ed',
                    100: '#ffedd5',
                    200: '#ffd9ac',
                    300: '#ffbd73',
                    400: '#ff9a3d',
                    500: '#f97316',
                    600: '#ea580c',
                    700: '#c2410c',
                    800: '#9a3412',
                    900: '#7c2d12',
                },
                ink: {
                    50: '#f9f9f8',
                    100: '#f1f0ee',
                    400: '#8a887f',
                    500: '#6b6a62',
                    700: '#3f3e38',
                    900: '#201f1c',
                },
            },
            boxShadow: {
                card: '0 1px 2px 0 rgb(154 52 18 / 0.04), 0 1px 3px 0 rgb(0 0 0 / 0.04)',
            },
        },
    },
    plugins: [],
};
