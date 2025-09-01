#!/bin/bash

# Script de inicialização do projeto GesPriority

echo "🚀 Iniciando configuração do projeto GesPriority..."

# Verificar se Docker está instalado
if ! command -v docker &> /dev/null; then
    echo "❌ Docker não está instalado. Por favor, instale o Docker primeiro."
    exit 1
fi

# Verificar se Docker Compose está instalado
if ! command -v docker-compose &> /dev/null; then
    echo "❌ Docker Compose não está instalado. Por favor, instale o Docker Compose primeiro."
    exit 1
fi

# Criar arquivo .env se não existir
if [ ! -f .env ]; then
    echo "📝 Copiando arquivo .env.example para .env..."
    cp .env.example .env
else
    echo "✅ Arquivo .env já existe"
fi

# Subir containers
echo "🐳 Subindo containers Docker..."
docker-compose up -d

# Aguardar containers iniciarem
echo "⏳ Aguardando containers iniciarem..."
sleep 10

# Verificar se o Laravel já está instalado
if [ ! -f "vendor/autoload.php" ]; then
    echo "📦 Criando projeto Laravel..."
    docker-compose exec app composer create-project --prefer-dist laravel/laravel .
    
    # Configurar permissões
    echo "🔐 Configurando permissões..."
    docker-compose exec app chown -R gespriority:gespriority /var/www
    docker-compose exec app chmod -R 755 /var/www/storage
    docker-compose exec app chmod -R 755 /var/www/bootstrap/cache
else
    echo "📦 Instalando dependências do Composer..."
    docker-compose exec app composer install
fi

# Gerar chave da aplicação
echo "🔑 Gerando chave da aplicação..."
docker-compose exec app php artisan key:generate

# Executar migrations
echo "🗄️  Executando migrations..."
docker-compose exec app php artisan migrate

echo ""
echo "✅ Configuração concluída!"
echo ""
echo "🌐 Acessos:"
echo "   Aplicação: http://localhost:8080"
echo "   PhpMyAdmin: http://localhost:8081 (usuário: root, senha: root)"
echo ""
echo "📚 Comandos úteis:"
echo "   docker-compose logs -f              # Ver logs"
echo "   docker-compose exec app bash        # Acessar container"
echo "   docker-compose exec app php artisan # Executar comandos Artisan"
echo ""
