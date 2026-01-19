module.exports = {
    content: [
        // Theme files
        './wp-content/themes/sardynkibiznesu-2.0/**/*.php',
        // Plugin files (ihumbak-* custom plugins)
        './wp-content/plugins/ihumbak-*/**/*.php',
        // JavaScript source
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
        // WordPress text alignment
        'has-text-align-center',
        'has-text-align-left',
        'has-text-align-right',

        // Grid columns pattern
        {
            pattern: /grid-cols-(1|2|3|4|5|6)/,
            variants: ['sm', 'md', 'lg', 'xl']
        },

        // Column span pattern
        {
            pattern: /col-span-(1|2|3|4|5|6)/,
            variants: ['sm', 'md', 'lg', 'xl']
        },

        // Arbitrary text values (used in price-compare)
        {
            pattern: /text-\[.+\]/,
        },

        // Arbitrary background values
        {
            pattern: /bg-\[.+\]/,
        },

        // Arbitrary height values
        {
            pattern: /h-\[.+\]/,
        },

        // Arbitrary width values
        {
            pattern: /w-\[.+\]/,
        },

        // Arbitrary top values
        {
            pattern: /top-\[.+\]/,
        },

        // Arbitrary border-radius values
        {
            pattern: /rounded-\[.+\]/,
        },

        // Arbitrary negative margin (used in price-compare)
        {
            pattern: /-?m[xy]?-\[.+\]/,
            variants: ['md', 'lg']
        },

        // Gap utilities
        {
            pattern: /gap-(0|0\.5|1|2|3|4|5|6|8|10|12)/,
            variants: ['sm', 'md', 'lg']
        },

        // Flex utilities
        'flex',
        'flex-col',
        'flex-row',
        'flex-wrap',
        'flex-1',
        'flex-grow',
        'flex-shrink-0',
        'items-center',
        'items-start',
        'items-end',
        'justify-center',
        'justify-between',
        'justify-start',
        'justify-end',

        // Display utilities
        'hidden',
        'block',
        'inline-block',
        'inline',
        'grid',

        // Responsive display
        {
            pattern: /(hidden|block|flex|grid|inline-block)/,
            variants: ['sm', 'md', 'lg', 'xl']
        },

        // Text utilities
        {
            pattern: /text-(sm|base|lg|xl|2xl|3xl|4xl|5xl|6xl)/,
            variants: ['sm', 'md', 'lg']
        },

        // Font weight
        'font-normal',
        'font-medium',
        'font-semibold',
        'font-bold',
        'font-mono',

        // Colors used in templates
        'text-white',
        'text-gray-500',
        'text-gray-900',
        'text-red-800',
        'text-link',
        'bg-blue-500',
        'border-gray-200',

        // Positioning
        'relative',
        'absolute',
        'sticky',
        'fixed',

        // Object fit
        'object-contain',
        'object-cover',

        // Min width
        'min-w-0',

        // Transitions (used in price-compare)
        {
            pattern: /transition-\[.+\]/,
        },
        'duration-300',

        // Rotation (used in collapse buttons)
        {
            pattern: /rotate-(0|45|90|180)/,
        },

        // Border utilities
        'border',
        'border-b',
        'border-t',
        'border-l',
        'border-r',
        'rounded',

        // Width/Height full
        'w-full',
        'h-full',

        // Auto columns (used in grid)
        'auto-cols-max',
        'auto-cols-min',
        'auto-cols-fr',
        'grid-flow-col',
        'grid-flow-row',
    ]
}
