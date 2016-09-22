FROM debian:testing

MAINTAINER Jan Loeffler <jan@plesk.com>

# Upgrade everything
RUN apt-get update && apt-get upgrade -y

# Basics software
RUN apt-get install -y wget curl mysql-client nginx

# PHP
RUN apt-get update && apt-get -y install \
    php7.0 \
    php7.0-cgi \
    php7.0-cli \
    php7.0-common \
    php7.0-curl \
    php7.0-dev \
    php7.0-gd \
    php7.0-gmp \
    php7.0-json \
    php7.0-ldap \
    php7.0-mysql \
    php7.0-odbc \
    php7.0-opcache \
    php7.0-pspell \
    php7.0-readline \
    php7.0-sqlite3 \
    php7.0-tidy \
    php7.0-xmlrpc \
    php7.0-xsl \
    php7.0-fpm \
    php7.0-intl \
    php7.0-zip \
    php7.0-mcrypt && apt-get clean

RUN mkdir /run/php

# nginx site conf
COPY docker/nginx-site.conf /etc/nginx/sites-available/default
RUN sed -i "s/nginx;/www-data;/" /etc/nginx/nginx.conf

# Install WP-CLI
RUN curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar \
	&& chmod +x wp-cli.phar \
	&& mv wp-cli.phar /usr/local/bin/wp

# Download WordPress
RUN wp core download --path=/usr/src/wordpress --allow-root

# Integrate user data
COPY content /usr/src/wordpress/wp-content
COPY docker/custom.sh /custom.sh
COPY docker/data.sql /data.sql

# Fix user permissions
RUN chown -R www-data:www-data /usr/src/wordpress

# Add New Relic repo
RUN echo 'deb http://apt.newrelic.com/debian/ newrelic non-free' | tee /etc/apt/sources.list.d/newrelic.list \
    && wget -O- https://download.newrelic.com/548C16BF.gpg | apt-key add -

COPY docker/docker-entrypoint.sh /entrypoint.sh

# grr, ENTRYPOINT resets CMD now
ENTRYPOINT ["/entrypoint.sh"]

WORKDIR /usr/src/wordpress
VOLUME ['/usr/src/wordpress/wp-content']

EXPOSE 80
EXPOSE 443
CMD ["nginx", "-g", "daemon off;"]
