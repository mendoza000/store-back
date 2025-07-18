<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

/**
 * @OA\Info(
 *     title="E-commerce API",
 *     version="1.0.0",
 *     description="API REST para sistema de e-commerce con autenticación y gestión de recursos.",
 *     @OA\Contact(
 *         email="api@ecommerce.com",
 *         name="E-commerce API Support"
 *     ),
 *     @OA\License(
 *         name="MIT",
 *         url="https://opensource.org/licenses/MIT"
 *     )
 * )
 * 
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="Servidor de desarrollo"
 * )
 * 
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="apiKey",
 *     in="header",
 *     name="Authorization",
 *     description="Token de autenticación Sanctum. Formato: 'Bearer {token}'"
 * )
 * 
 * @OA\Tag(
 *     name="Authentication",
 *     description="Endpoints de autenticación y gestión de usuarios"
 * )
 * 
 * @OA\Tag(
 *     name="Products",
 *     description="Gestión de productos, categorías y variantes"
 * )
 * 
 * @OA\Tag(
 *     name="Cart",
 *     description="Carrito de compras y gestión de items"
 * )
 * 
 * @OA\Tag(
 *     name="Orders",
 *     description="Gestión de pedidos y estados"
 * )
 * 
 * @OA\Tag(
 *     name="Payments",
 *     description="Procesamiento de pagos y comprobantes"
 * )
 * 
 * @OA\Tag(
 *     name="Coupons",
 *     description="Sistema de cupones y descuentos (módulo opcional)"
 * )
 * 
 * @OA\Tag(
 *     name="Admin",
 *     description="Endpoints de administración"
 * )
 * 
 * @OA\Schema(
 *     schema="ApiResponse",
 *     type="object",
 *     description="Estructura estándar de respuesta de la API",
 *     @OA\Property(
 *         property="success",
 *         type="boolean",
 *         description="Indica si la operación fue exitosa",
 *         example=true
 *     ),
 *     @OA\Property(
 *         property="message",
 *         type="string",
 *         description="Mensaje descriptivo de la operación",
 *         example="Operation completed successfully"
 *     ),
 *     @OA\Property(
 *         property="data",
 *         description="Datos de respuesta (variable según endpoint)"
 *     ),
 *     @OA\Property(
 *         property="meta",
 *         type="object",
 *         description="Metadatos adicionales (paginación, totales, etc.)",
 *         @OA\Property(
 *             property="pagination",
 *             type="object",
 *             @OA\Property(property="current_page", type="integer", example=1),
 *             @OA\Property(property="per_page", type="integer", example=15),
 *             @OA\Property(property="total", type="integer", example=150),
 *             @OA\Property(property="last_page", type="integer", example=10)
 *         )
 *     )
 * )
 * 
 * @OA\Schema(
 *     schema="ErrorResponse",
 *     type="object",
 *     description="Estructura de respuesta para errores",
 *     @OA\Property(
 *         property="success",
 *         type="boolean",
 *         description="Siempre false para errores",
 *         example=false
 *     ),
 *     @OA\Property(
 *         property="error",
 *         type="object",
 *         @OA\Property(
 *             property="code",
 *             type="string",
 *             description="Código del error",
 *             example="VALIDATION_ERROR"
 *         ),
 *         @OA\Property(
 *             property="message",
 *             type="string",
 *             description="Mensaje del error",
 *             example="The given data was invalid"
 *         ),
 *         @OA\Property(
 *             property="details",
 *             type="object",
 *             description="Detalles específicos del error (ej: errores de validación)",
 *             example={"email": {"The email field is required."}}
 *         )
 *     )
 * )
 * 
 * @OA\Schema(
 *     schema="ValidationErrorResponse",
 *     allOf={
 *         @OA\Schema(ref="#/components/schemas/ErrorResponse")
 *     },
 *     description="Respuesta específica para errores de validación (422)"
 * )
 * 
 * @OA\Schema(
 *     schema="UnauthorizedResponse",
 *     allOf={
 *         @OA\Schema(ref="#/components/schemas/ErrorResponse")
 *     },
 *     description="Respuesta para errores de autenticación (401)"
 * )
 * 
 * @OA\Schema(
 *     schema="ForbiddenResponse",
 *     allOf={
 *         @OA\Schema(ref="#/components/schemas/ErrorResponse")
 *     },
 *     description="Respuesta para errores de autorización (403)"
 * )
 * 
 * @OA\Schema(
 *     schema="NotFoundResponse",
 *     allOf={
 *         @OA\Schema(ref="#/components/schemas/ErrorResponse")
 *     },
 *     description="Respuesta para recursos no encontrados (404)"
 * )
 * 
 * @OA\Schema(
 *     schema="User",
 *     type="object",
 *     description="Modelo de usuario del sistema",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="ID único del usuario",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="Nombre completo del usuario",
 *         example="Juan Pérez"
 *     ),
 *     @OA\Property(
 *         property="email",
 *         type="string",
 *         format="email",
 *         description="Email del usuario",
 *         example="juan@example.com"
 *     ),
 *     @OA\Property(
 *         property="role",
 *         type="string",
 *         enum={"admin", "customer", "moderator"},
 *         description="Rol del usuario",
 *         example="customer"
 *     ),
 *     @OA\Property(
 *         property="status",
 *         type="string",
 *         enum={"active", "inactive", "suspended"},
 *         description="Estado del usuario",
 *         example="active"
 *     ),
 *     @OA\Property(
 *         property="phone",
 *         type="string",
 *         nullable=true,
 *         description="Número de teléfono",
 *         example="+58 412 1234567"
 *     ),
 *     @OA\Property(
 *         property="avatar",
 *         type="string",
 *         nullable=true,
 *         description="URL del avatar del usuario",
 *         example="https://example.com/avatar.jpg"
 *     ),
 *     @OA\Property(
 *         property="email_verified_at",
 *         type="string",
 *         format="date-time",
 *         nullable=true,
 *         description="Fecha de verificación del email",
 *         example="2024-01-15T10:30:00Z"
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         description="Fecha de creación",
 *         example="2024-01-01T12:00:00Z"
 *     ),
 *     @OA\Property(
 *         property="updated_at",
 *         type="string",
 *         format="date-time",
 *         description="Fecha de última actualización",
 *         example="2024-01-15T10:30:00Z"
 *     )
 * )
 */
abstract class Controller
{
    //
}
