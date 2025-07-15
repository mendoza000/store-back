# Docker PostgreSQL para Store Back

Este directorio contiene la configuración de Docker Compose para ejecutar PostgreSQL junto con pgAdmin para el proyecto store-back.

## 🚀 Servicios Incluidos

-   **PostgreSQL 16 Alpine**: Base de datos principal
-   **pgAdmin 4**: Interfaz web para administrar PostgreSQL (opcional)

## 📋 Requisitos Previos

-   Docker Desktop instalado
-   Docker Compose v3.8 o superior

## ⚙️ Configuración

### Variables de Entorno (.env)

Asegúrate de configurar estas variables en tu archivo `.env`:

```env
# Configuración de Base de Datos
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=store_back
DB_USERNAME=postgres
DB_PASSWORD=secret123

# Configuración de pgAdmin (opcional)
PGADMIN_EMAIL=admin@store.local
PGADMIN_PASSWORD=admin123
PGADMIN_PORT=8080
```

## 🐳 Comandos Docker

### Iniciar los servicios

```bash
docker-compose up -d
```

### Ver logs

```bash
docker-compose logs -f postgres
docker-compose logs -f pgadmin
```

### Detener los servicios

```bash
docker-compose down
```

### Reiniciar la base de datos (elimina datos)

```bash
docker-compose down -v
docker-compose up -d
```

### Conectar a PostgreSQL desde el terminal

```bash
docker-compose exec postgres psql -U postgres -d store_back
```

## 🌐 Acceso a Servicios

-   **PostgreSQL**: `localhost:5432`
-   **pgAdmin**: `http://localhost:8080`
    -   Email: admin@store.local (configurable)
    -   Password: admin123 (configurable)

## 📁 Estructura de Archivos

```
docker/
├── postgres/
│   └── init/
│       └── 01-init.sql    # Scripts de inicialización
└── README.md              # Este archivo
```

## 🔧 Scripts de Inicialización

Los archivos en `docker/postgres/init/` se ejecutan automáticamente cuando se crea el contenedor por primera vez:

-   `01-init.sql`: Configura extensiones y configuraciones básicas

## 🚀 Laravel + PostgreSQL

Después de levantar los contenedores:

1. Ejecuta las migraciones:

```bash
php artisan migrate
```

2. Ejecuta los seeders (si los tienes):

```bash
php artisan db:seed
```

## 📊 Monitoring y Salud

El contenedor PostgreSQL incluye health checks que verifican:

-   Conexión a la base de datos
-   Disponibilidad del servicio
-   Estado del usuario y base de datos configurados

## 🔐 Seguridad

Para producción, asegúrate de:

-   Cambiar las contraseñas por defecto
-   Configurar redes de Docker apropiadas
-   Habilitar SSL/TLS
-   Configurar backup automático

## 🛠️ Troubleshooting

### Error de conexión

-   Verifica que PostgreSQL esté corriendo: `docker-compose ps`
-   Revisa los logs: `docker-compose logs postgres`

### pgAdmin no carga

-   Verifica el puerto: `docker-compose ps pgadmin`
-   Revisa las variables de entorno de pgAdmin

### Datos perdidos

-   Los volúmenes persisten los datos automáticamente
-   Para backup manual: `docker-compose exec postgres pg_dump -U postgres store_back > backup.sql`
