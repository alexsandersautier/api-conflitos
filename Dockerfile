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
RUN a2enmod rewrite

# Instalar o Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Definir o diretório de trabalho
WORKDIR /var/www/html

# 1. Copiar apenas os arquivos do composer primeiro
COPY codigo-fonte/composer.json codigo-fonte/composer.lock ./

# 2. Instalar dependências sem scripts
RUN composer install --no-interaction --no-scripts --no-autoloader --no-dev

# 3. Copiar todo o restante do código
COPY codigo-fonte/ .

# 4. Gerar autoloader otimizado e rodar apenas os comandos necessários
RUN composer dump-autoload --optimize && \
    php artisan package:discover --ansi

# Configurar permissões
RUN chown -R www-data:www-data storage bootstrap/cache
RUN chmod -R 775 storage bootstrap/cache

# Expor a porta 80
EXPOSE 80