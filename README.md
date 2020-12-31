Project for AgileEngine

Dependencies:
-
* PHP 7.4
* Composer
* Docker-Compose

Deploy:
-
* docker-compose up -d
* composer install
* php artisan migrate
* php artisan passport:install
* php artisan db:seed
* php artisan serve
