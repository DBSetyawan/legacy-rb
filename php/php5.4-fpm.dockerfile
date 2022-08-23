FROM php:5.6-fpm

ARG user
ARG uid

RUN sed -i '/jessie-updates/d' /etc/apt/sources.list

RUN apt-get update && apt-get install -y libpq-dev

RUN yes | pecl install xdebug-2.4.1 \
    && docker-php-ext-install pdo pdo_pgsql pgsql \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && echo "zend_extension=$(find /usr/local/lib/php/extensions/ -name xdebug.so)" > /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_enable=on" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_autostart=off" >> /usr/local/etc/php/conf.d/xdebug.ini
RUN docker-php-ext-install pdo pdo_pgsql

COPY --from=composer:2.2 /usr/bin/composer /usr/bin/composer

# Create system user to run Composer and Artisan Commands
RUN useradd -G www-data,root -u $uid -d /home/$user $user
RUN mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user

RUN chown -R $user:www-data /var/www

RUN chown -R www-data:www-data /var/www

RUN set -eux; apt-get update; apt-get install -y libzip-dev zlib1g-dev; docker-php-ext-install zip

# Set working directory
WORKDIR /var/www
