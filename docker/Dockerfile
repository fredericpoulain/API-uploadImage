#indique que l’image de base à utiliser est php:8.2-apache
FROM php:8.2-apache

#met à jour la liste des packages disponibles et met à niveau les packages déjà installés dans l’image
RUN apt-get update \
    && apt-get install -y zlib1g-dev g++ git libicu-dev zip libzip-dev \
    && pecl install apcu \
    && docker-php-ext-install intl opcache pdo pdo_mysql \
    && docker-php-ext-enable apcu \
    && docker-php-ext-configure zip \
    && docker-php-ext-install zip



RUN a2enmod rewrite

WORKDIR /var/www

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN curl https://get.symfony.com/cli/installer | bash
RUN mv /root/.symfony5/bin/symfony /usr/local/bin/symfony

RUN curl -sL https://deb.nodesource.com/setup_21.x | bash -
#RUN apt-get install -y nodejs