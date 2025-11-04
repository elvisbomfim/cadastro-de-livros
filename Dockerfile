FROM php:8.3-fpm


RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd sockets \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Ajusta o UID do usuário www-data
RUN usermod -u 1000 www-data


WORKDIR /var/www


COPY --from=composer:latest /usr/bin/composer /usr/bin/composer


RUN pecl install redis \
    && docker-php-ext-enable redis \
    && rm -rf /tmp/pear


USER www-data


EXPOSE 9000
