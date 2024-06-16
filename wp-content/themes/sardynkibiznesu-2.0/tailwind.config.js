module.exports = {
    content: [
        './admin/**/*.php',
        './blocks/**/*.js',
        './blocks/**/*.php',
        './components/**/*.php',
        './inc/**/*.php',
        './template-blocks/**/*.php',
        './templates//**/*.php',
        './404.php',
        './archive.php',
        './footer.php',
        './functions.php',
        './header.php',
        './index.php',
        './page.php',
        './search.php',
        './single.php',
        './sidebar.php',
        './src/js/*.js',
        './blocks/**/*.js'
    ],
    darkMode: 'class', // or 'media' or 'class'
    theme: {
        extend: {
            colors: {
                base: '#666',
                link: '#008080'
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
