# define base php-apache image
FROM php:8.4-apache

# set container working directory
WORKDIR /var/www

# install required packages
RUN apt-get update && apt-get install -y \
    libicu-dev \
    rpm
    
# install PHP extensions
RUN docker-php-ext-install intl

# install OPCache extension
RUN docker-php-ext-install opcache

# set global server name (domain)
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# enable apache modules
RUN a2enmod rewrite
