# Stage 1: Build dos assets
FROM node:20.19.4-alpine AS node-build

WORKDIR /app

# Copia apenas os arquivos necessários para o build
COPY package*.json ./
RUN npm ci

# Copia arquivos necessários para o build
COPY vite.config.js ./
COPY resources ./resources
COPY . .

# Compila os assets
RUN npm run build

# Stage 2: PHP
FROM php:8.3-fpm

# Instala dependências do sistema
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

# Instala Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Instala extensão Redis
RUN pecl install redis \
    && docker-php-ext-enable redis \
    && rm -rf /tmp/pear

# Copia o código da aplicação
COPY --chown=www-data:www-data . .

USER www-data

EXPOSE 9000
