#!/bin/bash

# Script de utilidades para Docker Compose con PostgreSQL
# Uso: ./docker-commands.sh [comando]

case "$1" in
    "start"|"up")
        echo "🚀 Iniciando servicios Docker..."
        docker-compose up -d
        echo "✅ Servicios iniciados. PostgreSQL disponible en localhost:5432"
        echo "🌐 pgAdmin disponible en http://localhost:8080"
        ;;
    
    "stop"|"down")
        echo "🛑 Deteniendo servicios Docker..."
        docker-compose down
        echo "✅ Servicios detenidos"
        ;;
    
    "restart")
        echo "🔄 Reiniciando servicios..."
        docker-compose down
        docker-compose up -d
        echo "✅ Servicios reiniciados"
        ;;
    
    "logs")
        if [ -z "$2" ]; then
            echo "📋 Mostrando logs de todos los servicios..."
            docker-compose logs -f
        else
            echo "📋 Mostrando logs de $2..."
            docker-compose logs -f "$2"
        fi
        ;;
    
    "status"|"ps")
        echo "📊 Estado de los contenedores:"
        docker-compose ps
        ;;
    
    "connect"|"psql")
        echo "🔗 Conectando a PostgreSQL..."
        docker-compose exec postgres psql -U postgres -d store_back
        ;;
    
    "backup")
        BACKUP_FILE="backup_$(date +%Y%m%d_%H%M%S).sql"
        echo "💾 Creando backup: $BACKUP_FILE"
        docker-compose exec postgres pg_dump -U postgres store_back > "$BACKUP_FILE"
        echo "✅ Backup creado: $BACKUP_FILE"
        ;;
    
    "reset")
        echo "⚠️  ¿Estás seguro de que quieres eliminar todos los datos? (y/N)"
        read -r response
        if [[ "$response" =~ ^([yY][eE][sS]|[yY])$ ]]; then
            echo "🗑️  Eliminando volúmenes y reiniciando..."
            docker-compose down -v
            docker-compose up -d
            echo "✅ Base de datos reiniciada"
        else
            echo "❌ Operación cancelada"
        fi
        ;;
    
    "migrate")
        echo "🏗️  Ejecutando migraciones de Laravel..."
        if command -v php &> /dev/null; then
            php artisan migrate
        else
            echo "❌ PHP no encontrado. Ejecuta manualmente: php artisan migrate"
        fi
        ;;
    
    "seed")
        echo "🌱 Ejecutando seeders de Laravel..."
        if command -v php &> /dev/null; then
            php artisan db:seed
        else
            echo "❌ PHP no encontrado. Ejecuta manualmente: php artisan db:seed"
        fi
        ;;
    
    "fresh")
        echo "🆕 Reiniciando base de datos con migraciones frescas..."
        if command -v php &> /dev/null; then
            php artisan migrate:fresh --seed
        else
            echo "❌ PHP no encontrado. Ejecuta manualmente: php artisan migrate:fresh --seed"
        fi
        ;;
    
    "help"|*)
        echo "🐳 Docker Compose para Store Back - Comandos disponibles:"
        echo ""
        echo "  start|up     - Iniciar servicios"
        echo "  stop|down    - Detener servicios"
        echo "  restart      - Reiniciar servicios"
        echo "  logs [service] - Ver logs (opcional: especificar servicio)"
        echo "  status|ps    - Ver estado de contenedores"
        echo "  connect|psql - Conectar a PostgreSQL"
        echo "  backup       - Crear backup de la base de datos"
        echo "  reset        - ⚠️ Eliminar datos y reiniciar"
        echo "  migrate      - Ejecutar migraciones de Laravel"
        echo "  seed         - Ejecutar seeders de Laravel"
        echo "  fresh        - Migrate:fresh con seed"
        echo "  help         - Mostrar esta ayuda"
        echo ""
        echo "Ejemplos:"
        echo "  ./docker-commands.sh start"
        echo "  ./docker-commands.sh logs postgres"
        echo "  ./docker-commands.sh backup"
        ;;
esac 