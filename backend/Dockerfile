FROM antonmarin/php:7.4-alpine-fpm as runtime

FROM runtime as application
WORKDIR /app/
COPY composer.json composer.lock ./
RUN composer install --no-interaction --no-dev --no-suggest --optimize-autoloader
COPY bin ./bin/
COPY public ./public/
COPY src ./src/
COPY config ./config/

FROM application as debug
RUN composer install --no-interaction --no-suggest
COPY tests ./tests/
COPY phpstan.neon phpunit.xml ./
