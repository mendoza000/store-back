<?php

declare(strict_types=1);

namespace App\OpenApi\Schemas\Cart;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "CartResponse",
    type: "object",
    description: "Respuesta exitosa del carrito",
    properties: [
        new OA\Property(
            property: "success",
            type: "boolean",
            description: "Indica si la operación fue exitosa",
            example: true
        ),
        new OA\Property(
            property: "message",
            type: "string",
            description: "Mensaje descriptivo",
            example: "Carrito obtenido exitosamente"
        ),
        new OA\Property(
            property: "data",
            type: "object",
            properties: [
                new OA\Property(
                    property: "cart",
                    type: "object",
                    nullable: true,
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 1),
                        new OA\Property(property: "user_id", type: "integer", nullable: true, example: 1),
                        new OA\Property(property: "session_id", type: "string", nullable: true, example: "550e8400-e29b-41d4-a716-446655440000"),
                        new OA\Property(property: "status", type: "string", example: "active"),
                        new OA\Property(property: "expires_at", type: "string", format: "date-time", nullable: true, example: "2024-01-15T12:00:00Z"),
                        new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2024-01-15T10:00:00Z"),
                        new OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2024-01-15T11:30:00Z")
                    ]
                ),
                new OA\Property(
                    property: "items",
                    type: "array",
                    items: new OA\Items(
                        type: "object",
                        properties: [
                            new OA\Property(property: "id", type: "integer", example: 1),
                            new OA\Property(property: "product_id", type: "integer", example: 5),
                            new OA\Property(property: "quantity", type: "integer", example: 2),
                            new OA\Property(property: "price", type: "number", format: "float", example: 99.99),
                            new OA\Property(property: "total", type: "number", format: "float", example: 199.98),
                            new OA\Property(
                                property: "product_info",
                                type: "object",
                                properties: [
                                    new OA\Property(property: "id", type: "integer", example: 5),
                                    new OA\Property(property: "name", type: "string", example: "iPhone 14 Pro"),
                                    new OA\Property(property: "image", type: "string", nullable: true, example: "https://example.com/image.jpg"),
                                    new OA\Property(property: "stock_available", type: "boolean", example: true)
                                ]
                            ),
                            new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2024-01-15T10:30:00Z"),
                            new OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2024-01-15T11:00:00Z")
                        ]
                    )
                ),
                new OA\Property(
                    property: "summary",
                    type: "object",
                    properties: [
                        new OA\Property(property: "total_items", type: "integer", description: "Cantidad total de productos", example: 2),
                        new OA\Property(property: "subtotal", type: "number", format: "float", description: "Subtotal del carrito", example: 199.98),
                        new OA\Property(property: "items_count", type: "integer", description: "Número de tipos de productos diferentes", example: 1),
                        new OA\Property(property: "is_empty", type: "boolean", description: "Indica si el carrito está vacío", example: false)
                    ]
                )
            ]
        )
    ]
)]
class CartResponseSchema {}
