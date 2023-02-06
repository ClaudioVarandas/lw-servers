FROM composer:2 as composer
FROM php:8.1-fpm-alpine as base

ARG USER_UID=82
ARG USER_GID=82

# Recreate www-data user with user id matching the host
RUN deluser --remove-home www-data && \
    addgroup -S -g ${USER_GID} www-data && \
    adduser -u ${USER_UID} -D -S -G www-data www-data

# Necessary tools
RUN apk add --update --no-cache ${PHPIZE_DEPS} git curl

# Pickle - PHP Extension installer
# See https://wiki.php.net/rfc/deprecate-pear-include-composer
RUN wget https://github.com/FriendsOfPHP/pickle/releases/latest/download/pickle.phar && \
    mv pickle.phar /usr/local/sbin/pickle && \
    chmod +x /usr/local/sbin/pickle

# Redis extension
RUN pickle -n install redis@5.3.7
RUN docker-php-ext-enable redis

# ZIP module
RUN apk add --no-cache libzip-dev && docker-php-ext-configure zip && docker-php-ext-install zip

# Imagick module
RUN apk add --no-cache libgomp imagemagick imagemagick-dev && \
	pecl install -o -f imagick && \
	docker-php-ext-enable imagick

# Symfony CLI tool
RUN apk add --no-cache bash && \
	curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.alpine.sh' | bash && \
	apk add symfony-cli && \
	apk del bash

# XDebug from PECL
RUN pecl install xdebug-3.1.5

# Necessary build deps not longer needed
RUN apk del --no-cache ${PHPIZE_DEPS} \
    && docker-php-source delete

# Composer
COPY --from=composer /usr/bin/composer /usr/local/bin/composer

# XDebug wrapper
#COPY ./docker/xdebug /usr/local/bin/xdebug
#RUN chmod +x /usr/local/bin/xdebug

# Clean up image
RUN rm -rf /tmp/* /var/cache
