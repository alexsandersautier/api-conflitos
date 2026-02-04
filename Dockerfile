FROM php:8.2-apache-bullseye

RUN a2enmod rewrite

RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf


# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libicu-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    nano \
    unzip

#clear cache
RUN apt-get clean && rm -rf /var/lib/apt/list/*

# Install PHP extensions/modules.
RUN apt-get update -y \
	&& apt-get install -y apt-transport-https lsb-release dos2unix cron libpq-dev gnupg2 libxml2-dev libzip-dev libjpeg-dev libpng-dev libfreetype6-dev \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl \
    && apt-get install tzdata \
    && docker-php-ext-install pdo pdo_mysql mysqli  \
    && docker-php-ext-install xml \
    && docker-php-ext-install ctype \
    && docker-php-ext-install bcmath \
    && docker-php-ext-install zip \
    && docker-php-ext-configure gd --with-jpeg=/usr/include --with-freetype=/usr/include \
    && docker-php-ext-install gd \
    && apt-get -y clean \
    && rm -rf /var/lib/apt/lists/*

# Timezone configuration
ENV TZ="America/Sao_Paulo"

# Uploads.ini settings
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Apache settings
COPY ./codigo-fonte/000-default.conf /etc/apache2/sites-enabled/000-default.conf

# Install Composer.
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php -r "if (hash_file('SHA384', 'composer-setup.php') === trim(file_get_contents('https://composer.github.io/installer.sig'))) { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && php -r "unlink('composer-setup.php');"

# Change the working directory.
WORKDIR /var/www/html

# Install the backend application dependencies.
COPY codigo-fonte/database ./database
COPY codigo-fonte/composer.json ./composer.json
COPY codigo-fonte/composer.lock ./composer.lock
RUN composer update \
 --no-interaction \
 --no-plugins \
 --no-scripts \
 --prefer-dist

COPY ./codigo-fonte ./
COPY ./codigo-fonte/entrypoint.sh ./entrypoint.sh

# Alterando permissoes
RUN chmod +x /var/www/html/entrypoint.sh
RUN dos2unix ./entrypoint.sh

RUN echo "* * * * * www-data php /var/www/html/artisan schedule:run >> /var/log/cron.log 2>&1" >> /etc/crontab
RUN touch /var/log/cron.log \
    && chown www-data:www-data /var/log/cron.log

RUN chown -R www-data:www-data /var/www/html

EXPOSE 80

CMD [ "sh", "-c", "/var/www/html/entrypoint.sh; /usr/local/bin/apache2-foreground" ]
 