FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --no-progress

# Built only with --target qa: fails if style, static analysis or tests fail.
FROM composer:2 AS qa
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-interaction --no-progress
COPY phpunit.xml phpstan.dist.neon .php-cs-fixer.dist.php ./
COPY src/ src/
COPY tests/ tests/
COPY bin/ bin/
RUN composer qa

FROM php:8.5-cli-alpine
WORKDIR /app
COPY --from=vendor /app/vendor/ vendor/
COPY src/ src/
COPY bin/ bin/
ENTRYPOINT ["php", "bin/fizzbuzz"]
