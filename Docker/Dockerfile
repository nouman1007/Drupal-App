# Use Ubuntu as the base image
FROM ubuntu:22.04

# Set environment variables for non-interactive apt installs
ENV DEBIAN_FRONTEND=noninteractive

# Install necessary packages: Apache, PHP, and required extensions
RUN apt-get update && apt-get install -y \
    apache2 \
    php \
    libapache2-mod-php \
    php-mysql \
    php-pgsql \
    php-gd \
    php-zip \
    php-mbstring \
    php-xml \
    php-curl \
    php-bcmath \
    php-intl \
    php-soap \
    php-readline \
    php-ldap \
    php-imagick \
    unzip \
    curl \
    wget \
    && apt-get clean

RUN apt-get update && apt-get install -y openssh-server

# RUN echo "root:Docker!" | chpasswd
RUN echo "root:Docker!" | chpasswd

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Set the working directory
WORKDIR /var/www/html

# Download and set up Drupal
RUN wget https://www.drupal.org/download-latest/zip -O drupal.zip \
    && unzip drupal.zip \
    && mv drupal-*/* . \
    && rm drupal.zip \
    && rm -rf drupal-*

# Set permissions
RUN chmod 775 sites/default \
    && chown -R www-data:www-data /var/www/html

# # Expose Apache HTTP port
# EXPOSE 80

RUN ssh-keygen -A
COPY ./sshd_config /etc/ssh/.
EXPOSE 2222 80
COPY ./start.sh start.sh
RUN chmod +x ./start.sh
CMD ./start.sh