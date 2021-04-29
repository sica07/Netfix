FROM php:7.3-fpm

RUN apt-get update && apt-get install -y sudo curl software-properties-common libzip-dev git\
    && curl --version \
    && php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php \
    && php -r "unlink('composer-setup.php');" \
    && mv composer.phar /usr/local/bin/composer

RUN apt-get update && apt-get install -y sqlite3 \
        && docker-php-ext-install zip
