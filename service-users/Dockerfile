FROM hyperf/hyperf:8.2-alpine-v3.18-swoole

WORKDIR /opt/www

COPY composer.json composer.lock ./
RUN composer install

COPY . .

EXPOSE 9501

CMD ["php", "./bin/hyperf.php", "start"]