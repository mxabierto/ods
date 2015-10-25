# docker build -t mxabierto/ods .
# docker run --link postgres:postgres -P mxabierto/ods

FROM debian:jessie

# Setup
USER root
ENV DEBIAN_FRONTEND noninteractive

# Install dependencies
RUN \
  apt-get update && \
  apt-get install -y \
    apache2 \
    curl \
    libapache2-mod-php5 \
    php-apc \
    php5-cli \
    php5-curl \
    php5-fpm \
    php5-gd \
    php5-pgsql && \
  apt-get clean && \
  apt-get autoclean && \
  apt-get autoremove && \
  rm -rf /var/lib/apt/lists/*

# Composer
RUN \
  curl -sS https://getcomposer.org/installer | php && \
  mv composer.phar /usr/local/bin/composer && \
  sed -i 's/display_errors = Off/display_errors = On/' /etc/php5/apache2/php.ini && \
  sed -i 's/display_errors = Off/display_errors = On/' /etc/php5/cli/php.ini && \
  rm /var/www/html/index.html

# Drupal
ADD start.sh /start.sh
ADD public_html /var/www/html
EXPOSE 80
ENTRYPOINT ["/start.sh"]
