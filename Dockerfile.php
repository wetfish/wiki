# Note: This comment was in the previous dockerfile
# Note: Not 100% sure why
#
# Small note, make sure to create upload/thumbs prior to running this! 

#
# runtime container
FROM docker.io/debian:bookworm-slim

# install some packages
RUN set -exu \
  && DEBIAN_FRONTEND=noninteractive apt-get -yq update \
  && DEBIAN_FRONTEND=noninteractive apt-get -yq install \
    libjpeg-dev \
    libpng-dev \
    libfreetype6-dev \
    software-properties-common \
    ca-certificates \
    lsb-release \
    apt-transport-https \
    curl \
    ffmpeg

# setup nodejs repo
RUN set -exu \
  && curl -fsSL https://deb.nodesource.com/gpgkey/nodesource.gpg.key | gpg --dearmor | apt-key add - \
  && echo "deb https://deb.nodesource.com/node_14.x bookworm main" | tee /etc/apt/sources.list.d/nodesource.list

# setup php8.0 repo
RUN set -exu \
  && echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" > /etc/apt/sources.list.d/php.list \
  && curl -fsSL https://packages.sury.org/php/apt.gpg | apt-key add -

# install php5.6, some extensions, and nodejs
RUN set -exu \
  && DEBIAN_FRONTEND=noninteractive apt-get -yq update \
  && DEBIAN_FRONTEND=noninteractive apt-get -yq install \
    php8.0 \
    php8.0-fpm \
    php8.0-mysqli \
    php8.0-mysql \
    php8.0-exif \
    php8.0-gd \
    nodejs \
    npm

# clean apt caches
RUN set -exu \
  && DEBIAN_FRONTEND=noninteractive apt-get -yq clean

# create a builder user
RUN set -exu \
  && addgroup --gid 1101 builder \
  && adduser \
      --uid 1101 \
      --ingroup builder \
      --shell /sbin/nologin \
      --disabled-password \
      builder

# copy in sources
COPY ./wwwroot /var/www

# make sure our user owns the wwwroot
RUN set -exu \
  && chown -R builder:builder /var/www

# switch to our nonroot user
USER builder

# run npm install
WORKDIR /var/www/src
RUN set -exu \
  && cd /var/www/src \
  && npm install

# back to root
USER root

WORKDIR /var/www

RUN set -exu \
  && chown -R www-data:www-data /var/www

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["/usr/sbin/php-fpm8.0", "--nodaemonize", "--force-stderr"]
