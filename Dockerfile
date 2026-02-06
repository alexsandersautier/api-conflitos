FROM php:8.2-apache-bullseye

# Habilitar mod_rewrite do Apache
RUN a2enmod rewrite

# Configurar ServerName
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Instalar dependências do sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libicu-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    nano \
    unzip \
    apt-transport-https \
    lsb-release \
    dos2unix \
    cron \
    libpq-dev \
    gnupg2 \
    libzip-dev \
    libjpeg-dev \
    libfreetype6-dev \
    tzdata

# Limpar cache do apt
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalar extensões PHP necessárias para Laravel
RUN docker-php-ext-configure intl \
    && docker-php-ext-install intl \
    && docker-php-ext-install pdo pdo_mysql mysqli \
    && docker-php-ext-install xml \
    && docker-php-ext-install ctype \
    && docker-php-ext-install bcmath \
    && docker-php-ext-install zip \
    && docker-php-ext-configure gd --with-jpeg=/usr/include --with-freetype=/usr/include \
    && docker-php-ext-install gd

# Configurar timezone
ENV TZ="America/Sao_Paulo"

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copiar configuração do Apache
COPY ./codigo-fonte/000-default.conf /etc/apache2/sites-enabled/000-default.conf

# Definir diretório de trabalho
WORKDIR /var/www/html

# Copiar apenas composer.json e composer.lock primeiro (otimização de cache)
COPY codigo-fonte/composer.json ./composer.json
COPY codigo-fonte/composer.lock ./composer.lock

# Instalar dependências do Composer
RUN composer install \
    --no-dev \
    --no-interaction \
    --no-plugins \
    --no-scripts \
    --prefer-dist \
    --optimize-autoloader

# Copiar todo o código da aplicação
COPY ./codigo-fonte ./

# Copiar script de entrypoint
COPY ./codigo-fonte/entrypoint.sh ./entrypoint.sh

# Dar permissões de execução e converter formato de linha
RUN chmod +x /var/www/html/entrypoint.sh
RUN dos2unix ./entrypoint.sh

# Configurar cron para Laravel Scheduler
RUN echo "* * * * * www-data php /var/www/html/artisan schedule:run >> /var/log/cron.log 2>&1" >> /etc/crontab
RUN touch /var/log/cron.log \
    && chown www-data:www-data /var/log/cron.log

# Ajustar permissões de pastas críticas
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# Expor porta 80
EXPOSE 80

# Executar entrypoint e iniciar Apache
CMD [ "sh", "-c", "/var/www/html/entrypoint.sh; /usr/local/bin/apache2-foreground" ]
