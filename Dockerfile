FROM php:8.2-cli

RUN docker-php-ext-install pdo pdo_mysql mysqli

WORKDIR /app
COPY . /app

EXPOSE $PORT

CMD php -S 0.0.0.0:$PORT -t /app