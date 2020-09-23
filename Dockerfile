FROM larueli/php-base-image:7.4

COPY . /var/www/html/

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN composer install