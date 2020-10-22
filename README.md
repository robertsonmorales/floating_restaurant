<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>


## Sunrise Floating Restaurant Web App

### Getting Started, What command to run / install
- mkdir -p storage/framework/{sessions,views,cache}
- mkdir -p storage/framework/cache/data
- mkdir -p bootstrap/cache
- composer install
- npm install
- npm run watch
- php artisan cache:clear
- php artisan migrate:fresh --seed
- php artisan serve --host=hostname --port=portID
- php artisan vendor:publish --tag=laravel-pagination
