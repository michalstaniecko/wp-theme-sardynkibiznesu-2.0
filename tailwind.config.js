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
    darkMode: 'class', // or 'media' or 'class'
    theme: {
        extend: {
            colors: {
                base: '#666',
                link: '#0097b2'
            },
        },
    },
    variants: {
    },
    plugins: [],
    safelist: [
        'has-text-align-center',
        {
            pattern: /grid-cols-(2|3|4|5|6)/,
            variants: ['sm', 'md', 'lg']
        }
    ]
}
