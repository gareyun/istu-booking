/** @type {import('tailwindcss').Config} */

export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],

    theme: {
        extend: {
            colors: {
                primary: '#1A2A6C',
                secondary: '#858796',
                danger: '#e74a3b',
                success: '#1cc88a',
                warning: '#FFF3CD',
            }
        },
    },
    plugins: [],
}