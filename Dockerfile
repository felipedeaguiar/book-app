FROM php:8.3-fpm

ARG user=laravel
ARG uid=1000

# Instalações básicas
RUN apt-get update && apt-get install -y \
    git \
    curl \
    unzip \
    zip \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        sockets \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Redis
RUN pecl install redis && docker-php-ext-enable redis

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Cria usuário com mesmo UID que host
RUN useradd -G www-data,root -u $uid -d /home/$user $user && \
    mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user

# Diretório do projeto
WORKDIR /var/www

# Copia apenas os arquivos de dependências primeiro (para cache eficiente)
COPY --chown=$user:$user composer.json composer.lock ./

# Instala dependências sem scripts/autoloader ainda
RUN composer install --no-dev --no-scripts --no-autoloader

# Copia o resto do código
COPY --chown=$user:$user . .

# Agora roda os scripts do Laravel e gera autoload otimizado
RUN composer dump-autoload --optimize && \
    composer run-script post-autoload-dump || true

# Permissões para Laravel
RUN
