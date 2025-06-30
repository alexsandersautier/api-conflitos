# Use a imagem base do PHP com Apache
FROM php:8.2-apache

# 1. Primeiro resolver o problema das chaves GPG
RUN apt-get update -y --allow-releaseinfo-change && \
    apt-get install -y --no-install-recommends \
    ca-certificates \
    gnupg2 && \
    apt-key adv --keyserver hkp://keyserver.ubuntu.com:80 --recv-keys \
    0E98404D386FA1D9 \
    6ED0E7B82643E131 \
    F8D2585B8783D481 \
    54404762BBB6E853 \
    BDE6D2B9216EC7A8 && \
    rm -rf /var/lib/apt/lists/*

# 2. Atualizar repositórios com as novas chaves
RUN apt-get update -y

# 3. Instalar dependências do sistema
RUN apt-get install -y --no-install-recommends \
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
    sockets \
    && rm -rf /var/lib/apt/lists/*

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