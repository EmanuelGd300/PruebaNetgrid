#!/bin/bash

echo "🚀 Configurando Pokédex App..."

# Verificar si Docker está instalado
if ! command -v docker &> /dev/null; then
    echo "❌ Docker no está instalado. Por favor instala Docker primero."
    exit 1
fi

if ! command -v docker-compose &> /dev/null; then
    echo "❌ Docker Compose no está instalado. Por favor instala Docker Compose primero."
    exit 1
fi

echo "✅ Docker y Docker Compose encontrados"

# Construir y ejecutar contenedores
echo "📦 Construyendo contenedores..."
docker-compose build

echo "🚀 Iniciando servicios..."
docker-compose up -d

# Esperar a que MySQL esté listo
echo "⏳ Esperando a que MySQL esté listo..."
sleep 30

# Configurar backend
echo "🔧 Configurando backend..."
docker-compose exec backend cp .env.docker .env
docker-compose exec backend php artisan key:generate
docker-compose exec backend php artisan migrate --force

echo "✅ ¡Configuración completada!"
echo ""
echo "🌐 Aplicación disponible en:"
echo "   Frontend: http://localhost:5173"
echo "   Backend API: http://localhost:8000"
echo ""
echo "📚 Para ver logs: docker-compose logs -f"
echo "🛑 Para detener: docker-compose down"