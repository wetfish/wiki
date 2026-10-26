FROM docker.io/php:8.5.12-fpm-alpine

# gd for thumbnails and the captcha, exif for image info, mysqli for the database
RUN set -exu \
  && apk add --no-cache \
    freetype \
    libjpeg-turbo \
    libpng \
    libwebp \
  && apk add --no-cache --virtual .build-deps \
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    libwebp-dev \
  && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
  && docker-php-ext-install -j"$(nproc)" exif gd mysqli \
  && apk del .build-deps

# php's production defaults, plus the wiki's own settings
RUN cp "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY ./config/php.ini "$PHP_INI_DIR/conf.d/wiki.ini"

COPY ./wwwroot /var/www
# the images the wiki shows when an upload is missing, and for fishbux;
# they seed the uploads volume the first time it's created
COPY ./upload /var/www/upload

RUN set -exu \
  && addgroup --gid 1101 fishy \
  && adduser \
      --uid 1101 \
      --ingroup fishy \
      --no-create-home \
      --shell /sbin/nologin \
      --disabled-password \
      fishy \
  && chown -R fishy:fishy /var/www/upload

# php only writes to the uploads volume, the code stays read-only
USER fishy

# Expose port 9000 and start php-fpm server
EXPOSE 9000

WORKDIR /var/www

CMD ["php-fpm"]
