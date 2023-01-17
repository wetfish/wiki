# Note: This comment was in the previous dockerfile
# Note: Not 100% sure why
#
# Small note, make sure to create upload/thumbs prior to running this! 

# builder container
FROM docker.io/debian:stretch AS npm

# install some packages
RUN set -exu \
  && DEBIAN_FRONTEND=noninteractive apt-get -yq update \
  && DEBIAN_FRONTEND=noninteractive apt-get -yq install \
    curl \
    apt-utils \
    git \
    libjpeg-dev \
    libpng-dev \
    libfreetype6-dev

# setup nodejs repo
RUN curl -fsSL https://deb.nodesource.com/setup_14.x | bash -

RUN set -exu \
  && DEBIAN_FRONTEND=noninteractive apt-get -yq update \
  && DEBIAN_FRONTEND=noninteractive apt-get -yq install \
    nodejs

RUN set -exu \
  && DEBIAN_FRONTEND=noninteractive apt-get -yq clean

# copy in wwwroot
COPY ./wwwroot /var/www

# run npm install
RUN set -exu \
  && npm install --silent --prefix /var/www/src


#
# runtime container
FROM docker.io/php:5.6-fpm-stretch

# copy populated wwwroot from build container
COPY --from=npm /var/www /var/www

# create our user
RUN set -exu \
  && addgroup --gid 1101 fishy \
  && adduser \
      --uid 1101 \
      --ingroup fishy \
      --no-create-home \
      --shell /sbin/nologin \
      --disabled-password \
      fishy \
  && chown -R fishy:fishy /var/www

# setup nodejs repo
# RUN curl -sL https://deb.nodesource.com/setup_14.x | bash -

# install some packages
# RUN set -exu \
#   && DEBIAN_FRONTEND=noninteractive apt-get -yq update \
#   && DEBIAN_FRONTEND=noninteractive apt-get -yq install \
#     apt-utils \
#     git \
#     libjpeg-dev \
#     libpng-dev \
#     libfreetype6-dev \
#     nodejs \
#   && DEBIAN_FRONTEND=noninteractive apt-get -yq clean

# configure some php stuff
RUN set -exu \
  && docker-php-ext-configure gd \
    --with-freetype-dir=/usr/include/freetype2/ \
    --with-jpeg-dir=/usr/include/libjpeg \ 
  && docker-php-ext-install \
    mysqli \
    mysql \
    exif \
    gd

# run npm install
# RUN set -exu \
#   && mkdir -p /var/www/src/node_modules \
#   && npm install --prefix /var/www/src

# switch to our nonroot user
USER fishy

# Expose port 9000 and start php-fpm server
WORKDIR /var/www

EXPOSE 9000

CMD ["php-fpm"]
