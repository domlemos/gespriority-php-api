#!/bin/bash

# Script para comandos Docker Compose mais utilizados

case "$1" in
    "up")
        echo "🚀 Subindo containers..."
        docker-compose up -d
        ;;
    "down")
        echo "🛑 Parando containers..."
        docker-compose down
        ;;
    "restart")
        echo "🔄 Reiniciando containers..."
        docker-compose down && docker-compose up -d
        ;;
    "build")
        echo "🔨 Rebuilding containers..."
        docker-compose up -d --build
        ;;
    "logs")
        echo "📋 Visualizando logs..."
        docker-compose logs -f
        ;;
    "shell")
        echo "🐚 Acessando shell do container da aplicação..."
        docker-compose exec app bash
        ;;
    "artisan")
        echo "🎨 Executando comando Artisan: $2"
        docker-compose exec app php artisan $2
        ;;
    "composer")
        echo "📦 Executando comando Composer: $2"
        docker-compose exec app composer $2
        ;;
    "test")
        echo "🧪 Executando testes..."
        docker-compose exec app php artisan test
        ;;
    "fresh")
        echo "🆕 Executando fresh migration com seed..."
        docker-compose exec app php artisan migrate:fresh --seed
        ;;
    *)
        echo "GesPriority Docker Helper"
        echo ""
        echo "Uso: ./docker.sh [comando]"
        echo ""
        echo "Comandos disponíveis:"
        echo "  up       - Subir containers"
        echo "  down     - Parar containers"
        echo "  restart  - Reiniciar containers"
        echo "  build    - Rebuild containers"
        echo "  logs     - Ver logs dos containers"
        echo "  shell    - Acessar shell do container da aplicação"
        echo "  artisan  - Executar comando Artisan"
        echo "  composer - Executar comando Composer"
        echo "  test     - Executar testes"
        echo "  fresh    - Executar fresh migration com seed"
        echo ""
        echo "Exemplos:"
        echo "  ./docker.sh up"
        echo "  ./docker.sh artisan 'make:model User'"
        echo "  ./docker.sh composer 'require package/name'"
        ;;
esac
