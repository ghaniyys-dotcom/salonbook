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
                /* ——— Surface Hierarchy ——— */
                lacquer: {
                    DEFAULT: '#050505',
                    light: '#0a0a0a',
                },
                charcoal: {
                    50: '#f0f0ee',
                    100: '#d6d5d1',
                    200: '#adaba4',
                    300: '#848176',
                    400: '#5b5749',
                    500: '#3d3a30',
                    600: '#2b2921',
                    700: '#1f1e18',
                    800: '#161513',
                    900: '#0d0d0b',
                    950: '#080807',
                },
                /* Burnished brass — muted amber for structure */
                brass: {
                    50: '#fcf8ee',
                    100: '#f6edcd',
                    200: '#edda9c',
                    300: '#e2c36a',
                    400: '#d4a83f',
                    500: '#b88b2a',
                    600: '#9a7224',
                    700: '#7d5d20',
                    800: '#68501f',
                    900: '#5a4520',
                },
                /* Soft ivory — premium highlight */
                ivory: {
                    50: '#fdfdfb',
                    100: '#faf8f2',
                    200: '#f5f0e3',
                    300: '#ede4cc',
                    400: '#ddd0b0',
                    500: '#c9b89a',
                    600: '#b0987a',
                    700: '#8f7a61',
                    800: '#756452',
                    900: '#635345',
                },
                /* Dusty rose-gold whisper — beauty accent */
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
