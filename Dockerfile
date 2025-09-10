ARG APP_ENV=prod

FROM hyperf/hyperf:8.2-alpine-v3.19-swoole
LABEL maintainer="Hyperf Developers <group@hyperf.io>" version="1.0" license="MIT" app.name="Hyperf"

ARG timezone

ENV TIMEZONE=${timezone:-"America/Sao_Paulo"} \
    APP_ENV=prod \
    SCAN_CACHEABLE=(true)

RUN set -ex \
    && php -v \
    && php -m \
    && php --ri swoole \
    \
    #  ---------- some config ----------
    && cd /etc/php* \
    # - config PHP
    && { \
        echo "upload_max_filesize=128M"; \
        echo "post_max_size=128M"; \
        echo "memory_limit=1G"; \
        echo "date.timezone=${TIMEZONE}"; \
    } | tee conf.d/99_overrides.ini \
    # - config timezone
    && ln -sf /usr/share/zoneinfo/${TIMEZONE} /etc/localtime \
    && echo "${TIMEZONE}" > /etc/timezone \
    # ---------- clear works ----------
    && rm -rf /var/cache/apk/* /tmp/* /usr/share/man \
    && echo -e "\033[42;37m Build Completed :).\033[0m\n"

WORKDIR /opt/www

COPY composer.json composer.lock ./

RUN composer require hyperf/redis --with-all-dependencies || true

RUN if [ "${APP_ENV}" = "dev" ]; then \
    composer install; \
else \
    composer install --no-dev --prefer-dist --optimize-autoloader; \
fi

COPY . .

RUN php bin/hyperf.php

EXPOSE 9501

CMD ["php", "/opt/www/bin/hyperf.php", "start"]

