module.exports = {
    content: [
        './wp-content/themes/sardynkibiznesu-2.0/**/*.{php,html,js,ts}',
        '!./wp-content/themes/sardynkibiznesu-2.0/assets/**/*',
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
