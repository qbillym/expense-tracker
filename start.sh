#!/bin/bash
php-fpm -D
php artisan config:clear
php artisan config:cache
php artisan route:cache
php artisan migrate --force
php artisan db:seed --force
nginx -g "daemon off;"