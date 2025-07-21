FROM php:8.2-apache

RUN sed -i -e 's/deb.debian.org/archive.debian.org/g' \
           -e 's|security.debian.org|archive.debian.org/debian-security/|g' \
           -e '/stretch-updates/d' /etc/apt/sources.list

ARG user=anderson
ARG uid=1000

WORKDIR /var/www/html/

# Apache conf
COPY ./docker/000-default.conf /etc/apache2/sites-enabled/000-default.conf
COPY ./docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# Dependências do sistema e extensões PHP
RUN apt-get update && \
    apt-get install -y --no-install-recommends \
        nano git curl unzip zip \
        cron gnupg2 tzdata openssl \
        libpq-dev libzip-dev libonig-dev libxml2-dev \
        libjpeg-dev libpng-dev libfreetype6-dev \
        apt-transport-https lsb-release dos2unix && \
    docker-php-ext-install \
        pdo pdo_mysql pdo_pgsql \
        xml soap ctype bcmath zip opcache && \
    docker-php-ext-configure gd --with-jpeg --with-freetype && \
    docker-php-ext-install gd && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*

RUN a2enmod rewrite
RUN a2enmod headers

# Copiar os arquivos do projeto
COPY ./codigo-fonte /var/www/html

# Instalar o Composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
    php -r "if (hash_file('SHA384', 'composer-setup.php') === trim(file_get_contents('https://composer.github.io/installer.sig'))) { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); exit(1); } echo PHP_EOL;" && \
    php composer-setup.php --install-dir=/usr/local/bin --filename=composer && \
    php -r "unlink('composer-setup.php');"

# Instalar dependências do Composer como root
RUN composer config --no-interaction allow-plugins.kylekatarnls/update-helper true

#RUN composer update --no-interaction --prefer-dist --optimize-autoloader

# NodeJS
RUN curl -sL https://deb.nodesource.com/setup_20.x | bash - && \
    apt-get install -y nodejs && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*

# Cria usuário app
RUN useradd -G www-data,root -u $uid -d /home/$user $user && \
    mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user

#USER $user

EXPOSE 80 443
CMD ["sh", "-c", "/entrypoint.sh; apache2-foreground"]
 