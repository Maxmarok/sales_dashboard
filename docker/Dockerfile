FROM php:8.1.0-fpm

# Arguments defined in docker-compose.yml
ARG user
ARG uid

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libwebp-dev \
    libzip-dev

RUN docker-php-ext-configure gd --with-webp --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd zip

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql exif pcntl bcmath

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create system user to run Composer and Artisan Commands
RUN useradd -G www-data,root -u $uid -d /home/$user $user
RUN mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user

#COPY --chown=$user:$user ../. /var/www
# RUN chmod -R 755 /var/www/storage

## Install redis
# RUN pecl install -o -f redis \
#     &&  rm -rf /tmp/pear \
#     &&  docker-php-ext-enable redis

# Set working directory
WORKDIR /var/www

USER $user