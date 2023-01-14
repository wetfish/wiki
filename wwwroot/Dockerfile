FROM php:5.6-fpm-stretch

# Small note, make sure to create upload/thumbs prior to running this! 
WORKDIR /var/www/src
COPY ./src/package.json .
RUN apt-get update && apt-get install -y \
    apt-utils \
    git \
    libjpeg-dev \
    libpng-dev \
    libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/freetype2/ --with-jpeg-dir=/usr/include/libjpeg \ 
    && docker-php-ext-install mysqli mysql exif gd \
    && apt-get clean

RUN curl -sL https://deb.nodesource.com/setup_14.x | bash -\
    && apt-get install -y nodejs \
    && mkdir /var/www/src/node_modules \
    && npm install --prefix /var/www/src

RUN useradd fishy
USER fishy

COPY --chown=fishy:fishy . /var/www/

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]
