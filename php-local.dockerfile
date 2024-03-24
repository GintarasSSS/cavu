FROM php:8.1-apache

WORKDIR /var/www/html

RUN apt-get update
RUN apt-get install -y libzip-dev libpng-dev
RUN docker-php-ext-install pdo_mysql
RUN docker-php-ext-install zip
RUN docker-php-ext-install gd
RUN docker-php-ext-install exif

RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash -
RUN apt-get install -y nodejs \
    zip

RUN npm install -g npm@10.2.5

COPY ./ .

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN /usr/bin/composer install && npm install

ENV APACHE_DOCUMENT_ROOT /var/www/html/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

RUN a2enmod rewrite
