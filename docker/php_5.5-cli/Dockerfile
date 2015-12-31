FROM php:5.5
MAINTAINER Filipe Silva <silvam.filipe@gmail.com>

# Install modules
RUN apt-get update && apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libmcrypt-dev \
        libpng12-dev \
        libmemcached-dev \
        git \
        libicu-dev \
        libssl-dev \
        zlib1g-dev

RUN { yes '/usr' | pecl install memcached; } \
    && pecl install xdebug \
    && docker-php-ext-install iconv mcrypt pdo_mysql mbstring intl zip \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install gd \
    && docker-php-ext-enable memcached

COPY php.ini /usr/local/etc/php/

WORKDIR /usr/src/myapp

CMD ["php"]