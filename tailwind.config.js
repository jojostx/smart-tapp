const defaultTheme = require('tailwindcss/defaultTheme');
const colors = require('tailwindcss/colors');

module.exports = {
    darkMode: 'class',
    content: [
        './app/**/*.php',
        './resources/**/*.html',
        './resources/**/*.js',
        './resources/**/*.php',
        './vendor/filament/**/*.blade.php',
    ],
    theme: {
        extend: {
            backgroundImage: {
                'circles': "url('/images/circles.svg')",
            },
            fontFamily: {
                sans: ['Inter var', ...defaultTheme.fontFamily.sans],
                jet: ['JetBrains Mono', 'monospace', ...defaultTheme.fontFamily.sans],
            },
            keyframes: {
                'swing': {
                    '0%,100%' : { transform: 'rotate(15deg)' },
                    '50%' : { transform: 'rotate(-15deg)' },
                }
            },
            animation: {
                'swing': 'swing 1s infinite',
                'spin-slow': 'spin 3s linear infinite',
                'ping-slow': 'ping 3s linear infinite',
                'pulse-slow': 'pulse 3s linear infinite',
            },
            colors: {
                danger: colors.rose,
                primary: colors.indigo,
                success: colors.green,
                warning: colors.yellow,
            },
        },
    },
    variants: {
        extend: {
            backgroundColor: ['active'],
        }
    },
    plugins: [
        require('@tailwindcss/forms'),
        require('@tailwindcss/typography'),
        require('@tailwindcss/aspect-ratio'),
    ],
};