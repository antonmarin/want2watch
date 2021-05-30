FROM antonmarin/php:7.4-alpine-fpm as runtime
ENV PHP_INI_SESSION_USE_COOKIES=0

FROM runtime as application-deps
WORKDIR /app/
COPY composer.json composer.lock ./
RUN composer install --no-interaction --no-dev --no-suggest --optimize-autoloader

FROM application-deps as debug-deps
RUN composer install --no-interaction --no-suggest

FROM application-deps as application
COPY bin ./bin/
COPY public ./public/
COPY src ./src/
COPY config ./config/

FROM application as debug
COPY --from=debug-deps /app/vendor ./vendor/
COPY tests ./tests/
COPY phpstan.neon phpunit.xml ./
