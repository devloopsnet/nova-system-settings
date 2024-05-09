module.exports = {
    important: '#nova-system-settings',
    content: [
        "./dist/resources/**/*.{vue,js,ts,jsx,tsx}",
        "./resources/**/*.{js,vue}",
        './node_modules/flowbite-vue/**/*.{js,jsx,ts,tsx,vue}',
        "./node_modules/flowbite/**/*.js"
    ],
    theme: {
        extend: {},
    },
    plugins: [
        require('flowbite/plugin'),
        'tailwindcss',
        'autoprefixer'
    ],
}
