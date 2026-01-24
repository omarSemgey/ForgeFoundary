FROM composer:2 AS builder
WORKDIR /app
COPY . .

RUN composer install --no-dev --optimize-autoloader

FROM php:8.2-cli-alpine

WORKDIR /usr/src/forgefoundary

COPY --from=builder /app .

RUN chmod +x ForgeFoundary

RUN ln -s /usr/src/forgefoundary/ForgeFoundary /usr/local/bin/ForgeFoundary

ENTRYPOINT ["ForgeFoundary"]