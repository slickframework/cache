FROM slickframework/php:5.5
MAINTAINER Filipe Silva <silvam.filipe@gmail.com>

# Install modules
RUN apt-get update && apt-get install -y \
        libmemcached-dev \
        libicu-dev


RUN { yes '/usr' | pecl install memcached; } \
    && docker-php-ext-enable memcached