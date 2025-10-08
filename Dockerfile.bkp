# Use a imagem base do PHP com Apache
FROM php:8.2-apache

# Instalar dependências do sistema
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
	vim \
    && docker-php-ext-install \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip \
    sockets

# Habilitar mod_rewrite do Apache
RUN a2enmod rewrite && \
    service apache2 restart

# Instalar o Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Definir o diretório de trabalho
WORKDIR /var/www/html

# Copiar primeiro apenas os arquivos essenciais para instalar dependências
COPY codigo-fonte/composer.json codigo-fonte/composer.lock ./

# Instalar apenas as dependências (sem scripts)
RUN composer install --no-interaction --no-scripts --no-autoloader --no-dev

# Copiar todo o código fonte
COPY codigo-fonte/ .
COPY codigo-fonte/000-default.conf /etc/apache2/sites-enabled/000-default.conf

# Rodar o autoloader e scripts (agora com artisan disponível)
# RUN composer dump-autoload --optimize && \
#    composer run-script post-install-cmd

# Configurar permissões e ownership
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html/storage && \
    chmod -R 755 /var/www/html/bootstrap/cache
	
RUN php artisan config:cache && \
	php artisan route:cache && \
	php artisan view:cache
	
# 1. Copie o entrypoint para dentro da imagem
# COPY codigo-fonte/entrypoint.sh /usr/local/bin/
# RUN chmod +x /usr/local/bin/entrypoint.sh

# 2. Simplifique o CMD para executar apenas o entrypoint
# ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["apache2-foreground"]

# Expor a porta 80
EXPOSE 80
