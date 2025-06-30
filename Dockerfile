# Use a imagem base do PHP com Apache
FROM php:8.2-apache

# Atualizar repositórios sem verificação rigorosa (APENAS PARA AMBIENTES CONTROLADOS)
RUN echo "Acquire::Check-Valid-Until \"false\";\nAcquire::Check-Date \"false\";" > /etc/apt/apt.conf.d/99no-check-valid && \
    apt-get update -y --allow-insecure-repositories && \
    apt-get install -y \
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

# Copiar arquivos do composer primeiro
COPY codigo-fonte/composer.json codigo-fonte/composer.lock ./

# Instalar dependências sem scripts
RUN composer install --no-interaction --no-scripts --no-autoloader --no-dev

# Copiar todo o restante do código
COPY codigo-fonte/ .

# Gerar autoloader otimizado
RUN composer dump-autoload --optimize && \
    ([ -f artisan ] && php artisan package:discover --ansi || true)

# Configurar permissões
RUN chown -R www-data:www-data storage bootstrap/cache
RUN chmod -R 775 storage bootstrap/cache

# Expor a porta 80
EXPOSE 80