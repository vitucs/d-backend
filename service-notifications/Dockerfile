FROM hyperf/hyperf:8.2-alpine-v3.18-swoole

WORKDIR /opt/www

COPY . .

# Instala as dependências de produção
RUN composer install --no-dev -o

# Comando para iniciar o consumidor da fila, e não um servidor web
CMD ["php", "./bin/hyperf.php", "amqp:consume", "--name=notifications", "--pool=default", "-m", "128"]