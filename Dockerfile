FROM php:7-fpm

RUN apt-get update && apt-get install -y mysql-client nginx libpng12-dev libjpeg-dev && rm -rf /var/lib/apt/lists/* \
	&& docker-php-ext-configure gd --with-png-dir=/usr --with-jpeg-dir=/usr \
	&& docker-php-ext-install gd mysqli pdo pdo_mysql opcache

# set recommended PHP.ini settings
# see https://secure.php.net/manual/en/opcache.installation.php
RUN { \
		echo 'opcache.memory_consumption=128'; \
		echo 'opcache.interned_strings_buffer=8'; \
		echo 'opcache.max_accelerated_files=4000'; \
		echo 'opcache.revalidate_freq=60'; \
		echo 'opcache.fast_shutdown=1'; \
		echo 'opcache.enable_cli=1'; \
	} > /usr/local/etc/php/conf.d/opcache-recommended.ini

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

VOLUME /var/www/html

COPY docker-entrypoint.sh /entrypoint.sh

WORKDIR /usr/src/wordpress
VOLUME ['/usr/src/wordpress/wp-content']

# grr, ENTRYPOINT resets CMD now
ENTRYPOINT ["/entrypoint.sh"]

EXPOSE 80 443
CMD ["php-fpm"]
CMD ["nginx", "-g", "daemon off;"]
