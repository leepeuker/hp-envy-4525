FROM webdevops/php-nginx:8.2-alpine AS development
ENV WEB_DOCUMENT_ROOT="/app/public" \
    PHP_DISMOD=imagick
RUN apk add --no-cache make nano
RUN mkdir -p /scans-finished && chown -R application /scans-finished
USER application
WORKDIR /app

FROM development AS production
COPY --chown=application ./ ./
RUN composer install --no-dev
