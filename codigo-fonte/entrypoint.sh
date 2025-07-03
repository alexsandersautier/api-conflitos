#!/bin/sh

set -e
echo "Definindo permissões entrypoint"
chmod +x /var/www/html/entrypoint.sh
echo "Permissões aplicadas"
cp /var/www/html/000-default.conf /etc/apache2/sites-enabled/000-default.conf

composer install --ignore-platform-reqs --optimize-autoloader --no-dev

chown -R www-data:www-data /var/www/html/vendor

echo "Aplicando permissões nos diretórios"
chown -R www-data:www-data /var/www/html/
chown -R www-data:www-data /var/www/html/storage/logs
chmod -R 755 /var/www/html/storage/logs

php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Permissões aplicadas com sucesso."