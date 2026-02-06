#!/bin/sh

set -e

echo "🚀 Iniciando aplicação Laravel..."

# Aplicar permissões necessárias
echo "📁 Aplicando permissões nos diretórios..."
chown -R www-data:www-data /var/www/html/
chmod -R 775 /var/www/html/storage
chmod -R 775 /var/www/html/bootstrap/cache
chmod -R 755 /var/log/apache2/
echo "✅ Permissões aplicadas com sucesso."

# Executar migrations em produção (se necessário)
if [ "$APP_ENV" = "production" ] || [ "$APP_ENV" = "staging" ]; then
    echo "🗄️  Executando migrations..."
    php artisan migrate --force --no-interaction || echo "⚠️  Migrations falharam ou não foram necessárias"
fi

# Otimizar configurações Laravel (melhora performance)
echo "⚡ Otimizando cache de configurações..."
php artisan config:cache || echo "⚠️  Config cache falhou"
php artisan route:cache || echo "⚠️  Route cache falhou"
php artisan view:cache || echo "⚠️  View cache falhou"

# Limpar caches antigos (útil para deploys)
echo "🧹 Limpando caches antigos..."
php artisan cache:clear || echo "⚠️  Cache clear falhou"

echo "✅ Aplicação pronta e otimizada!"
echo "🌐 Apache iniciando..."
