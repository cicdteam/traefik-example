FROM php:7.1-fpm-alpine

RUN set -e \
	&& apk add --no-cache \
		mariadb-client \
	&& docker-php-ext-install \
		pdo \
		pdo_mysql

ADD index.php /var/www/index.php
