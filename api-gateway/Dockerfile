FROM hyperf/hyperf:8.2-alpine-v3.18-swoole

WORKDIR /opt/www

COPY . .

# Instala as dependências de produção
RUN composer install --no-dev -o

# Expõe a porta principal da aplicação
EXPOSE 9501

# Inicia o servidor Hyperf
CMD ["php", "./bin/hyperf.php", "start"]