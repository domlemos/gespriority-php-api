# GesPriority API - Laravel

Este é o projeto GesPriority API desenvolvido em Laravel com PHP 8.3.

## Requisitos

- Docker
- Docker Compose
- Git

## Configuração do Ambiente de Desenvolvimento

### 1. Clone o repositório

```bash
git clone <repository-url>
cd gespriority-api-php
```

### 2. Configuração inicial

```bash
# Copiar o arquivo de configuração
cp .env.example .env

# Subir os containers
docker-compose up -d

# Instalar dependências do Laravel
docker-compose exec app composer install

# Gerar chave da aplicação
docker-compose exec app php artisan key:generate

# Executar migrations
docker-compose exec app php artisan migrate

# Executar seeders (se houver)
docker-compose exec app php artisan db:seed
```

### 3. Acessos

- **Aplicação**: http://localhost:8080
- **PhpMyAdmin**: http://localhost:8081
  - Usuário: root
  - Senha: root

### 4. Comandos úteis

```bash
# Acessar container da aplicação
docker-compose exec app bash

# Ver logs
docker-compose logs -f

# Parar containers
docker-compose down

# Rebuild containers
docker-compose up -d --build

# Executar testes
docker-compose exec app php artisan test

# Executar comandos Artisan
docker-compose exec app php artisan <comando>
```

## Configuração do Xdebug

O Xdebug está configurado e funcionando na porta 9003. 

### Para VS Code:

1. Instale a extensão "PHP Debug"
2. Crie o arquivo `.vscode/launch.json`:

```json
{
    "version": "0.2.0",
    "configurations": [
        {
            "name": "Listen for Xdebug",
            "type": "php",
            "request": "launch",
            "port": 9003,
            "pathMappings": {
                "/var/www": "${workspaceFolder}"
            }
        }
    ]
}
```

### Para PhpStorm:

1. Vá em Settings → PHP → Debug
2. Configure Xdebug port para 9003
3. Configure Path Mappings:
   - Local path: projeto local
   - Server path: /var/www

## Estrutura do Projeto

```
.
├── app/                 # Código da aplicação
├── bootstrap/           # Arquivos de inicialização
├── config/             # Arquivos de configuração
├── database/           # Migrations, seeders e factories
├── docker/             # Configurações Docker
│   ├── nginx/          # Configuração Nginx
│   └── php/            # Configuração PHP/Xdebug
├── public/             # Arquivos públicos
├── resources/          # Views, assets
├── routes/             # Definição de rotas
├── storage/            # Logs, cache, uploads
├── tests/              # Testes automatizados
└── vendor/             # Dependências Composer
```

## Banco de Dados

- **Host**: db (dentro dos containers) ou localhost:3306 (do host)
- **Database**: gespriority_db
- **Username**: gespriority
- **Password**: password
- **Root Password**: root

## Stack Tecnológica

- **PHP**: 8.3
- **Framework**: Laravel
- **Banco de Dados**: MySQL 8.0
- **Servidor Web**: Nginx
- **Containerização**: Docker & Docker Compose
- **Debug**: Xdebug 3
- **Versionamento**: Git
