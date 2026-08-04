import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],

    theme: {
        extend: {
            // ─── Typography ──────────────────────────────────────────────
            fontFamily: {
                sans: ['Instrument Sans', 'Inter', ...defaultTheme.fontFamily.sans],
                display: ['Instrument Sans', 'Inter', ...defaultTheme.fontFamily.sans],
                mono: ['JetBrains Mono', ...defaultTheme.fontFamily.mono],
            },
            fontSize: {
                'xs':   ['0.75rem',  { lineHeight: '1rem' }],
                'sm':   ['0.875rem', { lineHeight: '1.25rem' }],
                'base': ['1rem',     { lineHeight: '1.5rem' }],
                'lg':   ['1.125rem', { lineHeight: '1.75rem' }],
                'xl':   ['1.25rem',  { lineHeight: '1.75rem' }],
                '2xl':  ['1.5rem',   { lineHeight: '2rem' }],
                '3xl':  ['1.875rem', { lineHeight: '2.25rem' }],
                '4xl':  ['2.25rem',  { lineHeight: '2.5rem' }],
                '5xl':  ['3rem',     { lineHeight: '1.15' }],
            },

            // ─── Brand Colors ─────────────────────────────────────────────
            colors: {
                // Primary Blue — trust, authority (DEFAULT enables bg-primary shorthand)
                primary: {
                    50:  '#EBF4FB',
                    100: '#D1E7F5',
                    200: '#A8D0EC',
                    300: '#7FB9E3',
                    400: '#528CBE',
                    500: '#3D78AB',
                    600: '#2D6499',
                    700: '#1F5080',
                    800: '#143C66',
                    900: '#0A284D',
                    DEFAULT: '#528CBE',
                },
                // Accent = Gold alias (enables bg-accent, text-accent used by sidebar)
                accent: {
                    DEFAULT: '#F0B43C',
                    50:  '#FEF9EC',
                    100: '#FDF0CA',
                    200: '#FBE196',
                    300: '#F8CC5E',
                    400: '#F0B43C',
                    500: '#E09A1F',
                    600: '#C07E15',
                    700: '#9A620F',
                },
                // AGO Navy — Attorney General module's own distinct brand color
                // (enables bg-ago, text-ago, border-ago). Same value as primary-900,
                // registered separately so Attorney General pages read as intentionally
                // branded rather than reusing the app-wide primary shade by coincidence.
                ago: {
                    DEFAULT: '#0A284D',
                    50:  '#EAEEF3',
                    100: '#C7D1DE',
                    200: '#9FB0C5',
                    300: '#748FAB',
                    400: '#4C6E90',
                    500: '#2E5175',
                    600: '#193B5C',
                    700: '#0A284D',
                    800: '#071B38',
                    900: '#040F20',
                },

                // Gold — excellence, prestige
                gold: {
                    50:  '#FEF9EC',
                    100: '#FDF0CA',
                    200: '#FBE196',
                    300: '#F8CC5E',
                    400: '#F0B43C',   // brand base
                    500: '#E09A1F',
                    600: '#C07E15',
                    700: '#9A620F',
                    800: '#764A09',
                    900: '#533406',
                    DEFAULT: '#F0B43C',
                },
                // Neutral Gray
                neutral: {
                    50:  '#F9FAFB',
                    100: '#F3F4F6',
                    200: '#D9D9D9',   // brand gray
                    300: '#C4C4C4',
                    400: '#9CA3AF',
                    500: '#6B7280',
                    600: '#4B5563',
                    700: '#374151',
                    800: '#1F2937',
                    900: '#111827',
                },
                // Semantic colors
                success: {
                    50:  '#F0FDF4',
                    100: '#DCFCE7',
                    200: '#BBF7D0',
                    300: '#86EFAC',
                    400: '#4ADE80',
                    500: '#22C55E',
                    600: '#16A34A',
                    700: '#15803D',
                },
                warning: {
                    50:  '#FFFBEB',
                    100: '#FEF3C7',
                    200: '#FDE68A',
                    300: '#FCD34D',
                    400: '#FBBF24',
                    500: '#F59E0B',
                    600: '#D97706',
                    700: '#B45309',
                },
                danger: {
                    50:  '#FFF1F2',
                    100: '#FFE4E6',
                    200: '#FECDD3',
                    300: '#FDA4AF',
                    400: '#FB7185',
                    500: '#EF4444',
                    600: '#DC2626',
                    700: '#B91C1C',
                },
                info: {
                    50:  '#EFF6FF',
                    100: '#DBEAFE',
                    200: '#BFDBFE',
                    300: '#93C5FD',
                    400: '#60A5FA',
                    500: '#3B82F6',
                    600: '#2563EB',
                    700: '#1D4ED8',
                },
            },

            // ─── Spacing (8px grid) ───────────────────────────────────────
            spacing: {
                '0.5': '0.125rem',   // 2px
                '1':   '0.25rem',    // 4px
                '1.5': '0.375rem',   // 6px
                '2':   '0.5rem',     // 8px  ← base unit
                '3':   '0.75rem',    // 12px
                '4':   '1rem',       // 16px
                '5':   '1.25rem',    // 20px
                '6':   '1.5rem',     // 24px
                '7':   '1.75rem',    // 28px
                '8':   '2rem',       // 32px
                '10':  '2.5rem',     // 40px
                '12':  '3rem',       // 48px
                '14':  '3.5rem',     // 56px
                '16':  '4rem',       // 64px
                '20':  '5rem',       // 80px
                '24':  '6rem',       // 96px
                '32':  '8rem',       // 128px
                '40':  '10rem',      // 160px
                '48':  '12rem',      // 192px
                '64':  '16rem',      // 256px
                '80':  '20rem',      // 320px
                '96':  '24rem',      // 384px
            },

            // ─── Border Radius ────────────────────────────────────────────
            borderRadius: {
                'none': '0',
                'sm':   '0.25rem',   // 4px
                DEFAULT:'0.375rem',  // 6px
                'md':   '0.5rem',    // 8px
                'lg':   '0.75rem',   // 12px
                'xl':   '1rem',      // 16px
                '2xl':  '1.25rem',   // 20px
                '3xl':  '1.5rem',    // 24px
                'full': '9999px',
            },

            // ─── Shadows ──────────────────────────────────────────────────
            boxShadow: {
                'xs':  '0 1px 2px 0 rgb(0 0 0 / 0.05)',
                'sm':  '0 1px 3px 0 rgb(0 0 0 / 0.10), 0 1px 2px -1px rgb(0 0 0 / 0.10)',
                DEFAULT:'0 4px 6px -1px rgb(0 0 0 / 0.10), 0 2px 4px -2px rgb(0 0 0 / 0.10)',
                'md':  '0 4px 6px -1px rgb(0 0 0 / 0.10), 0 2px 4px -2px rgb(0 0 0 / 0.10)',
                'lg':  '0 10px 15px -3px rgb(0 0 0 / 0.10), 0 4px 6px -4px rgb(0 0 0 / 0.10)',
                'xl':  '0 20px 25px -5px rgb(0 0 0 / 0.10), 0 8px 10px -6px rgb(0 0 0 / 0.10)',
                '2xl': '0 25px 50px -12px rgb(0 0 0 / 0.25)',
                'card':'0 4px 20px 0 rgb(82 140 190 / 0.12)',
                'none':'none',
            },

            // ─── Container ────────────────────────────────────────────────
            maxWidth: {
                'xs':  '20rem',
                'sm':  '24rem',
                'md':  '28rem',
                'lg':  '32rem',
                'xl':  '36rem',
                '2xl': '42rem',
                '3xl': '48rem',
                '4xl': '56rem',
                '5xl': '64rem',
                '6xl': '72rem',
                '7xl': '80rem',
                'content': '1280px',
            },

            // ─── Transitions ──────────────────────────────────────────────
            transitionDuration: {
                DEFAULT: '200ms',
                '75':  '75ms',
                '100': '100ms',
                '150': '150ms',
                '200': '200ms',
                '300': '300ms',
                '500': '500ms',
            },
        },
    },

    plugins: [forms],
};
