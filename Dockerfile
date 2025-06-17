FROM php:8.2-apache
WORKDIR /var/www/html
COPY ./codigo-fonte /var/www/html
 
# 1. Configurações críticas do Apache
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf && \
    a2enmod rewrite && \
    a2dismod -f mpm_event mpm_worker && \
    a2enmod mpm_prefork
 
# 2. Corrige problema comum de permissão
RUN mkdir -p /var/lock/apache2 /var/run/apache2 && \
    chown -R www-data:www-data /var/lock/apache2 /var/run/apache2
 
# 3. Instala dependências essenciais
RUN apt-get update && apt-get install -y \
    libpng-dev libjpeg-dev libonig-dev libxml2-dev libzip-dev \
&& docker-php-ext-install pdo pdo_mysql mbstring gd zip
 
# 4. Configuração segura do PHP
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" && \
    sed -i 's/;cgi.fix_pathinfo=1/cgi.fix_pathinfo=0/' "$PHP_INI_DIR/php.ini"
 
# 5. Cria usuário seguro
RUN useradd -G www-data -u 1000 -d /home/laravel laravel && \
    mkdir -p /home/laravel && \
    chown -R laravel:www-data /home/laravel
 
WORKDIR /var/www/html
USER laravel
 
# 6. Comando de inicialização robusto
CMD ["apache2ctl", "-D", "FOREGROUND"]