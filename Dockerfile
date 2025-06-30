# Dockerfile

# Estágio de construção para instalar dependências do Composer
FROM php:8.2-apache AS builder

# Instalação de utilitários e chaves GPG (método moderno)
RUN apt-get update && apt-get install -y \
    gnupg \
    curl \
    # Adicione aqui qualquer outra ferramenta que precise para baixar chaves, se necessário, como 'dirmngr'
    # dirmngr \
    && apt-get clean && rm -rf /var/lib/apt/lists/* \
    # --- Início da parte crucial: Adição das chaves GPG ---
    # As chaves listadas no erro (0E98404D386FA1D9, 6ED0E7B82643E131, F8D2585B8783D481, etc.)
    # pertencem aos repositórios principais do Debian e Security.
    # A maneira mais robusta é instalar o pacote 'debian-archive-keyring'.
    && apt-get update && apt-get install -y debian-archive-keyring \
    # Se ainda houver problemas, adicione as chaves específicas manualmente (uma por uma):
    # && curl -sS "https://keyserver.ubuntu.com/pks/lookup?op=get&search=0x0E98404D386FA1D9" | gpg --dearmor -o /etc/apt/trusted.gpg.d/debian-bookworm-main.gpg \
    # && curl -sS "https://keyserver.ubuntu.com/pks/lookup?op=get&search=0x6ED0E7B82643E131" | gpg --dearmor -o /etc/apt/trusted.gpg.d/debian-bookworm-updates.gpg \
    # && curl -sS "https://keyserver.ubuntu.com/pks/lookup?op=get&search=0xF8D2585B8783D481" | gpg --dearmor -o /etc/apt/trusted.gpg.d/debian-bookworm-backports.gpg \
    # && curl -sS "https://keyserver.ubuntu.com/pks/lookup?op=get&search=0x54404762BBB6E853" | gpg --dearmor -o /etc/apt/trusted.gpg.d/debian-bookworm-security.gpg \
    # && curl -sS "https://keyserver.ubuntu.com/pks/lookup?op=get&search=0xBDE6D2B9216EC7A8" | gpg --dearmor -o /etc/apt/trusted.gpg.d/debian-bookworm-security-alt.gpg \
    # --- Fim da parte crucial: Adição das chaves GPG ---
    && apt-get clean && rm -rf /var/lib/apt/lists/* # Limpeza dos apt lists após a instalação de gnupg/curl

# Instalação de dependências do sistema
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
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

# Instalação de utilitários e chaves GPG (método moderno)
RUN apt-get update && apt-get install -y \
    gnupg \
    curl \
    && apt-get clean && rm -rf /var/lib/apt/lists/* \
    && apt-get update && apt-get install -y debian-archive-keyring \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalação de dependências de runtime
RUN apt-get update && apt-get install -y \
    git \
    libzip-dev \
    libpng-dev \
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