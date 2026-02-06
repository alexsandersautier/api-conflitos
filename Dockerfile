FROM php:8.2-apache-bullseye

RUN a2enmod rewrite

RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

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

RUN apt-get clean && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-configure intl \
    && docker-php-ext-install intl \
    && docker-php-ext-install pdo pdo_mysql mysqli \
    && docker-php-ext-install xml \
    && docker-php-ext-install ctype \
    && docker-php-ext-install bcmath \
    && docker-php-ext-install zip \
    && docker-php-ext-configure gd --with-jpeg=/usr/include --with-freetype=/usr/include \
    && docker-php-ext-install gd

ENV TZ="America/Sao_Paulo"

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY ./codigo-fonte/000-default.conf /etc/apache2/sites-enabled/000-default.conf

WORKDIR /var/www/html

COPY codigo-fonte/composer.json ./composer.json
COPY codigo-fonte/composer.lock ./composer.lock

RUN composer install \
    --no-dev \
    --no-interaction \
    --no-plugins \
    --no-scripts \
    --prefer-dist \
    --optimize-autoloader

COPY ./codigo-fonte ./

COPY ./codigo-fonte/entrypoint.sh ./entrypoint.sh

RUN chmod +x /var/www/html/entrypoint.sh
RUN dos2unix ./entrypoint.sh

RUN echo "* * * * * www-data php /var/www/html/artisan schedule:run >> /var/log/cron.log 2>&1" >> /etc/crontab
RUN touch /var/log/cron.log \
    && chown www-data:www-data /var/log/cron.log

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

EXPOSE 80

CMD [ "sh", "-c", "/var/www/html/entrypoint.sh; /usr/local/bin/apache2-foreground" ]
