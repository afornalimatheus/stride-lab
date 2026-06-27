FROM php:8.4-cli

RUN apt-get update && apt-get install -y \
    libmariadb-dev \
    libzip-dev \
    unzip \
    git \
    curl \
    && docker-php-ext-install pdo pdo_mysql zip bcmath \
    && apt-get clean

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /usr/src/app

COPY . .

RUN composer install --no-interaction --prefer-dist

RUN chmod -R 777 storage bootstrap/cache

EXPOSE 8000

CMD php artisan serve --host=0.0.0.0 --port=8000
