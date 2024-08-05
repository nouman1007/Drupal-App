# Use the official PHP image with Apache
FROM php:8.2-apache

# Install necessary PHP extensions and other dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libxml2-dev \
    git \
    unzip \
    wget \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd \
    && docker-php-ext-install mysqli pdo pdo_mysql \
    && docker-php-ext-install opcache

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Set the working directory
WORKDIR /var/www/html

# Download Drupal
RUN wget https://www.drupal.org/download-latest/zip -O drupal.zip \
    || { echo 'Failed to download Drupal'; exit 1; }

# Unzip Drupal
RUN unzip drupal.zip \
    || { echo 'Failed to unzip Drupal'; exit 1; }

# Cleanup
RUN rm drupal.zip \
    || { echo 'Failed to remove Drupal zip'; exit 1; }

# Move files
RUN mv drupal-*/* . \
    || { echo 'Failed to move files'; exit 1; }

# Remove leftover directories
RUN rm -rf drupal-* \
    || { echo 'Failed to remove directory'; exit 1; }

# Copy custom settings.php file
COPY settings.php sites/default/settings.php

# Ensure permissions are correct
RUN chmod 664 sites/default/settings.php \
    && chown www-data:www-data sites/default/settings.php \
    && chmod 775 sites/default \
    && chown www-data:www-data sites/default

# Set ownership for the entire Drupal directory
RUN chown -R www-data:www-data /var/www/html

# Copy the default .htaccess file and ensure it's in place
COPY .htaccess /var/www/html/.htaccess

# Create OPcache configuration
RUN echo 'opcache.memory_consumption=128' >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo 'opcache.interned_strings_buffer=8' >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo 'opcache.max_accelerated_files=4000' >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo 'opcache.revalidate_freq=2' >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo 'opcache.fast_shutdown=1' >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo 'opcache.enable_cli=1' >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini

# Copy SSL certificate file to preferred location
COPY DigiCertGlobalRootCA.pem /var/www/html/bin/DigiCertGlobalRootCA.pem

# Expose port 80
EXPOSE 80

# Start Apache in the foreground
CMD ["apache2-foreground"]
