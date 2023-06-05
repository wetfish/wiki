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
    curl

# setup nodejs repo
RUN set -exu \
  && curl -fsSL https://deb.nodesource.com/gpgkey/nodesource.gpg.key | gpg --dearmor | apt-key add - \
  && echo "deb https://deb.nodesource.com/node_14.x bookworm main" | tee /etc/apt/sources.list.d/nodesource.list

# setup php5.6 repo
RUN set -exu \
  && echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" > /etc/apt/sources.list.d/php.list \
  && curl -fsSL https://packages.sury.org/php/apt.gpg | apt-key add -

# install php5.6, some extensions, and nodejs
RUN set -exu \
  && DEBIAN_FRONTEND=noninteractive apt-get -yq update \
  && DEBIAN_FRONTEND=noninteractive apt-get -yq install \
    php5.6 \
    php5.6-fpm \
    php5.6-mysqli \
    php5.6-mysql \
    php5.6-exif \
    php5.6-gd \
    nodejs \
    npm

# clean apt caches
RUN set -exu \
  && DEBIAN_FRONTEND=noninteractive apt-get -yq clean

# copy in sources
COPY ./wwwroot /var/www

# make sure our user owns the wwwroot
RUN set -exu \
  && chown -R www-data:www-data /var/www

# switch to our nonroot user
USER www-data

# run npm install
RUN set -exu \
  && npm install --prefix /var/www/src

# back to root
USER root

WORKDIR /var/www

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["/usr/sbin/php-fpm5.6", "--nodaemonize", "--force-stderr"]
