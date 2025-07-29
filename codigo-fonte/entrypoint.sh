#!/bin/sh

set -e
#echo "Definindo permissões entrypoint"
#chmod +x /var/www/html/entrypoint.sh
#echo "Permissões aplicadas"

composer install --ignore-platform-reqs --optimize-autoloader --no-dev

#chown -R www-data:www-data /var/www/html/vendor

echo "Aplicando permissões nos diretórios"
chown -R www-data:www-data /var/www/html/
chown -R www-data:www-data /var/www/html/storage/logs
chmod -R 755 /var/www/html/storage/logs
chmod -R 755 /var/log/apache2/
echo "Permissões aplicadas com sucesso."