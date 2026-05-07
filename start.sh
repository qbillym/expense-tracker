#!/bin/bash
php-fpm -D
php artisan config:cache
php artisan route:cache
php artisan migrate --force
nginx -g "daemon off;"