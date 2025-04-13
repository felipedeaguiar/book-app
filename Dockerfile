FROM php:8.3-fpm

ARG user=laravel
ARG uid=1000

# Instalações básicas
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Instala extensões PHP
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd sockets

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

# Copia o código Laravel (e muda o dono dos arquivos)
COPY . /var/www
RUN chown -R $user:$user /var/www

# Adiciona diretório seguro do Git
RUN git config --global --add safe.directory /var/www

# Usa o novo usuário
USER $user
