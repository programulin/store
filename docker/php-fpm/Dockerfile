FROM php:8.5.2-fpm-alpine3.23

ENV COMPOSER_HOME=/.composer

RUN curl -sSL https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions -o /usr/local/bin/install-php-extensions \
    && chmod +x /usr/local/bin/install-php-extensions \
    && install-php-extensions pdo_pgsql zip @composer \
    && rm /usr/local/bin/install-php-extensions \
    && mkdir -p $COMPOSER_HOME && chmod -R 777 $COMPOSER_HOME

WORKDIR /app

EXPOSE 9000
