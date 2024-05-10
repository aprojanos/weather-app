FROM php:8.2-fpm

RUN apt update 

RUN apt install -y libxml2-dev

RUN docker-php-ext-install xml
RUN docker-php-ext-enable xml
RUN docker-php-ext-install mysqli
RUN docker-php-ext-enable mysqli
RUN docker-php-ext-install pdo
RUN docker-php-ext-enable pdo
RUN docker-php-ext-install dom
RUN docker-php-ext-enable dom
RUN docker-php-ext-install opcache
RUN docker-php-ext-enable opcache

COPY . /var/www/html

WORKDIR /var/www/html

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
&& php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
&& php -r "unlink('composer-setup.php');"
RUN composer install

RUN apt install -y curl

RUN curl -sL https://deb.nodesource.com/setup_18.x | bash -
RUN apt install -y nodejs
RUN npm i

EXPOSE 80

CMD ["php", "artisan", "serve", "--port", "80"]
