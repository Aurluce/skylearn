/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ['./view/**/*.php', './public/assets/js/**/*.js'],
  theme: {
    extend: {
      colors: {
        brand: {
          DEFAULT: 'var(--brand, #1d4ed8)',
          dark:    'var(--brand-dark, #1e3a8a)',
          light:   'var(--brand-light, #dbeafe)',
        },
      },
      keyframes: {
        'fade-up': {
          '0%':   { opacity: '0', transform: 'translateY(16px)' },
          '100%': { opacity: '1', transform: 'translateY(0)' },
        },
        'float': {
          '0%, 100%': { transform: 'translate(0, 0)' },
          '50%':      { transform: 'translate(20px, -20px)' },
        },
        'float-slow': {
          '0%, 100%': { transform: 'translate(0, 0)' },
          '50%':      { transform: 'translate(-15px, 15px)' },
        },
      },
      animation: {
        'fade-up':       'fade-up 0.7s ease-out both',
        'fade-up-delay': 'fade-up 0.9s ease-out 0.2s both',
        'float':         'float 8s ease-in-out infinite',
        'float-slow':    'float-slow 12s ease-in-out infinite',
      },
    },
  },
  plugins: [],
};