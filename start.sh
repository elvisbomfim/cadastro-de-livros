#!/bin/bash

# Script para iniciar o ambiente Docker e executar as migrations

set -e

echo "🚀 Iniciando containers Docker..."
docker compose up -d

echo "⏳ Aguardando containers ficarem prontos..."

# Lê CONTAINER_NAME antes de usar (se ainda não foi lido)
if [ -z "$CONTAINER_NAME" ]; then
    if [ -f .env ]; then
        CONTAINER_NAME=$(grep -E "^CONTAINER_NAME=" .env 2>/dev/null | cut -d '=' -f2 | tr -d '"' | tr -d "'" || echo "cadastro-de-livros")
        CONTAINER_NAME=$(echo "$CONTAINER_NAME" | xargs)
    fi
    CONTAINER_NAME=${CONTAINER_NAME:-cadastro-de-livros}
fi
DB_CONTAINER_NAME="${CONTAINER_NAME}-db"

# Aguarda o MySQL estar pronto
echo "   Aguardando MySQL (container: ${DB_CONTAINER_NAME})..."
for i in {1..30}; do
    if docker compose exec -T db mysqladmin ping -h localhost --silent 2>/dev/null; then
        break
    fi
    sleep 2
done

# Aguarda o container app estar pronto
echo "   Aguardando container app..."
for i in {1..30}; do
    if docker compose ps app | grep -q "Up"; then
        break
    fi
    sleep 1
done
sleep 5

echo "📦 Instalando dependências do Composer..."
docker compose exec -T app composer install --no-interaction

# Verifica se npm está instalado localmente
if ! command -v npm &> /dev/null; then
    echo "⚠️  NPM não encontrado localmente!"
    echo "   Por favor, instale o Node.js e npm localmente (recomendado usar NVM)"
    echo "   Exemplo: nvm install 20.19.4 && nvm use 20.19.4"
    exit 1
fi

echo "📦 Instalando dependências do NPM (localmente)..."
npm install

# Verifica se .env existe, se não existir, copia do .env.example
if [ ! -f .env ]; then
    echo "📝 Arquivo .env não encontrado, copiando de .env.example..."
    cp .env.example .env
fi

# Lê variáveis do .env
if [ -f .env ]; then
    # Lê NGINX_PORT do .env de forma segura
    NGINX_PORT=$(grep -E "^NGINX_PORT=" .env 2>/dev/null | cut -d '=' -f2 | tr -d '"' | tr -d "'" || echo "8080")
    # Remove espaços em branco
    NGINX_PORT=$(echo "$NGINX_PORT" | xargs)
    
    # Lê CONTAINER_NAME do .env de forma segura
    CONTAINER_NAME=$(grep -E "^CONTAINER_NAME=" .env 2>/dev/null | cut -d '=' -f2 | tr -d '"' | tr -d "'" || echo "cadastro-de-livros")
    # Remove espaços em branco
    CONTAINER_NAME=$(echo "$CONTAINER_NAME" | xargs)
fi
NGINX_PORT=${NGINX_PORT:-8080}
CONTAINER_NAME=${CONTAINER_NAME:-cadastro-de-livros}

# Define o nome do container do banco de dados
DB_CONTAINER_NAME="${CONTAINER_NAME}-db"

echo "🔑 Gerando chave da aplicação Laravel..."
# Verifica se a chave já existe no .env
if ! grep -q "APP_KEY=base64:" .env 2>/dev/null; then
    echo "   Gerando nova chave..."
    docker compose exec -T app php artisan key:generate --force
else
    echo "   Chave já existe, pulando geração..."
fi

echo "🗄️  Executando migrations..."
docker compose exec -T app php artisan migrate --force

echo "✅ Ambiente configurado com sucesso!"
echo ""
echo "📝 Para acessar a aplicação:"
echo "   - Frontend: http://localhost:${NGINX_PORT}"
echo "   - API: http://localhost:${NGINX_PORT}/api"
echo ""
echo "📋 Comandos úteis:"
echo "   - Ver logs: docker compose logs -f"
echo "   - Parar containers: docker compose down"
echo "   - Executar testes: docker compose exec app php artisan test"
echo "   - Acessar container: docker compose exec app bash"
echo "   - Compilar assets (dev): npm run dev"
echo "   - Compilar assets (prod): npm run build"

