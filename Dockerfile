# Dockerfile

# Estágio de construção para instalar dependências do Composer
FROM php:8.2-apache AS builder

# Adiciona as chaves GPG ausentes para os repositórios Debian
# Este passo deve vir ANTES de qualquer 'apt-get update' para garantir que os repositórios sejam confiáveis.
# A chave para Bookworm (Debian 12) é geralmente 0E98404D386FA1D9.
# Incluí as chaves comuns mencionadas no seu log de erro para maior robustez.
RUN apt-get update && apt-get install -y gnupg \
    && apt-key adv --keyserver keyserver.ubuntu.com --recv-keys 0E98404D386FA1D9 \
    && apt-key adv --keyserver keyserver.ubuntu.com --recv-keys 6ED0E7B82643E131 \
    && apt-key adv --keyserver keyserver.ubuntu.com --recv-keys F8D2585B8783D481 \
    && apt-key adv --keyserver keyserver.ubuntu.com --recv-keys 54404762BBB6E853 \
    && apt-key adv --keyserver keyserver.ubuntu.com --recv-keys BDE6D2B9216EC7A8 \
    && rm -rf /var/lib/apt/lists/* # Limpeza imediata de apt lists para não inchar a camada
# Note: apt-key is deprecated, but still commonly used in many Docker images.
# For a more modern approach, one would use:
# curl -fsSL https://keyserver.ubuntu.com/pks/lookup?op=get&search=0xKEYID | gpg --dearmor -o /etc/apt/trusted.gpg.d/debian-bookworm.gpg


# Instalação de dependências do sistema
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    # Pacotes corretos para JPEG/WebP em Debian:
    libjpeg-dev \
    libwebp-dev \
    libicu-dev \
    libonig-dev \
    libsqlite3-dev \
    libpq-dev \
    mariadb-client \
    apache2-utils \
    && docker-php-ext-install -j$(nproc) pdo_mysql pdo_pgsql zip gd intl opcache bcmath exif pcntl \
    && docker-php-ext-enable opcache \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalação do Composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Define o diretório de trabalho dentro do estágio builder
WORKDIR /app

# COPIA TODO O CÓDIGO-FONTE DO LARAVEL PARA O ESTÁGIO DE BUILD
COPY codigo-fonte/ . /app/

# Instala as dependências do Composer
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# Estágio final para a imagem de produção
FROM php:8.2-apache

# Adiciona as chaves GPG ausentes para os repositórios Debian (também no estágio final)
RUN apt-get update && apt-get install -y gnupg \
    && apt-key adv --keyserver keyserver.ubuntu.com --recv-keys 0E98404D386FA1D9 \
    && apt-key adv --keyserver keyserver.ubuntu.com --recv-keys 6ED0E7B82643E131 \
    && apt-key adv --keyserver keyserver.ubuntu.com --recv-keys F8D2585B8783D481 \
    && apt-key adv --keyserver keyserver.ubuntu.com --recv-keys 54404762BBB6E853 \
    && apt-key adv --keyserver keyserver.ubuntu.com --recv-keys BDE6D2B9216EC7A8 \
    && rm -rf /var/lib/apt/lists/*

# Instalação de dependências de runtime
RUN apt-get update && apt-get install -y \
    git \
    libzip-dev \
    libpng-dev \
    # Pacotes corretos para JPEG/WebP em Debian:
    libjpeg-dev \
    libwebp-dev \
    libicu-dev \
    libonig-dev \
    libsqlite3-dev \
    libpq-dev \
    mariadb-client \
    apache2-utils \
    && docker-php-ext-install -j$(nproc) pdo_mysql pdo_pgsql zip gd intl opcache bcmath exif pcntl \
    && docker-php-ext-enable opcache \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Habilita o módulo rewrite do Apache (necessário para URLs amigáveis do Laravel)
RUN a2enmod rewrite

# Define o diretório de trabalho para a aplicação no contêiner final
WORKDIR /var/www/html

# Copia os arquivos da aplicação (incluindo as dependências Composer já instaladas)
COPY --from=builder /app /var/www/html

# Ajusta permissões de pasta
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Copia o arquivo de configuração do virtual host do Apache
# COPY docker/apache2/000-default.conf /etc/apache2/sites-available/000-default.conf

# Remove a configuração padrão do Apache (000-default.conf) para evitar conflitos
RUN rm /etc/apache2/sites-enabled/000-default.conf

# Habilita a nova configuração do site
RUN a2ensite 000-default.conf

# Expõe a porta 80 do contêiner
EXPOSE 80

# Comando padrão para iniciar o Apache
CMD ["apache2-foreground"]