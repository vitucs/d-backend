FROM hyperf/hyperf:8.2-alpine-v3.18-swoole

WORKDIR /opt/www

COPY . .

RUN composer install -o

CMD ["php", "./bin/hyperf.php", "start"]