# Используем официальный образ PHP с Apache
FROM php:8.3-apache

# Обновляем пакеты и устанавливаем необходимые зависимости
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    libicu-dev \
    libonig-dev \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && docker-php-ext-install intl pdo pdo_mysql

RUN a2enmod rewrite

COPY apache.conf /etc/apache2/sites-available/000-default.conf

RUN printf "zend_extension=xdebug\nxdebug.mode=debug\nxdebug.start_with_request=yes\nxdebug.client_host=host.docker.internal\nxdebug.client_port=9003\n" > /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/html

EXPOSE 80

CMD ["apache2-foreground"]