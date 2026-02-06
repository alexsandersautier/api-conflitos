#!/bin/sh

set -e


chown -R www-data:www-data /var/www/html/
chmod -R 775 /var/www/html/storage
chmod -R 775 /var/www/html/bootstrap/cache
chmod -R 755 /var/log/apache2/

if [ "$APP_ENV" = "production" ] || [ "$APP_ENV" = "staging" ]; then
    php artisan migrate --force --no-interaction
fi

php artisan config:cache
php artisan route:cache
php artisan view:cache

php artisan cache:clear
