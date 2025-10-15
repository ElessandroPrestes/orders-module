# 📚 OrderModule

> API RESTful para modulo de pedidos **cadastro, consulta e detalhes de pedidos**, com foco em arquitetura limpa, testes automatizados.

---

## 🧩 Funcionalidades

### 📖 Pedidos
- Cadastro, listagem, exibição, edição e exclusão
- Validação de campos obrigatórios

### 🔐 Segurança
- Registro e login de usuários
- Proteção de rotas com Laravel Sanctum
- Rate Limiting: Implementar um limite de requisições (rate limiting) para proteger a API contra abuso

### 🧾 Documentação
- Swagger UI para testes e visualização dos endpoints

### 📊 Qualidade de Código
- Análise contínua com SonarQube
- Métricas de cobertura, duplicação e vulnerabilidades

---

## 🧠 Arquitetura

### 📁 Camadas
- **Controller**: recebe requisições e delega ações
- **Service**: regras de negócio centralizadas
- **Repository**: abstração do acesso a dados
- **Interface**: contratos para repositórios
- **Trait**: padronização de respostas

### 🧪 Testes
- PestPHP com TDD
- Cobertura por função, linha e arquivo
- Relatório HTML via GitLab CI

### 🔁 Boas Práticas
- Princípios SOLID
- Separação de responsabilidades
- Tratamento de exceções específicas
- Respostas semânticas e consistentes

---

## 🚀 Primeiros Passos

Essas instruções ajudam você a rodar o projeto localmente para desenvolvimento e testes.

### 📋 Pré-requisitos

- Git
- Docker
- Docker Compose

---
### 🔧 Instalação

```bash
# 1. Clone o projeto
git clone https://github.com/ElessandroPrestes/orders-module.git

# 2. Acesse o diretório
cd orders-module

# 3. (opcional) Apague o histórico Git
rm -rf .git

# 4. Crie o arquivo .env
cp .env.example .env

# 5. Suba os containers
docker compose up -d --build

# 6. Acesse o container app
docker compose exec order_app1 bash

# 7. Instale dependências PHP
composer install

# 8. Gere a chave da aplicação
php artisan key:generate

# 9. Execute migrations
php artisan migrate

# 10. Popule o banco com dados de exemplo
php artisan db:seed
```

---

## 🧪 Testes Automatizados

```bash
# Acesse o container
  docker compose exec order_app1 bash

# Dentro do container order_app1
  composer test

# Para gerar o relatório de cobertura
  composer test:coverage
```

## 📈 Relatório de Cobertura

[![CI](https://github.com/ElessandroPrestes/book-base/actions/workflows/ci.yml/badge.svg?branch=main)](https://github.com/ElessandroPrestes/book-base/actions/workflows/ci.yml)
[![Coverage](https://codecov.io/gh/ElessandroPrestes/book-base/branch/main/graph/badge.svg)](https://codecov.io/gh/ElessandroPrestes/book-base)


📈 Com esse relatório, você pode inspecionar a cobertura de testes por linha, função e arquivo — focado especialmente em `app/`.

---

> Caso precise entrar novamente no container:
> 
> ```bash
> docker compose exec book_app1 bash
> 

---

## 🔗 Endpoints da API

| Ação                  | Método HTTP | URL                                               | Descrição                                                                                       |
|-----------------------|-------------|----------------------------------------------------|--------------------------------------------------------------------------------------------------|
| Login                 | POST        | `http://localhost:8081/api/v1/login`              | Autentica usuário e retorna token<br><pre>email: usuario@bookbase.com<br>senha: 123456</pre>     |
| 🔐 Logout             | POST        | `http://localhost:8081/api/v1/logout`             | Encerra a sessão do usuário (token obrigatório)                                                 |
| Listar Pedidos         | GET         | `http://localhost:8081/api/v1/orders`              | Lista todos os pedidos cadastrados                                                               |
| 🔐 Criar Pedido        | POST        | `http://localhost:8081/api/v1/orders`              | Cadastra um novo pedido (necessita autenticaçãoautenticação)                                                 |
| Mostrar Pedido         | GET         | `http://localhost:8081/api/v1/orders/{id}`         | Exibe detalhes de um pedido pelo ID                                                              |
| 🔐 Atualizar Pedido    | PUT         | `http://localhost:8081/api/v1/orders/{id}`         | Atualiza dados de um pedido (necessita autenticação)                                             |
| 🔐 Excluir Pedido      | DELETE      | `http://localhost:8081/api/v1/orders/{id}`         | Remove um pedido do sistema (necessita autenticação)                                             |

---

> **Importante:** Para as rotas protegidas, envie o token de autenticação no header `Authorization` como:
> ```
> Authorization: Bearer {seu_token_aqui}
> ```

---

## 🌐 Acessos Locais

| Serviço             | URL                                     | Detalhes                                                                                                        |
| ------------------- | --------------------------------------- | --------------------------------------------------------------------------------------------------------------- |
| Swagger UI          | http://localhost:8081/api/documentation | Interface interativa para explorar e testar os endpoints da API                                                 |
| Laravel Telescope   | http://localhost:8081/telescope         | Análise e monitoramento da aplicação                                                                            |
| Laravel Horizon     | http://localhost:8081/horizon           | Painel de monitoramento e controle de filas com Redis                                                           |
| Adminer (PostgreSQL GUI) | http://localhost:8080                   | <pre>Sistema: `PostgreSQL`<br>Servidor: `order_postgres`<br>Usuário: `root`  <br>Senha: `developer`  <br>Base de dados: `orders_db` </pre>|
| SonarQube           | http://localhost:9000                   | <pre>Usuário: `admin`<br>Senha: `admin`</pre>                                                            |

---

## 🧰 Tecnologias Utilizadas

- PHP 8.4
- Laravel 12.x
- PostgreSQL 16
- Redis 7.2
- Nginx 1.25
- Adminer
- Docker + Docker Compose
- PestPHP 3
- Laravel Horizon
- Laravel Telescope
- L5-Swagger
- SonarQube

---

## 📦 Estrutura de Pastas

```bash
orders-module/
├── .docker/                 # Configurações específicas para Docker
├── app/                     # Lógica principal da aplicação
│   ├── Console/             # Comandos Artisan personalizados
│   ├── Interfaces/          # Interfaces e contratos que definem comportamentos esperados
│   ├── Exceptions/Api/      # Tratamento de exceções específicas da API (ex: erros de validação)
│   ├── Http/                # Controllers, Middlewares, Form Requests e recursos HTTP
│   ├── Jobs/                # Tarefas assíncronas que podem ser enfileiradas
│   ├── Models/              # Modelos Eloquent que representam as entidades do banco de dados
│   ├── Providers/           # Para registrar serviços e configurações da aplicação
│   ├── Repositories/        # Camada de abstração para acesso a dados (ex: consultas ao banco)
│   ├── Services/            # Regras de negócio e lógica de aplicação reutilizável
│   └── Traits/              # Traits reutilizáveis para funcionalidades comuns entre classes
├── bootstrap/               # Inicialização do framework
├── config/                  # Arquivos de configuração
├── database/                # Migrations e seeders
├── docker/                  # Arquivos auxiliares para containers
├── public/                  # Arquivos públicos (index.php, assets)
├── resources/               # Views, arquivos estáticos e traduções
├── routes/                  # Definição das rotas
├── storage/                 # Logs, cache e arquivos gerados
├── tests/                   # Testes automatizados
├── .env*                    # Arquivos de ambiente
├── composer.*               # Dependências PHP
├── docker-compose.yml       # Orquestração dos containers


```

---


## ✒️ Autor

Desenvolvido por [**Elessandro Prestes Macedo**](https://www.linkedin.com/in/elessandro-prestes-macedo/)


---

## 📄 Licença

Distribuído sob a licença [MIT](https://opensource.org/licenses/MIT).