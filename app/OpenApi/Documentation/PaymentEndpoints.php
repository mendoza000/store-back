<?php

declare(strict_types=1);

namespace App\OpenApi\Documentation;

use OpenApi\Attributes as OA;

/**
 * Documentación de endpoints de pagos
 */
class PaymentEndpoints
{
    // ===== PAYMENT METHODS ENDPOINTS (Público) =====
    
    #[OA\Get(
        path: "/api/v1/payment-methods",
        summary: "Listar métodos de pago disponibles",
        description: "Obtiene la lista de métodos de pago activos de la tienda",
        tags: ["Payment Methods"],
        parameters: [
            new OA\Parameter(ref: "#/components/parameters/X-Store-Id")
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Lista de métodos de pago",
                content: new OA\JsonContent(
                    type: "array",
                    items: new OA\Items(ref: "#/components/schemas/PaymentMethod")
                )
            ),
            new OA\Response(
                response: 404,
                description: "Tienda no encontrada",
                content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
            )
        ]
    )]
    public function getPaymentMethods() {}

    #[OA\Get(
        path: "/api/v1/payment-methods/{id}",
        summary: "Obtener detalle de método de pago",
        description: "Devuelve la información detallada de un método de pago específico",
        tags: ["Payment Methods"],
        parameters: [
            new OA\Parameter(ref: "#/components/parameters/X-Store-Id"),
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "ID del método de pago",
                schema: new OA\Schema(type: "integer", example: 1)
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Detalle del método de pago",
                content: new OA\JsonContent(ref: "#/components/schemas/PaymentMethod")
            ),
            new OA\Response(
                response: 404,
                description: "Método de pago no encontrado",
                content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
            )
        ]
    )]
    public function getPaymentMethod() {}

    // ===== PAYMENT ENDPOINTS (Usuarios autenticados) =====

    #[OA\Post(
        path: "/api/v1/orders/{order}/payments",
        summary: "Reportar pago para una orden",
        description: "Permite al cliente reportar un pago realizado para una orden específica",
        security: [["sanctum" => []]],
        tags: ["Payments"],
        parameters: [
            new OA\Parameter(ref: "#/components/parameters/X-Store-Id"),
            new OA\Parameter(
                name: "order",
                in: "path",
                required: true,
                description: "ID de la orden",
                schema: new OA\Schema(type: "integer", example: 1)
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: [
                new OA\MediaType(
                    mediaType: "multipart/form-data",
                    schema: new OA\Schema(ref: "#/components/schemas/ReportPaymentRequest")
                ),
                new OA\MediaType(
                    mediaType: "application/json",
                    schema: new OA\Schema(ref: "#/components/schemas/ReportPaymentRequest")
                )
            ]
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Pago reportado exitosamente",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Pago reportado exitosamente"),
                        new OA\Property(property: "data", ref: "#/components/schemas/Payment")
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Orden no encontrada",
                content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
            ),
            new OA\Response(
                response: 422,
                description: "Error de validación",
                content: new OA\JsonContent(ref: "#/components/schemas/ValidationErrorResponse")
            ),
            new OA\Response(
                response: 403,
                description: "No autorizado para esta orden",
                content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
            )
        ]
    )]
    public function reportPayment() {}

    #[OA\Get(
        path: "/api/v1/payments/{id}",
        summary: "Obtener estado del pago",
        description: "Obtiene el estado actual de un pago específico",
        security: [["sanctum" => []]],
        tags: ["Payments"],
        parameters: [
            new OA\Parameter(ref: "#/components/parameters/X-Store-Id"),
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "ID del pago",
                schema: new OA\Schema(type: "integer", example: 1)
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Estado del pago",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Pago obtenido exitosamente"),
                        new OA\Property(property: "data", ref: "#/components/schemas/Payment")
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Pago no encontrado",
                content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
            ),
            new OA\Response(
                response: 403,
                description: "No autorizado para ver este pago",
                content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
            )
        ]
    )]
    public function getPayment() {}

    #[OA\Put(
        path: "/api/v1/payments/{id}",
        summary: "Actualizar comprobante de pago",
        description: "Permite actualizar el comprobante de pago (solo si está en estado pendiente)",
        security: [["sanctum" => []]],
        tags: ["Payments"],
        parameters: [
            new OA\Parameter(ref: "#/components/parameters/X-Store-Id"),
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "ID del pago",
                schema: new OA\Schema(type: "integer", example: 1)
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: [
                new OA\MediaType(
                    mediaType: "multipart/form-data",
                    schema: new OA\Schema(ref: "#/components/schemas/UpdatePaymentRequest")
                ),
                new OA\MediaType(
                    mediaType: "application/json",
                    schema: new OA\Schema(ref: "#/components/schemas/UpdatePaymentRequest")
                )
            ]
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Comprobante actualizado exitosamente",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Comprobante de pago actualizado exitosamente"),
                        new OA\Property(property: "data", ref: "#/components/schemas/Payment")
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Pago no encontrado",
                content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
            ),
            new OA\Response(
                response: 403,
                description: "No autorizado o pago no editable",
                content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
            ),
            new OA\Response(
                response: 409,
                description: "Conflicto - pago no está en estado pendiente",
                content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
            ),
            new OA\Response(
                response: 422,
                description: "Error de validación",
                content: new OA\JsonContent(ref: "#/components/schemas/ValidationErrorResponse")
            )
        ]
    )]
    public function updatePayment() {}

    // ===== ADMIN PAYMENT ENDPOINTS (Solo administradores) =====

    #[OA\Get(
        path: "/api/v1/admin/payments",
        summary: "Lista de pagos para administradores",
        description: "Obtiene la lista paginada de pagos con filtros avanzados para administradores",
        security: [["sanctum" => []]],
        tags: ["Admin Payments"],
        parameters: [
            new OA\Parameter(ref: "#/components/parameters/X-Store-Id"),
            new OA\Parameter(
                name: "per_page",
                in: "query",
                required: false,
                description: "Número de elementos por página (máximo 100)",
                schema: new OA\Schema(type: "integer", minimum: 1, maximum: 100, default: 15)
            ),
            new OA\Parameter(
                name: "status",
                in: "query",
                required: false,
                description: "Filtrar por estado del pago",
                schema: new OA\Schema(
                    type: "string",
                    enum: ["pending", "verified", "rejected", "refunded"]
                )
            ),
            new OA\Parameter(
                name: "payment_method_id",
                in: "query",
                required: false,
                description: "Filtrar por método de pago",
                schema: new OA\Schema(type: "integer")
            ),
            new OA\Parameter(
                name: "customer_id",
                in: "query",
                required: false,
                description: "Filtrar por cliente",
                schema: new OA\Schema(type: "integer")
            ),
            new OA\Parameter(
                name: "order_id",
                in: "query",
                required: false,
                description: "Filtrar por orden",
                schema: new OA\Schema(type: "integer")
            ),
            new OA\Parameter(
                name: "created_from",
                in: "query",
                required: false,
                description: "Fecha de inicio (formato: Y-m-d)",
                schema: new OA\Schema(type: "string", format: "date", example: "2024-01-01")
            ),
            new OA\Parameter(
                name: "created_to",
                in: "query",
                required: false,
                description: "Fecha de fin (formato: Y-m-d)",
                schema: new OA\Schema(type: "string", format: "date", example: "2024-01-31")
            ),
            new OA\Parameter(
                name: "amount_min",
                in: "query",
                required: false,
                description: "Monto mínimo",
                schema: new OA\Schema(type: "number", format: "float")
            ),
            new OA\Parameter(
                name: "amount_max",
                in: "query",
                required: false,
                description: "Monto máximo",
                schema: new OA\Schema(type: "number", format: "float")
            ),
            new OA\Parameter(
                name: "search",
                in: "query",
                required: false,
                description: "Búsqueda en referencia, notas o nombre del cliente",
                schema: new OA\Schema(type: "string")
            ),
            new OA\Parameter(
                name: "priority",
                in: "query",
                required: false,
                description: "Filtrar por prioridad",
                schema: new OA\Schema(
                    type: "string",
                    enum: ["high", "medium", "low"]
                )
            ),
            new OA\Parameter(
                name: "requires_attention",
                in: "query",
                required: false,
                description: "Solo pagos que requieren atención",
                schema: new OA\Schema(type: "boolean")
            ),
            new OA\Parameter(
                name: "sort_by",
                in: "query",
                required: false,
                description: "Campo de ordenamiento",
                schema: new OA\Schema(
                    type: "string",
                    enum: ["created_at", "updated_at", "amount", "status"],
                    default: "created_at"
                )
            ),
            new OA\Parameter(
                name: "sort_direction",
                in: "query",
                required: false,
                description: "Dirección del ordenamiento",
                schema: new OA\Schema(
                    type: "string",
                    enum: ["asc", "desc"],
                    default: "desc"
                )
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Lista de pagos obtenida exitosamente",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Lista de pagos obtenida exitosamente"),
                        new OA\Property(
                            property: "data",
                            type: "object",
                            properties: [
                                new OA\Property(
                                    property: "payments",
                                    type: "array",
                                    items: new OA\Items(ref: "#/components/schemas/Payment")
                                ),
                                new OA\Property(
                                    property: "pagination",
                                    type: "object",
                                    properties: [
                                        new OA\Property(property: "current_page", type: "integer", example: 1),
                                        new OA\Property(property: "last_page", type: "integer", example: 5),
                                        new OA\Property(property: "per_page", type: "integer", example: 15),
                                        new OA\Property(property: "total", type: "integer", example: 75),
                                        new OA\Property(property: "from", type: "integer", example: 1),
                                        new OA\Property(property: "to", type: "integer", example: 15)
                                    ]
                                ),
                                new OA\Property(
                                    property: "filters_applied",
                                    type: "object",
                                    description: "Filtros aplicados en la consulta"
                                )
                            ]
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 403,
                description: "Acceso denegado - solo administradores",
                content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
            )
        ]
    )]
    public function getAdminPayments() {}

    #[OA\Post(
        path: "/api/v1/admin/payments/{id}/verify",
        summary: "Verificar/Aprobar pago",
        description: "Permite a un administrador verificar y aprobar un pago pendiente",
        security: [["sanctum" => []]],
        tags: ["Admin Payments"],
        parameters: [
            new OA\Parameter(ref: "#/components/parameters/X-Store-Id"),
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "ID del pago",
                schema: new OA\Schema(type: "integer", example: 1)
            )
        ],
        requestBody: new OA\RequestBody(
            required: false,
            content: new OA\JsonContent(ref: "#/components/schemas/PaymentVerifyRequest")
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Pago verificado exitosamente",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Pago verificado exitosamente"),
                        new OA\Property(property: "data", ref: "#/components/schemas/Payment")
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Pago no encontrado",
                content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
            ),
            new OA\Response(
                response: 403,
                description: "Acceso denegado - solo administradores",
                content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
            ),
            new OA\Response(
                response: 409,
                description: "Conflicto - pago no puede ser verificado",
                content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
            ),
            new OA\Response(
                response: 422,
                description: "Error de validación",
                content: new OA\JsonContent(ref: "#/components/schemas/ValidationErrorResponse")
            )
        ]
    )]
    public function verifyPayment() {}

    #[OA\Post(
        path: "/api/v1/admin/payments/{id}/reject",
        summary: "Rechazar pago",
        description: "Permite a un administrador rechazar un pago con una razón específica",
        security: [["sanctum" => []]],
        tags: ["Admin Payments"],
        parameters: [
            new OA\Parameter(ref: "#/components/parameters/X-Store-Id"),
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "ID del pago",
                schema: new OA\Schema(type: "integer", example: 1)
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: "#/components/schemas/PaymentRejectRequest")
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Pago rechazado exitosamente",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Pago rechazado exitosamente"),
                        new OA\Property(property: "data", ref: "#/components/schemas/Payment")
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Pago no encontrado",
                content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
            ),
            new OA\Response(
                response: 403,
                description: "Acceso denegado - solo administradores",
                content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
            ),
            new OA\Response(
                response: 409,
                description: "Conflicto - pago no puede ser rechazado",
                content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
            ),
            new OA\Response(
                response: 422,
                description: "Error de validación",
                content: new OA\JsonContent(ref: "#/components/schemas/ValidationErrorResponse")
            )
        ]
    )]
    public function rejectPayment() {}

    #[OA\Get(
        path: "/api/v1/admin/payments/stats",
        summary: "Estadísticas de pagos",
        description: "Obtiene estadísticas detalladas de pagos para el dashboard administrativo",
        security: [["sanctum" => []]],
        tags: ["Admin Payments"],
        parameters: [
            new OA\Parameter(ref: "#/components/parameters/X-Store-Id"),
            new OA\Parameter(
                name: "period",
                in: "query",
                required: false,
                description: "Período de tiempo para las estadísticas",
                schema: new OA\Schema(
                    type: "string",
                    enum: ["today", "week", "month", "quarter", "year", "custom"],
                    default: "month"
                )
            ),
            new OA\Parameter(
                name: "start_date",
                in: "query",
                required: false,
                description: "Fecha de inicio para período personalizado (formato: Y-m-d)",
                schema: new OA\Schema(type: "string", format: "date", example: "2024-01-01")
            ),
            new OA\Parameter(
                name: "end_date",
                in: "query",
                required: false,
                description: "Fecha de fin para período personalizado (formato: Y-m-d)",
                schema: new OA\Schema(type: "string", format: "date", example: "2024-01-31")
            ),
            new OA\Parameter(
                name: "include_charts",
                in: "query",
                required: false,
                description: "Incluir datos para gráficos",
                schema: new OA\Schema(type: "boolean", default: false)
            ),
            new OA\Parameter(
                name: "group_by",
                in: "query",
                required: false,
                description: "Agrupar estadísticas por período",
                schema: new OA\Schema(
                    type: "string",
                    enum: ["day", "week", "month"],
                    default: "day"
                )
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Estadísticas obtenidas exitosamente",
                content: new OA\JsonContent(ref: "#/components/schemas/PaymentStatsResponse")
            ),
            new OA\Response(
                response: 403,
                description: "Acceso denegado - solo administradores",
                content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
            ),
            new OA\Response(
                response: 422,
                description: "Error de validación en parámetros",
                content: new OA\JsonContent(ref: "#/components/schemas/ValidationErrorResponse")
            )
        ]
    )]
    public function getPaymentStats() {}
} 