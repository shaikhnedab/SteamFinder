# Use Ubuntu as the base image
FROM ubuntu:22.04

# Set environment variables
ENV DEBIAN_FRONTEND noninteractive

# Update package lists
RUN apt-get update && \
    apt-get install -y \
    software-properties-common \
    curl \
    git \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nano \
    nginx \
    supervisor

RUN add-apt-repository ppa:ondrej/php

# Install PHP necessary packages
RUN apt-get update && \
    apt-get install -y \
    php7.4 \
    php7.4-fpm \
    php7.4-mysql \
    php7.4-xml \
    php7.4-mbstring \
    php7.4-json \
    php7.4-curl \
    php7.4-zip \
    php7.4-gd \
    php7.4-intl \
    php7.4-bcmath \
    php7.4-bz2 \
    php7.4-gmp

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Create directory for PHP-FPM socket
RUN mkdir -p /run/php/

# Set permissions for the directory
RUN chmod -R 755 /run/php/

# Create a directory for your Laravel application
WORKDIR /var/www/html

# Copy your Laravel application files into the container
COPY . .

RUN mv .env.example .env

# Install dependencies
RUN composer install

RUN php artisan key:generate

# Expose port 8000 for Laravel serve
EXPOSE 8000

# Start Laravel development server
CMD ["php", "artisan", "serve", "--host", "0.0.0.0", "--port", "8000"]
