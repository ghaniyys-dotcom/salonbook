/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
    ],
    theme: {
        extend: {
            colors: {
                /* ——— Rose Noir Surface Hierarchy ——— */
                lacquer: {
                    DEFAULT: '#1A1220',
                    light: '#2A1F30',
                },
                charcoal: {
                    50: '#f5f0e8',
                    100: '#ede5d9',
                    200: '#ddd0b8',
                    300: '#c9b496',
                    400: '#a89070',
                    500: '#7a6850',
                    600: '#5a4538',
                    700: '#3d2c28',
                    800: '#2a1f20',
                    900: '#1f1618',
                    950: '#120c10',
                },
                /* Rose Noir Gold — warm muted gold */
                brass: {
                    50: '#faf8f0',
                    100: '#f5f0d8',
                    200: '#ede5c0',
                    300: '#dcc9a0',
                    400: '#d4b896',
                    500: '#c9a96e',
                    600: '#b8944f',
                    700: '#9a7a38',
                    800: '#7d6530',
                    900: '#6a5528',
                },
                /* Cream — premium highlight */
                cream: {
                    50: '#fdfcf9',
                    100: '#faf8f5',
                    200: '#f5f0e8',
                    300: '#ede5d9',
                    400: '#ddd0b8',
                    500: '#c9b89a',
                    600: '#b0987a',
                    700: '#8f7a61',
                    800: '#756452',
                    900: '#635345',
                },
                /* Dusty rose — beauty accent */
                'rose-gold': {
                    50: '#fdf2f4',
                    100: '#fce4e9',
                    200: '#f9ced9',
                    300: '#f4a8bc',
                    400: '#ec7899',
                    500: '#d94a74',
                    600: '#c13262',
                    700: '#a22651',
                    800: '#882346',
                    900: '#74223f',
                },
                /* Mauve — dark surface tones */
                mauve: {
                    50: '#f8f5f6',
                    100: '#f0e8eb',
                    200: '#e0d0d8',
                    300: '#c9adb8',
                    400: '#a88090',
                    500: '#7a6068',
                    600: '#5a4548',
                    700: '#3d2c30',
                    800: '#2a1f20',
                    900: '#1f1618',
                    950: '#120c10',
                },
            fontFamily: {
                display: ['Playfair Display', 'Georgia', 'serif'],
                sans: ['Inter', 'system-ui', 'sans-serif'],
                mono: ['SF Mono', 'SFMono-Regular', 'ui-monospace', 'monospace'],
            },
            fontSize: {
                'micro': ['0.625rem', { lineHeight: '0.875rem', letterSpacing: '0.15em', fontWeight: '700' }],
                'label': ['0.6875rem', { lineHeight: '1rem', letterSpacing: '0.1em', fontWeight: '700' }],
            },
            spacing: {
                '18': '4.5rem',
                '22': '5.5rem',
                '30': '7.5rem',
            },
            animation: {
                'fade-in': 'fadeIn 0.5s ease-out',
                'fade-in-up': 'fadeInUp 0.6s ease-out',
                'fade-in-down': 'fadeInDown 0.4s ease-out',
                'slide-up': 'slideUp 0.8s cubic-bezier(0.16, 1, 0.3, 1)',
                'scale-in': 'scaleIn 0.5s cubic-bezier(0.16, 1, 0.3, 1)',
                'spin-slow': 'spin 3s linear infinite',
                'brass-reveal': 'brassReveal 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards',
                'line-grow': 'lineGrow 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards',
                'surface-lift': 'surfaceLift 0.4s cubic-bezier(0.16, 1, 0.3, 1)',
                'mask-reveal': 'maskReveal 0.7s cubic-bezier(0.16, 1, 0.3, 1) forwards',
            },
            keyframes: {
                fadeIn: {
                    '0%': { opacity: '0' },
                    '100%': { opacity: '1' },
                },
                fadeInUp: {
                    '0%': { opacity: '0', transform: 'translateY(20px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                fadeInDown: {
                    '0%': { opacity: '0', transform: 'translateY(-10px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                slideUp: {
                    '0%': { opacity: '0', transform: 'translateY(40px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                scaleIn: {
                    '0%': { opacity: '0', transform: 'scale(0.9)' },
                    '100%': { opacity: '1', transform: 'scale(1)' },
                },
                brassReveal: {
                    '0%': { opacity: '0', transform: 'translateY(8px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                lineGrow: {
                    '0%': { transform: 'scaleX(0)', transformOrigin: 'left' },
                    '100%': { transform: 'scaleX(1)', transformOrigin: 'left' },
                },
                surfaceLift: {
                    '0%': { transform: 'translateY(0)', boxShadow: '0 0 0 rgba(0,0,0,0)' },
                    '100%': { transform: 'translateY(-2px)', boxShadow: '0 8px 25px -5px rgba(245,158,11,0.15)' },
                },
                maskReveal: {
                    '0%': { clipPath: 'inset(0 100% 0 0)' },
                    '100%': { clipPath: 'inset(0 0 0 0)' },
                },
            },
        },
    },
    plugins: [],
}
