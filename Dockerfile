FROM php:8.2-fpm

RUN apt-get update && apt-get install -y --fix-missing \
    git \
    unzip \
    libpq-dev \
    libjpeg-dev \
    libpng-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libonig-dev \
    libzip-dev \
    zip \
    curl \
    libaio-dev \
    libxml2-dev \
    default-mysql-client \
    libsqlite3-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd

RUN docker-php-ext-install pdo pdo_mysql

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN curl -sS https://get.symfony.com/cli/installer | bash && \
    mv /root/.symfony*/bin/symfony /usr/local/bin/symfony

WORKDIR /var/www/symfony

COPY . .
RUN composer install --no-interaction --optimize-autoloader

RUN chown -R www-data:www-data /var/www/symfony

RUN php bin/console cache:warmup

RUN php bin/console doctrine:migrations:migrate --no-interactio

CMD ["php-fpm"]