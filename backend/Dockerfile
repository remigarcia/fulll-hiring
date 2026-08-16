FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --no-progress --ignore-platform-req=ext-pdo_pgsql

# Built only with --target qa: style, static analysis and the in-memory behat suite.
# Integration tests need PostgreSQL: run them via docker compose / CI, not at build time.
FROM composer:2 AS qa
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-interaction --no-progress --ignore-platform-req=ext-pdo_pgsql
COPY behat.yml phpstan.dist.neon .php-cs-fixer.dist.php schema.sql fleet ./
COPY src/ src/
COPY features/ features/
COPY bin/ bin/
RUN composer qa

FROM php:8.5-cli-alpine
RUN apk add --no-cache libpq \
    && apk add --no-cache --virtual .build-deps postgresql-dev \
    && docker-php-ext-install pdo_pgsql \
    && apk del .build-deps
WORKDIR /app
COPY --from=vendor /app/vendor/ vendor/
COPY src/ src/
COPY fleet ./
ENTRYPOINT ["php", "fleet"]
