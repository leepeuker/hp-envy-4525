FROM webdevops/php-nginx:8.2-alpine AS development
ENV WEB_DOCUMENT_ROOT="/app/public" \
    PHP_DISMOD=imagick
RUN apk add --no-cache make nano
RUN mkdir -p /paperless-consumption && chown -R application /paperless-consumption
USER application
WORKDIR /app
