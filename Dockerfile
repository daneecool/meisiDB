FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    libpq-dev \
    libldap2-dev \
    && docker-php-ext-configure ldap --with-libdir=lib/x86_64-linux-gnu \
    && docker-php-ext-install pdo pdo_pgsql ldap

COPY ./php.ini /usr/local/etc/php/

WORKDIR /var/www/html

