/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    theme: {
        extend: {
            colors: {
                'brand-teal': '#00C2A8',
                'dark-slate': '#1B2A33',
                'mint-wash': '#E6F7F5',
                'surface-white': '#FFFFFF',
                'text-primary': '#222222',
                'text-secondary': '#666666',
                'border-light': '#E5E7EB',
            },
            fontFamily: {
                sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
            },
            boxShadow: {
                soft: '0 4px 20px rgba(0, 0, 0, 0.05)',
            },
            borderRadius: {
                card: '16px',
            },
        },
    },
    plugins: [],
};
