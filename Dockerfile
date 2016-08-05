FROM php:7-fpm

RUN apt-get update && apt-get install -y mysql-client nginx libpng12-dev libjpeg-dev && rm -rf /var/lib/apt/lists/* \
	&& docker-php-ext-configure gd --with-png-dir=/usr --with-jpeg-dir=/usr \
	&& docker-php-ext-install gd mysqli pdo pdo_mysql opcache

# nginx site conf
ADD ./nginx-site.conf /etc/nginx/sites-available/default
#RUN sed -i "s/nginx;/www-data;/" /etc/nginx/nginx.conf

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

COPY docker-entrypoint.sh /entrypoint.sh

# grr, ENTRYPOINT resets CMD now
ENTRYPOINT ["/entrypoint.sh"]

RUN mkdir /usr/src/wordpress
RUN chown -R www-data:www-data /usr/src/wordpress
WORKDIR /usr/src/wordpress
VOLUME ['/usr/src/wordpress/wp-content']

EXPOSE 80
CMD ["php-fpm"]
CMD ["nginx", "-g", "daemon off;"]
