## Sunrise Floating Restaurant Web App

### Getting Started

#### What command to run / install
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
- php artisan storage:link


### Running into Errors
- if you found error in npm install
- try removing node_modules and package-lock.json file
- then run npm install
