

FROM ubuntu:22.04

# Set environment variables for non-interactive apt installs
ENV DEBIAN_FRONTEND=noninteractive

# Add repository for PHP 8.3
RUN apt-get update && apt-get install -y software-properties-common \
    && add-apt-repository ppa:ondrej/php \
    && apt-get update

# Install Apache, PHP 8.3, and other necessary dependencies
RUN apt-get update && apt-get install -y \
    apache2 \
    php8.3 \
    libapache2-mod-php8.3 \
    php8.3-mysql \
    php8.3-pgsql \
    php8.3-gd \
    php8.3-zip \
    php8.3-opcache \
    php8.3-mbstring \
    php8.3-xml \
    php8.3-curl \
    php8.3-bcmath \
    php8.3-intl \
    php8.3-soap \
    php8.3-readline \
    php8.3-ldap \
    php8.3-imagick \
    libfreetype6-dev \
    libjpeg-dev \
    libpng-dev \
    libpq-dev \
    libwebp-dev \
    libzip-dev \
    dos2unix \
    unzip \
    curl \
    wget \
    git \
    openssh-server \
    && apt-get clean

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install Drush globally
RUN composer global require drush/drush

ENV PATH="$PATH:/root/.composer/vendor/bin"
# RUN composer global require drush/drush \
#     && ln -s /root/.composer/vendor/bin/drush /usr/local/bin/drush
# Environment variables
ENV DRUPAL_VERSION=10.3.1 \
    COMPOSER_ALLOW_SUPERUSER=1

# Set recommended PHP.ini settings
RUN mkdir -p /etc/php/8.3/apache2/conf.d \
    && { \
        echo 'opcache.enable=1'; \
        echo 'opcache.memory_consumption=256'; \
        echo 'opcache.interned_strings_buffer=8'; \
        echo 'opcache.max_accelerated_files=10000'; \
        echo 'opcache.revalidate_freq=60'; \
        echo 'opcache.validate_timestamps=0'; \
        echo 'opcache.save_comments=1'; \
        echo 'opcache.enable_cli=0'; \
    } > /etc/php/8.3/apache2/conf.d/opcache-recommended.ini

WORKDIR /opt/drupal

# Copy your Drupal code to /opt/drupal
COPY . /opt/drupal

# Install Composer dependencies
RUN set -eux; \
    export COMPOSER_HOME="$(mktemp -d)"; \
    composer install --no-scripts --no-interaction; \
    if [ ! -d "web/core" ]; then \
        echo "Error: Drupal core is not installed"; \
        exit 1; \
    fi; \
    chown -R www-data:www-data web/sites web/modules web/themes; \
    rm -rf /var/www/html/*; \
    ln -sf /opt/drupal/web /var/www/html; \
    rm -rf "$COMPOSER_HOME"

# Use bash explicitly for the RUN instruction
RUN apt-get update && apt-get install -y curl
ENV NVM_DIR=/root/.nvm
RUN curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.2/install.sh | bash \
    && bash -c "source $NVM_DIR/nvm.sh && nvm install node && nvm use node"

# Ensure NVM and NPM are in the PATH
RUN echo "export NVM_DIR=$NVM_DIR" >> /root/.bashrc \
    && echo "[ -s \"$NVM_DIR/nvm.sh\" ] && \. \"$NVM_DIR/nvm.sh\"" >> /root/.bashrc \
    && echo "export PATH=\$NVM_DIR/versions/node/\$(nvm current)/bin:\$PATH" >> /root/.bashrc

# Copy and build the Drupal theme and install Storybook
RUN bash -c "source $NVM_DIR/nvm.sh && cd web/themes/custom/drupalx_theme && nvm install && nvm use && npm ci"
# Copy Apache configuration
COPY 000-default.conf /etc/apache2/sites-available/000-default.conf
COPY settings.php web/sites/default/settings.php

# Enable Apache modules
RUN a2enmod rewrite headers

# Ensure correct permissions for the web root directory
RUN chown -R www-data:www-data /var/www/html && chmod -R 755 /var/www/html

# Set up SSH
RUN mkdir /var/run/sshd \
    && echo 'root:password' | chpasswd \
    && sed -i 's/#PermitRootLogin prohibit-password/PermitRootLogin yes/' /etc/ssh/sshd_config \
    && sed -i 's/Port 22/Port 2222/' /etc/ssh/sshd_config \
    && echo "PermitUserEnvironment yes" >> /etc/ssh/sshd_config

# Copy SSL certificate file to preferred location
COPY DigiCertGlobalRootCA.crt.pem /var/www/html/DigiCertGlobalRootCA.crt.pem

# Expose ports
EXPOSE 80 2222 6006

# Set the entrypoint script
#COPY entrypoint.sh /usr/local/bin/entrypoint.sh
#RUN chmod +x /usr/local/bin/entrypoint.sh
#ENTRYPOINT ["entrypoint.sh"]

# Optional: If you need to run Drush commands on container start
CMD ["sh", "-c", "vendor/bin/drush && vendor/bin/drush updb -y && vendor/bin/drush cim -y && vendor/bin/drush cr && apache2ctl -D FOREGROUND"]
