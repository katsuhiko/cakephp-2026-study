FROM php:8.4-cli

RUN apt-get update

RUN apt-get install -y unzip

RUN apt-get install -y libicu-dev \
    && docker-php-ext-install intl
