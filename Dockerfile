# last version to have phpdbg enabled
FROM php:7.4.27-fpm-alpine as app

ENV COMPOSER_ALLOW_SUPERUSER=1

WORKDIR /var/www/html

COPY . /var/www/html

RUN NPROC=$(grep -c ^processor /proc/cpuinfo 2>/dev/null || 1) \
	&& apk add --update --no-cache --virtual .ext-deps \
		bash \
		mysql-client \
		icu-dev \
		make \
	&& docker-php-ext-install -j${NPROC} \
		opcache \
		pdo_mysql \
		intl

COPY --from=composer:1 /usr/bin/composer /usr/bin/composer

COPY ./docker/php.ini /usr/local/etc/php/conf.d/

CMD bash -c "bin/wait-for-db && bin/update && php-fpm"
