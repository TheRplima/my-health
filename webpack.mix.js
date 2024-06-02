const mix = require('laravel-mix')
mix
    .js('resources/js/app.tsx', 'public/js')
    .react()
    .sourceMaps()
    .postCss('resources/css/app.css', 'public/css', [
        require('tailwindcss'), // add this
    ])
if (!mix.inProduction()) {
    mix.browserSync({
        proxy: 'https://minha-saude.fly.dev',
        files: [
            'resources/views/**/*.php',
            'resources/js/**/*.vue',
            'resources/sass/**/*.scss',
            'public/js/**/*.js',
            'public/css/**/*.css'
        ]
    });
}
