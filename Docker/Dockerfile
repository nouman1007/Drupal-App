FROM ubuntu:22.04

# Set environment variables for non-interactive apt installs
ENV DEBIAN_FRONTEND=noninteractive

# Add repository for PHP 8.3
RUN apt-get update && apt-get install -y software-properties-common \
    && add-apt-repository ppa:ondrej/php \
    && apt-get update

# Install Apache, PHP 8.3, SSH, and necessary dependencies
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

# Start and enable SSH
RUN apt-get update \
    # && apt-get install -y --no-install-recommends dialog \
    # && apt-get install -y --no-install-recommends openssh-server \
    && echo "root:Docker!" | chpasswd

COPY sshd_config /etc/ssh/

EXPOSE 80
EXPOSE 2222
EXPOSE 8000

COPY init_container.sh /opt/startup/
RUN chmod 775 /opt/startup/init_container.sh

ENTRYPOINT ["/opt/startup/init_container.sh"]