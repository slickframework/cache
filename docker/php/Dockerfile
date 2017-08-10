FROM slickframework/php:7.0

RUN apt-install \
      libicu-dev \
      libmemcached-dev

ENV MEMCACHED_VERSION 3.0.3
RUN { yes '/usr' | pecl install memcached-$MEMCACHED_VERSION; } \
    && echo "extension=memcached.so" >> /usr/local/etc/php/conf.d/memcached.ini