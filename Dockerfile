# Default Dockerfile
#
# @link      https://www.hyperf.io
# @document https://hyperf.wiki
# @contact  group@hyperf.io
# @license  https://github.com/hyperf/hyperf/blob/master/LICENSE

# PASSO 1: Mudar a imagem base para a versão com PHP 8.2
FROM hyperf/hyperf:8.2-alpine-v3.19-swoole
LABEL maintainer="Hyperf Developers <group@hyperf.io>" version="1.0" license="MIT" app.name="Hyperf"

##
# ---------- env settings ----------
##
# --build-arg timezone=Asia/Shanghai
ARG timezone

ENV TIMEZONE=${timezone:-"America/Sao_Paulo"} \
    APP_ENV=prod \
    SCAN_CACHEABLE=(true)

# update
RUN set -ex \
    # show php version and extensions
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

# --- CORREÇÃO E MELHOR PRÁTICA ---
# 1. Copia apenas os arquivos do Composer.
COPY composer.json composer.lock ./

# 2. Adiciona o pacote `hyperf/redis` ao seu composer.json.
# O `|| true` garante que o comando não falhe se o pacote já existir.
RUN composer require hyperf/redis --with-all-dependencies || true

# 3. Instala todas as dependências (incluindo o redis agora).
# Esta camada será cacheada, acelerando builds futuros.
RUN composer install --no-dev -o --no-scripts

# 4. Copia o resto do código da aplicação.
COPY . .

# 5. Otimiza o autoloader final e executa o script do Hyperf.
RUN php bin/hyperf.php

EXPOSE 9501

ENTRYPOINT ["php", "/opt/www/bin/hyperf.php", "start"]

