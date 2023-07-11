#!/bin/bash

echo "## Turn on maintenance mode"
php artisan down || true

echo "## Install/update composer dependecies"
composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

echo "## Changing permission"
sudo chown -R :www-data .
sudo chmod -R 775 bootstrap\/cache

echo "## Restarting queue workers via supervisor"
sudo supervisorctl restart all

echo "## Restart FPM"
sudo -S service php8.1-fpm restart

echo "## Run database migrations && Run tenant migrations"
php artisan migrate --no-interaction --force
php artisan tenants:migrate

echo "## Run optimization commands for laravel"
php artisan route:cache
php artisan view:cache
php artisan config:cache
php artisan event:cache
php artisan livewire:discover

echo "## restart queue" 
php artisan queue:restart

echo "## Turn off maintenance mode"
php artisan up