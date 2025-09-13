FROM hyperf/hyperf:8.2-alpine-v3.18-swoole

WORKDIR /opt/www

# Copia os arquivos da aplicação
COPY . .

# Instala as dependências do Composer
RUN composer install --no-dev -o

# Expõe a porta do servidor
EXPOSE 9501

# Inicia o servidor Hyperf
CMD ["php", "./bin/hyperf.php", "start"]
