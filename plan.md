# Plan de Desarrollo E-commerce Backend API

## Laravel | Sistema Modular Multi-Cliente (Solo Backend)

---

## 🎯 **Objetivos del Proyecto**

-   **E-commerce API replicable**: Backend robusto que se puede desplegar para múltiples clientes
-   **Sistema modular**: Habilitar/deshabilitar características según configuración
-   **APIs completas**: Productos, variantes, pedidos, cupones, categorías
-   **Pagos manuales**: Sistema adaptado para Venezuela (captura de comprobantes)
-   **Panel de administración API**: Endpoints completos para gestión del e-commerce
-   **Arquitectura API-First**: Backend independiente que puede servir múltiples frontends

---

## 📋 **Fase 1: Configuración e Infraestructura Base**

### 1.1 Configuración del Backend Laravel

-   [ ] **Configurar environment variables modulares**

    -   [ ] Crear archivo `config/modules.php` para configuración de características
    -   [ ] Definir variables de entorno para cada módulo (cupones, variantes, etc.)
    -   [ ] Crear middleware para verificar módulos habilitados
    -   [ ] Crear comando artisan para generar configuración por cliente

-   [ ] **Configurar API y CORS**

    -   [ ] Instalar Laravel Sanctum para autenticación API
    -   [ ] Configurar CORS para permitir requests desde cualquier frontend
    -   [ ] Crear estructura base de rutas API (`/api/v1/`)
    -   [ ] Configurar rate limiting por rutas
    -   [ ] Implementar respuestas JSON estandarizadas

-   [ ] **Configurar base de datos**
    -   [ ] Actualizar configuración de base de datos
    -   [ ] Configurar migraciones base
    -   [ ] Crear seeders para datos iniciales
    -   [ ] Configurar índices para optimización

### 1.2 Configuración de Documentación API

-   [ ] **Documentación automática**
    -   [ ] Instalar y configurar Laravel Swagger/OpenAPI
    -   [ ] Configurar documentación automática de endpoints
    -   [ ] Crear ejemplos de requests/responses
    -   [ ] Configurar entorno de testing de API

---

## 📋 **Fase 2: Sistema de Autenticación y Usuarios**

### 2.1 Modelos y Migraciones

-   [ ] **Extender modelo User**

    -   [ ] Agregar campos: role, status, phone, avatar, email_verified_at
    -   [ ] Crear relaciones con otros modelos
    -   [ ] Implementar soft deletes
    -   [ ] Crear factory para testing

-   [ ] **Sistema de roles y permisos**
    -   [ ] Crear modelo Role con permisos
    -   [ ] Implementar middleware de autorización
    -   [ ] Definir roles: admin, customer, moderator
    -   [ ] Crear seeder para roles iniciales

### 2.2 API Endpoints de Autenticación

-   [ ] **AuthController**

    -   [ ] `POST /api/v1/auth/login` - Login con email/password
    -   [ ] `POST /api/v1/auth/register` - Registro de usuarios
    -   [ ] `POST /api/v1/auth/logout` - Logout y revoke token
    -   [ ] `POST /api/v1/auth/refresh` - Refresh token
    -   [ ] `GET /api/v1/auth/me` - Obtener usuario autenticado
    -   [ ] `PUT /api/v1/auth/profile` - Actualizar perfil

-   [ ] **Password Reset API**
    -   [ ] `POST /api/v1/auth/forgot-password` - Solicitar reset
    -   [ ] `POST /api/v1/auth/reset-password` - Confirmar reset
    -   [ ] `POST /api/v1/auth/change-password` - Cambiar password

### 2.3 Middleware y Validaciones

-   [ ] **Crear middleware personalizados**
    -   [ ] `EnsureModuleEnabled` - Verificar módulos habilitados
    -   [ ] `AdminOnly` - Solo administradores
    -   [ ] `CustomerOnly` - Solo clientes
    -   [ ] `ValidateApiKey` - Para integraciones externas

---

## 📋 **Fase 3: Sistema de Productos y Categorías**

### 3.1 Modelos de Productos

-   [ ] **Modelo Category**

    -   [ ] Campos: name, slug, description, image, parent_id, status, sort_order
    -   [ ] Relaciones jerárquicas (parent/children)
    -   [ ] Scope para categorías activas
    -   [ ] Mutators para slug automático

-   [ ] **Modelo Product**

    -   [ ] Campos: name, slug, description, short_description, sku, status
    -   [ ] Campos: price, compare_price, cost_price, track_quantity
    -   [ ] Relaciones: categories, images, variants, attributes
    -   [ ] Scopes: active, featured, on_sale

-   [ ] **Modelo ProductImage**
    -   [ ] Campos: product_id, url, alt_text, sort_order, is_primary
    -   [ ] Relación con Product
    -   [ ] Mutators para URLs completas

### 3.2 Sistema de Variantes Flexible

-   [ ] **Modelo Attribute**

    -   [ ] Campos: name, slug, type (select, color, size, text)
    -   [ ] Para diferentes tipos de productos (ropa, tech, etc.)
    -   [ ] Configuración de visualización

-   [ ] **Modelo AttributeValue**

    -   [ ] Campos: attribute_id, value, color_code, image
    -   [ ] Para valores específicos (Rojo, XL, 128GB, etc.)
    -   [ ] Soporte para códigos de color y imágenes

-   [ ] **Modelo ProductVariant**
    -   [ ] Campos: product_id, sku, price, compare_price, quantity
    -   [ ] Campos: weight, dimensions, status
    -   [ ] Relación many-to-many con AttributeValue
    -   [ ] Lógica para generar combinaciones

### 3.3 API Endpoints de Productos

-   [ ] **ProductController (Público)**

    -   [ ] `GET /api/v1/products` - Lista paginada con filtros
    -   [ ] `GET /api/v1/products/{slug}` - Detalle de producto
    -   [ ] `GET /api/v1/products/{id}/variants` - Variantes del producto
    -   [ ] `GET /api/v1/products/search` - Búsqueda de productos
    -   [ ] `GET /api/v1/products/featured` - Productos destacados

-   [ ] **CategoryController (Público)**

    -   [ ] `GET /api/v1/categories` - Árbol de categorías
    -   [ ] `GET /api/v1/categories/{slug}` - Detalle de categoría
    -   [ ] `GET /api/v1/categories/{slug}/products` - Productos por categoría

-   [ ] **Admin ProductController**
    -   [ ] `GET /api/v1/admin/products` - Lista admin con filtros
    -   [ ] `POST /api/v1/admin/products` - Crear producto
    -   [ ] `PUT /api/v1/admin/products/{id}` - Actualizar producto
    -   [ ] `DELETE /api/v1/admin/products/{id}` - Eliminar producto
    -   [ ] `POST /api/v1/admin/products/{id}/images` - Subir imágenes
    -   [ ] `POST /api/v1/admin/products/{id}/variants` - Crear variantes

### 3.4 Características Avanzadas

-   [ ] **Sistema de búsqueda**

    -   [ ] Implementar Laravel Scout con Algolia/Meilisearch
    -   [ ] Búsqueda por nombre, descripción, SKU
    -   [ ] Filtros por precio, categoría, atributos
    -   [ ] Autocomplete y sugerencias

-   [ ] **Gestión de inventario**
    -   [ ] Tracking de stock por variante
    -   [ ] Reserva temporal de stock
    -   [ ] Alertas de stock bajo
    -   [ ] Historial de movimientos de inventario

---

## 📋 **Fase 4: Sistema de Carrito y Pedidos**

### 4.1 Modelos de Carrito

-   [ ] **Modelo Cart**

    -   [ ] Campos: user_id, session_id, status, expires_at
    -   [ ] Soporte para usuarios guest
    -   [ ] Limpieza automática de carritos expirados

-   [ ] **Modelo CartItem**
    -   [ ] Campos: cart_id, product_variant_id, quantity, price
    -   [ ] Validaciones de stock disponible
    -   [ ] Cálculos automáticos de totales

### 4.2 Sistema de Pedidos

-   [ ] **Modelo Order**

    -   [ ] Campos: order_number, user_id, status, total, subtotal
    -   [ ] Campos: tax_amount, shipping_amount, discount_amount
    -   [ ] Campos de envío: shipping_address, billing_address
    -   [ ] Estados: pending, paid, processing, shipped, delivered, cancelled

-   [ ] **Modelo OrderItem**

    -   [ ] Campos: order_id, product_variant_id, quantity, price
    -   [ ] Snapshot de información del producto al momento del pedido
    -   [ ] Campos: product_name, variant_info, product_image

-   [ ] **Modelo OrderStatusHistory**
    -   [ ] Campos: order_id, status, notes, created_by, created_at
    -   [ ] Tracking completo de cambios de estado
    -   [ ] Notificaciones automáticas

### 4.3 API Endpoints de Carrito

-   [ ] **CartController**
    -   [ ] `GET /api/v1/cart` - Obtener carrito actual
    -   [ ] `POST /api/v1/cart/items` - Agregar item al carrito
    -   [ ] `PUT /api/v1/cart/items/{id}` - Actualizar cantidad
    -   [ ] `DELETE /api/v1/cart/items/{id}` - Remover item
    -   [ ] `DELETE /api/v1/cart` - Vaciar carrito
    -   [ ] `POST /api/v1/cart/merge` - Merge carrito guest con usuario

### 4.4 API Endpoints de Pedidos

-   [ ] **OrderController**

    -   [ ] `POST /api/v1/orders` - Crear pedido desde carrito
    -   [ ] `GET /api/v1/orders` - Lista de pedidos del usuario
    -   [ ] `GET /api/v1/orders/{orderNumber}` - Detalle del pedido
    -   [ ] `PUT /api/v1/orders/{id}/cancel` - Cancelar pedido

-   [ ] **Admin OrderController**
    -   [ ] `GET /api/v1/admin/orders` - Lista admin con filtros
    -   [ ] `PUT /api/v1/admin/orders/{id}/status` - Cambiar estado
    -   [ ] `GET /api/v1/admin/orders/stats` - Estadísticas de pedidos

---

## 📋 **Fase 5: Sistema de Pagos Manual (Venezuela)**

### 5.1 Modelos de Pago

-   [ ] **Modelo PaymentMethod**

    -   [ ] Campos: name, type, account_info, instructions, status
    -   [ ] Tipos: bank_transfer, mobile_payment, cash, crypto
    -   [ ] Información bancaria del comercio

-   [ ] **Modelo Payment**

    -   [ ] Campos: order_id, payment_method_id, amount, reference
    -   [ ] Campos: receipt_image, notes, status, verified_at, verified_by
    -   [ ] Estados: pending, verified, rejected, refunded

-   [ ] **Modelo PaymentVerification**
    -   [ ] Campos: payment_id, admin_id, action, notes, created_at
    -   [ ] Historial de verificaciones
    -   [ ] Razones de rechazo

### 5.2 API Endpoints de Pagos

-   [ ] **PaymentMethodController (Público)**

    -   [ ] `GET /api/v1/payment-methods` - Métodos de pago disponibles
    -   [ ] `GET /api/v1/payment-methods/{id}` - Detalle del método

-   [ ] **PaymentController**

    -   [ ] `POST /api/v1/orders/{id}/payments` - Reportar pago
    -   [ ] `PUT /api/v1/payments/{id}` - Actualizar comprobante
    -   [ ] `GET /api/v1/payments/{id}` - Estado del pago

-   [ ] **Admin PaymentController**
    -   [ ] `GET /api/v1/admin/payments` - Lista de pagos pendientes
    -   [ ] `POST /api/v1/admin/payments/{id}/verify` - Aprobar pago
    -   [ ] `POST /api/v1/admin/payments/{id}/reject` - Rechazar pago
    -   [ ] `GET /api/v1/admin/payments/stats` - Estadísticas de pagos

### 5.3 Gestión de Archivos

-   [ ] **Upload de comprobantes**
    -   [ ] Configurar storage para comprobantes
    -   [ ] Validación de tipos de archivo (jpg, png, pdf)
    -   [ ] Compresión automática de imágenes
    -   [ ] Generación de thumbnails

---

## 📋 **Fase 6: Sistema de Cupones y Promociones (Módulo)**

### 6.1 Modelos de Cupones

-   [ ] **Modelo Coupon**

    -   [ ] Campos: code, type, value, minimum_amount, maximum_discount
    -   [ ] Campos: usage_limit, used_count, starts_at, expires_at
    -   [ ] Tipos: percentage, fixed_amount, free_shipping
    -   [ ] Restricciones: categories, products, users

-   [ ] **Modelo CouponUsage**
    -   [ ] Campos: coupon_id, user_id, order_id, used_at
    -   [ ] Tracking de uso de cupones
    -   [ ] Prevención de uso múltiple

### 6.2 API Endpoints de Cupones

-   [ ] **CouponController**

    -   [ ] `POST /api/v1/coupons/validate` - Validar cupón
    -   [ ] `POST /api/v1/cart/apply-coupon` - Aplicar cupón al carrito
    -   [ ] `DELETE /api/v1/cart/coupon` - Remover cupón del carrito

-   [ ] **Admin CouponController**
    -   [ ] `GET /api/v1/admin/coupons` - Lista de cupones
    -   [ ] `POST /api/v1/admin/coupons` - Crear cupón
    -   [ ] `PUT /api/v1/admin/coupons/{id}` - Actualizar cupón
    -   [ ] `DELETE /api/v1/admin/coupons/{id}` - Eliminar cupón
    -   [ ] `GET /api/v1/admin/coupons/{id}/usage` - Estadísticas de uso

### 6.3 Lógica de Descuentos

-   [ ] **Service CouponService**
    -   [ ] Validación de condiciones del cupón
    -   [ ] Cálculo de descuentos aplicables
    -   [ ] Verificación de límites de uso
    -   [ ] Aplicación automática de mejores descuentos

---

## 📋 **Fase 7: Panel de Administración (APIs)**

### 7.1 Dashboard y Estadísticas

-   [ ] **DashboardController**

    -   [ ] `GET /api/v1/admin/dashboard` - Métricas generales
    -   [ ] `GET /api/v1/admin/dashboard/sales` - Estadísticas de ventas
    -   [ ] `GET /api/v1/admin/dashboard/products` - Productos más vendidos
    -   [ ] `GET /api/v1/admin/dashboard/customers` - Estadísticas de clientes

-   [ ] **ReportController**
    -   [ ] `GET /api/v1/admin/reports/sales` - Reporte de ventas
    -   [ ] `GET /api/v1/admin/reports/inventory` - Reporte de inventario
    -   [ ] `GET /api/v1/admin/reports/customers` - Reporte de clientes
    -   [ ] `POST /api/v1/admin/reports/export` - Exportar reportes

### 7.2 Gestión de Usuarios

-   [ ] **Admin UserController**
    -   [ ] `GET /api/v1/admin/users` - Lista de usuarios con filtros
    -   [ ] `GET /api/v1/admin/users/{id}` - Detalle de usuario
    -   [ ] `PUT /api/v1/admin/users/{id}` - Actualizar usuario
    -   [ ] `PUT /api/v1/admin/users/{id}/status` - Cambiar estado
    -   [ ] `GET /api/v1/admin/users/{id}/orders` - Pedidos del usuario

### 7.3 Configuración de la Tienda

-   [ ] **SettingsController**
    -   [ ] `GET /api/v1/admin/settings` - Configuración actual
    -   [ ] `PUT /api/v1/admin/settings` - Actualizar configuración
    -   [ ] `GET /api/v1/admin/settings/modules` - Módulos habilitados
    -   [ ] `PUT /api/v1/admin/settings/modules` - Configurar módulos

---

## 📋 **Fase 8: Sistema de Configuración Modular**

### 8.1 Configuración Dinámica

-   [ ] **Archivo config/modules.php**

    -   [ ] Configuración de módulos disponibles
    -   [ ] Variables de entorno por cliente
    -   [ ] Sistema de feature flags
    -   [ ] Configuración de límites por plan

-   [ ] **Service ModuleService**
    -   [ ] Verificación de módulos habilitados
    -   [ ] Carga dinámica de configuración
    -   [ ] Cache de configuración de módulos
    -   [ ] Validación de permisos por módulo

### 8.2 Middleware y Rutas Modulares

-   [ ] **Middleware ModuleEnabled**

    -   [ ] Verificación automática por ruta
    -   [ ] Respuestas 404 para módulos deshabilitados
    -   [ ] Logging de intentos de acceso no autorizados

-   [ ] **Configuración de rutas dinámicas**
    -   [ ] Registro condicional de rutas
    -   [ ] Agrupación por módulos
    -   [ ] Documentación automática según módulos

### 8.3 API de Configuración

-   [ ] **ConfigController**
    -   [ ] `GET /api/v1/config` - Configuración pública del cliente
    -   [ ] `GET /api/v1/config/modules` - Módulos habilitados
    -   [ ] `GET /api/v1/config/features` - Features disponibles

---

## 📋 **Fase 9: Características Adicionales Recomendadas**

### 9.1 Lista de Deseos (Módulo)

-   [ ] **Modelo Wishlist**

    -   [ ] Campos: user_id, product_id, created_at
    -   [ ] Relaciones con Product y User
    -   [ ] Validaciones de duplicados

-   [ ] **API Wishlist**
    -   [ ] `GET /api/v1/wishlist` - Lista de deseos del usuario
    -   [ ] `POST /api/v1/wishlist` - Agregar producto
    -   [ ] `DELETE /api/v1/wishlist/{productId}` - Remover producto
    -   [ ] `POST /api/v1/wishlist/move-to-cart` - Mover al carrito

### 9.2 Reviews y Calificaciones (Módulo)

-   [ ] **Modelo ProductReview**

    -   [ ] Campos: product_id, user_id, rating, title, comment
    -   [ ] Campos: status, helpful_count, created_at
    -   [ ] Validación: un review por usuario por producto

-   [ ] **API Reviews**
    -   [ ] `GET /api/v1/products/{id}/reviews` - Reviews del producto
    -   [ ] `POST /api/v1/products/{id}/reviews` - Crear review
    -   [ ] `PUT /api/v1/reviews/{id}/helpful` - Marcar como útil
    -   [ ] `GET /api/v1/admin/reviews` - Gestión admin de reviews

### 9.3 Notificaciones

-   [ ] **Sistema de notificaciones**

    -   [ ] Laravel Notifications con múltiples canales
    -   [ ] Email notifications para eventos importantes
    -   [ ] WhatsApp notifications (opcional)
    -   [ ] Push notifications para admins

-   [ ] **Eventos automatizados**
    -   [ ] Order created, paid, shipped, delivered
    -   [ ] Payment verified/rejected
    -   [ ] Low stock alerts
    -   [ ] New review submitted

### 9.4 Inventario Avanzado (Módulo)

-   [ ] **Modelo InventoryMovement**

    -   [ ] Campos: product_variant_id, type, quantity, reference
    -   [ ] Tipos: sale, restock, adjustment, return
    -   [ ] Tracking completo de movimientos

-   [ ] **API Inventario**
    -   [ ] `GET /api/v1/admin/inventory` - Estado actual del inventario
    -   [ ] `POST /api/v1/admin/inventory/adjustment` - Ajuste manual
    -   [ ] `GET /api/v1/admin/inventory/movements` - Historial de movimientos
    -   [ ] `GET /api/v1/admin/inventory/alerts` - Alertas de stock bajo

---

## 📋 **Fase 10: Testing, Optimización y Deploy**

### 10.1 Testing Completo

-   [ ] **Unit Tests**

    -   [ ] Tests para todos los modelos
    -   [ ] Tests para services y helpers
    -   [ ] Tests para validaciones y reglas de negocio

-   [ ] **Feature Tests**

    -   [ ] Tests para todos los endpoints API
    -   [ ] Tests de autenticación y autorización
    -   [ ] Tests de flujos completos (registro → compra → pago)

-   [ ] **Performance Tests**
    -   [ ] Load testing de APIs críticas
    -   [ ] Tests de consultas N+1
    -   [ ] Benchmarking de endpoints

### 10.2 Optimización

-   [ ] **Performance**

    -   [ ] Eager loading optimizado
    -   [ ] Índices de base de datos
    -   [ ] Cache de consultas frecuentes
    -   [ ] Optimización de imágenes

-   [ ] **Security**
    -   [ ] Rate limiting avanzado
    -   [ ] Validación estricta de inputs
    -   [ ] CSRF protection
    -   [ ] SQL injection prevention

### 10.3 Deploy y Configuración

-   [ ] **Containerización**

    -   [ ] Dockerfile optimizado para producción
    -   [ ] Docker-compose para desarrollo
    -   [ ] Scripts de deploy automatizado
    -   [ ] Health checks y monitoring

-   [ ] **Documentación**
    -   [ ] API documentation completa (Swagger/OpenAPI)
    -   [ ] README de instalación y configuración
    -   [ ] Guía de configuración de módulos por cliente
    -   [ ] Manual de API para frontend developers

---

## 🛠 **Stack Tecnológico Backend**

### Framework y Base

-   **Framework**: Laravel 11
-   **PHP**: PHP 8.2+
-   **Base de datos**: PostgreSQL 15+
-   **Cache**: Redis
-   **Queue**: Redis/Database

### Paquetes Recomendados

-   **Autenticación**: Laravel Sanctum
-   **API Documentation**: Laravel Swagger (darkaonline/l5-swagger)
-   **Image Processing**: Intervention Image
-   **Search**: Laravel Scout + Meilisearch
-   **Testing**: PHPUnit + Pest
-   **Code Quality**: PHP CS Fixer, PHPStan

### Storage y Files

-   **File Storage**: AWS S3 / DigitalOcean Spaces
-   **Image Optimization**: Tinify/TinyPNG
-   **Backup**: Laravel Backup (spatie/laravel-backup)

---

## 📝 **Estructura de Respuestas API**

```php
// Respuesta exitosa
{
    "success": true,
    "data": {...},
    "message": "Operation completed successfully",
    "meta": {
        "pagination": {...},
        "total": 150
    }
}

// Respuesta de error
{
    "success": false,
    "error": {
        "code": "VALIDATION_ERROR",
        "message": "The given data was invalid",
        "details": {
            "field": ["validation message"]
        }
    }
}
```

---

## 📝 **Configuración Modular Ejemplo**

```php
// config/modules.php
return [
    'coupons' => env('MODULE_COUPONS', true),
    'product_variants' => env('MODULE_VARIANTS', true),
    'wishlist' => env('MODULE_WISHLIST', false),
    'reviews' => env('MODULE_REVIEWS', false),
    'multi_payment_methods' => env('MODULE_MULTI_PAYMENT', true),
    'advanced_inventory' => env('MODULE_INVENTORY', false),
    'notifications' => [
        'email' => env('MODULE_EMAIL_NOTIFICATIONS', true),
        'whatsapp' => env('MODULE_WHATSAPP_NOTIFICATIONS', false),
        'push' => env('MODULE_PUSH_NOTIFICATIONS', false),
    ],
];

// .env para cliente específico
MODULE_COUPONS=true
MODULE_VARIANTS=true
MODULE_WISHLIST=false
MODULE_REVIEWS=true
MODULE_INVENTORY=false
MODULE_EMAIL_NOTIFICATIONS=true
MODULE_WHATSAPP_NOTIFICATIONS=true
```

---

## 🎯 **Cronograma Estimado (Solo Backend)**

-   **Fase 1**: 1 semana (Configuración base)
-   **Fase 2**: 1 semana (Autenticación y usuarios)
-   **Fase 3**: 2.5 semanas (Productos y categorías)
-   **Fase 4**: 1.5 semanas (Carrito y pedidos)
-   **Fase 5**: 1 semana (Pagos manuales)
-   **Fase 6**: 0.5 semanas (Cupones)
-   **Fase 7**: 1 semana (Admin APIs)
-   **Fase 8**: 0.5 semanas (Sistema modular)
-   **Fase 9**: 1.5 semanas (Características extra)
-   **Fase 10**: 0.5 semanas (Testing y deploy)

**Total estimado**: 2-2.5 meses de desarrollo backend

---

## 📋 **Próximos Pasos Inmediatos**

1. **Configurar el entorno base** (Sanctum, CORS, modules.php)
2. **Crear la estructura de base de datos** (migraciones principales)
3. **Implementar autenticación API** (login, register, middleware)
4. **Desarrollar APIs de productos** (CRUD completo con variantes)
5. **Sistema de carrito y pedidos** (flujo completo de compra)
