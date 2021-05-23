FROM php:7.4

RUN useradd -ms /bin/bash developer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN composer self-update
RUN apt-get update && apt-get install -y libzip-dev rubygems procps && \
    docker-php-ext-install zip && \
    pecl install xdebug && \
    docker-php-ext-enable xdebug && \
    gem install pact-provider-verifier

#USER developer
WORKDIR /app
