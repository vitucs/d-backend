FROM hyperf/hyperf:8.2-alpine-v3.18-swoole

WORKDIR /opt/www

COPY composer.json composer.lock ./

RUN composer install --no-dev --no-scripts --no-autoloader

COPY . .

RUN composer dump-autoload --optimize

RUN mkdir -p storage/logs runtime \
    && chmod -R 755 storage runtime

EXPOSE 9501

CMD ["php", "bin/hyperf.php", "start"]