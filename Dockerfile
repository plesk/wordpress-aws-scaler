FROM debian:testing

RUN apt-get update && apt-get install -y mysql-client nginx && rm -rf /var/lib/apt/lists/*

# PHP
RUN apt-get update && \
    apt-get -y install \
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
    php7.0-mcrypt && apt-get clean

RUN mkdir /run/php

# nginx site conf
ADD ./nginx-site.conf /etc/nginx/sites-available/default
RUN sed -i "s/nginx;/www-data;/" /etc/nginx/nginx.conf

# Install WP-CLI
RUN curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar \
	&& chmod +x wp-cli.phar \
	&& mv wp-cli.phar /usr/local/bin/wp

# Install New Relic SERVER and APM agent
RUN echo deb http://apt.newrelic.com/debian/ newrelic non-free >> /etc/apt/sources.list.d/newrelic.list \
    && apt-get update \
    && apt-get install -y ca-certificates wget \
    && wget -O- https://download.newrelic.com/548C16BF.gpg | apt-key add - \
    && apt-get install -y --force-yes \
	newrelic-sysmond \
    	newrelic-php5 \
    && nrsysmond-config --set license_key=22660887228aa6e487fab34c408663dff6dc2c50 \
    && /etc/init.d/newrelic-sysmond start \
    && newrelic-install install \
    && newrelic-php5 newrelic-php5/application-name string “WordPress AWS Scaler” | debconf-set-selections \
    && newrelic-php5 newrelic-php5/license-key string "22660887228aa6e487fab34c408663dff6dc2c50" | debconf-set-selections

COPY docker-entrypoint.sh /entrypoint.sh

# grr, ENTRYPOINT resets CMD now
ENTRYPOINT ["/entrypoint.sh"]

WORKDIR /usr/src/wordpress
VOLUME ['/usr/src/wordpress/wp-content']

EXPOSE 80
CMD ["nginx", "-g", "daemon off;"]
