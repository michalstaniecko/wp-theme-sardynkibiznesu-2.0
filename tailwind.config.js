module.exports = {
    content: [
        './wp-content/themes/sardynkibiznesu-2.0/admin/**/*.{php,html,js,ts}',
        './wp-content/themes/sardynkibiznesu-2.0/blocks/**/*.{php,html,js,ts}',
        './wp-content/themes/sardynkibiznesu-2.0/cmsmasters-shortcodes/**/*.{php,html,js,ts}',
        './wp-content/themes/sardynkibiznesu-2.0/components/**/*.{php,html,js,ts}',
        './wp-content/themes/sardynkibiznesu-2.0/inc/**/*.{php,html,js,ts}',
        './wp-content/themes/sardynkibiznesu-2.0/template-blocks/**/*.{php,html,js,ts}',
        './wp-content/themes/sardynkibiznesu-2.0/templates/**/*.{php,html,js,ts}',
        './wp-content/themes/sardynkibiznesu-2.0/*.php',
        './src/js/**/*.js',
    ],
    darkMode: 'class',
    theme: {
        extend: {
            colors: {
                base: '#666',
                link: '#0097b2',
                // Modernized primary color palette
                primary: {
                    DEFAULT: '#dc2626',
                    light: '#ef4444',
                    dark: '#b91c1c',
                    50: '#fef2f2',
                    100: '#fee2e2',
                },
            },
            borderRadius: {
                'sb-sm': '4px',
                'sb-md': '8px',
                'sb-lg': '12px',
                'sb-xl': '16px',
            },
            boxShadow: {
                'sb-sm': '0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1)',
                'sb-md': '0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1)',
                'sb-lg': '0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1)',
            },
        },
    },
    variants: {},
    plugins: [],
    safelist: [
        'has-text-align-center',
        {
            pattern: /grid-cols-(2|3|4|5|6)/,
            variants: ['sm', 'md', 'lg']
        }
    ]
}
