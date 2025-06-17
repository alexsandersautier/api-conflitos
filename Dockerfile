FROM php:8.2-apache

ARG user=anderson
ARG uid=1000

WORKDIR /var/www/html/

# Apache settings
COPY ./docker/000-default.conf /etc/apache2/sites-enabled/000-default.conf
# Alterando permissoes
COPY ./docker/entrypoint.sh /var/www/html/entrypoint.sh

# Alterando permissões
RUN chmod +x /var/www/html/entrypoint.sh


# Install required dependencies
RUN apt-get update -y && \
    apt-get install -y nano git curl libpng-dev libonig-dev libxml2-dev libzip-dev libjpeg-dev libfreetype6-dev libpq-dev zip unzip && \
    apt-get clean && rm -rf /var/lib/apt/lists/*
# Install PHP extensions/modules.
RUN apt-get update -y \
    && apt-get install -y cron \
    && apt-get install -y libpq-dev \
    && apt-get install -y gnupg2 \
    && apt-get install -y postgresql-client \
    && apt-get install -y vim \
    && apt-get install tzdata \
    && apt-get install -y --no-install-recommends openssl \
    && sed -i -E 's/(CipherString\s*=\s*DEFAULT@SECLEVEL=)2/\11/' /etc/ssl/openssl.cnf \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo pdo_pgsql \
    && apt-get install -y libxml2-dev \
    && docker-php-ext-install xml \
    && docker-php-ext-install soap \
    && docker-php-ext-install ctype \
    && docker-php-ext-install bcmath \
    && apt-get install -y libzip-dev \
    && docker-php-ext-install zip \
    && docker-php-ext-install opcache \
    && apt-get install -y libjpeg-dev libpng-dev libfreetype6-dev \
    && docker-php-ext-configure gd --with-jpeg=/usr/include --with-freetype=/usr/include \
    && docker-php-ext-install gd \
    && apt-get -y clean \
    && rm -rf /var/lib/apt/lists/*

# Install Composer.
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php -r "if (hash_file('SHA384', 'composer-setup.php') === trim(file_get_contents('https://composer.github.io/installer.sig'))) { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && php -r "unlink('composer-setup.php');"

# Install Node.js, npm.
RUN apt-get update -y \
    && apt-get install -y \
        apt-transport-https \
        lsb-release \
        dos2unix \
    && curl -sL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && apt-get -y clean \
    && rm -rf /var/lib/apt/lists/*

#COPY ./docker/entrypoint.sh /var/www/html/entrypoint.sh

RUN useradd -G www-data,root -u $uid -d /home/$user $user && \
    mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user

USER $user

EXPOSE 80

CMD [ "sh", "-c", "/var/www/html/entrypoint.sh; /usr/local/bin/apache2-foreground" ]