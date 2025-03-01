FROM php:8.3-apache

# Update and install required packages
RUN apt update && apt install -y \
    vim git default-mysql-client nano cron \
    libonig-dev libzip-dev libicu-dev locales-all \
    && docker-php-ext-install \
    bcmath mbstring pdo_mysql zip mysqli intl opcache \
    && rm -rf /var/lib/apt/lists/*

# Install Node
RUN curl -fsSL https://deb.nodesource.com/setup_21.x | bash - && \
    apt install -y nodejs && rm -rf /var/lib/apt/lists/*

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Configure PHP settings
RUN sed -i -e "s/;\?max_input_vars = .*/max_input_vars = 5000/g" \
           -e "s/post_max_size = 8M/post_max_size = 800M/g" \
           -e "s/upload_max_filesize = 2M/upload_max_filesize = 2000M/g" \
           -e "s/max_file_uploads = 20/max_file_uploads = 2000/g" \
           /usr/local/etc/php/php.ini-production && \
    mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

RUN a2enmod rewrite headers

RUN sed -ri -e 's!/var/www/html!/var/www/public!g' /etc/apache2/sites-available/*.conf && \
    sed -ri -e 's!/var/www/!/var/www/public!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf && \
    rm -r /var/www/html

COPY .docker/web/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

WORKDIR /var/www

ARG USERNAME=root
ARG UID=0
ARG GROUP=root
ARG ROOTPASS=l3tm31n

RUN echo "root:${ROOTPASS}" | chpasswd && \
    if [ "${USERNAME}" != "root" ]; then \
        useradd -ms /bin/bash ${USERNAME} -u ${UID}; \
    fi

USER ${USERNAME}:${GROUP}
