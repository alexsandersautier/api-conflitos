#!/bin/bash
set -e

# Corrige permissões
chown -R www-data:www-data /var/www/html/storage
chmod -R 775 /var/www/html/storage

# Testa configuração do Apache antes de iniciar
apache2ctl configtest

# Inicia o Apache em primeiro plano
exec apache2ctl -D FOREGROUND