FROM dasctfbase/web_php73_apache_mysql
COPY html/ /var/www/html
RUN chmod -R 777 /var/www/html
COPY php.ini /usr/local/etc/php/php.ini

RUN apt-get update && apt-get install -y zlib1g-dev && apt-get install -y libzip-dev

RUN docker-php-ext-install zip
