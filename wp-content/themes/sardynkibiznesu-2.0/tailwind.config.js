module.exports = {
    content: [
        './admin/**/*.php',
        './blocks/**/*.js',
        './blocks/**/*.php', //// Path to your PHP files
        './components/**/*.php', //// Path to your PHP files
        './inc/**/*.php',
        './template-blocks/**/*.php', //// Path to your PHP files
        './templates/**/*.php',
        './404.php',
        './archive.php',
        './footer.php',
        './functions.php',
        './header.php',
        './index.php',
        './page.php',
        './sidebar.php',
        './single.php',
        './src/js/*.js',
        './blocks/**/*.js'
    ],
    darkMode: false, // or 'media' or 'class'
    theme: {
        extend: {},
    },
    variants: {
        extend: {},
    },
    safelist: [
        'has-text-align-center',
    ],
    plugins: [],
}
