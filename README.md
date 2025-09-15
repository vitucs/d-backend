# Projeto PicPay Simplificado - Arquitetura de Microsserviços

Este projeto é uma implementação de uma arquitetura de microsserviços para simular um sistema de transações financeiras, utilizando o framework Hyperf. O objetivo é demonstrar a comunicação entre serviços de forma desacoplada, utilizando um API Gateway como ponto único de entrada e mensageria para operações assíncronas.

## ✨ Features

* **Arquitetura de Microsserviços:** O projeto é modularizado em serviços independentes, cada um com sua responsabilidade única.
* **API Gateway:** Centraliza o acesso aos microsserviços, atuando como um proxy reverso para todas as requisições externas.
* **Comunicação Assíncrona:** Uso do RabbitMQ para garantir a entrega de mensagens e a execução de tarefas em segundo plano, como o envio de notificações.
* **Containerização Completa:** Todo o ambiente de desenvolvimento é gerenciado pelo Docker e Docker Compose, garantindo consistência e facilidade na configuração.
* **Alta Performance:** Construído com Hyperf 3.1, um framework PHP moderno e de alta performance baseado em corrotinas com Swoole.

## 🚀 Tecnologias Utilizadas

* **Framework PHP:** [Hyperf 3.1](https://hyperf.wiki/)
* **Banco de Dados Relacional:** [MySQL](https://www.mysql.com/)
* **Cache em Memória:** [Redis](https://redis.io/)
* **Sistema de Mensageria:** [RabbitMQ](https://www.rabbitmq.com/)
* **Containerização:** [Docker](https://www.docker.com/) & [Docker Compose](https://docs.docker.com/compose/)

## 📂 Estrutura do Projeto

O repositório está organizado com cada microsserviço em sua própria pasta, facilitando o desenvolvimento e a manutenção independente de cada um.

```
.
├── api-gateway/          # Ponto de Entrada (Gateway) para os outros serviços
├── service-notifications/  # Microsserviço para envio de notificações
├── service-transactions/   # Microsserviço que gerencia as transações
├── service-users/          # Microsserviço para gerenciamento de usuários
├── .gitignore
├── commit.sh             # Script auxiliar para padronização de commits
├── desafio-pic.drawio    # Arquivo de diagrama da arquitetura (Draw.io)
├── docker-compose.yml    # Orquestrador dos containers de todos os serviços
├── LICENSE
└── README.md
```

### Sobre os Microsserviços

* **`api-gateway`**: Responsável por ser a única porta de entrada para as requisições do cliente. Ele as roteia para o serviço interno correspondente e pode agregar respostas de múltiplos serviços.
* **`service-users`**: Gerencia todas as operações relacionadas a usuários (ex: cadastro, autenticação, consulta de saldo).
* **`service-transactions`**: Orquestra a lógica de transferência de valores entre usuários, validações e autorizações de transações.
* **`service-notifications`**: Fica responsável por enviar notificações (e-mail, SMS, etc.) aos usuários após a conclusão de uma transação, recebendo a tarefa através de uma fila no RabbitMQ.

## 📋 Pré-requisitos

Antes de começar, certifique-se de que você tem as seguintes ferramentas instaladas em seu sistema:

* [Docker Engine](https://docs.docker.com/engine/install/)
* [Docker Compose](https://docs.docker.com/compose/install/)

## 🏁 Como Rodar o Projeto

Siga os passos abaixo para configurar e executar o ambiente de desenvolvimento localmente.

**1. Clone o repositório:**

```bash
git clone <URL_DO_SEU_REPOSITORIO_AQUI>
cd <NOME_DO_DIRETORIO>
```

**2. Configure as variáveis de ambiente:**

Cada microsserviço (`api-gateway`, `service-users`, etc.) possui um arquivo `.env.example`. Você precisa criar uma cópia chamada `.env` em cada um desses diretórios e, se necessário, ajustar as variáveis.

```bash
# Exemplo para o serviço de usuários
cp service-users/.env.example service-users/.env

# Exemplo para o serviço de transações
cp service-transactions/.env.example service-transactions/.env

# Repita o processo para todos os outros serviços
```

**3. Inicie os containers:**

Na pasta raiz do projeto (onde o arquivo `docker-compose.yml` está localizado), execute o comando abaixo. Ele irá construir as imagens e iniciar todos os serviços, bancos de dados e ferramentas em background.

```bash
docker-compose up -d --build
```

**4. Execute as migrações do banco de dados:**

Para criar as tabelas necessárias no MySQL, execute os comandos de migração do Hyperf nos serviços que interagem com o banco de dados.

```bash
docker-compose exec service-users php bin/hyperf.php migrate
docker-compose exec service-transactions php bin/hyperf.php migrate
```

Pronto! A aplicação agora deve estar em execução. O **API Gateway** estará escutando na porta definida no `docker-compose.yml`.

## 🤝 Como Contribuir

Contribuições são o que tornam a comunidade de código aberto um lugar incrível para aprender, inspirar e criar. Qualquer contribuição que você fizer será **muito apreciada**.

1.  Faça um **Fork** do projeto
2.  Crie uma **Branch** para sua Feature (`git checkout -b feature/AmazingFeature`)
3.  Faça o **Commit** de suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4.  Faça o **Push** para a Branch (`git push origin feature/AmazingFeature`)
5.  Abra um **Pull Request**

## 📄 Licença

Distribuído sob a Licença MIT. Veja o arquivo `LICENSE` para mais informações.