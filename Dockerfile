FROM dunglas/frankenphp:1.4-php8.4-bookworm
RUN apt-get update && apt-get install -y zip nano default-mysql-client
RUN curl -sSL https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions -o /usr/local/bin/install-php-extensions && chmod +x /usr/local/bin/install-php-extensions
RUN install-php-extensions mbstring pdo_mysql intl zip redis bcmath pcntl @composer
RUN echo "zend.assertions=-1" > /usr/local/etc/php/conf.d/99-custom.ini
WORKDIR /var/www/app
