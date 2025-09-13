FROM hyperf/hyperf:8.2-alpine-v3.18-swoole

WORKDIR /opt/www

COPY . .

# Instala as dependências, incluindo as de dev para rodar migrations, etc.
RUN composer install -o

EXPOSE 9501

# Nota: Para produção, você usaria Supervisor para iniciar o servidor E o consumidor AMQP.
# Para desenvolvimento, iniciamos apenas o servidor. O consumidor será iniciado conforme a necessidade.
CMD ["php", "./bin/hyperf.php", "start"]
