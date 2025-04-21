FROM php:8.4-apache
WORKDIR /var/www/html
COPY . /var/www/html

# Mod Rewrite
RUN a2enmod rewrite

RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!$/var/www/html/public!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Linux Library
RUN apt-get update -y && apt-get install -y \
    libicu-dev \
    libmariadb-dev \
    unzip zip \
    zlib1g-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# PHP Extension
RUN docker-php-ext-install gettext intl pdo_mysql gd

RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

RUN service apache2 restart

RUN composer update

RUN php artisan key:generate
#CMD php artisan serve --host=0.0.0.0 --port=9000
EXPOSE 80
