FROM php:8.3-fpm

# системные зависимости, нужные для расширений php
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libpq-dev \
    libzip-dev \
    libxml2-dev \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# расширения php — postgres, работа с zip и xml, точная арифметика для цен
RUN docker-php-ext-install \
    pdo \
    pdo_pgsql \
    pgsql \
    zip \
    xml \
    bcmath \
    opcache

# composer берем из официального образа, чтобы не тащить вручную
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# запускаем php-fpm от имени www-data
USER www-data
