const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/js/app.js', 'public/js')
    .sass('resources/sass/app.scss', 'public/css');

mix.scripts([
	'public/vendors/apexcharts/apexcharts.js',
	'public/js/dropdown.js',
	'public/vendors/sweetalert2/dist/sweetalert2.min.js'
], 'public/js/vendors.js');

mix.styles([
	'public/vendors/select2/select2.min.css',
	'public/vendors/jquery/jquery-ui.css',
	'public/vendors/sweetalert2/dist/sweetalert2.min.css',
	'public/vendors/agGrid/agGrid.css',
	'public/vendors/agGrid/ag-theme_material.css'
], 'public/css/vendors.css');
