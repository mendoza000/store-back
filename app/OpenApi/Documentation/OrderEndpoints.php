<?php

declare(strict_types=1);

namespace App\OpenApi\Documentation;

use OpenApi\Attributes as OA;

/**
 * Documentación de endpoints de pedidos
 */
class OrderEndpoints
{
    #[OA\Get(
        path: "/api/v1/orders",
        summary: "Lista de pedidos del usuario",
        description: "Obtiene la lista paginada de pedidos del usuario autenticado",
        security: [["sanctum" => []]],
        tags: ["Orders"],
        parameters: [
            new OA\Parameter(
                name: "per_page",
                description: "Número de elementos por página (máximo 50)",
                in: "query",
                required: false,
                schema: new OA\Schema(type: "integer", minimum: 1, maximum: 50, default: 15)
            ),
            new OA\Parameter(
                name: "status",
                description: "Filtrar por estado del pedido",
                in: "query",
                required: false,
                schema: new OA\Schema(
                    type: "string",
                    enum: ["pending", "paid", "processing", "shipped", "delivered", "cancelled"]
                )
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Lista de pedidos obtenida exitosamente",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Pedidos obtenidos exitosamente"),
                        new OA\Property(property: "data", type: "array", items: new OA\Items(ref: "#/components/schemas/OrderResponse/properties/data")),
                        new OA\Property(
                            property: "meta",
                            type: "object",
                            properties: [
                                new OA\Property(
                                    property: "pagination",
                                    type: "object",
                                    properties: [
                                        new OA\Property(property: "current_page", type: "integer", example: 1),
                                        new OA\Property(property: "per_page", type: "integer", example: 15),
                                        new OA\Property(property: "total", type: "integer", example: 25),
                                        new OA\Property(property: "last_page", type: "integer", example: 2)
                                    ]
                                )
                            ]
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: "No autenticado",
                content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
            ),
            new OA\Response(
                response: 500,
                description: "Error interno del servidor",
                content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
            )
        ]
    )]
    public function index() {}

    #[OA\Post(
        path: "/api/v1/orders",
        summary: "Crear nuevo pedido",
        description: "Crea un nuevo pedido desde el carrito activo del usuario",
        security: [["sanctum" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: "#/components/schemas/CreateOrderRequest")
        ),
        tags: ["Orders"],
        responses: [
            new OA\Response(
                response: 201,
                description: "Pedido creado exitosamente",
                content: new OA\JsonContent(ref: "#/components/schemas/OrderResponse")
            ),
            new OA\Response(
                response: 400,
                description: "Carrito vacío o no válido",
                content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
            ),
            new OA\Response(
                response: 401,
                description: "No autenticado",
                content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
            ),
            new OA\Response(
                response: 422,
                description: "Error de validación",
                content: new OA\JsonContent(ref: "#/components/schemas/ValidationErrorResponse")
            ),
            new OA\Response(
                response: 500,
                description: "Error interno del servidor",
                content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
            )
        ]
    )]
    public function store() {}

    #[OA\Get(
        path: "/api/v1/orders/{orderNumber}",
        summary: "Detalle de pedido",
        description: "Obtiene el detalle completo de un pedido específico",
        security: [["sanctum" => []]],
        tags: ["Orders"],
        parameters: [
            new OA\Parameter(
                name: "orderNumber",
                description: "Número del pedido",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "string", example: "ORD-20240115120000-1234")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Detalle del pedido obtenido exitosamente",
                content: new OA\JsonContent(ref: "#/components/schemas/OrderResponse")
            ),
            new OA\Response(
                response: 401,
                description: "No autenticado",
                content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
            ),
            new OA\Response(
                response: 404,
                description: "Pedido no encontrado",
                content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
            ),
            new OA\Response(
                response: 500,
                description: "Error interno del servidor",
                content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
            )
        ]
    )]
    public function show() {}

    #[OA\Put(
        path: "/api/v1/orders/{order}/cancel",
        summary: "Cancelar pedido",
        description: "Cancela un pedido específico del usuario",
        security: [["sanctum" => []]],
        requestBody: new OA\RequestBody(
            required: false,
            content: new OA\JsonContent(ref: "#/components/schemas/CancelOrderRequest")
        ),
        tags: ["Orders"],
        parameters: [
            new OA\Parameter(
                name: "order",
                description: "ID del pedido",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer", example: 1)
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Pedido cancelado exitosamente",
                content: new OA\JsonContent(ref: "#/components/schemas/OrderResponse")
            ),
            new OA\Response(
                response: 400,
                description: "El pedido no puede ser cancelado en su estado actual",
                content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
            ),
            new OA\Response(
                response: 401,
                description: "No autenticado",
                content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
            ),
            new OA\Response(
                response: 403,
                description: "No tiene permisos para cancelar este pedido",
                content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
            ),
            new OA\Response(
                response: 404,
                description: "Pedido no encontrado",
                content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
            ),
            new OA\Response(
                response: 422,
                description: "Error de validación",
                content: new OA\JsonContent(ref: "#/components/schemas/ValidationErrorResponse")
            ),
            new OA\Response(
                response: 500,
                description: "Error interno del servidor",
                content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
            )
        ]
    )]
    public function cancel() {}

    #[OA\Get(
        path: "/api/v1/admin/orders",
        summary: "Lista de pedidos (Admin)",
        description: "Obtiene la lista paginada de todos los pedidos con filtros avanzados",
        security: [["sanctum" => []], ["role" => ["admin"]]],
        tags: ["Orders - Admin"],
        parameters: [
            new OA\Parameter(
                name: "per_page",
                description: "Número de elementos por página (máximo 100)",
                in: "query",
                required: false,
                schema: new OA\Schema(type: "integer", minimum: 1, maximum: 100, default: 20)
            ),
            new OA\Parameter(
                name: "status",
                description: "Filtrar por estado del pedido",
                in: "query",
                required: false,
                schema: new OA\Schema(
                    type: "string",
                    enum: ["pending", "paid", "processing", "shipped", "delivered", "cancelled"]
                )
            ),
            new OA\Parameter(
                name: "user_id",
                description: "Filtrar por ID del usuario",
                in: "query",
                required: false,
                schema: new OA\Schema(type: "integer")
            ),
            new OA\Parameter(
                name: "date_from",
                description: "Filtrar desde fecha (YYYY-MM-DD)",
                in: "query",
                required: false,
                schema: new OA\Schema(type: "string", format: "date")
            ),
            new OA\Parameter(
                name: "date_to",
                description: "Filtrar hasta fecha (YYYY-MM-DD)",
                in: "query",
                required: false,
                schema: new OA\Schema(type: "string", format: "date")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Lista de pedidos obtenida exitosamente",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Pedidos obtenidos exitosamente"),
                        new OA\Property(property: "data", type: "array", items: new OA\Items(ref: "#/components/schemas/OrderResponse/properties/data"))
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: "No autenticado",
                content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
            ),
            new OA\Response(
                response: 403,
                description: "Sin permisos de administrador",
                content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
            )
        ]
    )]
    public function adminIndex() {}

    #[OA\Put(
        path: "/api/v1/admin/orders/{order}/status",
        summary: "Actualizar estado de pedido (Admin)",
        description: "Actualiza el estado de un pedido específico",
        security: [["sanctum" => []], ["role" => ["admin"]]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: "#/components/schemas/UpdateOrderStatusRequest")
        ),
        tags: ["Orders - Admin"],
        parameters: [
            new OA\Parameter(
                name: "order",
                description: "ID del pedido",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer", example: 1)
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Estado del pedido actualizado exitosamente",
                content: new OA\JsonContent(ref: "#/components/schemas/OrderResponse")
            ),
            new OA\Response(
                response: 400,
                description: "Transición de estado no válida",
                content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
            ),
            new OA\Response(
                response: 401,
                description: "No autenticado",
                content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
            ),
            new OA\Response(
                response: 403,
                description: "Sin permisos de administrador",
                content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
            ),
            new OA\Response(
                response: 404,
                description: "Pedido no encontrado",
                content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
            ),
            new OA\Response(
                response: 422,
                description: "Error de validación",
                content: new OA\JsonContent(ref: "#/components/schemas/ValidationErrorResponse")
            )
        ]
    )]
    public function updateStatus() {}

    #[OA\Get(
        path: "/api/v1/admin/orders/stats",
        summary: "Estadísticas de pedidos (Admin)",
        description: "Obtiene estadísticas detalladas de pedidos para el panel de administración",
        security: [["sanctum" => []], ["role" => ["admin"]]],
        tags: ["Orders - Admin"],
        parameters: [
            new OA\Parameter(
                name: "date_from",
                description: "Fecha inicio para estadísticas (YYYY-MM-DD)",
                in: "query",
                required: false,
                schema: new OA\Schema(type: "string", format: "date")
            ),
            new OA\Parameter(
                name: "date_to",
                description: "Fecha fin para estadísticas (YYYY-MM-DD)",
                in: "query",
                required: false,
                schema: new OA\Schema(type: "string", format: "date")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Estadísticas obtenidas exitosamente",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Estadísticas obtenidas exitosamente"),
                        new OA\Property(
                            property: "data",
                            type: "object",
                            properties: [
                                new OA\Property(property: "total_orders", type: "integer", example: 150),
                                new OA\Property(
                                    property: "orders_by_status",
                                    type: "object",
                                    properties: [
                                        new OA\Property(property: "pending", type: "integer", example: 10),
                                        new OA\Property(property: "paid", type: "integer", example: 25),
                                        new OA\Property(property: "processing", type: "integer", example: 30),
                                        new OA\Property(property: "shipped", type: "integer", example: 40),
                                        new OA\Property(property: "delivered", type: "integer", example: 35),
                                        new OA\Property(property: "cancelled", type: "integer", example: 10)
                                    ]
                                ),
                                new OA\Property(property: "total_revenue", type: "number", format: "float", example: 25600.50),
                                new OA\Property(property: "average_order_value", type: "number", format: "float", example: 170.67),
                                new OA\Property(
                                    property: "orders_per_day",
                                    type: "array",
                                    items: new OA\Items(
                                        type: "object",
                                        properties: [
                                            new OA\Property(property: "date", type: "string", format: "date", example: "2024-01-15"),
                                            new OA\Property(property: "count", type: "integer", example: 5)
                                        ]
                                    )
                                )
                            ]
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: "No autenticado",
                content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
            ),
            new OA\Response(
                response: 403,
                description: "Sin permisos de administrador",
                content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
            )
        ]
    )]
    public function stats() {}
}
