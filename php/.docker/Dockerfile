FROM php:7.3.1-fpm

RUN pecl install redis-4.0.1 \
    && docker-php-ext-enable redis

CMD ["php-fpm"]