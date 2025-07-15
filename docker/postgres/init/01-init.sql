-- Script de inicialización para PostgreSQL
-- Este archivo se ejecuta automáticamente cuando se crea el contenedor por primera vez

-- Crear extensiones útiles para Laravel y aplicaciones web
CREATE EXTENSION IF NOT EXISTS "uuid-ossp";

CREATE EXTENSION IF NOT EXISTS "pgcrypto";

-- Configurar timezone por defecto
SET timezone = 'America/Mexico_City';

-- Configurar configuraciones útiles para desarrollo
ALTER SYSTEM SET shared_preload_libraries = 'pg_stat_statements';

ALTER SYSTEM SET log_statement = 'all';

ALTER SYSTEM SET log_duration = on;

ALTER SYSTEM SET log_min_duration_statement = 1000;

-- Mensaje de confirmación
DO $$ BEGIN RAISE NOTICE 'Base de datos inicializada correctamente para store-back';

END $$;