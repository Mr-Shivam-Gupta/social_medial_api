const mix = require('laravel-mix');

mix.js('resources/js/app.js', 'public/js')
   .css('resources/css/app.css', 'public/css')
   .options({
       processCssUrls: false, // Necessary if you're using postcss-url
       postCss: [require('tailwindcss')],
   });
